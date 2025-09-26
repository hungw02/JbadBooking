<?php

namespace App\Http\Controllers;

use App\Models\Import;
use App\Models\ImportItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class ImportController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->role !== 'owner') {
                return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này!');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $imports = Import::orderBy('created_at', 'desc')->get();
        return view('owner.import.import-manager', compact('imports'));
    }

    public function create()
    {
        $products = Product::all();
        return view('owner.import.add-import', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'workshop_name' => 'required|string|max:255',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.import_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Calculate total price for the entire import
            $totalImportPrice = 0;
            foreach ($request->products as $item) {
                $totalImportPrice += $item['quantity'] * $item['import_price'];
            }

            // Create import record
            $import = Import::create([
                'owner_id' => Auth::id(),
                'workshop_name' => $request->workshop_name,
                'total_price' => $totalImportPrice,
            ]);

            // Process each product
            foreach ($request->products as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Create import item
                ImportItem::create([
                    'import_id' => $import->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'import_price' => $item['import_price'],
                ]);

                // Update product quantity and import price
                $product->quantity += $item['quantity'];
                $product->import_price = $item['import_price']; // Update import price
                $product->save();
            }

            DB::commit();
            return redirect()->route('imports.index')->with('success', 'Nhập hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Import $import)
    {
        $import->load('items.product');
        return view('owner.import.import-detail', compact('import'));
    }

    public function destroy(Import $import)
    {
        $import->delete();
        return redirect()->route('imports.index')->with('success', 'Xóa thông tin nhập hàng thành công!');
    }

    public function productHistory($productId)
    {
        $product = Product::findOrFail($productId);
        $importItems = ImportItem::where('product_id', $productId)
            ->with('import')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('owner.import.product-history', compact('product', 'importItems'));
    }
}
