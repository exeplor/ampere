<?php

namespace Ampere\Services\ReflectionModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Class ReflectionModel
 * @package Ampere\Services\ReflectionModel
 */
class ReflectionModel
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var ModelField[]
     */
    private $fields = [];

    /**
     * @var ModelField
     */
    private $primaryField = null;

    /**
     * @var array
     */
    private $relationWithoutField = [];

    /**
     * @var array
     */
    private static $modelReference = [];

    /**
     * ReflectionModel constructor.
     * @param string $modelClass
     */
    public function __construct(string $modelClass)
    {
        if (!class_exists($modelClass)) {
            throw new \Exception('Class ' . $modelClass . ' not found');
        }

        if (!is_a($modelClass, Model::class, true)) {
            throw new \Exception('Class ' . $modelClass . ' is not eloquent model');
        }

        $this->model = new $modelClass;
    }

    /**
     * @return ReflectionModel
     */
    public function load(): self
    {
        $this->mapDatabaseFields();
        return $this;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return get_class($this->model);
    }

    /**
     * @return string
     */
    public function getModelName(): string
    {
        return substr(strrchr($this->getClassName(), "\\"), 1);
    }

    /**
     * @return ModelField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return ModelField|null
     */
    public function getPrimaryField(): ?ModelField
    {
        return $this->primaryField;
    }

    /**
     * @return array
     */
    public function getPrimaryStringFields(): array
    {
        $fields = array_filter($this->fields, function(ModelField $field){
            return $field->isSmallString();
        });

        return $fields;
    }

    /**
     * @return ReflectionModel[]
     */
    public function getRelationsWithoutFields(): array
    {
        return $this->relationWithoutField;
    }

    /**
     * Get fields
     */
    private function mapDatabaseFields()
    {
        $schema = \DB::select('DESCRIBE ' . $this->model->getTable());

        $reflectionClass = new \ReflectionClass($this->model);
        $classMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        $classMethods = array_filter($classMethods, function(\ReflectionMethod $method){
            if ($method->class === get_class($this->model)) {
                if ($method->getNumberOfRequiredParameters() === 0) {
                    return true;
                }
            }
        });

        $relations = [];

        foreach($classMethods as $method) {
            $reflectionMethod = new \ReflectionMethod($this->model, $method->getName());
            if (!preg_match('/@return.+?BelongsTo/i', $reflectionMethod->getDocComment())) {
                continue;
            }

            $relation = $this->model->{$method->getName()}();
            if ($relation instanceof Relation) {
                if ($relation instanceof BelongsTo) {
                    $relations[$relation->getForeignKeyName()] = [
                        'model' => $relation->getModel(),
                        'method' => $method->getName()
                    ];
                }

                if ($relation instanceof HasManyThrough) {
                    $reflectionModel = new self(get_class($relation->getModel()));
                    $reflectionModel->load();

                    $this->relationWithoutField[$method->getName()] = $reflectionModel;
                }
            }
        }

        $fields = [];
        foreach($schema as $row) {
            $field = new ModelField((array)$row);

            if ($field->isPrimary()) {
                $this->primaryField = $field;
            }

            if (isset($relations[$field->getName()])) {
                $relation = $relations[$field->getName()];

                $className = get_class($relation['model']);

                if (empty(self::$modelReference[$className])) {
                    self::$modelReference[$className] = new self(get_class($relation['model']));
                    self::$modelReference[$className]->load();
                }

                $reflectionModel = self::$modelReference[$className];

                $field->setRelation($relation['method'], $reflectionModel);
            }

            $fields[$field->getName()] = $field;
        }

        $this->fields = $fields;
    }
}