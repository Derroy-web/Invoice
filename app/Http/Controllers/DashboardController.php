<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung data untuk dashboard
        $totalRevenue = Invoice::where('status', 'paid')->sum('total_amount');
        $pendingInvoices = Invoice::where('status', 'unpaid')->count();
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();
        
        // Ambil 5 invoice terbaru untuk ditampilkan di dashboard
        $recentInvoices = Invoice::with('customer')->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalRevenue', 
            'pendingInvoices', 
            'totalCustomers', 
            'totalProducts',
            'recentInvoices'
        ));
    }
}