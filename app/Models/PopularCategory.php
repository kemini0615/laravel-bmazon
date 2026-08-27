<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PopularCategory extends Model
{
    protected $fillable = [
        'id',
        'categories',
    ];

    protected $casts = [
        'categories' => 'array',
    ];
}
