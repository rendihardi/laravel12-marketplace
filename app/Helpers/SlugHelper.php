<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SlugHelper
{
    public static function generate($model, $name, $column = 'slug')
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while ($model::where($column, $slug)->exists()) {
            $slug = $originalSlug.'-'.$count++;
        }

        return $slug;
    }
}
