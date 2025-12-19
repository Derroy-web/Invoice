<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Invoice Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8" id="printableArea">
                
                <div class="flex justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">INVOICE</h1>
                        <p class="text-sm text-gray-500">Original Receipt</p>
                    </div>
                    <div class="text-right">
                        <h2 class="text-xl font-bold text-gray-700">#{{ $invoice->invoice_number }}</h2>
                        <p class="text-gray-600">Date: {{ $invoice->invoice_date }}</p>
                        @if($invoice->due_date)
                            <p class="text-red-500 text-sm">Due: {{ $invoice->due_date }}</p>
                        @endif
                        <span class="inline-block mt-2 px-3 py-1 rounded-full text-sm font-bold {{ $invoice->status == 'paid' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                            {{ strtoupper($invoice->status) }}
                        </span>
                    </div>
                </div>

                <div class="mb-8 border-b pb-4">
                    <h3 class="text-lg font-bold text-gray-700 mb-2">Bill To:</h3>
                    <p class="text-gray-900 font-bold">{{ $invoice->customer->name }}</p>
                    <p class="text-gray-600">{{ $invoice->customer->address }}</p>
                    <p class="text-gray-600">{{ $invoice->customer->phone }}</p>
                    <p class="text-gray-600">{{ $invoice->customer->email }}</p>
                </div>

                <table class="w-full mb-8">
                    <thead>
                        <tr class="border-b-2 border-gray-300">
                            <th class="text-left py-2 font-bold text-gray-700">Item</th>
                            <th class="text-right py-2 font-bold text-gray-700">Price</th>
                            <th class="text-center py-2 font-bold text-gray-700">Qty</th>
                            <th class="text-right py-2 font-bold text-gray-700">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                            <tr class="border-b border-gray-100">
                                <td class="py-2 text-gray-800">
                                    <span class="font-bold">{{ $item->product->code }}</span> - {{ $item->product->name }}
                                </td>
                                <td class="py-2 text-right text-gray-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="py-2 text-center text-gray-600">{{ $item->quantity }}</td>
                                <td class="py-2 text-right text-gray-800 font-bold">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="flex justify-end">
                    <div class="w-1/2">
                        <div class="flex justify-between py-2 border-t-2 border-gray-800">
                            <span class="text-xl font-bold text-gray-800">Grand Total</span>
                            <span class="text-xl font-bold text-blue-600">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-6 flex justify-end space-x-3 no-print">
                <a href="{{ route('invoices.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Back</a>
                
                @if($invoice->status == 'unpaid')
                    <form action="{{ route('invoices.markAsPaid', $invoice) }}" method="POST" class="inline-block">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="bg-green-600 hover:bg-green-800 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Mark as Paid?')">
                            Mark as Paid
                        </button>
                    </form>
                @endif

                <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded">
                    Print / Save PDF
                </button>
            </div>
        </div>
    </div>
    
    <style>
        @media print {
            .no-print, nav, header {
                display: none !important;
            }
            body {
                background-color: white;
            }
            .shadow-sm {
                box-shadow: none !important;
            }
        }
    </style>
</x-app-layout>