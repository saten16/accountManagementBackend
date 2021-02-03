<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    public $timestamps = false;
    public $primaryKey = 'transaction_id';

    protected $fillable = [
        'transaction_id',
        'account_id',
        'balance'
    ];
}
