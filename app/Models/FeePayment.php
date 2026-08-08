<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    protected $fillable = [
        'fee_invoice_id',
        'transaction_id',
        'amount_paid',
        'payment_method',
        'paid_at',
        'notes',
        'received_by',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(FeeInvoice::class, 'fee_invoice_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
