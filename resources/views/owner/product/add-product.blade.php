@extends('layout.main-owner')

@section('title', 'Thêm sản phẩm mới')

@section('content')
    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                <div class="px-6 py-5 border-b">
                    <h2 class="text-2xl font-bold text-gray-800">Thêm sản phẩm mới</h2>
                </div>

                <div class="p-6">
                    <form action="{{ route('products.store') }}" method="POST" class="space-y-5" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Tên sản phẩm</label>
                            <input type="text" name="name" id="name" 
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Loại sản phẩm</label>
                            <select name="type" id="type" 
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none">
                                <option value="sale" {{ old('type') === 'sale' ? 'selected' : '' }}>Bán</option>
                                <option value="rent" {{ old('type') === 'rent' ? 'selected' : '' }}>Cho thuê</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="import_price" class="block text-sm font-medium text-gray-700">Giá nhập</label>
                            <input type="number" name="import_price" id="import_price" 
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                value="{{ old('import_price') }}" min="0" required>
                            @error('import_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="selling_price" class="block text-sm font-medium text-gray-700">Giá bán</label>
                            <input type="number" name="selling_price" id="selling_price" 
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                value="{{ old('selling_price') }}" min="0" required>
                            @error('selling_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="sale" class="block text-sm font-medium text-gray-700">Giảm giá (%)</label>
                            <input type="number" name="sale" id="sale" 
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                value="{{ old('sale', 0) }}" min="0" max="100" required>
                            @error('sale')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700">Số lượng</label>
                            <input type="number" name="quantity" id="quantity" 
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none"
                                value="{{ old('quantity', 0) }}" min="0" required>
                            @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                            <select name="status" id="status" 
                                class="mt-2 w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 shadow-sm focus:outline-none">
                                <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>Còn hàng</option>
                                <option value="out_of_stock" {{ old('status') === 'out_of_stock' ? 'selected' : '' }}>Hết hàng</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Hình ảnh sản phẩm</label>
                            <div class="mt-2 flex items-center">
                                <input type="file" name="image" id="image" 
                                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    accept="image/*">
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                Định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB
                            </p>
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">
                                Hủy
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                Thêm sản phẩm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
