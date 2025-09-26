let bookingDate = '';
let selectedCourts = [];
let startTime = '';
let endTime = '';
let totalPrice = 0;
let paymentMethod = 'full';
let paymentType = 'vnpay';
let promotions = [];
let selectedPromotionId = null;
let discountPercent = 0;
let originalPrice = 0;

// Khởi tạo các biến và lắng nghe sự kiện
document.addEventListener('DOMContentLoaded', function() {
    const bookingDateInput = document.getElementById('booking-date');
    const startTimeSelect = document.getElementById('start-time');
    const endTimeSelect = document.getElementById('end-time');
    const courtCheckboxes = document.querySelectorAll('.court-select');
    const paymentTypeRadios = document.querySelectorAll('input[name="payment_type"]');
    const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
    const courtErrorDisplay = document.getElementById('court-error');
    const bookingForm = document.getElementById('bookingForm');
    
    // Khởi tạo giá trị mặc định
    if (bookingDateInput) bookingDate = bookingDateInput.value;
    if (startTimeSelect) startTime = startTimeSelect.value;
    if (endTimeSelect) endTime = endTimeSelect.value;
    
    // Hiển thị ngày trong tuần
    updateDayOfWeekDisplay();
    
    // Tải sẵn tình trạng sân khi trang được tải
    if (bookingDateInput) {
        loadCourtAvailability();
    }
    
    // Đảm bảo xác thực ban đầu
    setTimeout(() => {
        validateTimeAndUpdateSummary();
    }, 500);
    
    // Các sự kiện lắng nghe
    if (bookingDateInput) {
        bookingDateInput.addEventListener('change', function() {
            bookingDate = this.value;
            updateDayOfWeekDisplay();
            loadCourtAvailability();
        });
    }
    
    if (startTimeSelect) {
        startTimeSelect.addEventListener('change', function() {
            startTime = this.value;
            validateTimeAndUpdateSummary();
        });
    }
    
    if (endTimeSelect) {
        endTimeSelect.addEventListener('change', function() {
            endTime = this.value;
            validateTimeAndUpdateSummary();
        });
    }
    
    courtCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const courtId = parseInt(this.value);
            const courtNameElement = this.closest('.court-card').querySelector('.font-semibold');
            const courtName = courtNameElement ? courtNameElement.textContent : `Court ${courtId}`;
            
            if (this.checked) {
                if (!selectedCourts.includes(courtId)) {
                    selectedCourts.push(courtId);
                }
            } else {
                selectedCourts = selectedCourts.filter(id => id !== courtId);
            }
            
            updateSummary();
            updateSelectedCourtsDisplay();
            
            // Xóa thông báo lỗi nếu đã chọn sân
            if (selectedCourts.length > 0 && courtErrorDisplay) {
                courtErrorDisplay.textContent = '';
            }

            // Kiểm tra số dư ví khi thay đổi sân
            checkWalletBalance();
        });
    });
    
    paymentTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            paymentType = this.value;
            updateSummary();
            // Kiểm tra số dư ví khi thay đổi loại thanh toán
            checkWalletBalance();
        });
    });
    
    paymentMethodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            paymentMethod = this.value;
            updateSummary();
            
            // Kiểm tra số dư ví khi chọn thanh toán bằng ví
            checkWalletBalance();
        });
    });
    
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            let isValid = true;
            
            if (!validateTime()) {
                isValid = false;
            }
            
            // Kiểm tra xem có ít nhất một sân được chọn không
            const courtsSelected = Array.from(courtCheckboxes).some(checkbox => checkbox.checked);
            if (!courtsSelected) {
                if (courtErrorDisplay) {
                    courtErrorDisplay.textContent = 'Vui lòng chọn sân mà bạn muốn đặt';
                }
                isValid = false;
            } else if (courtErrorDisplay) {
                courtErrorDisplay.textContent = '';
            }
            
            // Kiểm tra số dư ví nếu chọn thanh toán bằng ví
            if (!checkWalletBalance()) {
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // Kiểm tra ngay khi trang tải xong
    setTimeout(() => {
        checkWalletBalance();
    }, 1000);

    // Load the best promotion automatically
    loadAndApplyBestPromotion();
});

