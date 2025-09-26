<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SingleBooking;
use App\Models\SubscriptionBooking;
use App\Models\Storage;
use App\Models\Court;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class StatisticalController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->role !== 'owner') {
                return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập trang này!');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $totalRevenue = $this->calculateTotalRevenue();
        $revenueBySource = $this->getRevenueBySource();
        $topCourts = $this->getTopCourts(6);
        $topProducts = $this->getTopProducts(6);

        return view('owner.statistical.statistical', [
            'totalRevenue' => $totalRevenue,
            'revenueBySource' => $revenueBySource,
            'topCourts' => $topCourts,
            'topProducts' => $topProducts
        ]);
    }

    public function getRevenueData(Request $request)
    {
        $period = $request->input('period', 'month');
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Get revenue data for the specified period
        $singleBookingRevenue = $this->getSingleBookingRevenue($startDate, $endDate, $period);
        $subscriptionBookingRevenue = $this->getSubscriptionBookingRevenue($startDate, $endDate, $period);
        $salesRevenue = $this->getSalesRevenue($startDate, $endDate, $period);
        $rentalRevenue = $this->getRentalRevenue($startDate, $endDate, $period);

        return response()->json([
            'labels' => $singleBookingRevenue['labels'],
            'datasets' => [
                [
                    'label' => 'Đặt sân theo buổi',
                    'data' => $singleBookingRevenue['data'],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgb(54, 162, 235)',
                ],
                [
                    'label' => 'Đặt sân định kỳ',
                    'data' => $subscriptionBookingRevenue['data'],
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderColor' => 'rgb(255, 99, 132)',
                ],
                [
                    'label' => 'Bán sản phẩm',
                    'data' => $salesRevenue['data'],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                    'borderColor' => 'rgb(75, 192, 192)',
                ],
                [
                    'label' => 'Cho thuê sản phẩm',
                    'data' => $rentalRevenue['data'],
                    'backgroundColor' => 'rgba(255, 206, 86, 0.5)',
                    'borderColor' => 'rgb(255, 206, 86)',
                ],
            ]
        ]);
    }

    public function getBookingData()
    {
        // Get court usage data
        $courts = Court::all();
        $courtData = [];

        foreach ($courts as $court) {
            $singleBookings = SingleBooking::where('court_id', $court->id)
                ->where('status', 'completed')
                ->count();

            $subscriptionBookings = SubscriptionBooking::where('court_id', $court->id)
                ->where('status', 'completed')
                ->count();

            $courtData[] = [
                'name' => 'Sân ' . $court->name,
                'singleBookings' => $singleBookings,
                'subscriptionBookings' => $subscriptionBookings,
                'total' => $singleBookings + $subscriptionBookings
            ];
        }

        // Get bookings by day of week
        $bookingsByDay = $this->getBookingsByDayOfWeek();

        // Get peak hours
        $peakHours = $this->getPeakHours();

        return response()->json([
            'courtUsage' => $courtData,
            'bookingsByDay' => $bookingsByDay,
            'peakHours' => $peakHours
        ]);
    }

    public function getProductData()
    {
        // Get top selling products
        $topSelling = Storage::where('transaction_type', 'sale')
            ->where('status', 'completed')
            ->select('product_name', DB::raw('SUM(CAST(quantity AS UNSIGNED)) as total_quantity'))
            ->groupBy('product_name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // Get top rented products
        $topRented = Storage::where('transaction_type', 'rent')
            ->where('status', 'returned')
            ->select('product_name', DB::raw('COUNT(*) as rental_count'))
            ->groupBy('product_name')
            ->orderByDesc('rental_count')
            ->limit(10)
            ->get();

        // Get current inventory
        $inventory = Product::select('name', 'quantity', 'status')->get();

        return response()->json([
            'topSelling' => $topSelling,
            'topRented' => $topRented,
            'inventory' => $inventory
        ]);
    }

    private function calculateTotalRevenue()
    {
        $singleBookingRevenue = SingleBooking::where('status', 'completed')->sum('total_price');
        $subscriptionBookingRevenue = SubscriptionBooking::where('status', 'completed')->sum('total_price');

        $salesRevenue = Storage::where('transaction_type', 'sale')
            ->where('status', 'completed')
            ->sum('total_price');

        $rentalRevenue = Storage::where('transaction_type', 'rent')
            ->where('status', 'returned')
            ->sum('total_price');

        return [
            'single_bookings' => $singleBookingRevenue,
            'subscription_bookings' => $subscriptionBookingRevenue,
            'sales' => $salesRevenue,
            'rentals' => $rentalRevenue,
            'total' => $singleBookingRevenue + $subscriptionBookingRevenue + $salesRevenue + $rentalRevenue
        ];
    }

    private function getRevenueBySource()
    {
        $singleBookingRevenue = SingleBooking::where('status', 'completed')->sum('total_price');
        $subscriptionBookingRevenue = SubscriptionBooking::where('status', 'completed')->sum('total_price');

        $salesRevenue = Storage::where('transaction_type', 'sale')
            ->where('status', 'completed')
            ->sum('total_price');

        $rentalRevenue = Storage::where('transaction_type', 'rent')
            ->where('status', 'returned')
            ->sum('total_price');

        return [
            ['name' => 'Đặt sân theo buổi', 'value' => $singleBookingRevenue, 'color' => 'rgb(54, 162, 235)'],
            ['name' => 'Đặt sân định kỳ', 'value' => $subscriptionBookingRevenue, 'color' => 'rgb(255, 99, 132)'],
            ['name' => 'Bán sản phẩm', 'value' => $salesRevenue, 'color' => 'rgb(75, 192, 192)'],
            ['name' => 'Cho thuê thiết bị', 'value' => $rentalRevenue, 'color' => 'rgb(255, 206, 86)']
        ];
    }

    private function getTopCourts($limit = 6)
    {
        $courts = Court::all();
        $courtStats = [];

        foreach ($courts as $court) {
            $singleBookingRevenue = SingleBooking::where('court_id', $court->id)
                ->where('status', 'completed')
                ->sum('total_price');

            $subscriptionBookingRevenue = SubscriptionBooking::where('court_id', $court->id)
                ->where('status', 'completed')
                ->sum('total_price');

            $courtStats[] = [
                'name' => 'Sân ' . $court->name,
                'total_revenue' => $singleBookingRevenue + $subscriptionBookingRevenue,
                'booking_count' => SingleBooking::where('court_id', $court->id)->where('status', 'completed')->count() +
                    SubscriptionBooking::where('court_id', $court->id)->where('status', 'completed')->count()
            ];
        }

        // Sort by revenue
        usort($courtStats, function ($a, $b) {
            return $b['total_revenue'] - $a['total_revenue'];
        });

        return array_slice($courtStats, 0, $limit);
    }

    private function getTopProducts($limit = 6)
    {
        // Combine sold and rented products
        $salesProducts = Storage::where('transaction_type', 'sale')
            ->where('status', 'completed')
            ->select('product_name', DB::raw('SUM(total_price) as revenue'))
            ->groupBy('product_name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();

        $rentalProducts = Storage::where('transaction_type', 'rent')
            ->where('status', 'returned')
            ->select('product_name', DB::raw('SUM(total_price) as revenue'))
            ->groupBy('product_name')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();

        $merged = $salesProducts->concat($rentalProducts);

        $topProducts = [];
        foreach ($merged as $product) {
            if (!isset($topProducts[$product->product_name])) {
                $topProducts[$product->product_name] = [
                    'name' => $product->product_name,
                    'revenue' => $product->revenue,
                ];
            } else {
                $topProducts[$product->product_name]['revenue'] += $product->revenue;
            }
        }

        // Sort by revenue
        usort($topProducts, function ($a, $b) {
            return $b['revenue'] - $a['revenue'];
        });

        return array_slice(array_values($topProducts), 0, $limit);
    }

    private function getSingleBookingRevenue($startDate, $endDate, $period = 'month')
    {
        return $this->getRevenueByPeriod(
            SingleBooking::where('status', 'completed'),
            'start_time',
            $startDate,
            $endDate,
            $period
        );
    }

    private function getSubscriptionBookingRevenue($startDate, $endDate, $period = 'month')
    {
        return $this->getRevenueByPeriod(
            SubscriptionBooking::where('status', 'completed'),
            'start_date',
            $startDate,
            $endDate,
            $period
        );
    }

    private function getSalesRevenue($startDate, $endDate, $period = 'month')
    {
        return $this->getRevenueByPeriod(
            Storage::where('transaction_type', 'sale')->where('status', 'completed'),
            'created_at',
            $startDate,
            $endDate,
            $period
        );
    }

    private function getRentalRevenue($startDate, $endDate, $period = 'month')
    {
        return $this->getRevenueByPeriod(
            Storage::where('transaction_type', 'rent')->where('status', 'returned'),
            'created_at',
            $startDate,
            $endDate,
            $period
        );
    }

    private function getRevenueByPeriod($query, $dateField, $startDate, $endDate, $period = 'month')
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $labels = [];
        $data = [];

        if ($period == 'day') {
            for ($date = $start; $date->lte($end); $date->addDay()) {
                $labels[] = $date->format('d/m');

                $dayRevenue = (clone $query)
                    ->whereDate($dateField, $date->format('Y-m-d'))
                    ->sum('total_price');

                $data[] = $dayRevenue;
            }
        } elseif ($period == 'week') {
            for ($date = $start; $date->lte($end); $date->addWeek()) {
                $weekEnd = (clone $date)->addDays(6);
                if ($weekEnd->gt($end)) {
                    $weekEnd = clone $end;
                }

                $labels[] = $date->format('d/m') . '-' . $weekEnd->format('d/m');

                $weekRevenue = (clone $query)
                    ->whereBetween($dateField, [
                        $date->format('Y-m-d'),
                        $weekEnd->format('Y-m-d')
                    ])
                    ->sum('total_price');

                $data[] = $weekRevenue;
            }
        } elseif ($period == 'month') {
            for ($date = $start->startOfMonth(); $date->lte($end); $date->addMonth()) {
                $labels[] = $date->format('m/Y');

                $monthRevenue = (clone $query)
                    ->whereYear($dateField, $date->year)
                    ->whereMonth($dateField, $date->month)
                    ->sum('total_price');

                $data[] = $monthRevenue;
            }
        } elseif ($period == 'year') {
            for ($date = $start->startOfYear(); $date->lte($end); $date->addYear()) {
                $labels[] = $date->format('Y');

                $yearRevenue = (clone $query)
                    ->whereYear($dateField, $date->year)
                    ->sum('total_price');

                $data[] = $yearRevenue;
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getBookingsByDayOfWeek()
    {
        $daysOfWeek = [
            2 => 'Thứ 2',
            3 => 'Thứ 3',
            4 => 'Thứ 4',
            5 => 'Thứ 5',
            6 => 'Thứ 6',
            7 => 'Thứ 7',
            8 => 'Chủ nhật'
        ];

        $singleBookingsByDay = [];
        $subscriptionBookingsByDay = [];

        // For single bookings, we need to extract the day of week from the datetime
        foreach ($daysOfWeek as $day => $name) {
            $singleBookingsByDay[$day] = SingleBooking::where('status', 'completed')
                ->whereRaw("DAYOFWEEK(start_time) = " . ($day == 8 ? 1 : $day))
                ->count();
        }

        // For subscription bookings, they already have day_of_week
        foreach ($daysOfWeek as $day => $name) {
            $subscriptionBookingsByDay[$day] = SubscriptionBooking::where('status', 'completed')
                ->where('day_of_week', $day)
                ->count();
        }

        $result = [];
        foreach ($daysOfWeek as $day => $name) {
            $result[] = [
                'day' => $name,
                'single' => $singleBookingsByDay[$day] ?? 0,
                'subscription' => $subscriptionBookingsByDay[$day] ?? 0,
                'total' => ($singleBookingsByDay[$day] ?? 0) + ($subscriptionBookingsByDay[$day] ?? 0)
            ];
        }

        return $result;
    }

    private function getPeakHours()
    {
        $hours = [];

        // Generate hours from 5AM to 11PM
        for ($i = 5; $i <= 23; $i++) {
            $hours[$i] = [
                'hour' => $i . ':00',
                'count' => 0
            ];
        }

        // Count bookings by hour
        $singleBookings = SingleBooking::where('status', 'completed')
            ->select(DB::raw('HOUR(start_time) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('HOUR(start_time)'))
            ->get();

        foreach ($singleBookings as $booking) {
            if (isset($hours[$booking->hour])) {
                $hours[$booking->hour]['count'] += $booking->count;
            }
        }

        // For subscription bookings
        $subscriptionBookings = SubscriptionBooking::where('status', 'completed')
            ->select(DB::raw('HOUR(start_time) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('HOUR(start_time)'))
            ->get();

        foreach ($subscriptionBookings as $booking) {
            if (isset($hours[$booking->hour])) {
                $hours[$booking->hour]['count'] += $booking->count;
            }
        }

        // Convert to array and sort by hour
        $result = array_values($hours);

        return $result;
    }
}
