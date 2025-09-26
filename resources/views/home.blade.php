@extends('layout.main-customer')

@section('title', 'Trang Chủ')

@section('content')
<div class="home-container">
    {{-- Slider --}}
    @include('components.slider')
    <!-- Sidebar -->
    @include('components.sidebar-customer')

    <!-- Nút liên hệ -->
    <div class="contact-buttons">
        <a href="https://www.facebook.com" target="_blank" class="contact-btn facebook">
            <img src="{{ asset('image/facebook.png') }}" alt="Facebook">
        </a>
        <a href="https://m.me/" target="_blank" class="contact-btn messenger">
            <img src="{{ asset('image/messenger.png') }}" alt="Messenger">
        </a>
        <a href="https://zalo.me/" target="_blank" class="contact-btn zalo">
            <img src="{{ asset('image/zalo.png') }}" alt="Zalo">
        </a>
    </div>

    <!-- Action buttons below slider -->
    <div class="home-options">
        <a href="#introduce" class="option-btn">Giới thiệu</a>
        <a href="#regulations" class="option-btn">Quy định</a>
        <a href="#question" class="option-btn">Câu hỏi</a>
    </div>

    <!-- Giới thiệu về hệ thống -->
    <div class="introduce-container" id="introduce">
        <h2>Giới thiệu về JBADMINTON</h2>
        <span>JBADMINTON là hệ thống đặt sân cầu lông trực tuyến, giúp bạn tìm kiếm, đặt sân nhanh chóng và dễ dàng. Chúng
            tôi cam kết mang đến trải nghiệm tốt nhất với nhiều sân cầu lông chất lượng, giá cả hợp lý.</span>
        <div class="introduce-img">
            <img src="{{ asset('image/gioithieu1.jpg') }}" alt="Sân cầu lông">
            <img src="{{ asset('image/gioithieu2.jpg') }}" alt="Sân cầu lông">
            <img src="{{ asset('image/gioithieu3.jpg') }}" alt="Sân cầu lông">
        </div>
    </div>

    <!-- Quy định đặt sân -->
    <div class="regulations-container" id="regulations">
        <h2>Những quy định tại JBADMINTON</h2>
        @php
        $regulations = [
        [
        'summary' => 'Quy định khi đặt sân',
        'details' => [
        'Thời gian đặt sân tối thiểu là 1 tiếng.',
        'Thanh toán trước khi vào sân.',
        'Bạn có thể hủy đặt sân trong vòng 5p sau đặt để được hoàn lại 100%.',
        ],
        ],
        [
        'summary' => 'Quy định khi hủy lịch đặt theo buổi',
        'details' => [
        'Hủy trong vòng 5 phút sau khi đặt: hoàn 100% phí.',
        'Hủy trước giờ chơi trên 24 tiếng: hoàn 50% phí.',
        'Hủy trước giờ chơi từ 12-24 tiếng: hoàn 25% phí.',
        'Hủy trong vòng 12 tiếng trước giờ chơi: không hoàn phí',
        'Số tiền hoàn sẽ được chuyển vào ví cá nhân, sử dụng cho lần tới đặt sân.',
        ],
        ],
        [
        'summary' => 'Quy định khi hủy lịch đặt định kỳ',
        'details' => [
        'Hủy trong vòng 5 phút sau khi đặt: hoàn 100% phí.',
        'Hủy trước ngày chơi đầu tiên trên 24 tiếng: hoàn 50% phí.',
        'Hủy trước ngày chơi đầu tiên từ 12-24 tiếng: hoàn 25% phí.',
        'Hủy trước ngày chơi đầu tiên dưới 12 tiếng: không hoàn phí',
        'Số tiền hoàn sẽ được chuyển vào ví cá nhân, sử dụng cho lần tới đặt sân.',
        ],
        ],
        [
        'summary' => 'Quy định khi chơi tại sân',
        'details' => [
        'Không gây mất trật tự, ảnh hưởng người chơi khác.',
        'Không hút thuốc trong sân.',
        'Giữ gìn vệ sinh và bảo vệ cơ sở vật chất.',
        ],
        ],
        ];
        @endphp
        @foreach ($regulations as $regulation)
        <details>
            <summary>{{ $regulation['summary'] }}</summary>
            <ul class="regulation-list">
                @foreach ($regulation['details'] as $detail)
                <li class="regulation-item"><img src="{{ asset('image/icon-option.png') }}"
                        alt="">{{ $detail }}</li>
                @endforeach
            </ul>
        </details>
        @endforeach
    </div>

    <!-- Câu hỏi thường gặp -->
    <div class="question-container" id="question">
        <h2>Câu hỏi thường gặp</h2>
        @php
        $questions = [
        [
        'summary' => 'Làm sao để đặt sân?',
        'details' =>
        'Bạn có thể đặt sân trực tuyến qua hệ thống hoặc liên hệ trực tiếp với quản lý sân.',
        ],
        [
        'summary' => 'Tôi có thể hủy đặt lịch không?',
        'details' => 'Bạn có thể hủy đặt lịch theo quy định của sân.',
        ],
        [
        'summary' => 'Hình thức thanh toán?',
        'details' => 'Chúng tôi hỗ trợ thanh toán qua VNPAY, số dư trong ví cá nhân.',
        ],
        ];
        @endphp
        @foreach ($questions as $question)
        <details>
            <summary>{{ $question['summary'] }}</summary>
            <p><img src="{{ asset('image/icon-option.png') }}" alt="">{{ $question['details'] }}</p>
        </details>
        @endforeach
    </div>
</div>
@endsection