// Hàm cập nhật hiển thị ngày trong tuần khi chọn ngày
function updateDayOfWeekDisplay() {
    const dateInput = document.getElementById('booking-date');
    const dayDisplay = document.getElementById('day-of-week-display');
    
    if (!dateInput || !dayDisplay) return;
    
    const date = dateInput.value;
    if (!date) return;
    
    const days = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
    const dayNumber = new Date(date).getDay(); // 0-6 (0 is Sunday)
    const dayOfWeekForServer = dayNumber === 0 ? 8 : dayNumber + 1; // Convert to 2-8 format
    
    dayDisplay.textContent = `${days[dayNumber]} (${dayOfWeekForServer})`;
    
    // Tải giá theo ngày
    BookingUtils.loadRatesByDay(dayOfWeekForServer, updateTimeSlotsWithRates);
}

// Cập nhật dropdown thời gian với giá
function updateTimeSlotsWithRates(rates) {
    const startTimeSelect = document.getElementById('start-time');
    const endTimeSelect = document.getElementById('end-time');
    
    BookingUtils.updateTimeSlotsWithRates(rates, startTimeSelect, endTimeSelect);
}

// Định dạng số tiền không có ký tự VNĐ
function formatCurrency(amount, includeVND = true) {
    return BookingUtils.formatCurrency(amount, includeVND);
}

// Tải tình trạng sân trống dựa trên ngày đã chọn
function loadCourtAvailability() {
    const bookingDateInput = document.getElementById('booking-date');
    if (!bookingDateInput) return;
    
    const date = bookingDateInput.value;
    const courtCards = document.querySelectorAll('.court-card');
    
    // Tính toán ngày trong tuần để sử dụng trong cả hàm
    const dayObj = new Date(date);
    const dayNames = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
    const dayIndex = dayObj.getDay();
    const dayOfWeekForServer = dayIndex === 0 ? 8 : dayIndex + 1;
    
    // Hiển thị thông tin ngày đã chọn
    const dayInfo = document.getElementById('day-of-week-display');
    if (dayInfo) {
        dayInfo.innerHTML = `${dayNames[dayIndex]} (${dayOfWeekForServer})`;
    }
    
    courtCards.forEach(court => {
        const courtId = court.dataset.courtId;
        const timelineContainer = court.querySelector('.time-slots');
        
        if (!timelineContainer) return;
        
        // Hiển thị trạng thái đang tải
        timelineContainer.innerHTML = '<div class="col-span-19 h-full flex justify-center items-center text-sm text-gray-500"><span class="animate-pulse">Đang tải...</span></div>';
        
        // Hàm fetchData với retry
        function fetchWithRetry(url, maxRetries = 3, delay = 1000, retryCount = 0) {
            return fetch(url)
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 500 && retryCount < maxRetries) {
                            return new Promise(resolve => setTimeout(resolve, delay))
                                .then(() => fetchWithRetry(url, maxRetries, delay * 1.5, retryCount + 1));
                        }
                        throw new Error(`Server responded with ${response.status}`);
                    }
                    return response.json();
                });
        }
        
        // Sử dụng hàm fetch với retry
        fetchWithRetry(`/booking/check-availability?date=${date}&court_id=${courtId}`)
            .then(data => {
                renderTimelineView(timelineContainer, data, date);
            })
            .catch(error => {
                console.error('Fetch error:', error);
                // Hiển thị lịch trống nếu không tải được
                timelineContainer.innerHTML = '<div class="col-span-19 h-full flex justify-center items-center text-sm text-red-500">Không thể tải lịch. Hiển thị sân trống.</div>';
                
                // Mặc định hiển thị sân trống
                setTimeout(() => {
                    // Tạo dữ liệu trống
                    const emptyData = {
                        single_bookings: [],
                        subscription_bookings: []
                    };
                    renderTimelineView(timelineContainer, emptyData, date);
                }, 2000);
            });
    });
    
    // Cập nhật tóm tắt sau khi tải dữ liệu mới
    validateTimeAndUpdateSummary();
}

