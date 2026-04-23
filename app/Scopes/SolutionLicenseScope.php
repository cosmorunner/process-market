<?php

namespace App\Scopes;

use App\Models\Solution;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Wendet eine Query-Scope an
 */
class SolutionLicenseScope implements Scope {

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model) {
        $builder->where('resource_type', '=', Solution::class);
    }
}
