<?php

namespace Ampere\Services\Grid;

use Ampere\Ampere;
use Ampere\Services\Common;
use Ampere\Services\Grid\Source\DataSource;
use Ampere\Services\Grid\Source\ExternalDataSource;
use Ampere\Services\Grid\Source\ModelDataSource;
use Ampere\Services\Route;
use Ampere\Services\Workshop\Component;
use Ampere\Services\Workshop\Layout;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

/**
 * Class Grid
 * @package Ampere\Services
 */
class Grid
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Ampere
     */
    private $ampere;

    /**
     * @var GridColumn[]
     */
    private $columns = [];

    /**
     * @var GridAction[]
     */
    private $actions = [];

    /**
     * @var DataSource
     */
    private $dataSource;

    /**
     * @var string
     */
    private $tableName = 'default';

    /**
     * @var int
     */
    private $itemsPerPage = 12;

    /**
     * @var bool
     */
    private $exportEnabled = true;

    /**
     * @var int
     */
    private $exportLimit = 1000;

    /**
     * Grid constructor.
     * @param Request $request
     */
    public function __construct(Request $request, Ampere $ampere)
    {
        $this->request = $request;
        $this->ampere = $ampere;
    }

    /**
     * @param string $field
     * @param string|null $title
     * @return GridColumn
     */
    public function column(string $field, string $title = null): GridColumn
    {
        $gridColumn = new GridColumn($this, $field, $title);
        $this->columns[$field] = $gridColumn;

        return $gridColumn;
    }

    /**
     * @param string $title
     * @param string|null $attribute
     * @return GridAction
     */
    public function action(string $title, string $attribute = null): GridAction
    {
        $gridAction = new GridAction();
        $gridAction->title($title);

        if ($attribute) {
            $gridAction->attribute($attribute);
        }

        $this->actions[] = $gridAction;

        return $gridAction;
    }

    /**
     * @param bool $enabled
     * @return Grid
     */
    public function export(bool $enabled): self
    {
        $this->exportEnabled = $enabled;
        return $this;
    }

    /**
     * @param string $class
     * @return Grid
     */
    public function model(string $class): self
    {
        $this->dataSource = new ModelDataSource($this);
        $this->dataSource->setModel($class);
        return $this;
    }

    /**
     * @param \Closure $closure
     * @return Grid
     */
    public function external(\Closure $closure): self
    {
        $this->dataSource = new ExternalDataSource($this);
        $this->dataSource->setCallback($closure);
        return $this;
    }

    /**
     * @param int $perPage
     * @return Grid
     */
    public function limit(int $perPage): self
    {
        $this->itemsPerPage = $perPage;
        return $this;
    }

    /**
     * @param string $name
     * @return Grid
     */
    public function name(string $name): self
    {
        $this->tableName = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function search()
    {
        $filter = $this->getFilter();
        $source = $this->dataSource;

        $offset = ($this->getCurrentPage() - 1) * $this->itemsPerPage;

        $source->setColumns($this->columns)
            ->setFilter($filter)
            ->setLimit($this->itemsPerPage)
            ->setOffset($offset);

        if ($sortField = $this->getSortField()) {
            $source->setSortField($sortField);
        }

        if ($sortDirection = $this->getSortDirection()) {
            $source->setSortDirection($sortDirection);
        }

        $this->processExportRequest();

        if ($this->actions) {
            $this->addActionsColumn();
        }

        $source->execute();

        $this->processAjaxRequest();
    }

    /**
     * @return array[]
     */
    public function getColumns(): array
    {
        $columns = array_map(function(GridColumn $column){
            $meta = $column->get();

            $title = $meta['title'] ?? Common::convertModelFieldToTitle($meta['field']);
            $data = array_merge($meta, [
                'title' => $title,
                'hasFilter' => $meta['searchable'] || $meta['sortable'] || $meta['date'] || $meta['dropdown'],
                'isInputFilter' => $meta['searchable'] || $meta['sortable'] || $meta['date'],
                'isBaseColumn' => $meta['field'] === '__actions'
            ]);

            return $data;
        }, $this->columns);

        return $columns;
    }

    /**
     * @return array
     */
    public function getRows(): array
    {
        $rows = $this->dataSource->getData();

        $data = array_map(function($row) {
            $rows = [];
            foreach($this->columns as $field => $column) {
                $fields = explode('.', $field);

                if (count($fields) === 2) {
                    $value = $row->{$fields[0]} ? $row->{$fields[0]}->{$fields[1]} : null;
                } else {
                    $value = $row->$field;
                }

                if ($dropdown = $column->get('dropdown')) {
                    $value = $dropdown[$value] ?? null;
                }

                if ($display = $column->get('display')) {
                    $rows[$field] = $display($row);
                } else {
                    $rows[$field] = $value;
                }
            }

            return $rows;
        }, $rows);

        return $data;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->dataSource->getTotalCount();
    }

    /**
     * @return LengthAwarePaginator
     */
    public function getPagination(): LengthAwarePaginator
    {
        $pagination = new LengthAwarePaginator($this->getRows(), $this->getTotalCount(), $this->itemsPerPage, $this->getCurrentPage());
        return $pagination;
    }

    /**
     * @return bool
     */
    public function hasExport(): bool
    {
        return $this->exportEnabled;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'tableFilter_' . $this->tableName;
    }

    /**
     * @return array
     */
    private function getFilter(): array
    {
        $query = $this->getQuery();
        return $query['filter'] ?? [];
    }

    /**
     * @return array
     */
    private function getQuery(): array
    {
        return $this->request->input($this->getTableName(), []);
    }

    /**
     * @return int
     */
    private function getCurrentPage(): int
    {
        $filter = $this->getQuery();
        return $filter['page'] ?? 1;
    }

    /**
     * @return null|string
     */
    private function getSortField(): ?string
    {
        $filter = $this->getQuery();
        $sortField = $filter['sort']['field'] ?? null;

        if (empty($sortField)) {
            foreach($this->columns as $field => $column) {
                if ($column->get('sortByDefault')) {
                    return $field;
                }
            }

        } else {
            if (empty($this->columns[$sortField])) {
                throw new \Exception('Sorting field "' . $sortField . '" is incorrect');
            }

            if (!$this->columns[$sortField]->get('sortable')) {
                throw new \Exception('Sorting field "' . $sortField . '" is not sortable');
            }

            return $sortField;
        }
    }

    /**
     * @return null|string
     */
    private function getSortDirection(): ?string
    {
        $filter = $this->getQuery();
        $sortDirection = strtoupper($filter['sort']['direction'] ?? GridColumn::SORT_ASC);

        if (!in_array($sortDirection, [GridColumn::SORT_ASC, GridColumn::SORT_DESC])) {
            throw new \Exception('Incorrect sort direction "' . $sortDirection . '"');
        }

        return $sortDirection;
    }

    /**
     * Handle ajax
     */
    private function processAjaxRequest()
    {
        if ($this->request->ajax()) {
            $component = new Component(new Layout\Builder());

            throw new HttpResponseException(response()->json([
                'rows' => $component->build('grid.rows', ['rows' => $this->getRows()]) . '',
                'pagination' => $component->build('grid.pagination', ['pagination' => $this->getPagination()]) . '',
            ]));
        }
    }

    /**
     * Handle export
     */
    private function processExportRequest()
    {
        $filter = $this->getQuery();
        
        if (isset($filter['__export'])) {
            $this->dataSource->setLimit($this->exportLimit);
            $this->dataSource->setOffset(0);
            $this->dataSource->execute();

            $rows = [];

            $columns = array_filter($this->getColumns(), function($column){
                return $column['export'];
            });

            $rows[] = array_values(array_map(function($column){
                return $column['title'];
            }, $columns));

            $rows = array_merge($rows, array_map(function($row) use ($columns){
                return Arr::only($row, array_keys($columns));
            }, $this->getRows()));

            header("Content-type: text/csv");
            header("Content-Disposition: attachment; filename=" . $this->dataSource->getName() . ".csv");
            header("Pragma: no-cache");
            header("Expires: 0");

            $file = fopen('php://output', 'w');
            foreach($rows as $row) {
                fputcsv($file, $row);
            }

            exit();
        }
    }

    /**
     * Actions
     */
    private function addActionsColumn()
    {
        $actions = array_filter($this->actions,function(GridAction $gridAction){
            $params = $gridAction->getData();

            if ($params['route'] instanceof Route) {
                return $params['route']->access();
            }

            if (getType($params['route']) === 'string') {
                return $this->ampere->guard()->hasAccess($params['route']);
            }

            return true;
        });

        if (count($actions) > 0) {
            $this->column('__actions', '')->export(false)->display(function ($data) use ($actions) {
                $columnActions = [];

                foreach($actions as $action) {
                    $params = $action->getData();

                    $route = $params['route'];
                    $routeParams = $params['routeParams'];

                    $href = '#';

                    $query = $routeParams ? ($query = gettype($routeParams) === 'string' ? $data[$routeParams] : $routeParams($data)) : null;

                    if ($route instanceof Route) {
                        $href = $route->setParams([$query])->url();

                    } else if (getType($route) === 'string') {
                        $query = gettype($routeParams) === 'string' ? $data[$routeParams] : $routeParams($data);
                        $href = ampere_route($route, $query);

                    } else if (is_callable($route)) {
                        $href = $route($data);
                    }

                    $columnActions[] = '<a class="btn btn-' . $params['level'] . '" href="' . $href . '"' . ($params['attribute'] ? ' data-name="' . $params['attribute'] . '"' : null) . '>' . $params['title'] . '</a>';
                }

                return '<div align="right">' . implode('&nbsp;&nbsp;', $columnActions) . '</div>';
            });
        }
    }
}