<?php

namespace App\Http\Controllers;

use App\Models\CourtRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class CourtRatesController extends BaseController
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
        $courtRates = CourtRate::orderBy('day_of_week')->orderBy('start_time')->get();
        return view('owner.court_rate.court-rate-manager', compact('courtRates'));
    }

    public function create()
    {
        return view('owner.court_rate.add-court-rate');
    }

    private function checkTimeConflict($dayOfWeek, $startTime, $endTime, $excludeId = null)
    {
        $conflictService = app(\App\Services\BookingConflictService::class);
        return $conflictService->checkRateTimeConflict($dayOfWeek, $startTime, $endTime, $excludeId);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'required|integer|between:2,8',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'price_per_hour' => 'required|integer|min:0',
        ]);

        // Kiểm tra xung đột cho tất cả các ngày được chọn
        foreach ($data['days_of_week'] as $day) {
            if ($this->checkTimeConflict($day, $data['start_time'], $data['end_time'])) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'time_conflict' => "Đã tồn tại khung giờ cho Thứ {$day} trong khoảng thời gian này!"
                    ]);
            }
        }

        // Nếu không có xung đột, tạo các bản ghi mới
        foreach ($data['days_of_week'] as $day) {
            CourtRate::create([
                'day_of_week' => $day,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'price_per_hour' => $data['price_per_hour'],
            ]);
        }

        return redirect()->route('court-rates.index')
            ->with('success', 'Thêm giá sân thành công!');
    }

    public function edit(CourtRate $courtRate)
    {
        return view('owner.court_rate.update-court-rate', compact('courtRate'));
    }

    public function update(Request $request, CourtRate $courtRate)
    {
        $data = $request->validate([
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'required|integer|between:2,8',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'price_per_hour' => 'required|integer|min:0',
        ]);

        // Kiểm tra xung đột cho tất cả các ngày được chọn, ngoại trừ ngày hiện tại
        foreach ($data['days_of_week'] as $day) {
            if (
                $day != $courtRate->day_of_week &&
                $this->checkTimeConflict($day, $data['start_time'], $data['end_time'])
            ) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'time_conflict' => "Đã tồn tại khung giờ cho {$this->getDayName($day)} trong khoảng thời gian này!"
                    ]);
            }
        }

        // Cập nhật bản ghi hiện tại thay vì xóa và tạo mới
        $courtRate->update([
            'day_of_week' => $data['days_of_week'][0], // Lấy ngày đầu tiên được chọn
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'price_per_hour' => $data['price_per_hour'],
        ]);

        // Tạo thêm các bản ghi mới cho các ngày khác (nếu có)
        for ($i = 1; $i < count($data['days_of_week']); $i++) {
            CourtRate::create([
                'day_of_week' => $data['days_of_week'][$i],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'price_per_hour' => $data['price_per_hour'],
            ]);
        }

        return redirect()->route('court-rates.index')
            ->with('success', 'Cập nhật giá sân thành công!');
    }

    private function getDayName($day)
    {
        // Sử dụng phương thức tĩnh từ model CourtRate
        return CourtRate::getDayNameStatic($day);
    }

    public function destroy(CourtRate $courtRate)
    {
        $courtRate->delete();
        return redirect()->route('court-rates.index')->with('success', 'Xóa giá sân thành công!');
    }
}
