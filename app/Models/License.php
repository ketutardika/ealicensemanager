<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_id',
        'license_key',
        'account_quota',
        'used_quota',
        'license_creation_date',
        'license_expiration',
        'license_expiration_date',
        'status',
        'source',
        'subscription_id',
        'subscription_status',
        'renewal_date',
        'last_renewal_date',
        'payment_status'
    ];

    // License belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }



    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'license_creation_date' => 'datetime',
        'license_expiration_date' => 'datetime',
        'renewal_date' => 'datetime',
        'last_renewal_date' => 'datetime',
    ];
}

