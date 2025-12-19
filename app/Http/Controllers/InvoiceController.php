<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    // Tampilkan daftar invoice
    public function index()
    {
        // Kita load relasi 'customer' agar tidak berat (Eager Loading)
        $invoices = Invoice::with('customer')->latest()->get();
        return view('invoices.index', compact('invoices'));
    }

    // Form buat invoice baru
    public function create()
    {
        $customers = Customer::all();
        $products = Product::all(); // Kita butuh data produk untuk dropdown nanti
        
        // Generate nomor invoice otomatis (opsional, bisa diedit user)
        $nextId = Invoice::max('id') + 1;
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return view('invoices.create', compact('customers', 'products', 'invoiceNumber'));
    }

    // Simpan Invoice & Detail Barang
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'products' => 'required|array|min:1', // Harus ada minimal 1 barang
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Hitung Total Tagihan dulu
                $totalAmount = 0;
                $itemsToSave = [];

                foreach ($request->products as $item) {
                    $product = Product::find($item['product_id']);
                    $subtotal = $product->price * $item['quantity'];
                    $totalAmount += $subtotal;

                    $itemsToSave[] = [
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->price, // Simpan harga saat transaksi terjadi
                        'total_price' => $subtotal,
                    ];
                }

                // 2. Simpan Header Invoice
                $invoice = Invoice::create([
                    'invoice_number' => $request->invoice_number,
                    'customer_id' => $request->customer_id,
                    'invoice_date' => $request->invoice_date,
                    'due_date' => $request->due_date,
                    'total_amount' => $totalAmount,
                    'status' => 'unpaid',
                ]);

                // 3. Simpan Detail Item
                // Kita map array agar ada invoice_id-nya
                foreach ($itemsToSave as $itemData) {
                    $invoice->items()->create($itemData);
                }
            });

            return redirect()->route('invoices.index')->with('success', 'Invoice created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error creating invoice: ' . $e->getMessage())->withInput();
        }
    }

    // Detail Invoice (untuk Print/View)
    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product']); // Load relasi biar lengkap
        return view('invoices.show', compact('invoice'));
    }
    
    // Hapus Invoice
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function markAsPaid(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid']);
        return back()->with('success', 'Invoice marked as Paid successfully!');
    }
}