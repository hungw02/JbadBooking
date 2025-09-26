<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AccountController
{
    public function showLoginForm()
    {
        return view('account.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $redirect = redirect()->intended(route('home'));
            
            return $redirect->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors(['login_error' => 'Thông tin đăng nhập không chính xác']);
    }

    public function showRegisterForm()
    {
        return view('account.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|string|regex:/^0\d{9}$/|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $user = User::create([
                'fullname' => $data['fullname'],
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'role' => 'customer',
            ]);

            return redirect()->route('login')->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['register_error' => 'Có lỗi xảy ra khi đăng ký. Vui lòng thử lại.']);
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home')->with('success', 'Đăng xuất thành công!');
    }

    public function showForgotPasswordForm()
    {
        return view('account.forgot-password');
    }

    public function handleResetPassword(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string|regex:/^0\d{9}$/',
        ]);

        // Tìm người dùng theo username, email và phone
        $user = User::where('username', $data['username'])
            ->where('email', $data['email'])
            ->where('phone', $data['phone'])
            ->first();

        if ($user) {
            // Tạo mật khẩu ngẫu nhiên 6 chữ số
            $newPassword = rand(100000, 999999);
            $user->password = Hash::make($newPassword);
            $user->save();

            return redirect()->back()->with('success', 'Mật khẩu mới của bạn là: ' . $newPassword);
        }

        return redirect()->back()->withErrors(['reset_error' => 'Thông tin không chính xác.']);
    }

    public function profile()
    {
        return view('account.profile');
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . Auth::id(),
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'required|string|regex:/^0\d{9}$/|unique:users,phone,' . Auth::id(),
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user = User::find(Auth::id());
        if (!$user) {
            return redirect()->route('profile')->withErrors(['error' => 'User not found.']);
        }

        $user->fullname = $data['fullname'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];

        if ($data['password']) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Cập nhật thông tin thành côngg!');
    }
}
