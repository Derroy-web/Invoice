<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer_id',
        'invoice_date',
        'due_date',
        'total_amount',
        'status',
    ];

    // Relasi: Invoice pasti milik satu Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi: Invoice punya banyak detail barang (Items)
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}