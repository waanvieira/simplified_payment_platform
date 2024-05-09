<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Uuid;

trait UuidTrait
{
    public static function bootUuidTrait()
    {
        static::creating(function ($model) {
            // if (Schema::hasColumn($model->table, 'creator_id')) {
            //     $model->creator_id = auth()->user()->id ?? null;
            // }
            $model->id = Uuid::uuid4()->toString();
        });
    }
}
