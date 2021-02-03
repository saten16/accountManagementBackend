<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    use HasFactory;

    protected $table = 'accounts';
    public $timestamps = false;
    public $primaryKey = 'account_id';

    protected $fillable = [
        'account_id',
        'balance'
    ];
}