// Hiển thị chế độ xem dòng thời gian dựa trên dữ liệu khả dụng
function renderTimelineView(container, data, date) {
    if (!container || !data) return;
    
    container.innerHTML = '';
    
    // Thử xác định xem ngày được chọn có phải là thứ 2 không
    const dateObj = new Date(date);
    const dayOfWeek = dateObj.getDay();
    const dayOfWeekForServer = dayOfWeek === 0 ? 8 : dayOfWeek + 1;
    
    // Court ID từ container
    const courtCardElement = container.closest('.court-card');
    const courtId = courtCardElement ? parseInt(courtCardElement.dataset.courtId) : null;
    
    // Kết hợp đặt sân đơn lẻ và thuê bao
    const bookings = [
        ...(data.single_bookings || []).map(booking => ({...booking, bookingType: 'single'})),
        ...(data.subscription_bookings || []).map(booking => ({...booking, bookingType: 'subscription'}))
    ];
    
    // Tạo 19 ô thời gian (5:00 đến 00:00)
    for (let hour = 5; hour <= 23; hour++) {
        const timeSlot = document.createElement('div');
        timeSlot.classList.add('time-slot');
        timeSlot.setAttribute('data-hour', hour);
        
        // Thêm sự kiện click cho time slot
        timeSlot.addEventListener('click', function() {
            if (!this.classList.contains('single-booking') && !this.classList.contains('subscription-booking')) {
                const courtCard = this.closest('.court-card');
                if (courtCard) {
                    const checkbox = courtCard.querySelector('.court-select');
                    if (checkbox) {
                        checkbox.checked = !checkbox.checked;
                        // Kích hoạt sự kiện change của checkbox
                        const event = new Event('change');
                        checkbox.dispatchEvent(event);
                    }
                }
            }
        });
        
        // Biến để theo dõi trạng thái slot
        let isBooked = false;
        let hasPartialBooking = false;
        let bookingStartsHalf = false;
        let bookingEndsHalf = false;
        let foundBooking = null;
        let slotType = 'available';
        
        // Tạo mảng giữ các phút trong khoảng giờ này (0-59)
        // Cần kiểm tra từng phút trong giờ để phát hiện chính xác sự chồng chéo
        const minuteStatus = Array(60).fill('available');
        
        // Kiểm tra ảnh hưởng của tất cả các booking lên slot hiện tại
        bookings.forEach(booking => {
            if (!booking) return;
            
            // Xử lý định dạng thời gian khác nhau giữa SingleBooking và SubscriptionBooking
            let bookingStart, bookingEnd;
            
            if (booking.bookingType === 'single') {
                // Đặt sân đơn lẻ
                bookingStart = new Date(booking.start_time);
                bookingEnd = new Date(booking.end_time);
            } else {
                // Đặt sân định kỳ
                bookingStart = new Date(`${date}T${booking.start_time}`);
                bookingEnd = new Date(`${date}T${booking.end_time}`);
                
                // Xử lý trường hợp end_time là 00:00
                if (booking.end_time === '00:00:00') {
                    bookingEnd = new Date(`${date}T00:00:00`);
                    bookingEnd.setDate(bookingEnd.getDate() + 1);
                }
            }
            
            // Lấy giờ và phút bắt đầu/kết thúc của booking
            const bookingStartHour = bookingStart.getHours();
            const bookingStartMin = bookingStart.getMinutes();
            const bookingEndHour = bookingEnd.getHours() === 0 ? 24 : bookingEnd.getHours();
            const bookingEndMin = bookingEnd.getMinutes();
            
            // Chuyển đổi thành phút để so sánh
            const bookingStartTotalMins = bookingStartHour * 60 + bookingStartMin;
            const bookingEndTotalMins = bookingEndHour * 60 + bookingEndMin;
            const slotStartTotalMins = hour * 60;
            const slotEndTotalMins = (hour + 1) * 60;
            
            // Kiểm tra xem booking có ảnh hưởng đến slot này không
            if (bookingEndTotalMins > slotStartTotalMins && bookingStartTotalMins < slotEndTotalMins) {
                // Đánh dấu từng phút trong khoảng được đặt
                for (let i = 0; i < 60; i++) {
                    const currentMinuteTotalMins = slotStartTotalMins + i;
                    if (currentMinuteTotalMins >= bookingStartTotalMins && currentMinuteTotalMins < bookingEndTotalMins) {
                        minuteStatus[i] = booking.bookingType;
                        
                        // Nếu có bất kỳ phút nào được đặt, đánh dấu slot là đã được đặt
                        isBooked = true;
                        foundBooking = booking;
                    }
                }
                
                // Kiểm tra nếu booking bắt đầu hoặc kết thúc ở nửa giờ trong slot
                if (bookingStartHour === hour && bookingStartMin === 30) {
                    bookingStartsHalf = true;
                }
                
                if (bookingEndHour === hour && bookingEndMin === 30) {
                    bookingEndsHalf = true;
                }
                
                hasPartialBooking = bookingStartsHalf || bookingEndsHalf;
            }
        });
        
        // Phân tích kết quả từ mảng phút để quyết định loại slot
        if (isBooked) {
            // Kiểm tra nửa đầu và nửa sau của slot
            const firstHalfType = minuteStatus[15]; // lấy điểm đại diện nửa đầu
            const secondHalfType = minuteStatus[45]; // lấy điểm đại diện nửa sau
            
            if (firstHalfType !== 'available' && secondHalfType !== 'available') {
                // Cả hai nửa đều được đặt
                if (firstHalfType === secondHalfType) {
                    // Cùng loại booking - hiển thị đầy đủ
                    slotType = firstHalfType;
                } else {
                    // Khác loại booking - hiển thị như partial với loại phù hợp
                    hasPartialBooking = true;
                    const halfwayStatus = minuteStatus[30];
                    
                    if (halfwayStatus === 'available') {
                        // Nếu điểm giữa không được đặt, đây có thể là hai booking riêng biệt
                        // Ưu tiên hiển thị loại booking ở nửa sau
                        slotType = secondHalfType;
                    } else {
                        // Điểm giữa được đặt, ưu tiên loại booking đó
                        slotType = halfwayStatus;
                    }
                }
            } else if (firstHalfType !== 'available') {
                // Chỉ nửa đầu được đặt
                slotType = firstHalfType;
                hasPartialBooking = true;
                bookingEndsHalf = true;
            } else if (secondHalfType !== 'available') {
                // Chỉ nửa sau được đặt
                slotType = secondHalfType;
                hasPartialBooking = true;
                bookingStartsHalf = true;
            }
        }
        
        // Thêm lớp phù hợp dựa trên trạng thái đặt lịch
        if (isBooked) {
            if (hasPartialBooking) {
                timeSlot.classList.add('partial');
                
                if (bookingStartsHalf) {
                    timeSlot.classList.add('starts-half');
                }
                
                if (bookingEndsHalf) {
                    timeSlot.classList.add('ends-half');
                }
            }
            
            // Áp dụng lớp dựa trên loại đặt lịch
            if (slotType === 'subscription') {
                timeSlot.classList.add('subscription-booking');
            } else {
                timeSlot.classList.add('single-booking');
            }
        } else {
            timeSlot.classList.add('available');
        }
        
        container.appendChild(timeSlot);
    }
    
    // Sau khi vẽ timeline, cập nhật thêm trạng thái selected
    updateSelectedTimeHighlight();
}

