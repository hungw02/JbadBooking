<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;

class PromotionController extends BaseController
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
        $promotions = Promotion::orderBy('start_date', 'desc')->get();
        return view('owner.promotion.promotion-manager', compact('promotions'));
    }

    public function create()
    {
        return view('owner.promotion.add-promotion');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'discount_percent' => 'required|integer|between:0,100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive',
            'booking_type' => 'required|in:all,single,subscription',
        ]);

        // Kiểm tra nếu checkbox "khuyến mãi vĩnh viễn" được chọn, set end_date về null
        if ($request->has('is_permanent_checkbox') && $request->is_permanent_checkbox) {
            $data['end_date'] = null;
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('image/promotion'), $imageName);
            $data['image'] = 'image/promotion/' . $imageName;
        }

        Promotion::create($data);
        return redirect()->route('promotions.index')->with('success', 'Thêm khuyến mãi thành công!');
    }

    public function edit(Promotion $promotion)
    {
        return view('owner.promotion.update-promotion', compact('promotion'));
    }

    public function update(Request $request, Promotion $promotion)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'discount_percent' => 'required|integer|between:0,100',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,inactive',
            'booking_type' => 'required|in:all,single,subscription',
        ]);

        // Kiểm tra nếu checkbox "khuyến mãi vĩnh viễn" được chọn, set end_date về null
        if ($request->has('is_permanent_checkbox') && $request->is_permanent_checkbox) {
            $data['end_date'] = null;
        }

        if ($request->hasFile('image')) {
            if ($promotion->image && file_exists(public_path($promotion->image))) {
                unlink(public_path($promotion->image));
            }
            
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('image/promotion'), $imageName);
            $data['image'] = 'image/promotion/' . $imageName;
        }

        $promotion->update($data);
        return redirect()->route('promotions.index')->with('success', 'Cập nhật khuyến mãi thành công!');
    }

    public function destroy(Promotion $promotion)
    {
        if ($promotion->image && file_exists(public_path($promotion->image))) {
            unlink(public_path($promotion->image));
        }
        $promotion->delete();
        return redirect()->route('promotions.index')->with('success', 'Xóa khuyến mãi thành công!');
    }
}
