@extends('layout.main-customer')

@section('title', 'Đăng nhập')

@section('content')
    <div class="bg-container">
        <video autoplay loop muted playsinline>
            <source src="{{ asset('video/bg-login.mp4') }}" type="video/mp4">
            Trình duyệt của bạn không hỗ trợ video.
        </video>
    </div>
    <div class="auth-container">
        <h2>Bắt đầu nào !!!</h2>
        <img src="{{ asset('image/logo-login.png') }}" alt="Logo">
        <form action="{{ route('login') }}" method="POST">
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
            <div class="form-group @error('password') has-error @enderror">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" id="password" class="@error('password') is-invalid @enderror"
                    placeholder=" ">
                <label for="password">Mật khẩu</label>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            @error('login_error')
                <span style="color: red;" class="error-message">{{ $message }}</span>
            @enderror

            <div class="auth-options">
                <a href="{{ route('password.request') }}" class="forgot-password">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="auth-btn">Đăng nhập</button>
        </form>
    </div>
@endsection