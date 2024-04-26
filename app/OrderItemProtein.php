<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItemProtein extends Model
{
    

    protected $fillable = [
        'order_item_id', 'protein_id', 'quantity', 'price'
    ];

    public function orderItem() {
        return $this->belongsTo(OrderItem::class);
    }

    public function protein() {
        return $this->belongsTo(Protein::class);
    }

    
}
