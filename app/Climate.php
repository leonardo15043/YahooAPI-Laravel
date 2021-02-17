<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Climate extends Model
{
    protected $fillable = [   
        'city',
        'humidity'      
    ];
}
