document.addEventListener('DOMContentLoaded', function() {
    // Get message data from DOM element
    const sessionData = document.getElementById('session-data');
    const successMessage = sessionData?.getAttribute('data-success');
    const errorMessage = sessionData?.getAttribute('data-error');
    
    // Kiểm tra xem có thông báo success không
    if (successMessage) {
        Swal.fire({
            title: 'Khó thế cũng làm được!',
            text: successMessage,
            icon: 'success',
            confirmButtonText: 'Duyệt',
            customClass: {
                popup: 'swal-custom-popup',
                title: 'swal-custom-title',
                confirmButton: 'swal-custom-button'
            }
        });
    }

    // Kiểm tra xem có thông báo error không
    if (errorMessage) {
        Swal.fire({
            title: 'Có biến xảy ra!',
            text: errorMessage,
            icon: 'error',
            confirmButtonText: 'Duyệt',
            customClass: {
                popup: 'swal-custom-popup',
                title: 'swal-custom-title',
                confirmButton: 'swal-custom-button'
            }
        });
    }
}); 