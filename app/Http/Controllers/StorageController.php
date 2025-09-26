<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class StorageController extends BaseController
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
        $query = Storage::query()->with('product');

        // Filter by transaction type
        if (request()->has('transaction_type') && in_array(request('transaction_type'), ['sale', 'rent'])) {
            $query->where('transaction_type', request('transaction_type'));
        }

        // Filter by status
        if (request()->has('status') && in_array(
            request('status'),
            ['returned', 'not_returned', 'completed']
        )) {
            $query->where('status', request('status'));
        }

        $transactions = $query->latest()->get();
        return view('owner.storage.storage-manager', compact('transactions'));
    }

    public function create()
    {
        $transactionType = request('transaction_type', 'rent');

        // Chỉ lấy sản phẩm đúng loại: loại bán chỉ được bán, loại thuê chỉ được cho thuê
        $products = Product::where('type', $transactionType)
            ->where('status', 'available')
            ->where('quantity', '>', 0)
            ->get();

        // Kiểm tra nếu không có sản phẩm phù hợp thì hiển thị thông báo
        if ($products->isEmpty()) {
            $messageType = $transactionType === 'sale' ? 'bán' : 'cho thuê';
            return redirect()->route('storage.index')
                ->with('error', "Không có sản phẩm loại \"{$messageType}\" nào có sẵn. Vui lòng thêm sản phẩm mới!");
        }

        return view('owner.storage.add-storage', compact('products', 'transactionType'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'transaction_type' => 'required|in:sale,rent',
            'note' => 'nullable|string',
        ]);

        // Handle different validation based on transaction type
        if ($data['transaction_type'] === 'rent') {
            $productData = $request->validate([
                'products.0.product_id' => 'required|exists:products,id',
                'products.0.quantity' => 'required|integer|min:1',
            ]);

            // Only one product for rent transactions
            $productId = $productData['products'][0]['product_id'];
            $quantity = $productData['products'][0]['quantity'];

            $product = Product::findOrFail($productId);

            // Check if product type matches transaction type
            if ($product->type !== $data['transaction_type']) {
                return back()->with('error', 'Loại sản phẩm không phù hợp với loại giao dịch! Chỉ sản phẩm loại "thuê" mới có thể cho thuê.');
            }

            // Check if there's enough quantity
            if ($product->quantity < $quantity) {
                return back()->with('error', 'Số lượng sản phẩm không đủ!');
            }

            // Calculate total price
            $totalPrice = $product->selling_price * $quantity;

            // Create transaction record for rent
            $transaction = Storage::create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'total_price' => $totalPrice,
                'transaction_type' => $data['transaction_type'],
                'status' => 'not_returned',
                'note' => $data['note'],
            ]);

            // Update product quantity
            $product->quantity -= $quantity;
            $product->save();

            return redirect()->route('storage.index')->with('success', 'Tạo hóa đơn cho thuê thành công!');
        } else {
            // For sale transactions - multiple products allowed
            if (!$request->has('products') || !is_array($request->products)) {
                return back()->with('error', 'Vui lòng chọn ít nhất một sản phẩm!');
            }

            // Combine duplicate products
            $productMap = [];

            foreach ($request->products as $index => $productData) {
                // Validate each product entry
                $request->validate([
                    "products.{$index}.product_id" => 'required|exists:products,id',
                    "products.{$index}.quantity" => 'required|integer|min:1',
                ]);

                $productId = $productData['product_id'];
                $quantity = (int)$productData['quantity'];

                // Add quantity to existing product or create new entry
                if (isset($productMap[$productId])) {
                    $productMap[$productId]['quantity'] += $quantity;
                } else {
                    $productMap[$productId] = [
                        'quantity' => $quantity,
                        'product' => null
                    ];
                }
            }

            // Validate and process each product
            $totalOrderPrice = 0;
            $productIds = [];
            $productNames = [];
            $productQuantities = [];

            foreach ($productMap as $productId => $productData) {
                $product = Product::findOrFail($productId);
                $productMap[$productId]['product'] = $product;
                $quantity = $productData['quantity'];

                // Check if product type matches transaction type
                if ($product->type !== $data['transaction_type']) {
                    return back()->with('error', "Sản phẩm \"{$product->name}\" không phải là sản phẩm loại bán!");
                }

                // Check if there's enough quantity
                if ($product->quantity < $quantity) {
                    return back()->with('error', "Số lượng sản phẩm \"{$product->name}\" không đủ! Hiện còn {$product->quantity}, bạn yêu cầu {$quantity}.");
                }

                // Calculate item price and add to total
                $itemPrice = $product->selling_price * $quantity;
                $totalOrderPrice += $itemPrice;

                // Store product info
                $productIds[] = $product->id;
                $productNames[] = $product->name;
                $productQuantities[] = $quantity;

                // Update product quantity
                $product->quantity -= $quantity;
                $product->save();
            }

            // Create transaction record for sale with multiple products
            $transaction = Storage::create([
                'product_id' => implode(',', $productIds),
                'product_name' => implode(',', $productNames),
                'quantity' => implode(',', $productQuantities),
                'total_price' => $totalOrderPrice,
                'transaction_type' => $data['transaction_type'],
                'status' => 'completed',
                'note' => $data['note'],
            ]);

            return redirect()->route('storage.index')->with('success', 'Tạo hóa đơn bán hàng thành công!');
        }
    }

    public function show(Storage $rental)
    {
        return view('owner.storage.view-storage', compact('rental'));
    }

    public function edit(Storage $rental)
    {
        return view('owner.storage.update-storage', compact('rental'));
    }

    public function update(Request $request, Storage $rental)
    {
        $validStatuses = $rental->transaction_type === 'rent'
            ? ['returned', 'not_returned']
            : ['completed'];

        $data = $request->validate([
            'status' => 'required|in:' . implode(',', $validStatuses),
            'note' => 'nullable|string',
        ]);

        $oldStatus = $rental->status;
        $rental->update($data);

        // If rental status changed from not_returned to returned, update product quantity
        if ($rental->transaction_type === 'rent' && $oldStatus === 'not_returned' && $data['status'] === 'returned') {
            if (str_contains($rental->product_id, ',')) {
                // Handle multiple products (should not happen for rent, but just in case)
                $productIds = explode(',', $rental->product_id);
                $quantities = explode(',', $rental->quantity);

                foreach ($productIds as $index => $productId) {
                    $quantity = isset($quantities[$index]) ? (int)$quantities[$index] : 0;
                    if ($quantity > 0) {
                        $product = Product::find($productId);
                        if ($product) {
                            $product->quantity += $quantity;
                            $product->save();
                        }
                    }
                }
            } else {
                // Single product (normal case for rent)
                $product = Product::find($rental->product_id);
                if ($product) {
                    $product->quantity += (int)$rental->quantity;
                    $product->save();
                }
            }
        }
        // If rental status changed from returned to not_returned, subtract product quantity
        else if ($rental->transaction_type === 'rent' && $oldStatus === 'returned' && $data['status'] === 'not_returned') {
            if (str_contains($rental->product_id, ',')) {
                // Handle multiple products (should not happen for rent, but just in case)
                $productIds = explode(',', $rental->product_id);
                $quantities = explode(',', $rental->quantity);

                foreach ($productIds as $index => $productId) {
                    $quantity = isset($quantities[$index]) ? (int)$quantities[$index] : 0;
                    if ($quantity > 0) {
                        $product = Product::find($productId);
                        if ($product) {
                            // Check if enough quantity available
                            if ($product->quantity >= $quantity) {
                                $product->quantity -= $quantity;
                                $product->save();
                            } else {
                                // Revert the status change if not enough quantity
                                $rental->status = $oldStatus;
                                $rental->save();
                                return redirect()->route('storage.index')->with(
                                    'error',
                                    'Không đủ số lượng sản phẩm trong kho để chuyển trạng thái này.'
                                );
                            }
                        }
                    }
                }
            } else {
                // Single product (normal case for rent)
                $product = Product::find($rental->product_id);
                if ($product) {
                    // Check if enough quantity available
                    if ($product->quantity >= (int)$rental->quantity) {
                        $product->quantity -= (int)$rental->quantity;
                        $product->save();
                    } else {
                        // Revert the status change if not enough quantity
                        $rental->status = $oldStatus;
                        $rental->save();
                        return redirect()->route('storage.index')->with(
                            'error',
                            'Không đủ số lượng sản phẩm trong kho để chuyển trạng thái này.'
                        );
                    }
                }
            }
        }

        return redirect()->route('storage.index')->with(
            'success',
            $rental->transaction_type === 'rent'
                ? 'Cập nhật hóa đơn cho thuê thành công!'
                : 'Cập nhật hóa đơn bán hàng thành công!'
        );
    }

    public function returnRental(Storage $rental)
    {
        if ($rental->transaction_type !== 'rent') {
            return back()->with('error', 'Chỉ áp dụng cho phiếu thuê!');
        }

        if ($rental->status !== 'not_returned') {
            return back()->with('error', 'Phiếu thuê này đã được trả!');
        }

        // Update rental status
        $rental->status = 'returned';
        $rental->save();

        // Update product quantity
        if (str_contains($rental->product_id, ',')) {
            // Handle multiple products (should not happen for rent, but just in case)
            $productIds = explode(',', $rental->product_id);
            $quantities = explode(',', $rental->quantity);

            foreach ($productIds as $index => $productId) {
                $quantity = isset($quantities[$index]) ? (int)$quantities[$index] : 0;
                if ($quantity > 0) {
                    $product = Product::find($productId);
                    if ($product) {
                        $product->quantity += $quantity;
                        $product->save();
                    }
                }
            }
        } else {
            // Single product (normal case for rent)
            $product = Product::find($rental->product_id);
            if ($product) {
                $product->quantity += (int)$rental->quantity;
                $product->save();
            }
        }

        return redirect()->route('storage.index')->with('success', 'Đã cập nhật trạng thái trả đồ thành công!');
    }

    public function printInvoice(Storage $rental)
    {
        return view('owner.storage.invoice', compact('rental'));
    }
}
