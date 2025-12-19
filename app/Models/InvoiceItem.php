<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity',
        'price',
        'total_price',
    ];

    // Relasi kebalikannya: Item milik satu Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // Relasi: Item merujuk ke satu Produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}