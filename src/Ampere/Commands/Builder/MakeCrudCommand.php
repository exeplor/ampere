<?php

namespace Ampere\Commands\Builder;

use Ampere\Commands\Command;
use Ampere\Services\Common;
use Ampere\Services\Helper\SelectSearch;
use Ampere\Services\ReflectionModel\ModelField;
use Ampere\Services\ReflectionModel\ReflectionModel;
use Ampere\Services\StubBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Class MakeCrudCommand
 * @package Ampere\Commands\Builder
 */
class MakeCrudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'am:crud {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make crud controller';

    /**
     * @var ReflectionModel
     */
    protected $model;

    /**
     * @var array
     */
    protected $menu = [];

    /**
     * @var StubBuilder
     */
    protected $stubBuilder;

    /**
     * @var array
     */
    protected $usedControllers = [];

    /**
     * MakeCrudCommand constructor.
     * @param StubBuilder $stubBuilder
     */
    public function __construct(StubBuilder $stubBuilder)
    {
        parent::__construct();

        $this->stubBuilder = $stubBuilder;
    }

    /**
     * Handle command
     */
    public function handle()
    {
        $className = $this->getModelClass();

        $this->model = $this->getReflectionModel($className);
        $this->menu = $this->getMenu();

        $this->buildController();
        $this->buildViews();
    }

    /**
     * @return array
     */
    private function getMenu(): array
    {
        $this->alert('Do you want create menu item?');
        $this->info('For create submenu just use \'>\' symbol.');

        $menu = $this->ask('Enter menu name (press Enter to skip)');

        if (empty($menu)) {
            return [];
        }

        $menu = ucwords($menu);
        return preg_split('/\s*>\s*/', $menu);
    }

    /**
     * @return string
     */
    private function getModelClass(): string
    {
        $this->alert('First you need to set model for your CRUD controller');

        $name = $this->ask('Enter model name');

        /**
         * @var Application $appNamespace
         */
        $application = \Illuminate\Container\Container::getInstance();
        $appNamespace = $application->getNamespace();

        $classes = collect(File::allFiles(app_path()))->map(function ($item) use ($appNamespace) {
            $rel = $item->getRelativePathName();
            $class = sprintf('\%s%s%s', $appNamespace, '',
                implode('\\', explode('/', substr($rel, 0, strrpos($rel, '.')))));
            return class_exists($class) ? $class : null;
        })->filter();

        $models = [];
        foreach($classes as $class) {
            if (is_subclass_of($class, Model::class)) {
                if (preg_match('/' . $name . '$/i', $class)) {
                    $models[] = $class;
                }
            }
        }

        if (empty($models)) {
            $this->comment('No models found with name "' . $name . '". Please choice another model.');
            return $this->getModelClass();
        }

        $className = $models[0];

        if (count($models) > 1) {
            $className = $this->choice('Founded a few models with name "' . $name . '". Select one:', $models);
        }

        $this->info('Good. Used model ' . $className . PHP_EOL);

        return substr($className, 1, strlen($className));
    }

    /**
     * @return string
     */
    private function getControllerNamespace(): string
    {
        $namespace = ampere_config('routing.namespace');
        $names = $this->getControllerNamePath();

        if (count($names) > 1) {
            $namespace .= '\\' . implode('\\', array_slice($names, 0, -1));
        }

        return $namespace;
    }

    /**
     * @return string
     */
    private function getControllerName(): string
    {
        $names = $this->getControllerNamePath();
        return ucfirst(strtolower($names[count($names) - 1])) . 'Controller';
    }

    /**
     * @param bool $lowerCase
     * @return array
     */
    private function getControllerNamePath(bool $lowerCase = false): array
    {
        $names = [];

        $argument = $this->argument('name');
        $list = explode('/', $argument);

        foreach($list as $name) {
            $name = strtolower($name);
            $names[] = $lowerCase ? $name : ucfirst($name);
        }

        return $names;
    }

    /**
     * @param string $className
     * @return ReflectionModel
     */
    private function getReflectionModel(string $className): ReflectionModel
    {
        $reflectionModel = new ReflectionModel($className);
        return $reflectionModel->load();
    }

    /**
     * @return bool
     */
    private function buildController(): bool
    {
        $params = [
            'namespace' => $this->getControllerNamespace(),
            'controller' => $this->getControllerName(),
            'menu' => $this->getMenuTemplate(),
            'modelClass' => $this->model->getClassName(),
            'modelName' => $this->model->getModelName(),
            'modelTitle' => $this->model->getModelName(),
            'view' => implode('.', $this->getControllerNamePath(true)),
            'grid' => $this->getGridTemplate(),
            'validationRules' => $this->getValidationRulesTemplate(),
            'search' => $this->getSearchTemplate(),
            'usedControllers' => $this->getUsedControllersTemplate()
        ];

        $folder = str_replace(ampere_config('routing.namespace'), '', $params['namespace']);
        $folder = str_replace('\\', '/', $folder);

        $targetTemplatePath = ampere_config('routing.folder') . $folder . '/' . $params['controller'] . '.php';
        $targetPath = base_path($targetTemplatePath);

        $this->stubBuilder->setStub('crud/controller')
            ->setTargetPath($targetPath);

        $this->stubBuilder->setParams($params);

        $controllerPath = str_replace('\\', '/', $params['namespace'] . '\\' . $params['controller']);

        if ($this->stubBuilder->isFileExists()) {
            $this->error('Controller ' . $controllerPath . ' already exists');
            $answer = $this->ask('You really want to rewrite this file? (enter "yes" for accept)');

            if ($answer !== 'yes') {
                $this->comment('Controller not created');
                return false;
            }
        }

        if ($this->stubBuilder->create()) {
            $this->comment('Controller ' . $controllerPath . ' created');
        }

        return true;
    }

    /**
     * @return bool
     */
    private function buildViews(): bool
    {
        $params = [
            'controller' => $this->getControllerNamespace() . '\\' . $this->getControllerName(),
            'menu' => $this->getMenuTemplate(),
            'modelClass' => $this->model->getClassName(),
            'modelName' => $this->model->getModelName(),
            'modelTitle' => $this->model->getModelName(),
            'modelTitlePlural' => Str::plural($this->model->getModelName()),
            'view' => implode('.', $this->getControllerNamePath(true))
        ];

        $viewFolderPath = resource_path('views/'. ampere_config('views.name') . '/pages/' . str_replace('.', '/', $params['view'])) . '/';
        $this->stubBuilder->setStub('crud/index.view')
            ->setTargetPath($viewFolderPath . 'index.blade.php');

        $this->stubBuilder->setParams($params);

        if ($this->stubBuilder->create()) {
            $this->comment('Index view created');
        }

        $this->stubBuilder->setStub('crud/form.view')
            ->setTargetPath($viewFolderPath . 'form.blade.php');

        $params['controls'] = $this->getFormControlsTemplate();

        $this->stubBuilder->setParams($params);

        if ($this->stubBuilder->create()) {
            return true;
        }
    }

    /**
     * @return string
     */
    private function getMenuTemplate(): string
    {
        if (count($this->menu) > 0) {
            return PHP_EOL . "\t * @menu " . implode(' > ', $this->menu);
        }
    }

    /**
     * @return string
     */
    private function getSearchTemplate(): ?string
    {
        $fields = $this->model->getFields();

        $template = [];

        foreach($fields as $field) {
            if ($field->hasRelation()) {
                $model = $field->getRelationReflectionModel();
                $fields = $model->getPrimaryStringFields();

                $fields = array_values(array_map(function(ModelField $field){
                    return $field->getName();
                }, $fields));

                $template[] = "\$search->add('{$field->getName()}', {$model->getModelName()}::class, ['" . implode("', '", $fields) . "'], '$fields[0]');";

                $this->usedControllers[] = $model->getClassName();
            }
        }

        $relations = $this->model->getRelationsWithoutFields();
        foreach($relations as $name => $model) {
            $fields = $model->getPrimaryStringFields();

            $fields = array_values(array_map(function(ModelField $field){
                return $field->getName();
            }, $fields));

            $template[] = "\$search->add('{$name}', {$model->getModelName()}::class, ['" . implode("', '", $fields) . "'], '$fields[0]');";

            $this->usedControllers[] = $model->getClassName();
        }

        if (count($template) === 0) {
            return null;
        }

        $this->usedControllers[] = SelectSearch::class;

        $template = implode("\n\t\t", $template);

        $this->stubBuilder->setStub('crud/search.method');
        $this->stubBuilder->setParams([
            'fields' => $template
        ]);

        return $this->stubBuilder->render();
    }

    /**
     * @return string
     */
    private function getGridTemplate(): ?string
    {
        $fields = $this->model->getFields();

        $grid = [];

        foreach($fields as $field) {
            $name = $field->getName();

            if ($field->isPrimary()) {
                $grid[] = "->column('$name', '#')->strict()->sortable()->asc()";

            } else if ($field->isString() && in_array($field->getStringType(), ['char', 'varchar'])) {
                $grid[] = "->column('$name')->search()";

            } else if ($field->isDate()) {
                $grid[] = "->column('$name')->date()->sortable()";

            } else if ($field->isBit()) {
                $grid[] = "->column('$name')->dropdown([0 => 'No', 1 => 'Yes'])";

            } else if ($field->hasRelation()) {
                $relationName = $field->getRelationName();
                $relationField = $field->getRelationField();

                $grid[] = "->column('$relationName.$relationField')->strict()";

            } else if ($field->isInteger()) {
                $grid[] = "->column('$name')->strict()->sortable()";
            }
        }

        if (count($grid) > 0) {
            $content = ["\t\t" . '$grid'];
            foreach ($grid as $item) {
                $content[] = "\t\t\t" . $item;
            }

            return implode(PHP_EOL, $content) . ';';
        }
    }

    /**
     * @return string|null
     */
    private function getValidationRulesTemplate(): ?string
    {
        $fields = $this->model->getFields();

        $rules = [];

        foreach($fields as $field) {
            $rule = [];

            if (in_array($field->getName(), ['created_at', 'updated_at', 'id'])) {
                continue;
            }

            if ($field->isRequired()) {
                $rule[] = 'required';
            }

            if ($field->isString()) {
                $rule[] = 'string';
            }

            if ($field->isJson()) {
                $rule[] = 'array';
            }

            if ($field->isBit()) {
                $rule[] = 'boolean';

            } else if ($field->isNumeric()) {
                $rule[] = $field->isInteger() ? 'integer' : 'numeric';
            }

            if ($field->isDate()) {
                $rule[] = 'date';
            }

            $rules[$field->getName()] = $rule;
        }

        if (count($rules) > 0) {
            $content = [];

            foreach ($rules as $field => $list) {
                $content[] = "\t\t\t'$field' => ['" . implode("', '", $list) . "']";
            }

            return implode(', ' . PHP_EOL, $content);
        }
    }

    /**
     * @return string
     */
    private function getFormControlsTemplate(): string
    {
        $fields = $this->model->getFields();
        $form = [];

        foreach($fields as $field) {
            $name = $field->getName();
            $control = [];

            if (in_array($name, ['id'])) {
                continue;
            }

            $title = Common::convertModelFieldToTitleExtended($name);
            $titleLower = strtolower($title);

            if ($field->hasRelation()) {
                $controller = $this->getControllerNamespace() . '\\' . $this->getControllerName();
                $control = [
                    "\$form->select('$name', '$title')",
                    "->placeholder('Select $titleLower')",
                    "->source($controller::route('search'))"
                ];

            } else {
                if ($field->isBit()) {
                    $control = [
                        "\$form->select('$name', '$title')",
                        "->options([1 => 'Yes', 0 => 'No'])"
                    ];

                } else if ($field->isNumeric()) {
                    $control = [
                        "\$form->input('$name', '$title')",
                        "->placeholder('Enter $titleLower')"
                    ];

                } else if ($field->isString()) {
                    if ($field->isBigString()) {
                        $control = [
                            "\$form->textarea('$name', '$title')",
                            "->placeholder('Enter $titleLower')"
                        ];
                    } else {
                        $control = [
                            "\$form->input('$name', '$title')",
                            "->placeholder('Enter $titleLower')"
                        ];
                    }
                }
            }

            if (count($control) > 0) {
                $form[] = '{!! ' . $control[0];
                $form[] = "\t\t" . implode("\n\t\t\t\t\t\t\t", array_slice($control, 1));
                $form[] = "!!}\n";
            }
        }

        $relations = $this->model->getRelationsWithoutFields();

        foreach($relations as $field => $model) {
            $title = Common::convertModelFieldToTitleExtended($field);
            $titleLower = strtolower($title);

            $controller = $this->getControllerNamespace() . '\\' . $this->getControllerName();
            $control = [
                "\$form->select('$field', '$title')",
                "->placeholder('Select $titleLower')",
                "->multiple()",
                "->source($controller::route('search'))"
            ];

            if (count($control) > 0) {
                $form[] = '{!! ' . $control[0];
                $form[] = "\t\t" . implode("\n\t\t\t\t\t\t\t", array_slice($control, 1));
                $form[] = "!!}\n";
            }
        }

        return implode("\n\t\t\t\t\t", $form);
    }

    /**
     * @return string
     */
    private function getUsedControllersTemplate(): string
    {
        $list = array_diff($this->usedControllers, [$this->model->getClassName()]);

        $controllers = array_unique($list);
        $controllers = array_map(function ($name) {
            return 'use ' . $name . ';';
        }, $controllers);

        return "\n" . implode("\n", $controllers);
    }
}
