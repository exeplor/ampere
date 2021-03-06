<?php

namespace Ampere\Services\Helper;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class SelectSearch
 * @package Ampere\Services
 */
class SelectSearch
{
    /**
     * @var array
     */
    private $sources = [];

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $query;

    /**
     * @var int|array
     */
    private $targetId;

    /**
     * @var int
     */
    private $limit = 10;

    /**
     * SelectSearch constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->handle($request->all());
    }

    /**
     * @param int $limit
     * @return SelectSearch
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param string $field
     * @param string $model
     * @param array $fields
     * @param string $key
     * @param \Closure|null $filter
     * @return $this
     */
    public function add(string $field, string $model, array $fields, string $key, \Closure $filter = null): self
    {
        $this->sources[$field] = (object)[
            'type' => 'model',
            'model' => $model,
            'fields' => $fields,
            'key' => $key,
            'filter' => $filter
        ];

        return $this;
    }

    /**
     * @param string $fields
     * @param \Closure $callback
     * @return $this
     */
    public function addSource(string $fields, \Closure $callback): self
    {
        $list = explode(',', $fields);
        foreach($list as $field) {
            $this->sources[$field] = (object)[
                'type' => 'source',
                'callback' => $callback
            ];
        }

        return $this;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function response()
    {
        $data = $this->get();
        return response()->json([
            'results' => $data
        ]);
    }

    /**
     * @return array
     */
    private function get(): array
    {
        $source = $this->sources[$this->field];

        $data = [];
        if ($source->type == 'model') {

            /**
             * @var Builder $query
             */
            $query = $source->model::query()->limit($this->limit);

            if ($this->targetId) {
                $query->whereIn('id', is_array($this->targetId) ? $this->targetId : [$this->targetId]);

            } else {
                foreach ($source->fields as $field) {
                    $query->orWhere($field, 'LIKE', '%' . $this->query . '%');
                }

                if ($source->filter) {
                    $filter = $source->filter;
                    $filter($query);
                }
            }

            $data = $query->get()->map(function ($model) use ($source) {
                return [
                    'id' => $model->id,
                    'text' => $model->{$source->key},
                    'additional' => []
                ];
            })->toArray();
        }

        if ($source->type == 'source') {
            $callback = $source->callback;
            $list = $callback($this->query);

            foreach($list as $row) {
                $data[] = [
                    'id' => $row[0],
                    'text' => $row[1],
                    'additional' => $row[2] ?? []
                ];
            }
        }

        return $data;
    }

    /**
     * @param array $request
     */
    private function handle(array $request)
    {
        /**
         * @var \Illuminate\Validation\Validator $validator
         */
        $validator = Validator::make($request, [
            'id' => ['nullable'],
            'query' => ['nullable', 'string'],
            'field' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(response()->json($validator->getMessageBag()));
        }

        $this->field = $request['field'];
        $this->query = $request['query'] ?? null;
        $this->targetId = $request['id'] ?? null;
    }
}
