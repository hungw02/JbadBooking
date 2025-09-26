@extends('layout.main-customer')

@section('title', 'Quản lý tài khoản')

@section('content')
    <div class="bg-container">
        <img src="{{ asset('image/bg-profile.jpg') }}" alt="Logo">
    </div>
    <div class="content-wrapper">
        <button type="button" class="back-btn" onclick="history.back()"><i class="fa-solid fa-backward-step"></i> Quay lại</button>
        <div class="profile-container">
            <h2>Thông tin tài khoản</h2>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <div class="form-group @error('fullname') has-error @enderror">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="fullname" value="{{ old('fullname', $user->fullname) }}"
                        class="@error('fullname') is-invalid @enderror" placeholder=" " required>
                    <label for="fullname">Họ và tên</label>
                    @error('fullname')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group @error('username') has-error @enderror">
                    <i class="fas fa-user-circle input-icon"></i>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}"
                        class="@error('username') is-invalid @enderror" placeholder=" " required>
                    <label for="username">Tên đăng nhập</label>
                    @error('username')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group @error('email') has-error @enderror">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="@error('email') is-invalid @enderror" placeholder=" " required>
                    <label for="email">Email</label>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group @error('phone') has-error @enderror">
                    <i class="fas fa-phone input-icon"></i>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="@error('phone') is-invalid @enderror" placeholder=" " required>
                    <label for="phone">Số điện thoại</label>
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group @error('password') has-error @enderror">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" id="password" class="@error('password') is-invalid @enderror"
                        placeholder=" ">
                    <label for="password">Mật khẩu mới</label>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group @error('password_confirmation') has-error @enderror">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="@error('password_confirmation') is-invalid @enderror" placeholder=" ">
                    <label for="password_confirmation">Xác nhận mật khẩu</label>
                    @error('password_confirmation')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="auth-btn">Cập nhật</button>
            </form>
        </div>

        <div class="account-container">
            <h2>Hồ sơ thân thiết</h2>
            <p>Mức hạng: {{ $user->rank }}</p>
            <img src="{{ asset('image/rank/' . ($user->rank_image ?? 'no_rank') . '.png') }}" alt="{{ $user->rank }}" class="h-6 mr-2">
            <p>Ví cá nhân: {{ number_format($user->wallets, 0, ',', '.') }} Xu</p>
            <a href="{{ route('booking.index') }}" class="inline-flex items-center justify-center rounded-md bg-cyan-900 px-3 py-2 text-sm font-medium text-cyan-300 hover:bg-cyan-800 border border-cyan-700">
                    <i class="fa-solid fa-plus mr-1"></i> Đặt sân
                </a>
        </div>
    </div>
@endsection
