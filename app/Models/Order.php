<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'product_id',
        'product_name',
        'program_sn',
        'total_purchase',
        'currency',
        'language',
        'transaction_date',
        'source',
    ];

    // Order belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Order belongs to a license
    public function license()
    {
        return $this->hasOne(License::class, 'user_id', 'user_id');
    }

}