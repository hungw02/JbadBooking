@extends('layout.main-owner')

@section('title', $transactionType === 'rent' ? 'Tạo hóa đơn cho thuê mới' : 'Tạo hóa đơn bán hàng')

@section('content')
<div class="py-6">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden">
            <div class="px-6 py-5 border-b">
                <h2 class="text-2xl font-bold text-gray-800">
                    {{ $transactionType === 'rent' ? 'Tạo hóa đơn cho thuê mới' : 'Tạo hóa đơn bán hàng' }}
                </h2>
            </div>

            <div class="p-6">
                @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
                @endif

                <form action="{{ route('storage.store') }}" method="POST" class="space-y-5" id="orderForm">
                    @csrf
                    <input type="hidden" name="transaction_type" value="{{ $transactionType }}">
                    
                    <div id="products-container">
                        <div class="product-row mb-4 pb-4 border-b border-gray-200">
                            <div>
                                <label for="product_id_0" class="block text-sm font-medium text-gray-700">
                                    {{ $transactionType === 'rent' ? 'Sản phẩm cho thuê' : 'Sản phẩm bán' }}
                                </label>
                                <select name="products[0][product_id]" id="product_id_0" 
                                    class="product-select mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none" required>
                                    <option value="">Chọn sản phẩm</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ old('products.0.product_id') == $product->id ? 'selected' : '' }}
                                        data-price="{{ $product->selling_price }}"
                                        data-max="{{ $product->quantity }}">
                                        {{ $product->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('products.0.product_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-3">
                                <label for="quantity_0" class="block text-sm font-medium text-gray-700">Số lượng</label>
                                <input type="number" name="products[0][quantity]" id="quantity_0"
                                    class="quantity-input mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                    value="{{ old('products.0.quantity', 1) }}" min="1" required>
                                <p class="mt-1 text-sm text-gray-500 max-quantity-info"></p>
                                @error('products.0.quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-3">
                                <label for="price_0" class="block text-sm font-medium text-gray-700">Giá</label>
                                <input type="text" id="price_0" class="price-display mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 cursor-not-allowed" readonly>
                            </div>
                        </div>
                    </div>

                    @if($transactionType === 'sale')
                    <div class="flex justify-end">
                        <button type="button" id="addProductBtn" class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                            + Thêm sản phẩm
                        </button>
                    </div>
                    @endif

                    <div>
                        <label for="total_price_display" class="block text-sm font-medium text-gray-700">Tổng tiền</label>
                        <input type="text" id="total_price_display"
                            class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 cursor-not-allowed"
                            readonly>
                    </div>

                    <div>
                        <label for="note" class="block text-sm font-medium text-gray-700">Ghi chú</label>
                        <textarea name="note" id="note" rows="3"
                            class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none">{{ old('note') }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="{{ route('storage.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">
                            Hủy
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                            {{ $transactionType === 'rent' ? 'Tạo hóa đơn cho thuê' : 'Tạo hóa đơn bán hàng' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productsContainer = document.getElementById('products-container');
        const addProductBtn = document.getElementById('addProductBtn');
        const totalPriceDisplay = document.getElementById('total_price_display');
        const transactionType = "{{ $transactionType }}";
        
        // Initialize the first product row
        updateProductRow(0);
        
        // Add product button only for 'sale' transactions
        if (transactionType === 'sale' && addProductBtn) {
            addProductBtn.addEventListener('click', function() {
                const productRows = document.querySelectorAll('.product-row');
                const newIndex = productRows.length;
                
                const productRow = document.createElement('div');
                productRow.className = 'product-row mb-4 pb-4 border-b border-gray-200';
                
                // Get the product options from the first row
                const firstProductSelect = document.querySelector('.product-select');
                const productOptions = firstProductSelect.innerHTML;
                
                productRow.innerHTML = `
                    <div class="flex justify-between items-center">
                        <label for="product_id_${newIndex}" class="block text-sm font-medium text-gray-700">
                            Sản phẩm bán
                        </label>
                        <button type="button" class="remove-product text-red-600 hover:text-red-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    <div>
                        <select name="products[${newIndex}][product_id]" id="product_id_${newIndex}" 
                            class="product-select mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none" required>
                            ${productOptions}
                        </select>
                    </div>
                    <div class="mt-3">
                        <label for="quantity_${newIndex}" class="block text-sm font-medium text-gray-700">Số lượng</label>
                        <input type="number" name="products[${newIndex}][quantity]" id="quantity_${newIndex}"
                            class="quantity-input mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                            value="1" min="1" required>
                        <p class="mt-1 text-sm text-gray-500 max-quantity-info"></p>
                    </div>
                    <div class="mt-3">
                        <label for="price_${newIndex}" class="block text-sm font-medium text-gray-700">Giá</label>
                        <input type="text" id="price_${newIndex}" class="price-display mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 cursor-not-allowed" readonly>
                    </div>
                `;
                
                productsContainer.appendChild(productRow);
                
                // Initialize the new row
                updateProductRow(newIndex);
                
                // Add event listener to remove button
                productRow.querySelector('.remove-product').addEventListener('click', function() {
                    productRow.remove();
                    renumberProducts();
                    calculateTotalPrice();
                    checkDuplicateProducts();
                });

                // Check for duplicates after adding new row
                checkDuplicateProducts();
            });
        }
        
        // Helper function to update event listeners for a product row
        function updateProductRow(index) {
            const productSelect = document.getElementById(`product_id_${index}`);
            const quantityInput = document.getElementById(`quantity_${index}`);
            const priceDisplay = document.getElementById(`price_${index}`);
            const maxQuantityInfo = document.querySelector(`.product-row:nth-child(${index + 1}) .max-quantity-info`);
            
            // Initial calculation
            calculateRowPrice(productSelect, quantityInput, priceDisplay);
            updateMaxQuantityInfo(productSelect, maxQuantityInfo, quantityInput);
            
            // Event listeners
            productSelect.addEventListener('change', function() {
                calculateRowPrice(productSelect, quantityInput, priceDisplay);
                updateMaxQuantityInfo(productSelect, maxQuantityInfo, quantityInput);
                calculateTotalPrice();
                checkDuplicateProducts();
            });
            
            quantityInput.addEventListener('input', function() {
                calculateRowPrice(productSelect, quantityInput, priceDisplay);
                calculateTotalPrice();
            });
        }
        
        // Function to check for duplicate products and display warning
        function checkDuplicateProducts() {
            // Remove existing warning
            const existingWarning = document.getElementById('duplicate-warning');
            if (existingWarning) {
                existingWarning.remove();
            }
            
            const productRows = document.querySelectorAll('.product-row');
            if (productRows.length <= 1) return;
            
            const productMap = {};
            let hasDuplicates = false;
            
            // Check for duplicates
            productRows.forEach((row, index) => {
                const productSelect = document.getElementById(`product_id_${index}`);
                if (productSelect && productSelect.value) {
                    const productId = productSelect.value;
                    const productName = productSelect.options[productSelect.selectedIndex].text;
                    
                    if (productMap[productId]) {
                        productMap[productId].count++;
                        productMap[productId].indexes.push(index);
                        hasDuplicates = true;
                    } else {
                        productMap[productId] = { 
                            name: productName,
                            count: 1,
                            indexes: [index]
                        };
                    }
                }
            });
            
            // If duplicates found, show warning
            if (hasDuplicates) {
                const duplicateList = Object.values(productMap)
                    .filter(item => item.count > 1)
                    .map(item => `<li>${item.name} (${item.count} lần)</li>`)
                    .join('');
                
                const warningDiv = document.createElement('div');
                warningDiv.id = 'duplicate-warning';
                warningDiv.className = 'bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4 mt-4';
                warningDiv.innerHTML = `
                    <p class="font-bold">Lưu ý: Phát hiện sản phẩm trùng lặp</p>
                    <p>Những sản phẩm sau đây được chọn nhiều lần:</p>
                    <ul class="list-disc ml-5 mt-2">
                        ${duplicateList}
                    </ul>
                    <p class="mt-2">Khi lưu, hệ thống sẽ tự động gộp số lượng của các sản phẩm trùng lặp.</p>
                `;
                
                const formElement = document.getElementById('orderForm');
                const productsContainer = document.getElementById('products-container');
                
                // Insert after products container
                productsContainer.after(warningDiv);
            }
        }
        
        // Function to renumber products after removal
        function renumberProducts() {
            const productRows = document.querySelectorAll('.product-row');
            productRows.forEach((row, index) => {
                // Update select name and id
                const productSelect = row.querySelector('.product-select');
                productSelect.name = `products[${index}][product_id]`;
                productSelect.id = `product_id_${index}`;
                
                // Update quantity name and id
                const quantityInput = row.querySelector('.quantity-input');
                quantityInput.name = `products[${index}][quantity]`;
                quantityInput.id = `quantity_${index}`;
                
                // Update price id
                const priceDisplay = row.querySelector('.price-display');
                priceDisplay.id = `price_${index}`;
            });
        }
        
        // Calculate price for a single row
        function calculateRowPrice(productSelect, quantityInput, priceDisplay) {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            if (!selectedOption || selectedOption.value === '') {
                priceDisplay.value = '0 VNĐ';
                return 0;
            }
            
            const price = parseFloat(selectedOption.dataset.price) || 0;
            const quantity = parseInt(quantityInput.value) || 0;
            
            const total = price * quantity;
            priceDisplay.value = new Intl.NumberFormat('vi-VN').format(total) + ' VNĐ';
            
            return total;
        }
        
        // Calculate total price across all products
        function calculateTotalPrice() {
            const productRows = document.querySelectorAll('.product-row');
            let grandTotal = 0;
            
            productRows.forEach((row, index) => {
                const productSelect = document.getElementById(`product_id_${index}`);
                const quantityInput = document.getElementById(`quantity_${index}`);
                
                const selectedOption = productSelect.options[productSelect.selectedIndex];
                if (selectedOption && selectedOption.value !== '') {
                    const price = parseFloat(selectedOption.dataset.price) || 0;
                    const quantity = parseInt(quantityInput.value) || 0;
                    grandTotal += price * quantity;
                }
            });
            
            totalPriceDisplay.value = new Intl.NumberFormat('vi-VN').format(grandTotal) + ' VNĐ';
        }
        
        // Update max quantity info
        function updateMaxQuantityInfo(productSelect, maxQuantityInfo, quantityInput) {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            if (!selectedOption || selectedOption.value === '') {
                maxQuantityInfo.textContent = '';
                return;
            }
            
            const maxQuantity = parseInt(selectedOption.dataset.max) || 0;
            
            if (maxQuantity > 0) {
                maxQuantityInfo.textContent = `Số lượng có sẵn: ${maxQuantity}`;
                quantityInput.max = maxQuantity;
            } else {
                maxQuantityInfo.textContent = '';
            }
        }
        
        // Initialize form submission
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            if (transactionType === 'sale') {
                // Get all product rows
                const productRows = document.querySelectorAll('.product-row');
                
                // Check if at least one product is selected
                let hasProduct = false;
                productRows.forEach((row, index) => {
                    const productSelect = document.getElementById(`product_id_${index}`);
                    if (productSelect.value !== '') {
                        hasProduct = true;
                    }
                });
                
                if (!hasProduct) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một sản phẩm!');
                }
            }
        });

        // Check for duplicates on page load
        if (transactionType === 'sale') {
            checkDuplicateProducts();
        }
    });
</script>
@endsection