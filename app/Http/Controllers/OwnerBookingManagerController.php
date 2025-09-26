<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\SingleBooking;
use App\Models\SubscriptionBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OwnerBookingManagerController extends BaseBookingController
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

    public function ownerIndex(Request $request)
    {
        $search = $request->get('search', '');
        $type = $request->get('type', 'all');
        $courtId = $request->get('court_id');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $paymentType = $request->get('payment_type');
        $paymentMethod = $request->get('payment_method');
        $status = $request->get('status');

        $singleBookingsQuery = SingleBooking::with(['customer', 'court', 'refunds'])
            ->when(!empty($search), function ($query) use ($search) {
                if (is_numeric($search)) {
                    $query->where('id', $search);
                } else {
                    $query->whereHas('customer', function ($q) use ($search) {
                        $q->where('fullname', 'like', "%{$search}%");
                    });
                }
            })
            // Filter by court
            ->when(!empty($courtId), function ($query) use ($courtId) {
                $query->where('court_id', $courtId);
            })
            // Filter by date range - start_time
            ->when(!empty($fromDate), function ($query) use ($fromDate) {
                $query->whereDate('start_time', '>=', $fromDate);
            })
            ->when(!empty($toDate), function ($query) use ($toDate) {
                $query->whereDate('start_time', '<=', $toDate);
            })
            // Filter by payment type
            ->when(!empty($paymentType), function ($query) use ($paymentType) {
                $query->where('payment_type', $paymentType);
            })
            // Filter by payment method
            ->when(!empty($paymentMethod), function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            })
            // Filter by status
            ->when(!empty($status), function ($query) use ($status) {
                $query->where('status', $status);
            });

        // Query for SubscriptionBookings
        $subscriptionBookingsQuery = SubscriptionBooking::with(['court', 'user', 'customer', 'refunds'])
            ->when(!empty($search), function ($query) use ($search) {
                if (is_numeric($search)) {
                    $query->where('id', $search);
                } else {
                    // Search by customer name
                    $query->whereHas('user', function ($q) use ($search) {
                        $q->where('username', 'like', "%{$search}%");
                    });
                }
            })
            // Filter by court
            ->when(!empty($courtId), function ($query) use ($courtId) {
                $query->where('court_id', $courtId);
            })
            // Filter by date range - start_date
            ->when(!empty($fromDate), function ($query) use ($fromDate) {
                $query->whereDate('start_date', '>=', $fromDate);
            })
            ->when(!empty($toDate), function ($query) use ($toDate) {
                $query->whereDate('end_date', '<=', $toDate);
            })
            // Filter by payment type
            ->when(!empty($paymentType), function ($query) use ($paymentType) {
                $query->where('payment_type', $paymentType);
            })
            // Filter by payment method
            ->when(!empty($paymentMethod), function ($query) use ($paymentMethod) {
                $query->where('payment_method', $paymentMethod);
            })
            // Filter by status
            ->when(!empty($status), function ($query) use ($status) {
                $query->where('status', $status);
            });

        // Filter by booking type if specified
        if ($type === 'single') {
            $singleBookings = $singleBookingsQuery->orderBy('created_at', 'desc')->paginate(10);
            $subscriptionBookings = collect([]);
        } elseif ($type === 'subscription') {
            $singleBookings = collect([]);
            $subscriptionBookings = $subscriptionBookingsQuery->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $singleBookings = $singleBookingsQuery->orderBy('created_at', 'desc')->paginate(10);
            $subscriptionBookings = $subscriptionBookingsQuery->orderBy('created_at', 'desc')->paginate(10);
        }

        return view('owner.booking.index', compact(
            'singleBookings',
            'subscriptionBookings',
            'search',
            'type',
            'courtId',
            'fromDate',
            'toDate',
            'paymentType',
            'paymentMethod',
            'status'
        ));
    }

    public function showSingleBooking($id)
    {
        $booking = SingleBooking::with(['court', 'customer', 'promotion', 'refunds'])->findOrFail($id);
        $courts = Court::where('status', 'available')->get();

        $conflictService = app(\App\Services\BookingConflictService::class);
        $availableCourts = $conflictService->findAvailableCourts($booking->start_time, $booking->end_time, []);
        $availableCourtIds = array_column($availableCourts, 'id');

        if (!in_array($booking->court_id, $availableCourtIds)) {
            $availableCourtIds[] = $booking->court_id;
        }

        return view('owner.booking.single-detail', compact('booking', 'courts', 'availableCourtIds'));
    }

    public function showSubscriptionBooking($id)
    {
        $booking = SubscriptionBooking::with(['court', 'user', 'customer', 'promotion', 'refunds'])->findOrFail($id);
        $courts = Court::where('status', 'available')->get();

        $conflictService = app(\App\Services\BookingConflictService::class);
        $availableCourts = $conflictService->findAvailableCourtsForSubscription(
            $booking->day_of_week,
            $booking->start_time,
            $booking->end_time,
            $booking->start_date,
            $booking->end_date,
            []
        );
        $availableCourtIds = array_column($availableCourts, 'id');

        if (!in_array($booking->court_id, $availableCourtIds)) {
            $availableCourtIds[] = $booking->court_id;
        }

        return view('owner.booking.subscription-detail', compact('booking', 'courts', 'availableCourtIds'));
    }

    public function printSingleBooking($id)
    {
        $booking = SingleBooking::with(['court', 'customer', 'promotion', 'refunds'])->findOrFail($id);
        return view('owner.booking.single-invoice', compact('booking'));
    }

    public function printSubscriptionBooking($id)
    {
        $booking = SubscriptionBooking::with(['court', 'user', 'customer', 'promotion', 'refunds'])->findOrFail($id);
        return view('owner.booking.subscription-invoice', compact('booking'));
    }

    public function completeBooking($id)
    {
        try {
            // Tìm đơn đặt theo ID
            $singleBooking = SingleBooking::find($id);
            $subscriptionBooking = SubscriptionBooking::find($id);

            if ($singleBooking) {

                // Cập nhật trạng thái
                $singleBooking->status = 'completed';
                $singleBooking->save();

                return back()->with('success', 'Đã xác nhận hoàn thành đơn đặt!');
            } elseif ($subscriptionBooking) {

                // Cập nhật trạng thái
                $subscriptionBooking->status = 'completed';
                $subscriptionBooking->save();

                return back()->with('success', 'Đã xác nhận hoàn thành đơn đặt!');
            }

            return back()->with('error', 'Không tìm thấy đơn đặt!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
