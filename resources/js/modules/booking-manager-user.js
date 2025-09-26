function showTab(tabName) {
    // Ẩn tất cả nội dung tab
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Hiển thị nội dung tab được chọn
    const tabContent = document.getElementById('content-' + tabName);
    if (tabContent) {
        tabContent.classList.remove('hidden');
    }
    
    // Cập nhật trạng thái active của tab
    document.querySelectorAll('[id^="tab-"]').forEach(tab => {
        tab.classList.remove('active', 'border-blue-600', 'text-blue-600');
        tab.classList.add('border-transparent');
    });
    
    const activeTab = document.getElementById('tab-' + tabName);
    if (activeTab) {
        activeTab.classList.add('active', 'border-blue-600', 'text-blue-600');
        activeTab.classList.remove('border-transparent');
    }
}

function filterStatus(status, bookingType) {
    if (!event || !event.target) return;
    
    // Cập nhật style của nút lọc
    document.querySelectorAll('.status-filter-' + bookingType).forEach(btn => {
        btn.classList.remove('bg-blue-100', 'text-blue-800');
        btn.classList.add('bg-gray-100', 'text-gray-800');
    });
    
    // Đánh dấu nút được chọn
    event.target.classList.remove('bg-gray-100', 'text-gray-800');
    event.target.classList.add('bg-blue-100', 'text-blue-800');
    
    // Lọc các hàng theo trạng thái
    const rows = document.querySelectorAll('.' + bookingType + '-booking');
    rows.forEach(row => {
        if (status === 'all' || row.getAttribute('data-status') === status) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });
}

// Khởi tạo mặc định và thiết lập event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Thiết lập các tab
    const tabSingle = document.getElementById('tab-single');
    const tabSubscription = document.getElementById('tab-subscription');
    
    if (tabSingle) {
        tabSingle.addEventListener('click', function() {
            showTab('single');
        });
    }
    
    if (tabSubscription) {
        tabSubscription.addEventListener('click', function() {
            showTab('subscription');
        });
    }
    
    // Thiết lập các nút lọc trạng thái
    document.querySelectorAll('[data-status]').forEach(button => {
        button.addEventListener('click', function(e) {
            const status = this.getAttribute('data-status');
            const type = this.getAttribute('data-type');
            
            // Lưu lại event để dùng trong hàm filterStatus
            window.event = e;
            filterStatus(status, type);
        });
    });
    
    // Hiển thị tab mặc định và trạng thái lọc mặc định
    const contentSingle = document.getElementById('content-single');
    if (contentSingle) {
        showTab('single');
        
        // Đặt mặc định cho các nút lọc
        const defaultSingleFilter = document.querySelector('[data-status="all"][data-type="single"]');
        if (defaultSingleFilter) {
            window.event = { target: defaultSingleFilter };
            filterStatus('all', 'single');
        }
    }
    
    const contentSubscription = document.getElementById('content-subscription');
    if (!contentSingle && contentSubscription) {
        showTab('subscription');
        
        // Đặt mặc định cho các nút lọc
        const defaultSubscriptionFilter = document.querySelector('[data-status="all"][data-type="subscription"]');
        if (defaultSubscriptionFilter) {
            window.event = { target: defaultSubscriptionFilter };
            filterStatus('all', 'subscription');
        }
    }
});