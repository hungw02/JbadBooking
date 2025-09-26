document.addEventListener('DOMContentLoaded', function () {
    const bookingForm = document.getElementById('subscriptionBookingForm');
    if (!bookingForm) return;

    const courtSelects = document.querySelectorAll('.court-select');
    const dayOfWeekSelect = document.getElementById('day-of-week');
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');
    const startTimeSelect = document.getElementById('start-time');
    const endTimeSelect = document.getElementById('end-time');
    const sessionCountDisplay = document.getElementById('session-count');
    const totalPriceDisplay = document.getElementById('totalPriceDisplay');
    const paymentAmountDisplay = document.getElementById('payment-amount');
    const paymentTypeRadios = document.querySelectorAll('input[name="payment_type"]');
    const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
    const timeErrorDisplay = document.getElementById('time-error');
    const courtErrorDisplay = document.getElementById('court-error');
    const dayDisplay = document.getElementById('dayDisplay');
    const timeDisplay = document.getElementById('timeDisplay');
    const sessionsDisplay = document.getElementById('sessionsDisplay');
    const pricePerSessionDisplay = document.getElementById('pricePerSessionDisplay');
    const courtChangeModal = document.getElementById('courtChangeModal');
    const confirmCourtChange = document.getElementById('confirmCourtChange');
    const courtsDisplay = document.getElementById('courtsDisplay');
    const pricePreview = document.getElementById('price-preview');
    const previewHours = document.getElementById('preview-hours');
    const previewPrice = document.getElementById('preview-price');
    const walletBalanceInput = document.getElementById('user-wallet-balance');
    const walletBalance = walletBalanceInput ? parseFloat(walletBalanceInput.value) || 0 : 0;

    // Set default time values
    const defaultStartTime = '05:00';
    const defaultEndTime = '06:00';

    // Set default values if not already set
    if (!startTimeSelect.value) {
        startTimeSelect.value = defaultStartTime;
    }
    if (!endTimeSelect.value) {
        endTimeSelect.value = defaultEndTime;
    }

    // Event listeners
    startDateInput.addEventListener('change', () => {
        validateDateRange();
        updateBookingSummary();
    });

    endDateInput.addEventListener('change', () => {
        validateDateRange();
        updateBookingSummary();
    });

    dayOfWeekSelect.addEventListener('change', () => {
        updateBookingSummary();

        // Load rates by day of week
        BookingUtils.loadRatesByDay(dayOfWeekSelect.value, updateTimeSlotsWithRates);
    });

    startTimeSelect.addEventListener('change', () => {
        validateTimeAndUpdateSummary();
    });

    endTimeSelect.addEventListener('change', () => {
        validateTimeAndUpdateSummary();
    });

    // Add event listeners for court selection
    document.querySelectorAll('input[name="court_ids[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            validateCourts();
            updateBookingSummary();
            checkWalletBalance();
        });
    });

    // Add event listeners for payment type selection
    paymentTypeRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            // Update price calculation completely instead of just the payment amount
            updatePrice(getSessionCount());
            updatePricePreview();
            checkWalletBalance();
        });
    });

    // Add event listeners for payment method selection
    paymentMethodRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            // Update the price calculation when payment method changes to ensure displays are updated properly
            updatePrice(getSessionCount());
            checkWalletBalance();
        });
    });

    // Handle court change confirmation
    confirmCourtChange?.addEventListener('click', function () {
        if (courtChangeModal) courtChangeModal.classList.add('hidden');
        // Update the form with the new court selections
        updateCourtSelections();
    });

    bookingForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        let isValid = true;

        // Validate required fields
        if (!dayOfWeekSelect.value) {
            alert('Vui lòng chọn ngày trong tuần');
            isValid = false;
        }

        if (!validateCourts()) {
            isValid = false;
        }

        if (!validateSessionCount()) {
            isValid = false;
        }

        if (!validateTime()) {
            isValid = false;
        }

        if (!checkWalletBalance()) {
            isValid = false;
        }

        if (!isValid) {
            return;
        }

        // Submit the form if validation passes
        this.submit();
    });

    // Validate date range
    function validateDateRange() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        const minEndDate = new Date(startDate);
        minEndDate.setDate(startDate.getDate() + 27); // Minimum 28 days (4 weeks)

        if (endDate < minEndDate) {
            endDateInput.value = minEndDate.toISOString().split('T')[0];
        }
    }

    // Update booking summary information
    function updateBookingSummary() {
        // Update day display
        const dayOfWeek = dayOfWeekSelect.value;
        const dayMapping = {
            '2': 'Thứ 2',
            '3': 'Thứ 3',
            '4': 'Thứ 4',
            '5': 'Thứ 5',
            '6': 'Thứ 6',
            '7': 'Thứ 7',
            '8': 'Chủ nhật'
        };
        if (dayDisplay) {
            dayDisplay.textContent = dayOfWeek ? dayMapping[dayOfWeek] : 'Chưa chọn';
        }

        // Update time display using default values
        const startTime = startTimeSelect.value || defaultStartTime;
        const endTime = endTimeSelect.value || defaultEndTime;
        if (timeDisplay) {
            timeDisplay.textContent = `${startTime} - ${endTime}`;
        }

        // Update selected courts display
        const selectedCourts = Array.from(document.querySelectorAll('input[name="court_ids[]"]:checked'))
            .map(checkbox => {
                const courtCard = checkbox.closest('.court-select-container');
                return courtCard ? courtCard.querySelector('.font-semibold').textContent : checkbox.value;
            });
        if (courtsDisplay) {
            courtsDisplay.textContent = selectedCourts.length > 0 ? selectedCourts.join(', ') : 'Chưa chọn';
        }

        // Update session count if we have the required fields
        if (startDateInput.value && endDateInput.value && dayOfWeek) {
            const sessionCount = getSessionCount();
            if (sessionCountDisplay) {
                sessionCountDisplay.textContent = sessionCount;
            }
            if (sessionsDisplay) {
                sessionsDisplay.textContent = `${sessionCount} buổi`;
            }

            // Calculate and update price per session
            const pricePerSession = calculatePricePerSession();
            if (pricePerSessionDisplay) {
                pricePerSessionDisplay.textContent = formatCurrency(pricePerSession);
            }

            // Update total price
            updatePrice(sessionCount);
        } else {
            if (sessionCountDisplay) {
                sessionCountDisplay.textContent = '0';
            }
            if (sessionsDisplay) {
                sessionsDisplay.textContent = '0 buổi';
            }
            if (pricePerSessionDisplay) {
                pricePerSessionDisplay.textContent = formatCurrency(0);
            }
            if (totalPriceDisplay) {
                totalPriceDisplay.textContent = formatCurrency(0);
            }
            if (paymentAmountDisplay) {
                paymentAmountDisplay.textContent = formatCurrency(0);
            }
        }

        // Update price preview
        updatePricePreview();
    }

    // Định dạng số tiền không có ký tự VNĐ
    function formatCurrency(amount, includeVND = true) {
        return BookingUtils.formatCurrency(amount, includeVND);
    }

    // Hàm cập nhật hiển thị giá dự kiến
    function updatePricePreview() {
        if (!pricePreview || !previewHours || !previewPrice) return;

        // Always show the preview section, even if time is invalid
        // This allows users to see the discount percentage early
        pricePreview.classList.remove('hidden');

        // Kiểm tra thời gian hợp lệ - nhưng không ẩn preview nếu invalid
        const timeValid = validateTime(true);

        // Tính giờ dưới dạng số thập phân
        const startMinutes = BookingUtils.convertTimeToMinutes(startTimeSelect.value);
        let endMinutes = BookingUtils.convertTimeToMinutes(endTimeSelect.value);

        // Adjust for midnight (00:00)
        if (endMinutes === 0) {
            endMinutes = 24 * 60;
        }

        const hours = (endMinutes - startMinutes) / 60;

        // Hiển thị tổng thời gian nếu thời gian hợp lệ
        previewHours.textContent = timeValid ? `${hours.toFixed(1)} giờ` : "Chọn thời gian hợp lệ";

        // Tính giá mỗi buổi
        const pricePerSession = calculatePricePerSession();
        previewPrice.textContent = formatCurrency(pricePerSession, false);

        // Lấy các thông tin cần thiết để tính chi phí dự kiến
        const sessionCount = getSessionCount();
        const selectedCourtsCount = document.querySelectorAll('input[name="court_ids[]"]:checked').length || 1; // Mặc định là 1 sân nếu chưa chọn

        // Tính các chi phí liên quan
        const subtotal = timeValid ? (pricePerSession * sessionCount * selectedCourtsCount) : 0;
        let discountAmount = 0;
        let discountPercent = 0;
        let total = subtotal;
        
        // Get promotion ID if available
        const promotionId = document.getElementById('promotion-select')?.value;
        
        // Get promotions from server if promotion ID is set
        if (promotionId) {
            fetch('/booking/subscription/promotions', {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.promotions) {
                    const selectedPromotion = data.promotions.find(p => p.id == promotionId);
                    if (selectedPromotion) {
                        // Calculate promotion discount
                        discountAmount = subtotal * (selectedPromotion.discount_percent / 100);
                        discountPercent = selectedPromotion.discount_percent;
                        total = subtotal - discountAmount;
                        
                        // Update preview with promotion discount
                        updatePreviewDisplay(subtotal, discountAmount, discountPercent, total);
                    } else {
                        // No matching promotion found
                        updatePreviewDisplay(subtotal, 0, 0, subtotal);
                    }
                } else {
                    // No promotions available
                    updatePreviewDisplay(subtotal, 0, 0, subtotal);
                }
            })
            .catch(error => {
                console.error('Error loading promotion details:', error);
                // Fall back to no discount
                updatePreviewDisplay(subtotal, 0, 0, subtotal);
            });
        } else {
            // No promotion selected, no discount
            updatePreviewDisplay(subtotal, 0, 0, subtotal);
        }
    }
    
    // Helper function to update the preview display
    function updatePreviewDisplay(subtotal, discountAmount, discountPercent, total) {
        // Hiển thị thông tin chi tiết về cách tính chi phí
        const previewSessionPrice = document.getElementById('preview-session-price');
        const previewSessionCount = document.getElementById('preview-session-count');
        const previewCourtCount = document.getElementById('preview-court-count');
        const previewSubtotal = document.getElementById('preview-subtotal');
        const previewDiscount = document.getElementById('preview-discount');
        const previewTotal = document.getElementById('preview-total');
        const previewPaymentPercent = document.getElementById('preview-payment-percent');
        const previewPayment = document.getElementById('preview-payment');
        
        if (previewSessionPrice) previewSessionPrice.textContent = formatCurrency(calculatePricePerSession(), false);
        if (previewSessionCount) previewSessionCount.textContent = getSessionCount();
        if (previewCourtCount) previewCourtCount.textContent = document.querySelectorAll('input[name="court_ids[]"]:checked').length || 1;
        if (previewSubtotal) previewSubtotal.textContent = formatCurrency(subtotal, false);
        
        // Show discount percentage even when amount is 0 (no courts selected yet)
        if (previewDiscount) {
            if (discountPercent > 0) {
                previewDiscount.textContent = subtotal > 0 ? 
                    `${formatCurrency(discountAmount, false)} (${discountPercent}%)` : 
                    `Giảm ${discountPercent}%`;
                previewDiscount.classList.add('text-green-600');
            } else {
                previewDiscount.textContent = '0 đ (0%)';
                previewDiscount.classList.remove('text-green-600');
            }
        }
        
        if (previewTotal) previewTotal.textContent = formatCurrency(total, false);

        // Hiển thị thông tin về số tiền cần thanh toán dựa trên loại thanh toán
        const paymentType = document.querySelector('input[name="payment_type"]:checked')?.value || 'full';
        const paymentPercent = paymentType === 'deposit' ? '50%' : '100%';
        const paymentAmount = paymentType === 'deposit' ? total * 0.5 : total;

        if (previewPaymentPercent) previewPaymentPercent.textContent = paymentPercent;
        if (previewPayment) previewPayment.textContent = formatCurrency(paymentAmount, false);
    }

    // Cập nhật dropdown thời gian với giá
    function updateTimeSlotsWithRates(rates) {
        BookingUtils.updateTimeSlotsWithRates(rates, startTimeSelect, endTimeSelect);
    }

    // Calculate price per session
    function calculatePricePerSession() {
        const dayOfWeek = parseInt(dayOfWeekSelect.value);
        if (!dayOfWeek) return 0;

        const startTime = startTimeSelect.value || defaultStartTime;
        const endTime = endTimeSelect.value || defaultEndTime;

        // Calculate session duration
        const startMinutes = BookingUtils.convertTimeToMinutes(startTime);
        let endMinutes = BookingUtils.convertTimeToMinutes(endTime);

        // Adjust for midnight (00:00)
        if (endMinutes === 0) {
            endMinutes = 24 * 60;
        }

        const durationHours = (endMinutes - startMinutes) / 60;

        // Get hour rate based on time and day
        const currentHour = parseInt(startTime.split(':')[0]);
        const isWeekend = dayOfWeek >= 7;

        let hourlyRate = 0;
        if (currentHour >= 5 && currentHour < 10) {
            hourlyRate = isWeekend ? 70000 : 60000;
        } else if (currentHour >= 10 && currentHour < 14) {
            hourlyRate = isWeekend ? 60000 : 50000;
        } else if (currentHour >= 14 && currentHour < 18) {
            hourlyRate = isWeekend ? 80000 : 70000;
        } else {
            hourlyRate = isWeekend ? 90000 : 80000;
        }

        return Math.round(hourlyRate * durationHours);
    }

    // Validate time selection and update summary
    function validateTimeAndUpdateSummary() {
        validateTime();
        updateBookingSummary();
        updatePricePreview();
    }

    // Validate time selection
    function validateTime(silent = false) {
        const startTime = startTimeSelect.value;
        const endTime = endTimeSelect.value;

        const isValid = BookingUtils.validateTimeRange(startTime, endTime, 60);

        if (isValid) {
            if (!silent && timeErrorDisplay) timeErrorDisplay.textContent = '';
            return true;
        } else {
            if (!silent && timeErrorDisplay) {
                // Determine specific error message
                const [startHour, startMinute] = startTime.split(':').map(Number);
                const [endHour, endMinute] = endTime.split(':').map(Number);
                let startMinutes = startHour * 60 + startMinute;
                let endMinutes = endHour * 60 + endMinute;

                if (endMinutes === 0) endMinutes = 24 * 60;

                if (endMinutes <= startMinutes && !(endHour === 0 && endMinute === 0)) {
                    timeErrorDisplay.textContent = 'Giờ kết thúc phải sau giờ bắt đầu';
                } else {
                    timeErrorDisplay.textContent = 'Thời gian đặt sân phải ít nhất 1 tiếng';
                }
            }
            return false;
        }
    }

    // Validate court selection
    function validateCourts() {
        const selectedCourts = Array.from(document.querySelectorAll('input[name="court_ids[]"]:checked'));

        if (selectedCourts.length === 0) {
            if (courtErrorDisplay) courtErrorDisplay.textContent = 'Vui lòng chọn sân mà bạn muốn đặt';
            return false;
        }

        if (courtErrorDisplay) courtErrorDisplay.textContent = '';
        return true;
    }

    // Validate minimum session count (4 weeks)
    function validateSessionCount() {
        const count = getSessionCount();

        if (count < 4) {
            alert('Đặt sân định kỳ phải có ít nhất 4 buổi. Vui lòng chọn khoảng thời gian dài hơn.');
            return false;
        }

        return true;
    }

    // Get session count based on current inputs
    function getSessionCount() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        const dayOfWeek = dayOfWeekSelect.value;

        if (!startDate || !endDate || !dayOfWeek) {
            return 0;
        }

        return countSessions(startDate, endDate, dayOfWeek);
    }

    // Update price based on session count and number of courts
    function updatePrice(sessionCount) {
        // Calculate base price (price per session * session count * number of courts)
        const pricePerSession = calculatePricePerSession();
        const selectedCourtsCount = document.querySelectorAll('input[name="court_ids[]"]:checked').length || 1;
        const subtotal = pricePerSession * sessionCount * selectedCourtsCount;

        // Initialize variables for discount
        let totalPrice = subtotal;
        let discountAmount = 0;
        let discountPercent = 0;

        // Get promotion discount if available
        const promotionId = document.getElementById('promotion-select')?.value;
        const promotionNameDisplay = document.getElementById('promotion-name');
        const originalPriceDisplay = document.getElementById('original-price-display');
        const discountDisplay = document.getElementById('discount-display');

        if (promotionId) {
            // Get promotion details
            fetch('/booking/subscription/promotions', {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.promotions) {
                        const selectedPromotion = data.promotions.find(p => p.id == promotionId);
                        if (selectedPromotion) {
                            // Calculate promotion discount
                            discountAmount = subtotal * (selectedPromotion.discount_percent / 100);
                            discountPercent = selectedPromotion.discount_percent;
                            totalPrice = subtotal - discountAmount;

                            // Display discount information
                            if (discountDisplay && originalPriceDisplay) {
                                originalPriceDisplay.classList.remove('hidden');
                                discountDisplay.classList.remove('hidden');
                                discountDisplay.textContent = `Giảm giá: ${formatCurrency(discountAmount)} (${discountPercent}%)`;
                            }
                        } else {
                            // No valid promotion found
                            if (discountDisplay && originalPriceDisplay) {
                                originalPriceDisplay.classList.add('hidden');
                                discountDisplay.classList.add('hidden');
                            }
                        }
                    } else {
                        // No promotions available
                        if (discountDisplay && originalPriceDisplay) {
                            originalPriceDisplay.classList.add('hidden');
                            discountDisplay.classList.add('hidden');
                        }
                    }

                    // Update display elements
                    updatePriceDisplays(subtotal, totalPrice);
                })
                .catch(error => {
                    console.error('Error loading promotion details:', error);
                    // Fall back to no discount
                    if (discountDisplay && originalPriceDisplay) {
                        originalPriceDisplay.classList.add('hidden');
                        discountDisplay.classList.add('hidden');
                    }
                    updatePriceDisplays(subtotal, subtotal);
                });
        } else {
            // No promotion selected
            if (discountDisplay && originalPriceDisplay) {
                originalPriceDisplay.classList.add('hidden');
                discountDisplay.classList.add('hidden');
            }

            updatePriceDisplays(subtotal, subtotal);
        }
    }

    // Helper function to update price displays
    function updatePriceDisplays(originalPrice, finalPrice) {
        if (totalPriceDisplay) {
            totalPriceDisplay.textContent = formatCurrency(originalPrice);
        }

        // Update payment amount
        updatePaymentAmount(finalPrice);
    }

    // Update payment amount based on payment type
    function updatePaymentAmount(amount) {
        if (!paymentAmountDisplay) return;

        // If amount is provided, use it (this is the discounted price from updatePrice)
        // Otherwise, try to get it from the totalPriceDisplay
        let finalAmount = amount;
        if (finalAmount === undefined && totalPriceDisplay) {
            const totalPriceText = totalPriceDisplay.textContent;
            finalAmount = parseFloat(totalPriceText.replace(/[^\d]/g, '')) || 0;
        }

        const paymentType = document.querySelector('input[name="payment_type"]:checked')?.value;
        const paymentAmount = paymentType === 'deposit' ? finalAmount * 0.5 : finalAmount;

        paymentAmountDisplay.textContent = formatCurrency(Math.round(paymentAmount));
        
        // Don't hide the discount display when changing payment type
        const originalPriceDisplay = document.getElementById('original-price-display');
        const discountDisplay = document.getElementById('discount-display');
        
        // Check if there's a promotion and keep it visible
        const promotionId = document.getElementById('promotion-select')?.value;
        if (promotionId && originalPriceDisplay && discountDisplay) {
            originalPriceDisplay.classList.remove('hidden');
            discountDisplay.classList.remove('hidden');
        }
        
        checkWalletBalance();
    }

    // Kiểm tra số dư ví
    function checkWalletBalance() {
        const walletRadio = document.getElementById('payment_method_wallet');
        const walletWarning = document.getElementById('wallet-warning');
        const submitButton = document.getElementById('submit-booking');

        return BookingUtils.checkWalletBalance(walletRadio, walletWarning, paymentAmountDisplay, walletBalance, submitButton);
    }

    // Convert time string (HH:MM) to minutes since midnight
    function convertTimeToMinutes(timeString) {
        return BookingUtils.convertTimeToMinutes(timeString);
    }

    // Count number of sessions between start and end date
    function countSessions(startDate, endDate, dayOfWeek) {
        if (!startDate || !endDate || !dayOfWeek) {
            return 0;
        }

        // Parse dates using UTC to avoid timezone issues
        const start = new Date(startDate);
        const end = new Date(endDate);

        // Convert day of week from our format (2-8) to JavaScript's format (0-6)
        // Our format: 2 = Monday, 3 = Tuesday, ..., 8 = Sunday
        // JS format: 0 = Sunday, 1 = Monday, ..., 6 = Saturday
        const jsWeekDay = parseInt(dayOfWeek) === 8 ? 0 : parseInt(dayOfWeek) - 1;

        let count = 0;
        let current = new Date(start);

        // Loop through all days from start to end
        while (current <= end) {
            // Check if current day matches the selected day of week
            if (current.getDay() === jsWeekDay) {
                count++;
            }
            // Move to next day
            current.setDate(current.getDate() + 1);
        }

        return count;
    }

    // Update court selections after confirmation
    function updateCourtSelections() {
        // Uncheck all courts
        courtSelects.forEach(select => select.checked = false);

        // Check only the alternative courts
        if (typeof conflicts !== 'undefined' && conflicts) {
            conflicts.forEach(conflict => {
                const courtSelect = Array.from(courtSelects).find(select =>
                    select.value === conflict.alternative_court_id
                );
                if (courtSelect) {
                    courtSelect.checked = true;
                }
            });
        }

        // Update the booking summary
        updateBookingSummary();
    }

    // Add a new function to load and apply the best promotion
    function loadAndApplyBestPromotion() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        // Get all valid promotions from the API
        fetch('/booking/subscription/promotions', {
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
                        
                        // Immediately update preview discount display
                        const previewDiscount = document.getElementById('preview-discount');
                        if (previewDiscount) {
                            // Calculate a simple sample discount to show the discount percentage
                            // Actual amount will be recalculated when user selects courts and time
                            previewDiscount.textContent = `Giảm ${bestPromotion.discount_percent}%`;
                            previewDiscount.classList.add('text-green-600');
                        }

                        // Update price calculations with the promotion
                        updatePrice(getSessionCount());
                        
                        // Also update the preview display for immediate feedback
                        if (validateTime(true)) {
                            // Trigger an immediate preview update
                            updatePricePreview();
                        }
                    }
                } else {
                    // No valid promotions found
                    const promotionSelect = document.getElementById('promotion-select');
                    if (promotionSelect) {
                        promotionSelect.value = '';
                    }
                    
                    const promotionNameDisplay = document.getElementById('promotion-name');
                    if (promotionNameDisplay) {
                        promotionNameDisplay.textContent = 'Không có';
                    }
                    
                    // Update preview to show no discount
                    const previewDiscount = document.getElementById('preview-discount');
                    if (previewDiscount) {
                        previewDiscount.textContent = '0 đ (0%)';
                    }
                    
                    // Hide discount information
                    const originalPriceDisplay = document.getElementById('original-price-display');
                    const discountDisplay = document.getElementById('discount-display');
                    if (originalPriceDisplay) originalPriceDisplay.classList.add('hidden');
                    if (discountDisplay) discountDisplay.classList.add('hidden');
                    
                    // Update price without discount
                    updatePrice(getSessionCount());
                }
            })
            .catch(error => {
                console.error('Error loading promotions:', error);
                // Handle error case
                const promotionSelect = document.getElementById('promotion-select');
                if (promotionSelect) {
                    promotionSelect.value = '';
                }
                
                const promotionNameDisplay = document.getElementById('promotion-name');
                if (promotionNameDisplay) {
                    promotionNameDisplay.textContent = 'Không có';
                }
                
                // Update preview to show no discount
                const previewDiscount = document.getElementById('preview-discount');
                if (previewDiscount) {
                    previewDiscount.textContent = '0 đ (0%)';
                }
                
                // Hide discount information
                const originalPriceDisplay = document.getElementById('original-price-display');
                const discountDisplay = document.getElementById('discount-display');
                if (originalPriceDisplay) originalPriceDisplay.classList.add('hidden');
                if (discountDisplay) discountDisplay.classList.add('hidden');
                
                // Update price without discount
                updatePrice(getSessionCount());
            });
    }

    // Initial calls
    validateDateRange();
    updateBookingSummary();
    // Only load rates if a day is selected
    if (dayOfWeekSelect.value) {
        BookingUtils.loadRatesByDay(dayOfWeekSelect.value, updateTimeSlotsWithRates);
    }
    validateTime();
    checkWalletBalance();

    // Load the best promotion automatically
    loadAndApplyBestPromotion();
});