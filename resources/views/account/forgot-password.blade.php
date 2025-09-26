@extends('layout.main-customer')

@section('content')
    <div class="bg-container">
        <video autoplay loop muted playsinline>
            <source src="{{ asset('video/bg-login.mp4') }}" type="video/mp4">
            Trình duyệt của bạn không hỗ trợ video.
        </video>
    </div>
    <div class="auth-container">
        <h2>Quên mật khẩu</h2>
        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group @error('username') has-error @enderror">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="username" value="{{ old('username') }}"
                    class="@error('username') is-invalid @enderror" placeholder=" ">
                <label for="username">Tài khoản</label>
                @error('username')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group @error('email') has-error @enderror">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="@error('email') is-invalid @enderror" placeholder=" ">
                <label for="email">Email</label>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group @error('phone') has-error @enderror">
                <i class="fas fa-phone input-icon"></i>
                <input type="text" name="phone" value="{{ old('phone') }}"
                    class="@error('phone') is-invalid @enderror" placeholder=" ">
                <label for="phone">Số điện thoại</label>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            @error('reset_error')
                <span style="color: red;" class="error-message">{{ $message }}</span>
            @enderror

            <div class="auth-options">
                <a href="{{ route('login') }}" class="forgot-password">Về trang đăng nhập</a>
            </div>

            <button type="submit" class="auth-btn">Cấp lại mật khẩu</button>
        </form>
    </div>
@endsection