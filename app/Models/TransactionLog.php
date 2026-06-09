<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    protected $fillable = [
        'transaction_id',
        'old_status',
        'new_status',
        'changed_at'
    ];

    public $timestamps = false;

    protected $table = 'transaction_logs';

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}