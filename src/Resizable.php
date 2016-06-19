<?php

namespace lagbox\resizer;

use Illuminate\Database\Eloquent\Model;

class Resizable extends Model
{
    public static $resizer;

    protected $fillable = [
        'original', 'lg', 'md', 'sm', 'xs'
    ];

    public function resizeable()
    {
        return $this->morphTo();
    }
}
