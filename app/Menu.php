<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Category;

class Menu extends Model
{
    protected $fillable = ['category_id', 'name', 'price', 'measure', 'image', 'isAvailable'];

    public function category() {
        return $this->belongsTo(Category::class);
    }
}
