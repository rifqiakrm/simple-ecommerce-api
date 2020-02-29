<?php

namespace App\Traits\Responser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

trait ApiResponser
{
    protected function errorResponse($code, $message, $data = null)
    {
        return response()->json(['meta' => ['code' => $code, 'status' => 'error', 'message' => $message], 'data' => $data], $code);
    }

    protected function validatorResponse($message, $code, $data = null)
    {
        return response()->json(['meta' => ['code' => $code, 'status' => 'error', 'message' => $message], 'data' => $data], $code);
    }

    protected function successResponse($code, $message, $data)
    {
        return response()->json(['meta' => ['code' => $code, 'status' => 'success', 'message' => $message], 'data' => $data], $code);
    }

    protected function showAll(Collection $collection, $code = 200)
    {
        if ($collection->isEmpty()) {
            return $this->successResponse(['data' => $collection], $code);
        }
        $transformer = $collection->first()->transformer;
        $collection = $this->filterData($collection, $transformer);
        $collection = $this->sortData($collection, $transformer);
        $collection = $this->collectionData($collection, $transformer);
        return $this->successResponse('success', $collection, $code);
    }

    protected function showOne(Model $instance, $code = 200)
    {
        $transformer = $instance->transformer;
        $instance = $this->singleData($instance, $transformer);
        return $this->successResponse('success', $instance, $code);
    }

    protected function filterData(Collection $collection, $transformer)
    {
        foreach (request()->query() as $query => $value) {
            $attribute = $transformer::originalAttribute($query);

            if (isset($attribute, $value)) {
                $collection = $collection->where($attribute, $value);
            }
        }
        return $collection;
    }

    protected function sortData(Collection $collection, $transformer)
    {
        if (request()->has('sort_by')) {
            $attribute = $transformer::originalAttribute(request()->sort_by);
            $collection = $collection->sortBy->{$attribute};
        }
        return $collection;
    }

    protected function paginate(Collection $collection)
    {
        $rules = [
            'per_page' => 'integer|min:2|max:50',
        ];

        Validator::validate(request()->all(), $rules, $messages);

        $page = LengthAwarePaginator::resolveCurrentPage();

        $perPage = 15;
        if (request()->has('per_page')) {
            $perPage = (int) request()->per_page;
        }

        $results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        $paginated->appends(request()->all());

        return $paginated;

    }

    protected function collectionData($data, $transformer)
    {
        return $transformer::collection($data);
    }

    protected function singleData($data, $transformer)
    {
        return (new $transformer($data));
    }
}
