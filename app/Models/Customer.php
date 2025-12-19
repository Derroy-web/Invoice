<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'address'];

    // Relasi: Satu Customer bisa punya banyak Invoice
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}