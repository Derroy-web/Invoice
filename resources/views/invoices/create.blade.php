<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Invoice') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('invoices.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nomor Invoice</label>
                                <input type="text" name="invoice_number" value="{{ $invoiceNumber }}" class="shadow border rounded w-full py-2 px-3 bg-gray-100" readonly>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Pelanggan</label>
                                <select name="customer_id" class="shadow border rounded w-full py-2 px-3" required>
                                    <option value="">-- Select Customer --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tanggal</label>
                                <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" class="shadow border rounded w-full py-2 px-3" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tenggat Waktu</label>
                                <input type="date" name="due_date" class="shadow border rounded w-full py-2 px-3">
                            </div>
                        </div>

                        <hr class="mb-6">

                        <h3 class="text-lg font-semibold mb-4">Invoice Items</h3>
                        <table class="min-w-full border-collapse block md:table" id="items_table">
                            <thead class="block md:table-header-group">
                                <tr class="border border-grey-500 md:border-none block md:table-row absolute -top-full md:top-auto -left-full md:left-auto  md:relative ">
                                    <th class="bg-gray-100 p-2 text-gray-900 font-bold md:border md:border-grey-500 text-left block md:table-cell">Product</th>
                                    <th class="bg-gray-100 p-2 text-gray-900 font-bold md:border md:border-grey-500 text-left block md:table-cell w-32">Harga (Rp)</th>
                                    <th class="bg-gray-100 p-2 text-gray-900 font-bold md:border md:border-grey-500 text-left block md:table-cell w-24">Jumlah</th>
                                    <th class="bg-gray-100 p-2 text-gray-900 font-bold md:border md:border-grey-500 text-left block md:table-cell w-40">Subtotal</th>
                                    <th class="bg-gray-100 p-2 text-gray-900 font-bold md:border md:border-grey-500 text-left block md:table-cell w-16">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="block md:table-row-group" id="table_body">
                                <tr class="item-row bg-white border border-grey-500 md:border-none block md:table-row">
                                    <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                                        <select name="products[0][product_id]" class="product-select w-full p-2 border rounded" onchange="updatePrice(this)" required>
                                            <option value="" data-price="0">-- Pilih Product --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                    {{ $product->code }} - {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                                        <input type="number" class="price-input w-full p-2 border rounded bg-gray-100" value="0" readonly>
                                    </td>
                                    <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                                        <input type="number" name="products[0][quantity]" class="qty-input w-full p-2 border rounded" value="1" min="1" oninput="calculateSubtotal(this)" required>
                                    </td>
                                    <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                                        <input type="text" class="subtotal-input w-full p-2 border rounded bg-gray-100" value="0" readonly>
                                    </td>
                                    <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                                        <button type="button" class="bg-red-500 text-white p-2 rounded hover:bg-red-700" onclick="removeRow(this)">X</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <button type="button" class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-700" onclick="addRow()">+ Add Item</button>

                        <div class="flex justify-end mt-4">
                            <h3 class="text-xl font-bold">Total: Rp <span id="grand_total">0</span></h3>
                        </div>

                        <div class="flex justify-end mt-6">
                            <a href="{{ route('invoices.index') }}" class="text-gray-600 hover:underline mr-4 py-2">Cancel</a>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-800">Save Invoice</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let rowCount = 1;

        function updatePrice(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            const row = selectElement.closest('tr');
            
            // Update input harga
            row.querySelector('.price-input').value = price;
            
            // Hitung ulang subtotal baris ini
            calculateSubtotal(row.querySelector('.qty-input'));
        }

        function calculateSubtotal(inputElement) {
            const row = inputElement.closest('tr');
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            const subtotal = price * qty;
            
            row.querySelector('.subtotal-input').value = subtotal;
            
            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.subtotal-input').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            document.getElementById('grand_total').innerText = total.toLocaleString('id-ID');
        }

        function addRow() {
            const tableBody = document.getElementById('table_body');
            const newRow = document.createElement('tr');
            newRow.classList.add('item-row', 'bg-white', 'border', 'border-grey-500', 'md:border-none', 'block', 'md:table-row');
            
            // Copy HTML dari baris pertama, tapi update index name="products[x]..."
            newRow.innerHTML = `
                <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                    <select name="products[${rowCount}][product_id]" class="product-select w-full p-2 border rounded" onchange="updatePrice(this)" required>
                        <option value="" data-price="0">-- Select Product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                {{ $product->code }} - {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                    <input type="number" class="price-input w-full p-2 border rounded bg-gray-100" value="0" readonly>
                </td>
                <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                    <input type="number" name="products[${rowCount}][quantity]" class="qty-input w-full p-2 border rounded" value="1" min="1" oninput="calculateSubtotal(this)" required>
                </td>
                <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                    <input type="text" class="subtotal-input w-full p-2 border rounded bg-gray-100" value="0" readonly>
                </td>
                <td class="p-2 md:border md:border-grey-500 text-left block md:table-cell">
                    <button type="button" class="bg-red-500 text-white p-2 rounded hover:bg-red-700" onclick="removeRow(this)">X</button>
                </td>
            `;
            
            tableBody.appendChild(newRow);
            rowCount++;
        }

        function removeRow(button) {
            const row = button.closest('tr');
            // Cegah hapus semua baris (sisakan 1)
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                calculateGrandTotal();
            } else {
                alert("Minimum 1 item required.");
            }
        }
    </script>
</x-app-layout>