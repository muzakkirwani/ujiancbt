<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    public $timestamps = false;

    protected $fillable = [
        'app_name',
        'school_name',
        'logo_url',
        'address',
        'timezone',
        'website',
    ];
}
