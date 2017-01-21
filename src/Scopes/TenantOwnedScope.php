<?php
namespace Ollieread\Multitenancy\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Ollieread\Multitenancy\Facades\Multitenancy;

/**
 * Class TenantOwnedScope
 *
 * @package Ollieread\Multitenancy\Scopes
 */
class TenantOwnedScope implements Scope
{

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $builder
     * @param  \Illuminate\Database\Eloquent\Model   $model
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where($model->getTenantKey(), '=', Multitenancy::tenant()->id);
    }

    public function extend(Builder $builder)
    {
        $this->addWithAll($builder);
    }

    protected function addWithAll(Builder $builder)
    {
        $builder->macro('withAll', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}