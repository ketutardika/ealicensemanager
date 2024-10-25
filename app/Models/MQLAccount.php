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

    // Specify the table name
    protected $table = 'mql_accounts';

    public function license()
    {
        return $this->belongsTo(License::class, 'license_id');
    }

}
