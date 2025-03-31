<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class BaseFilter
{
    public function __construct(
        protected Request $request
    ) {}

    public function applyFilters(Builder $query): Builder
    {
        $filters = $this->getFilters();

        foreach ($filters as $filter) {
            if (method_exists($this, $filter)) {
                $query = $this->$filter($query);
            }
        }

        return $query;
    }

    protected function getFilters(): array
    {
        $filters = [];
        $parameters = $this->request->query();

        foreach ($parameters as $key => $value) {
            $filters[] = $key;
        }

        return $filters;
    }
}