// Xác thực thời gian và cập nhật tổng thời gian và tổng sân
function validateTimeAndUpdateSummary() {
    const isValid = validateTime();
    updateSummary();
    updateSelectedTimeHighlight();
    updatePricePreview();
}

// Hàm cập nhật hiển thị giá dự kiến
function updatePricePreview() {
    const startTimeSelect = document.getElementById('start-time');
    const endTimeSelect = document.getElementById('end-time');
    const bookingDateInput = document.getElementById('booking-date');
    const pricePreview = document.getElementById('price-preview');
    const previewHours = document.getElementById('preview-hours');
    const previewPrice = document.getElementById('preview-price');
    
    if (!startTimeSelect || !endTimeSelect || !bookingDateInput || !pricePreview || !previewHours || !previewPrice) return;
    
    const startTime = startTimeSelect.value;
    const endTime = endTimeSelect.value;
    const bookingDate = bookingDateInput.value;
    
    // Kiểm tra thời gian hợp lệ
    if (!validateTime()) {
        pricePreview.classList.add('hidden');
        return;
    }
    
    // Hiển thị tổng thời gian
    const totalHours = calculateTotalHours();
    previewHours.textContent = `${totalHours.toFixed(1)} giờ`;
    
    // Lấy token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    // Hiển thị phần preview trong khi đang tải giá
    pricePreview.classList.remove('hidden');
    previewPrice.textContent = 'Đang tính...';
    
    // Gọi API để tính giá
    fetch('/booking/calculate-price', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            start_time: startTime,
            end_time: endTime,
            date: bookingDate
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        // Kiểm tra dữ liệu hợp lệ
        if (data && typeof data.total_price !== 'undefined') {
            // Cập nhật giá dự kiến
            const pricePerCourt = Math.abs(data.total_price);
            previewPrice.textContent = formatCurrency(pricePerCourt);
        }
    })
    .catch(error => {
        previewPrice.textContent = 'Không thể tính giá';
    });
}

