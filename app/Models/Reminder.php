<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'title',
        'due_date',
        'is_completed',
    ];
}
