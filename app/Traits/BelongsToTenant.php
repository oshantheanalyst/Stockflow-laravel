<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    // Boot the trait to apply global tenant scoping and auto-saving of tenant_id.
    protected static function bootBelongsToTenant()
    {
        // 1. Automatically scope queries to the current authenticated user's tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->hasUser() && auth()->user()) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', auth()->user()->tenant_id);
            }
        });

        // 2. Automatically assign tenant_id on model creation
        static::creating(function (Model $model) {
            if (auth()->hasUser() && auth()->user()) {
                if (empty($model->tenant_id)) {
                    $model->tenant_id = auth()->user()->tenant_id;
                }
            }
        });
    }
}
