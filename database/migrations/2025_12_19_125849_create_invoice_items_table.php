<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            // Relasi ke invoice
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            // Relasi ke produk
            $table->foreignId('product_id')->constrained('products');
            
            $table->integer('quantity');
            $table->decimal('price', 15, 2); // Harga saat transaksi (takutnya harga master berubah)
            $table->decimal('total_price', 15, 2); // quantity * price
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
