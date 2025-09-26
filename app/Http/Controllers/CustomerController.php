<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class CustomerController extends BaseController
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

    public function index(Request $request)
    {
        $query = User::where('role', 'customer');

        // Tìm kiếm theo tên
        if ($request->has('search')) {
            $query->where('fullname', 'like', '%' . $request->search . '%');
        }

        // Lấy danh sách khách hàng
        $customers = $query->orderBy('point', 'desc')->get();

        // Thống kê số lượng theo rank
        $rankStats = [
            'no_rank' => $customers->filter(fn($user) => $user->point == 0)->count(),
            'bronze' => $customers->filter(fn($user) => $user->point >= 1 && $user->point <= 5)->count(),
            'silver' => $customers->filter(fn($user) => $user->point >= 6 && $user->point <= 10)->count(),
            'gold' => $customers->filter(fn($user) => $user->point >= 11 && $user->point <= 20)->count(),
            'platinum' => $customers->filter(fn($user) => $user->point >= 21 && $user->point <= 30)->count(),
            'diamond' => $customers->filter(fn($user) => $user->point >= 31 && $user->point <= 40)->count(),
            'ruby' => $customers->filter(fn($user) => $user->point >= 41)->count(),
        ];

        return view('owner.customer.customer-manager', compact('customers', 'rankStats'));
    }

    public function show(User $customer)
    {
        return view('owner.customer.customer-detail', compact('customer'));
    }

    public function toggleStatus(User $customer)
    {
        $customer->status = $customer->status === 'active' ? 'inactive' : 'active';
        $customer->save();

        return redirect()->route('customers.index')
            ->with('success', 'Cập nhật trạng thái thành công!');
    }
}
