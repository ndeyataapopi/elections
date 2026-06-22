<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    // protected static function booted()
    // {
    //     static::creating(function ($model) {
    //         if (app()->has('currentTenant')) {
    //             $model->tenant_id = app('currentTenant')->id;
    //         }
    //     });
    // }

    // public function election()
    // {
    //     return $this->belongsTo(Election::class);
    // }

    // public function candidates()
    // {
    //     return $this->hasMany(Candidate::class);
    // }
}