// Cải thiện hàm validateTime
function validateTime() {
    const startTimeSelect = document.getElementById('start-time');
    const endTimeSelect = document.getElementById('end-time');
    const timeErrorDisplay = document.getElementById('time-error');
    
    if (!startTimeSelect || !endTimeSelect || !timeErrorDisplay) return false;
    
    const startTime = startTimeSelect.value;
    const endTime = endTimeSelect.value;
    
    // Kiểm tra thời gian trong quá khứ
    const bookingDateInput = document.getElementById('booking-date');
    if (bookingDateInput) {
        const bookingDate = bookingDateInput.value;
        const now = new Date();
        const startDateTime = new Date(`${bookingDate}T${startTime}`);
        
        if (bookingDate === now.toISOString().split('T')[0] && startDateTime < now) {
            timeErrorDisplay.textContent = 'Không thể đặt sân cho thời gian đã qua';
            return false;
        }
    }
    
    // Sử dụng hàm chung để kiểm tra thời gian hợp lệ
    const isValid = BookingUtils.validateTimeRange(startTime, endTime, 59.5);
    
    if (!isValid) {
        // Xác định lỗi cụ thể để hiển thị thông báo phù hợp
        const [startHour, startMinute] = startTime.split(':').map(Number);
        const [endHour, endMinute] = endTime.split(':').map(Number);
        
        let startMinutes = startHour * 60 + startMinute;
        let endMinutes = endHour * 60 + endMinute;
        
        // Xử lý trường hợp qua đêm
        if (endHour === 0 && endMinute === 0) {
            endMinutes = 24 * 60; // 00:00 nghĩa là cuối ngày (24:00)
        }
        
        if (endMinutes <= startMinutes && !(endHour === 0 && endMinute === 0)) {
            timeErrorDisplay.textContent = 'Giờ kết thúc phải sau giờ bắt đầu';
        } else {
            timeErrorDisplay.textContent = 'Thời gian đặt sân phải ít nhất 1 tiếng';
        }
        return false;
    }
    
    timeErrorDisplay.textContent = '';
    return true;
}

