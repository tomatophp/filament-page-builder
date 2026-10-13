<?php

namespace TomatoPHP\FilamentPageBuilder\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'builder_test_pages';

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    public function meta(string $key, mixed $value = null): mixed
    {
        $meta = $this->meta ?? [];

        if (func_num_args() === 1) {
            return $meta[$key] ?? null;
        }

        $meta[$key] = $value;
        $this->meta = $meta;
        $this->save();

        return $value;
    }
}
