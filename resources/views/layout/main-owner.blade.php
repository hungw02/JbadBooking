<!DOCTYPE html>
<html lang="vi">
@include('layout.head')

<body>
    <div class="bg-container">
        <img src="{{ asset('image/bg-home.jpg') }}" alt="">
    </div>
    
    <div class="relative z-10">
        <div id="header-container" class="ml-16">
            @include('layout.header')
        </div>

        @include('components.sidebar-owner')

        @include('components.loading')

        <main class="ml-16 pt-4 px-4" id="main-content">
            <div class="bg-white text-gray-800 rounded-lg shadow-lg">
                @yield('content')
            </div>
        </main>
    </div>
</body>
<script src="{{ asset('js/app.js') }}" defer></script>

</html>