// Cập nhật tổng thời gian và tổng sân
function updateSummary() {
    const totalHoursDisplay = document.getElementById('total-hours');
    const totalCourtsDisplay = document.getElementById('total-courts');
    const totalPriceDisplay = document.getElementById('total-price');
    const paymentAmountDisplay = document.getElementById('payment-amount');
    const originalPriceDisplay = document.getElementById('original-price-display');
    const discountDisplay = document.getElementById('discount-display');
    const courtCheckboxes = document.querySelectorAll('.court-select');
    const bookingDateInput = document.getElementById('booking-date');
    const startTimeSelect = document.getElementById('start-time');
    const endTimeSelect = document.getElementById('end-time');
    const promotionSelect = document.getElementById('promotion-select');
    
    if (!courtCheckboxes || !courtCheckboxes.length) return;
    
    // Đếm số sân đã chọn và hiển thị tên sân
    const selectedCourtsCount = Array.from(courtCheckboxes).filter(checkbox => checkbox.checked).length;
    updateSelectedCourtsDisplay();
    
    // Tính tổng số giờ
    const totalHours = calculateTotalHours();
    
    if (totalHoursDisplay) {
        totalHoursDisplay.textContent = `${totalHours.toFixed(1)} giờ`;
    }
    
    // Thực hiện xác thực nhưng không xóa giá khi không thành công trong quá trình cập nhật thông thường
    const isTimeValid = validateTime();
    
    // Nếu không có sân nào được chọn, xóa giá và trả về
    if (selectedCourtsCount === 0) {
        if (totalPriceDisplay) totalPriceDisplay.textContent = '0';
        if (paymentAmountDisplay) paymentAmountDisplay.textContent = '0';
        if (originalPriceDisplay) originalPriceDisplay.classList.add('hidden');
        if (discountDisplay) discountDisplay.classList.add('hidden');
        return;
    }
    
    // Lấy token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    if (startTimeSelect && endTimeSelect && bookingDateInput) {
        // Gọi API để tính giá
        fetch('/booking/calculate-price', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                start_time: startTimeSelect.value,
                end_time: endTimeSelect.value,
                date: bookingDateInput.value
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Kiểm tra dữ liệu hợp lệ
            if (data && typeof data.total_price !== 'undefined') {
                // Get the promotion discount if available
                const promotionId = promotionSelect ? promotionSelect.value : '';
                const promotionNameDisplay = document.getElementById('promotion-name');
                let discountPercent = 0;
                
                // If a promotion is selected, get its discount percentage
                if (promotionId) {
                    // Get the promotion name and discount percentage
                    fetch('/booking/single/promotions', {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(promotionData => {
                        if (promotionData.success && promotionData.promotions) {
                            const selectedPromotion = promotionData.promotions.find(p => p.id == promotionId);
                            if (selectedPromotion) {
                                discountPercent = selectedPromotion.discount_percent;
                                
                                // Cập nhật tổng giá (giá mỗi sân * số sân)
                                const pricePerCourt = Math.abs(data.total_price);
                                const originalPrice = pricePerCourt * selectedCourtsCount;
                                
                                // Apply discount
                                const discountAmount = originalPrice * (discountPercent / 100);
                                const finalPrice = originalPrice - discountAmount;
                                
                                // Update price displays
                                if (totalPriceDisplay) {
                                    totalPriceDisplay.textContent = formatCurrency(originalPrice);
                                }
                                
                                // Show original price and discount
                                if (originalPriceDisplay && discountDisplay) {
                                    originalPriceDisplay.classList.remove('hidden');
                                    discountDisplay.classList.remove('hidden');
                                    discountDisplay.textContent = `Giảm giá: ${formatCurrency(discountAmount)} (${discountPercent}%)`;
                                }
                                
                                // Tính số tiền thanh toán dựa trên loại thanh toán
                                let paymentAmount = finalPrice;
                                if (paymentType === 'deposit') {
                                    paymentAmount = finalPrice * 0.5;
                                }
                                
                                if (paymentAmountDisplay) {
                                    paymentAmountDisplay.textContent = formatCurrency(paymentAmount);
                                }
                                
                                // Kiểm tra số dư ví sau khi cập nhật tổng tiền
                                checkWalletBalance();
                            } else {
                                // No promotion found with the selected ID
                                updateDisplayWithoutDiscount(data.total_price, selectedCourtsCount);
                            }
                        } else {
                            // Failed to get promotions data
                            updateDisplayWithoutDiscount(data.total_price, selectedCourtsCount);
                        }
                    })
                    .catch(error => {
                        console.error('Error loading promotion details:', error);
                        updateDisplayWithoutDiscount(data.total_price, selectedCourtsCount);
                    });
                } else {
                    // No promotion selected
                    updateDisplayWithoutDiscount(data.total_price, selectedCourtsCount);
                }
            }
        })
        .catch(error => {
            if (totalPriceDisplay) {
                totalPriceDisplay.textContent = 'Không thể tính giá';
            }
            if (paymentAmountDisplay) {
                paymentAmountDisplay.textContent = 'Không thể tính giá';
            }
            if (originalPriceDisplay) originalPriceDisplay.classList.add('hidden');
            if (discountDisplay) discountDisplay.classList.add('hidden');
        });
    }
}

