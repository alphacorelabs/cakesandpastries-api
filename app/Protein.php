<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Protein extends Model
{
    protected $fillable = ['name', 'price', 'isAvailable'];
}
