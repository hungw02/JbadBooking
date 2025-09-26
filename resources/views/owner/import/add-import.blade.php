@extends('layout.main-owner')

@section('title', 'Nhập hàng mới')

@section('content')
    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                <div class="px-6 py-5 border-b">
                    <h2 class="text-2xl font-bold text-gray-800">Nhập hàng mới</h2>
                </div>

                <div class="p-6">
                    <form action="{{ route('imports.store') }}" method="POST" class="space-y-5" id="importForm">
                        @csrf
                        
                        <div>
                            <label for="workshop_name" class="block text-sm font-medium text-gray-700">Tên nhà cung cấp</label>
                            <input type="text" name="workshop_name" id="workshop_name" 
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                value="{{ old('workshop_name') }}" required>
                            @error('workshop_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="border-t pt-4">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Danh sách sản phẩm</h3>
                            
                            <div id="product-list" class="space-y-4">
                                <!-- Product items will be added here -->
                                <div class="product-item bg-gray-50 p-4 rounded-lg">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Sản phẩm</label>
                                            <div class="mt-2 relative">
                                                <select name="products[0][product_id]" class="product-select w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none" required>
                                                    <option value="">Chọn sản phẩm</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}">
                                                            {{ $product->name }} (Giá nhập: {{ number_format($product->import_price) }} VNĐ, SL: {{ $product->quantity }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <p class="error-message mt-1 text-sm text-red-600" style="display: none;"></p>
                                            </div>
                                            @error('products.0.product_id')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Số lượng</label>
                                            <input type="number" name="products[0][quantity]" 
                                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                                value="1" min="1" required>
                                            @error('products.0.quantity')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Giá nhập / 1 sản phẩm</label>
                                            <input type="number" name="products[0][import_price]" 
                                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                                value="0" min="0" required>
                                            @error('products.0.import_price')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2 flex justify-end">
                                        <button type="button" class="remove-product text-red-600 hover:text-red-800 text-sm" style="display: none;">
                                            <i class="fas fa-trash"></i> Xóa sản phẩm
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="button" id="add-product" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-200">
                                    <i class="fas fa-plus"></i> Thêm sản phẩm
                                </button>
                            </div>
                        </div>

                        <div class="pt-3 border-t">
                            <div class="text-sm text-gray-700 mb-2">
                                <strong>Lưu ý:</strong> Giá nhập mới sẽ được cập nhật vào thông tin sản phẩm và cập nhật số lượng sản phẩm.
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <a href="{{ route('imports.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">
                                Hủy
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                Nhập hàng
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productList = document.getElementById('product-list');
            const addProductBtn = document.getElementById('add-product');
            let productCount = 1;
            
            // Initialize Select2 for the first product
            initializeSelect2();
            
            // Add product button click handler
            addProductBtn.addEventListener('click', function() {
                const productItem = document.createElement('div');
                productItem.className = 'product-item bg-gray-50 p-4 rounded-lg';
                productItem.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sản phẩm</label>
                            <div class="mt-2 relative">
                                <select name="products[${productCount}][product_id]" class="product-select w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none" required>
                                    <option value="">Chọn sản phẩm</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->name }} (Giá nhập: {{ number_format($product->import_price) }} VNĐ, SL: {{ $product->quantity }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="error-message mt-1 text-sm text-red-600" style="display: none;"></p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Số lượng</label>
                            <input type="number" name="products[${productCount}][quantity]" 
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                value="1" min="1" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Giá nhập / 1 sản phẩm</label>
                            <input type="number" name="products[${productCount}][import_price]" 
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                value="0" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mt-2 flex justify-end">
                        <button type="button" class="remove-product text-red-600 hover:text-red-800 text-sm">
                            <i class="fas fa-trash"></i> Xóa sản phẩm
                        </button>
                    </div>
                `;
                
                productList.appendChild(productItem);
                productCount++;
                
                // Initialize Select2 for the new product
                initializeSelect2();
                
                // Show all remove buttons if there's more than one product
                if (productList.querySelectorAll('.product-item').length > 1) {
                    document.querySelectorAll('.remove-product').forEach(btn => {
                        btn.style.display = 'block';
                    });
                }
            });
            
            // Remove product button click handler (using event delegation)
            productList.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-product') || e.target.closest('.remove-product')) {
                    const productItem = e.target.closest('.product-item');
                    productItem.remove();
                    
                    // Hide remove buttons if there's only one product left
                    if (productList.querySelectorAll('.product-item').length <= 1) {
                        document.querySelectorAll('.remove-product').forEach(btn => {
                            btn.style.display = 'none';
                        });
                    }
                }
            });
            
            // Initialize Select2 for product dropdowns
            function initializeSelect2() {
                // If you're using Select2, uncomment this code
                /*
                $('.product-select').select2({
                    placeholder: 'Tìm kiếm sản phẩm...',
                    allowClear: true,
                    width: '100%'
                });
                */
                
                // If not using Select2, you can implement a simple search functionality
                document.querySelectorAll('.product-select').forEach(select => {
                    // Add your custom search functionality here if needed
                });
            }
            
            // Form submission validation
            document.getElementById('importForm').addEventListener('submit', function(e) {
                const selects = document.querySelectorAll('.product-select');
                const selectedProducts = new Set();
                let hasDuplicates = false;
                let hasEmptySelection = false;
                
                // Clear previous error messages
                document.querySelectorAll('.error-message').forEach(el => {
                    el.style.display = 'none';
                    el.textContent = '';
                });
                
                selects.forEach(select => {
                    if (selectedProducts.has(select.value)) {
                        hasDuplicates = true;
                        const errorElement = select.parentElement.querySelector('.error-message');
                        errorElement.textContent = 'Sản phẩm này đã được chọn';
                        errorElement.style.display = 'block';
                    } else {
                        selectedProducts.add(select.value);
                    }
                });
                
                if (hasEmptySelection || hasDuplicates) {
                    e.preventDefault();
                    
                    // Add a general error message at the top if needed
                    if (hasDuplicates) {
                        const formElement = document.getElementById('importForm');
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4';
                        errorDiv.textContent = 'Mỗi sản phẩm chỉ được nhập một lần trong một đơn. Vui lòng điều chỉnh số lượng thay vì chọn cùng một sản phẩm nhiều lần.';
                        
                        // Insert at the top of the form
                        formElement.insertBefore(errorDiv, formElement.firstChild);
                        
                        // Remove the error message after 5 seconds
                        setTimeout(() => {
                            errorDiv.remove();
                        }, 5000);
                    }
                    return;
                }
            });
        });
    </script>
@endsection 