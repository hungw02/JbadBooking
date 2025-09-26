module.exports = BookingManagerOwner;

function BookingManagerOwner() {
    this.init = function() {
        this.setupPrintInvoice();
        this.setupCancelConfirmation();
        this.setupDateTimeValidation();
    };

    this.setupPrintInvoice = function() {
        const printButtons = document.querySelectorAll('.print-invoice-btn');
        if (printButtons.length === 0) return;

        printButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                window.open(url, '_blank', 'width=800,height=600');
            });
        });
    };

    this.setupCancelConfirmation = function() {
        const cancelForms = document.querySelectorAll('form[data-confirm]');
        if (cancelForms.length === 0) return;

        cancelForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const confirmMessage = this.getAttribute('data-confirm');
                if (confirm(confirmMessage)) {
                    this.submit();
                }
            });
        });
    };

    this.setupDateTimeValidation = function() {
        // For single booking
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        
        if (startTimeInput && endTimeInput) {
            startTimeInput.addEventListener('change', function() {
                if (endTimeInput.value && endTimeInput.value <= startTimeInput.value) {
                    alert('Thời gian kết thúc phải sau thời gian bắt đầu!');
                    endTimeInput.value = '';
                }
            });
            
            endTimeInput.addEventListener('change', function() {
                if (startTimeInput.value && endTimeInput.value <= startTimeInput.value) {
                    alert('Thời gian kết thúc phải sau thời gian bắt đầu!');
                    endTimeInput.value = '';
                }
            });
        }
        
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', function() {
                if (endDateInput.value && endDateInput.value <= startDateInput.value) {
                    alert('Ngày kết thúc phải sau ngày bắt đầu!');
                    endDateInput.value = '';
                }
            });
            
            endDateInput.addEventListener('change', function() {
                if (startDateInput.value && endDateInput.value <= startDateInput.value) {
                    alert('Ngày kết thúc phải sau ngày bắt đầu!');
                    endDateInput.value = '';
                }
            });
        }
    }
} 