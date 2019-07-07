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
     * @return SelectSearch
     */
    public function add(string $field, string $model, array $fields, string $key): self
    {
        $this->sources[$field] = (object)[
            'model' => $model,
            'fields' => $fields,
            'key' => $key
        ];

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
        }

        $data = $query->get()->map(function($model) use ($source) {
            return [
                'id' => $model->id,
                'text' => $model->{$source->key}
            ];
        })->toArray();

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