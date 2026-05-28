<?php

namespace App\Traits;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Trait BelongsToTenant
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 * @method static void addGlobalScope(string $identifier, Closure $scope)
 * @method static void creating(Closure $callback)
 */
trait BelongsToTenant
{
    // Boot the trait to apply global tenant scoping and auto-saving of tenant_id.
    protected static function bootBelongsToTenant()
    {
        // 1. Automatically scope queries to the current authenticated user's tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', Auth::user()->tenant_id);
            }
        });

        // 2. Automatically assign tenant_id on model creation
        static::creating(function (Model $model) {
            if (Auth::check()) {
                if (empty($model->tenant_id)) {
                    $model->tenant_id = Auth::user()->tenant_id;
                }
            }
        });
    }
}
