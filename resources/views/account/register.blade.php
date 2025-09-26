@extends('layout.main-customer')

@section('title', 'Đăng ký')

@section('content')
    <div class="bg-container">
        <video autoplay loop muted playsinline>
            <source src="{{ asset('video/bg-register.mp4') }}" type="video/mp4">
            Trình duyệt của bạn không hỗ trợ video.
        </video>
    </div>
    <div class="auth-container">
        <h2>Tham gia cùng hội lông thủ</h2>
        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="fullname" value="{{ old('fullname') }}"
                    class="@error('fullname') is-invalid @enderror" placeholder=" ">
                <label for="fullname">Họ và tên</label>
                @error('fullname')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <i class="fas fa-user-circle input-icon"></i>
                <input type="text" name="username" value="{{ old('username') }}"
                    class="@error('username') is-invalid @enderror" placeholder=" ">
                <label for="username">Tên đăng nhập</label>
                @error('username')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <i class="fas fa-envelope input-icon"></i>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="@error('email') is-invalid @enderror" placeholder=" ">
                <label for="email">Email</label>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <i class="fas fa-phone input-icon"></i>
                <input type="text" name="phone" value="{{ old('phone') }}" pattern="^0\d{9}$"
                    title="Số điện thoại phải có 10 chữ số và bắt đầu bằng số 0"
                    class="@error('phone') is-invalid @enderror" placeholder=" ">
                <label for="phone">Số điện thoại</label>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" id="password" minlength="6"
                    class="@error('password') is-invalid @enderror" placeholder=" ">
                <label for="password">Mật khẩu</label>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder=" ">
                <label for="password_confirmation">Xác nhận mật khẩu</label>
            </div>

            @error('register_error')
                <span class="error-message">{{ $message }}</span>
            @enderror

            <div class="auth-options">
                <a href="{{ route('login') }}" class="forgot-password">Về trang đăng nhập</a>
            </div>

            <button type="submit" class="auth-btn">Đăng ký</button>
        </form>
    </div>
@endsection