// Helper function to update price display without discount
function updateDisplayWithoutDiscount(pricePerCourt, selectedCourtsCount) {
    const totalPriceDisplay = document.getElementById('total-price');
    const paymentAmountDisplay = document.getElementById('payment-amount');
    const originalPriceDisplay = document.getElementById('original-price-display');
    const discountDisplay = document.getElementById('discount-display');
    
    // Calculate total price
    const totalPrice = Math.abs(pricePerCourt) * selectedCourtsCount;
    
    // Update displays
    if (totalPriceDisplay) {
        totalPriceDisplay.textContent = formatCurrency(totalPrice);
    }
    
    // Hide discount info
    if (originalPriceDisplay) originalPriceDisplay.classList.add('hidden');
    if (discountDisplay) discountDisplay.classList.add('hidden');
    
    // Tính số tiền thanh toán dựa trên loại thanh toán
    let paymentAmount = totalPrice;
    if (paymentType === 'deposit') {
        paymentAmount = totalPrice * 0.5;
    }
    
    if (paymentAmountDisplay) {
        paymentAmountDisplay.textContent = formatCurrency(paymentAmount);
    }
    
    // Kiểm tra số dư ví sau khi cập nhật tổng tiền
    checkWalletBalance();
}

// Cập nhật highlight cho khoảng thời gian đã chọn
function updateSelectedTimeHighlight() {
    // Xóa tất cả highlight trước đó
    document.querySelectorAll('.time-slot.selected, .time-slot.selected-start-half, .time-slot.selected-end-half').forEach(el => {
        if (el) {
            el.classList.remove('selected');
            el.classList.remove('selected-start-half');
            el.classList.remove('selected-end-half');
            el.innerHTML = '';
        }
    });
    
    const startTimeSelect = document.getElementById('start-time');
    const endTimeSelect = document.getElementById('end-time');
    
    if (!startTimeSelect || !endTimeSelect) return;
    
    const startTime = startTimeSelect.value;
    const endTime = endTimeSelect.value;
    
    if (!startTime || !endTime) return;
    
    // Chuyển đổi thời gian thành chỉ số
    let [startHour, startMinute] = startTime.split(':').map(Number);
    let [endHour, endMinute] = endTime.split(':').map(Number);
    
    // Xử lý trường hợp 00:00
    if (endHour === 0 && endMinute === 0) {
        endHour = 24;
        endMinute = 0;
    }
    
    // Tính toán chỉ số slot để highlight dựa trên mốc giờ đầy đủ
    const startFullHourIndex = startHour - 5; // Chỉ số slot cho giờ chứa thời gian bắt đầu
    let endFullHourIndex = endHour - 5;     // Chỉ số slot cho giờ chứa thời gian kết thúc
    
    if (endHour === 24) endFullHourIndex = 19; // 00:00 là slot thứ 19 (5->23 và 00)
    
    // Lấy tất cả court timeline
    document.querySelectorAll('.court-timeline').forEach(timeline => {
        if (!timeline) return;
        
        const slots = timeline.querySelectorAll('.time-slot');
        if (!slots || !slots.length) return;
        
        // Highlight các slot giữa các mốc bắt đầu và kết thúc (các ô hoàn chỉnh)
        for (let i = startFullHourIndex; i < endFullHourIndex; i++) {
            if (i >= 0 && i < slots.length && slots[i] && 
                !slots[i].classList.contains('single-booking') && 
                !slots[i].classList.contains('subscription-booking')) {
                // Xử lý các trường hợp đặc biệt cho nửa giờ
                if (i === startFullHourIndex && startMinute === 30) {
                    // Nếu bắt đầu từ 30 phút, chỉ highlight nửa sau của ô
                    slots[i].classList.add('selected-start-half');
                } else {
                    // Ô giữa luôn được highlight toàn bộ
                    slots[i].classList.add('selected');
                }
            }
        }
        
        // Xử lý riêng ô kết thúc
        if (endFullHourIndex >= 0 && endFullHourIndex < slots.length) {
            if (endMinute === 30 && slots[endFullHourIndex] && 
                !slots[endFullHourIndex].classList.contains('single-booking') && 
                !slots[endFullHourIndex].classList.contains('subscription-booking')) {
                // Nếu kết thúc tại 30 phút, highlight nửa đầu của ô kết thúc
                slots[endFullHourIndex].classList.add('selected-end-half');
            } else if (endMinute === 0 && endHour === 24) {
                // Trường hợp đặc biệt: kết thúc 00:00
                if (endFullHourIndex-1 >= 0 && slots[endFullHourIndex-1] && 
                    !slots[endFullHourIndex-1].classList.contains('single-booking') && 
                    !slots[endFullHourIndex-1].classList.contains('subscription-booking')) {
                    slots[endFullHourIndex-1].classList.add('selected');
                }
            }
        }
    });
}

