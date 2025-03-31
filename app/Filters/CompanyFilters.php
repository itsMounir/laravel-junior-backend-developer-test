<?php

namespace App\Filters;


use App\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CompanyFilters extends BaseFilter
{
    public function name(Builder $query): Builder
    {
        return $query->where('name', 'like', '%' . $this->request->input('name') . '%');
    }

    public function ownerId(Builder $query): Builder
    {
        return $query->where('owner_id', $this->request->input('ownerId'));
    }
}
