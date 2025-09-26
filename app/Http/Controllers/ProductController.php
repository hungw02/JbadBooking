<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class ProductController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->role !== 'owner') {
                return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này!');
            }
            
            // Create product image directory if it doesn't exist
            $directory = public_path('image/product');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            return $next($request);
        });
    }

    public function index()
    {
        $query = Product::query();
        
        // Tìm kiếm sản phẩm
        if (request()->has('search')) {
            $searchTerm = request('search');
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }
        
        // Filter by type
        if (request()->has('type') && in_array(request('type'), ['sale', 'rent'])) {
            $query->where('type', request('type'));
        }
        
        $products = $query->get();
        return view('owner.product.product-manager', compact('products'));
    }

    public function create()
    {
        return view('owner.product.add-product');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:products',
            'import_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'sale' => 'required|numeric|min:0|max:100',
            'quantity' => 'required|numeric|min:0',
            'status' => 'required|in:available,out_of_stock',
            'type' => 'required|in:sale,rent',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('image/product'), $imageName);
            $data['image'] = 'image/product/' . $imageName;
        }

        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function edit(Product $product)
    {
        return view('owner.product.update-product', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'import_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'sale' => 'required|numeric|min:0|max:100',
            'quantity' => 'required|numeric|min:0',
            'status' => 'required|in:available,out_of_stock',
            'type' => 'required|in:sale,rent',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }
            
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('image/product'), $imageName);
            $data['image'] = 'image/product/' . $imageName;
        } else {
            // Keep existing image if no new image is uploaded
            unset($data['image']);
        }

        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(Product $product)
    {
        // Delete image if exists
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }
        
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Xóa sản phẩm thành công!');
    }
} 