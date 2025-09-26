import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function () {
    // Only initialize charts on the statistical page
    if (!document.getElementById('revenue-chart')) return;

    // Initialize date inputs
    const today = new Date();
    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);

    document.getElementById('start-date').value = formatDate(startOfMonth);
    document.getElementById('end-date').value = formatDate(today);

    // Load initial data
    loadRevenueChart();
    loadRevenueSourceChart();
    loadBookingData();

    // Handle filter changes
    document.getElementById('apply-filter').addEventListener('click', function () {
        loadRevenueChart();
    });
});

// Format date as YYYY-MM-DD for input elements
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Common chart options
const commonChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top',
        }
    }
};

// Revenue Chart
let revenueChart = null;
function loadRevenueChart() {
    const period = document.getElementById('period-filter').value;
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;

    fetch(`/owner/statistical/revenue-data?period=${period}&start_date=${startDate}&end_date=${endDate}`)
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('revenue-chart').getContext('2d');

            if (revenueChart) {
                revenueChart.destroy();
            }

            revenueChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: data.datasets
                },
                options: {
                    ...commonChartOptions,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: getPeriodLabel(period)
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Doanh thu (₫)'
                            },
                            ticks: {
                                callback: function (value) {
                                    return formatCurrency(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        ...commonChartOptions.plugins,
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return context.dataset.label + ': ' + formatCurrency(context.raw);
                                }
                            }
                        }
                    }
                }
            });
        });
}

// Revenue Source Chart (Pie/Doughnut)
let revenueSourceChart = null;
function loadRevenueSourceChart() {
    // Extract data from the page (passed from controller)
    const revenueBySource = [];
    const colors = [];

    document.querySelectorAll('.revenue-source-item').forEach(el => {
        revenueBySource.push({
            name: el.dataset.name,
            value: parseInt(el.dataset.value),
            color: el.dataset.color
        });
    });

    // If no elements found, use static data passed by controller
    if (revenueBySource.length === 0) {
        // Get data from data attribute
        const revenueDataElement = document.getElementById('revenue-data');
        const sourceData = revenueDataElement ? JSON.parse(revenueDataElement.dataset.revenue) : [];

        sourceData.forEach(source => {
            revenueBySource.push({
                name: source.name,
                value: source.value,
                color: source.color
            });
            colors.push(source.color);
        });
    }

    const ctx = document.getElementById('revenue-source-chart').getContext('2d');

    if (revenueSourceChart) {
        revenueSourceChart.destroy();
    }

    revenueSourceChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: revenueBySource.map(source => source.name),
            datasets: [{
                data: revenueBySource.map(source => source.value),
                backgroundColor: colors.length > 0 ? colors : [
                    'rgb(54, 162, 235)',
                    'rgb(255, 99, 132)',
                    'rgb(75, 192, 192)',
                    'rgb(255, 206, 86)'
                ]
            }]
        },
        options: {
            ...commonChartOptions,
            plugins: {
                ...commonChartOptions.plugins,
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return context.label + ': ' + formatCurrency(value) + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

// Booking Data (includes court usage, bookings by day, peak hours)
let courtUsageChart = null;
let bookingsByDayChart = null;
let peakHoursChart = null;

function loadBookingData() {
    fetch('/owner/statistical/booking-data')
        .then(response => response.json())
        .then(data => {
            loadCourtUsageChart(data.courtUsage);
            loadBookingsByDayChart(data.bookingsByDay);
            loadPeakHoursChart(data.peakHours);
        });

    fetch('/owner/statistical/product-data')
        .then(response => response.json())
        .then(data => {
            // We could display additional product charts here if needed
        });
}

function loadCourtUsageChart(courtData) {
    const ctx = document.getElementById('court-usage-chart').getContext('2d');

    if (courtUsageChart) {
        courtUsageChart.destroy();
    }

    courtUsageChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: courtData.map(court => court.name),
            datasets: [
                {
                    label: 'Đặt sân theo buổi',
                    data: courtData.map(court => court.singleBookings),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                },
                {
                    label: 'Đặt sân định kỳ',
                    data: courtData.map(court => court.subscriptionBookings),
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgb(255, 99, 132)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            ...commonChartOptions,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Sân'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Số lượt đặt'
                    }
                }
            },
            plugins: {
                ...commonChartOptions.plugins,
                tooltip: {
                    callbacks: {
                        footer: function (tooltipItems) {
                            const idx = tooltipItems[0].dataIndex;
                            return 'Tổng: ' + courtData[idx].total + ' lượt đặt';
                        }
                    }
                }
            }
        }
    });
}

function loadBookingsByDayChart(bookingsByDay) {
    const ctx = document.getElementById('bookings-by-day-chart').getContext('2d');

    if (bookingsByDayChart) {
        bookingsByDayChart.destroy();
    }

    bookingsByDayChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: bookingsByDay.map(item => item.day),
            datasets: [
                {
                    label: 'Đặt sân theo buổi',
                    data: bookingsByDay.map(item => item.single),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgb(54, 162, 235)',
                    borderWidth: 1
                },
                {
                    label: 'Đặt sân định kỳ',
                    data: bookingsByDay.map(item => item.subscription),
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgb(255, 99, 132)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            ...commonChartOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Số lượt đặt'
                    }
                }
            },
            plugins: {
                ...commonChartOptions.plugins,
                tooltip: {
                    callbacks: {
                        footer: function (tooltipItems) {
                            const idx = tooltipItems[0].dataIndex;
                            return 'Tổng: ' + bookingsByDay[idx].total + ' lượt đặt';
                        }
                    }
                }
            }
        }
    });
}

function loadPeakHoursChart(peakHours) {
    const ctx = document.getElementById('peak-hours-chart').getContext('2d');

    if (peakHoursChart) {
        peakHoursChart.destroy();
    }

    peakHoursChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: peakHours.map(hour => hour.hour),
            datasets: [{
                label: 'Số lượt đặt',
                data: peakHours.map(hour => hour.count),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            ...commonChartOptions,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Giờ trong ngày'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Số lượt đặt'
                    }
                }
            }
        }
    });
}

// Helper Functions
function getPeriodLabel(period) {
    switch (period) {
        case 'day': return 'Ngày';
        case 'week': return 'Tuần';
        case 'month': return 'Tháng';
        case 'year': return 'Năm';
        default: return 'Thời gian';
    }
}

function formatCurrency(value) {
    return new Intl.NumberFormat('vi-VN', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value) + ' ₫';
}
