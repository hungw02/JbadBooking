<?php

namespace App\Http\Controllers;

use App\Models\Court;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class CourtController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->role !== 'owner') {
                return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này!');
            }
            
            // Create court image directory if it doesn't exist
            $directory = public_path('image/court');
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            return $next($request);
        });
    }

    public function index()
    {
        $courts = Court::all();
        return view('owner.court.court-manager', compact('courts'));
    }

    public function create()
    {
        return view('owner.court.add-court');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:5|unique:courts',
            'status' => 'required|in:available,maintenance',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'maintenance_start_date' => 'nullable|required_if:status,maintenance|date',
            'maintenance_end_date' => 'nullable|required_if:status,maintenance|date|after_or_equal:maintenance_start_date',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('image/court'), $imageName);
            $data['image'] = 'image/court/' . $imageName;
        }

        // Clear maintenance dates if status is not maintenance
        if ($data['status'] !== 'maintenance') {
            $data['maintenance_start_date'] = null;
            $data['maintenance_end_date'] = null;
        }

        Court::create($data);
        return redirect()->route('courts.index')->with('success', 'Thêm sân thành công!');
    }

    public function edit(Court $court)
    {
        return view('owner.court.update-court', compact('court'));
    }

    public function update(Request $request, Court $court)
    {
        $data = $request->validate([
            'name' => 'required|string|max:5|unique:courts,name,' . $court->id,
            'status' => 'required|in:available,maintenance',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'maintenance_start_date' => 'nullable|required_if:status,maintenance|date',
            'maintenance_end_date' => 'nullable|required_if:status,maintenance|date|after_or_equal:maintenance_start_date',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($court->image && file_exists(public_path($court->image))) {
                unlink(public_path($court->image));
            }
            
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('image/court'), $imageName);
            $data['image'] = 'image/court/' . $imageName;
        } else {
            // Keep existing image if no new image is uploaded
            unset($data['image']);
        }

        // Clear maintenance dates if status is not maintenance
        if ($data['status'] !== 'maintenance') {
            $data['maintenance_start_date'] = null;
            $data['maintenance_end_date'] = null;
        }

        $court->update($data);
        return redirect()->route('courts.index')->with('success', 'Cập nhật sân thành công!');
    }

    public function destroy(Court $court)
    {
        // Check if court has any bookings
        if ($court->singleBookings()->exists() || $court->subscriptionBookings()->exists()) {
            return redirect()->route('courts.index')->with('error', 'Không thể xóa sân này vì đã có đơn đặt liên quan!');
        }
        
        // Delete image if exists
        if ($court->image && file_exists(public_path($court->image))) {
            unlink(public_path($court->image));
        }
        
        $court->delete();
        return redirect()->route('courts.index')->with('success', 'Xóa sân thành công!');
    }
}