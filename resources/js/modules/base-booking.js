// Định dạng số tiền không có ký tự VNĐ
function formatCurrency(amount, includeVND = true) {
    if (amount === undefined || amount === null) {
        return '0' + (includeVND ? ' Xu' : '');
    }
    
    // Đảm bảo số tiền luôn là số dương để hiển thị
    amount = Math.abs(amount);
    return includeVND = new Intl.NumberFormat('vi-VN').format(amount);
}

// Convert time string (HH:MM) to minutes since midnight
function convertTimeToMinutes(timeString) {
    const [hours, minutes] = timeString.split(':').map(Number);
    return hours * 60 + minutes;
}

// Tải giá thuê sân theo ngày trong tuần
function loadRatesByDay(dayOfWeek, callback) {
    if (!dayOfWeek) return;

    fetch(`/booking/get-rates-by-day/${dayOfWeek}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data && data.rates && typeof callback === 'function') {
                callback(data.rates);
            }
        })
        .catch(error => {
            console.error('Error loading rates:', error);
        });
}

// Cập nhật dropdown thời gian với giá
function updateTimeSlotsWithRates(rates, startTimeSelect, endTimeSelect) {
    if (!startTimeSelect || !endTimeSelect) return;
    
    // Lưu lại các giá trị đang chọn
    const currentStartTime = startTimeSelect.value;
    const currentEndTime = endTimeSelect.value;
    
    // Tạo đối tượng giữ giá cho từng khung giờ
    const ratesByTime = {};
    rates.forEach(rate => {
        const startTime = rate.start_time;
        const endTime = rate.end_time;
        const price = rate.price_per_hour;
        
        // Khởi tạo các ô 30 phút từ thời gian bắt đầu đến kết thúc
        let time = new Date(`2000-01-01T${startTime}:00`);
        const endTimeDate = new Date(`2000-01-01T${endTime}:00`);
        
        while (time < endTimeDate) {
            const timeStr = time.getHours().toString().padStart(2, '0') + ':' + 
                           time.getMinutes().toString().padStart(2, '0');
            ratesByTime[timeStr] = price;
            time.setMinutes(time.getMinutes() + 30);
        }
    });
    
    // Cập nhật các option trong dropdown thời gian bắt đầu
    Array.from(startTimeSelect.options).forEach(option => {
        const timeValue = option.value;
        const price = ratesByTime[timeValue];
        
        if (price) {
            // Sử dụng khoảng trắng em space để tạo khoảng cách đều
            const space = '\u2003\u2003\u2003\u2003'; // 4 ký tự em space
            option.textContent = `${timeValue}${space}${formatCurrency(price, false)}`;
        } else {
            option.textContent = timeValue;
        }
    });
    
    // Cập nhật các option trong dropdown thời gian kết thúc
    Array.from(endTimeSelect.options).forEach(option => {
        const timeValue = option.value;
        // Với giờ kết thúc, chúng ta hiển thị giá của khoảng 30 phút trước đó
        // Trừ trường hợp 00:00 là cuối ngày
        if (timeValue === '00:00') {
            // Giá của 23:30
            const price = ratesByTime['23:30'];
            if (price) {
                const space = '\u2003\u2003\u2003\u2003'; // 4 ký tự em space
                option.textContent = `${timeValue}${space}${formatCurrency(price, false)}`;
            } else {
                option.textContent = timeValue;
            }
        } else {
            // Tính toán thời gian 30 phút trước
            let time = new Date(`2000-01-01T${timeValue}:00`);
            time.setMinutes(time.getMinutes() - 30);
            const prevTimeStr = time.getHours().toString().padStart(2, '0') + ':' + 
                               time.getMinutes().toString().padStart(2, '0');
            
            const price = ratesByTime[prevTimeStr];
            if (price) {
                const space = '\u2003\u2003\u2003\u2003'; // 4 ký tự em space
                option.textContent = `${timeValue}${space}${formatCurrency(price, false)}`;
            } else {
                option.textContent = timeValue;
            }
        }
    });
    
    // Khôi phục giá trị đã chọn
    startTimeSelect.value = currentStartTime;
    endTimeSelect.value = currentEndTime;
}

// Kiểm tra số dư ví
function checkWalletBalance(walletRadio, walletWarning, paymentAmountDisplay, walletBalance, submitButton) {
    // Chỉ kiểm tra khi phương thức thanh toán là ví cá nhân
    if (!walletRadio || !walletRadio.checked || !walletWarning || !paymentAmountDisplay) {
        if (walletWarning) walletWarning.classList.add('hidden');
        if (submitButton) submitButton.disabled = false;
        return true;
    }
    
    // Lấy số tiền cần thanh toán (loại bỏ ký tự không phải số và dấu phẩy)
    const paymentText = paymentAmountDisplay.textContent;
    // Lấy tất cả các chữ số, loại bỏ dấu phẩy và các ký tự khác
    const paymentAmount = parseInt(paymentText.replace(/[^\d]/g, ''), 10);
    
    console.log("Số dư ví:", walletBalance);
    console.log("Số tiền cần thanh toán:", paymentAmount);
    
    // Kiểm tra xem số dư có đủ không
    if (paymentAmount > walletBalance) {
        walletWarning.textContent = `Số dư ví không đủ, cần thêm ${formatCurrency(paymentAmount - walletBalance)}`;
        walletWarning.classList.remove('hidden');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
        return false;
    } else {
        walletWarning.classList.add('hidden');
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
        }
        return true;
    }
}

// Basic time validation for both single and subscription bookings
function validateTimeRange(startTime, endTime, minimum = 60) {
    if (!startTime || !endTime) return false;
    
    // Chuyển đổi thành giá trị có thể so sánh (phút tính từ nửa đêm)
    const [startHour, startMinute] = startTime.split(':').map(Number);
    const [endHour, endMinute] = endTime.split(':').map(Number);
    
    let startMinutes = startHour * 60 + startMinute;
    let endMinutes = endHour * 60 + endMinute;
    
    // Xử lý trường hợp qua đêm
    if (endHour === 0 && endMinute === 0) {
        endMinutes = 24 * 60; // 00:00 nghĩa là cuối ngày (24:00)
    }
    
    // Kiểm tra giờ kết thúc phải sau giờ bắt đầu
    if (endMinutes <= startMinutes && !(endHour === 0 && endMinute === 0)) {
        return false;
    }
    
    // Kiểm tra thời gian tối thiểu
    const diffMinutes = endMinutes - startMinutes;
    return diffMinutes >= minimum;
}

// Xử lý hiệu ứng hover và chọn sân
function initializeCourtSelection() {
    // Thêm lớp CSS vào head cho hover và active states
    const style = document.createElement('style');
    style.innerHTML = `
        /* Styles for single booking page */
        .court-card {
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .court-card:hover {
            border: 2px solid #3b82f6;
            border-radius: 0.375rem;
        }
        .court-card.selected {
            border-radius: 0.375rem;
            background-color: #e0e7ff;
        }
        
        /* Styles for subscription booking page */
        .court-select-container {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid transparent;
            border-radius: 0.375rem;
        }
        .court-select-container:hover {
            border: 2px solid #3b82f6;
        }
        .court-select-container.selected {
            border: 2px solid #3b82f6;
            background-color: #e0e7ff;
        }
    `;
    document.head.appendChild(style);
    
    // Xử lý cho trang single-booking (court-card)
    const courtCards = document.querySelectorAll('.court-card');
    courtCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Ngăn sự kiện click lan tỏa để tránh ảnh hưởng đến việc chọn khung giờ
            if (e.target.closest('.time-slots')) {
                return;
            }
            
            // Tìm checkbox trong court card
            const checkbox = card.querySelector('input.court-select');
            if (checkbox) {
                // Đảo trạng thái checkbox
                checkbox.checked = !checkbox.checked;
                
                // Cập nhật trạng thái selected cho card
                if (checkbox.checked) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
                
                // Kích hoạt sự kiện change trên checkbox để các handler khác có thể phản ứng
                checkbox.dispatchEvent(new Event('change'));
            }
        });
        
        // Đồng bộ trạng thái selected dựa trên giá trị checkbox khi tải trang
        const checkbox = card.querySelector('input.court-select');
        if (checkbox && checkbox.checked) {
            card.classList.add('selected');
        }
        
        // Thêm sự kiện thay đổi cho checkbox để cập nhật trạng thái selected
        if (checkbox) {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            });
        }
    });
    
    // Xử lý cho trang subscription-booking (court-select-container)
    const courtContainers = document.querySelectorAll('.court-select-container');
    courtContainers.forEach(container => {
        container.addEventListener('click', function(e) {
            // Ngăn click trực tiếp vào checkbox để xử lý bằng logic của chúng ta
            if (e.target.type === 'checkbox') {
                e.preventDefault();
            }
            
            // Tìm checkbox trong container
            const checkbox = container.querySelector('input.court-select');
            if (checkbox) {
                // Đảo trạng thái checkbox
                checkbox.checked = !checkbox.checked;
                
                // Cập nhật trạng thái selected cho container
                if (checkbox.checked) {
                    container.classList.add('selected');
                } else {
                    container.classList.remove('selected');
                }
                
                // Kích hoạt sự kiện change trên checkbox để các handler khác có thể phản ứng
                checkbox.dispatchEvent(new Event('change'));
                
                // Ngăn sự kiện lan tỏa
                e.stopPropagation();
            }
        });
        
        // Đồng bộ trạng thái selected dựa trên giá trị checkbox khi tải trang
        const checkbox = container.querySelector('input.court-select');
        if (checkbox && checkbox.checked) {
            container.classList.add('selected');
        }
        
        // Thêm sự kiện thay đổi cho checkbox để cập nhật trạng thái selected
        if (checkbox) {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    container.classList.add('selected');
                } else {
                    container.classList.remove('selected');
                }
            });
        }
    });
}

// Hàm khởi tạo khi DOM đã tải xong
document.addEventListener('DOMContentLoaded', function() {
    initializeCourtSelection();
});

// Export common functions
window.BookingUtils = {
    formatCurrency,
    convertTimeToMinutes,
    loadRatesByDay,
    updateTimeSlotsWithRates,
    checkWalletBalance,
    validateTimeRange
};
