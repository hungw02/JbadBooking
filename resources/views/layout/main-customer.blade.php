<!DOCTYPE html>
<html lang="vi">
@include('layout.head')

<body>
    @include('layout.header')

    <div class="bg-container">
        <img src="{{ asset('image/bg-home.jpg') }}" alt="">
    </div>

    @include('components.loading')

    <main>
        @yield('content')
    </main>

    @include('layout.footer')

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Thành công!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'Xác nhận',
                customClass: {
                    popup: 'swal-custom-popup',
                    title: 'swal-custom-title',
                    confirmButton: 'swal-custom-button'
                }
            });
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Không thành công!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'Xác nhận',
                customClass: {
                    popup: 'swal-custom-popup',
                    title: 'swal-custom-title',
                    confirmButton: 'swal-custom-button'
                }
            });
        });
    </script>
    @endif

    @if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Thông báo!',
                text: "{{ session('info') }}",
                icon: 'info',
                confirmButtonText: 'Đồng ý',
                customClass: {
                    popup: 'swal-custom-popup',
                    title: 'swal-custom-title',
                    confirmButton: 'swal-custom-button'
                }
            });
        });
    </script>
    @endif

    @stack('scripts')
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>

</html>
