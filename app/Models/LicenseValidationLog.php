<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseValidationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_sn',
        'account_mql',
        'license_key',
        'source',
        'validation_status',
        'message_validation',
        'order_id',
        'user_id',
        'product_id',
        'account_quota',
        'remaining_quota',
    ];
}
