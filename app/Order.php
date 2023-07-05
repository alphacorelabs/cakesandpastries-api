<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Order extends Model
{
    protected $fillable = [
        'payment_ref', 'amount', 'status', 'name', 'phone', 'location', 'driver_id', 'date_delivered', 'deliveryFee', 'items', 'user_id', 'address', 'protein'
    ];

    public function users() {
        return $this->belongsTo(User::class);
    }
}
