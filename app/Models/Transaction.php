<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Transaction extends Model
{
    protected $fillable = ['user_id', 'ticket_id', 'transaction_code', 'quantity', 'total_price', 'visit_date', 'payment_status', 'payment_method'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function logs()
    {
        return $this->hasMany(TransactionLog::class);
    }
}