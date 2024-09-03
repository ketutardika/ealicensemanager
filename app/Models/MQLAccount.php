<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MQLAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_id',
        'account_mql',
        'status',
        'validation_status',
    ];
}
