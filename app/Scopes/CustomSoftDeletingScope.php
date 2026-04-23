<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * Entfernt standardgemäß den "WithTrashed" Scope, siehe "extensions".
 */
class CustomSoftDeletingScope extends SoftDeletingScope implements Scope {

    /**
     * All of the extensions to be added to the builder.
     * @var string[]
     */
    protected $extensions = [
        'Restore',
        'WithTrashed',
        'WithoutTrashed',
        'OnlyTrashed'
    ];

    /**
     * Apply the scope to a given Eloquent query builder.
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model) {
        /**
         * Standardgemäß wird dieser Scope deaktiviert, weil dieser lediglich an einzelnen Stellen benötigt wird.
         */
        // $builder->whereNull($model->getQualifiedDeletedAtColumn());
    }
}