// Tính tổng thời gian đặt sân
function calculateTotalHours() {
    const startTimeSelect = document.getElementById('start-time');
    const endTimeSelect = document.getElementById('end-time');
    
    if (!startTimeSelect || !endTimeSelect) return 0;
    
    const startTime = startTimeSelect.value;
    const endTime = endTimeSelect.value;
    
    if (!startTime || !endTime) return 0;
    
    // Chuyển đổi thời gian thành phút sử dụng hàm chung
    const startTotalMinutes = BookingUtils.convertTimeToMinutes(startTime);
    let endTotalMinutes = BookingUtils.convertTimeToMinutes(endTime);
    
    // Xử lý trường hợp 00:00
    if (endTime === '00:00') {
        endTotalMinutes = 24 * 60; // 00:00 nghĩa là cuối ngày (24:00)
    } else if (endTotalMinutes < startTotalMinutes) {
        endTotalMinutes += 24 * 60; // Thêm 24 giờ cho các đặt sân qua đêm
    }
    
    // Tính giờ dưới dạng số thập phân
    const hours = (endTotalMinutes - startTotalMinutes) / 60;
    
    // Đảm bảo không bao giờ trả về ít hơn 1.0 cho các lựa chọn hợp lệ
    if (hours >= 0.99 && hours < 1.0) {
        return 1.0;
    }
    
    return hours;
}

// Cập nhật hiển thị sân đã chọn
function updateSelectedCourtsDisplay() {
    const totalCourtsDisplay = document.getElementById('total-courts');
    if (!totalCourtsDisplay) return;

    const courtNames = [];
    document.querySelectorAll('.court-select:checked').forEach(checkbox => {
        const courtCard = checkbox.closest('.court-card');
        // Get the court information from the image alt text which contains the actual name
        const courtImage = courtCard ? courtCard.querySelector('img') : null;
        const courtName = courtImage ? courtImage.alt.replace('Sân ', '') : `${checkbox.value}`;
        courtNames.push(courtName);
    });

    totalCourtsDisplay.textContent = courtNames.length > 0 ? courtNames.join(', ') : 'Chưa chọn';
}

// Hàm kiểm tra số dư ví
function checkWalletBalance() {
    const walletRadio = document.getElementById('payment_method_wallet');
    const walletWarning = document.getElementById('wallet-warning');
    const walletBalance = parseFloat(document.getElementById('user-wallet-balance')?.value || 0);
    const paymentAmountDisplay = document.getElementById('payment-amount');
    const submitButton = document.getElementById('submit-booking');
    
    return BookingUtils.checkWalletBalance(walletRadio, walletWarning, paymentAmountDisplay, walletBalance, submitButton);
}

// Xử lý chọn chương trình khuyến mãi tốt nhất
function loadAndApplyBestPromotion() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    // Get all valid promotions from the API
    fetch('/booking/single/promotions', {
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.promotions && data.promotions.length > 0) {
            // Find the promotion with the highest discount percentage
            const bestPromotion = data.promotions.reduce((best, current) => {
                return current.discount_percent > best.discount_percent ? current : best;
            }, data.promotions[0]);
            
            // Apply the best promotion
            const promotionSelect = document.getElementById('promotion-select');
            if (promotionSelect) {
                promotionSelect.value = bestPromotion.id;
                
                // Update the promotion name display
                const promotionNameDisplay = document.getElementById('promotion-name');
                if (promotionNameDisplay) {
                    promotionNameDisplay.textContent = `${bestPromotion.name} (Giảm ${bestPromotion.discount_percent}%)`;
                }
                
                // Update the price summary with the discount
                updateSummary();
            }
        } else {
            // No valid promotions found
            const promotionNameDisplay = document.getElementById('promotion-name');
            if (promotionNameDisplay) {
                promotionNameDisplay.textContent = 'Không có';
            }
        }
    })
    .catch(error => {
        console.error('Error loading promotions:', error);
        // Handle error case
        const promotionNameDisplay = document.getElementById('promotion-name');
        if (promotionNameDisplay) {
            promotionNameDisplay.textContent = 'Không có';
        }
    });
} 