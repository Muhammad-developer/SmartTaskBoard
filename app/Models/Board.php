<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    protected $fillable = [
        'name',
        'description',
        'color',
        'position',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function columns(): HasMany
    {
        return $this->hasMany(Column::class)->orderBy('position');
    }
}
