<?php

namespace App\Services;

use App\Models\SingleBooking;
use App\Models\SubscriptionBooking;
use App\Models\Court;
use App\Models\CourtRate;
use Carbon\Carbon;

class BookingConflictService
{
    /**
     * Kiểm tra xung đột khi đặt sân đơn lẻ
     *
     * @param int $courtId
     * @param string $startDateTime
     * @param string $endDateTime
     * @param int|null $excludeBookingId
     * @return array
     */
    public function checkSingleBookingConflicts($courtId, $startDateTime, $endDateTime, $excludeBookingId = null)
    {
        $startDate = Carbon::parse($startDateTime)->format('Y-m-d');
        $endDate = Carbon::parse($endDateTime)->format('Y-m-d');
        $startTime = Carbon::parse($startDateTime)->format('H:i:s');
        $endTime = Carbon::parse($endDateTime)->format('H:i:s');
        
        // Kiểm tra xung đột với lịch đặt sân đơn lẻ
        $singleConflicts = SingleBooking::where('court_id', $courtId)
            ->where('id', '!=', $excludeBookingId)
            ->whereDate('start_time', $startDate)
            ->where('status', 'confirmed')
            ->where(function($query) use ($startTime, $endTime, $startDate) {
                $fullStartTime = $startDate . ' ' . $startTime;
                $fullEndTime = $startDate . ' ' . $endTime;
                
                $query->where(function($q) use ($fullStartTime, $fullEndTime) {
                    $q->where('start_time', '>=', $fullStartTime)
                      ->where('start_time', '<', $fullEndTime);
                })
                ->orWhere(function($q) use ($fullStartTime, $fullEndTime) {
                    $q->where('end_time', '>', $fullStartTime)
                      ->where('end_time', '<=', $fullEndTime);
                })
                ->orWhere(function($q) use ($fullStartTime, $fullEndTime) {
                    $q->where('start_time', '<=', $fullStartTime)
                      ->where('end_time', '>=', $fullEndTime);
                });
            })
            ->get();
            
        // Kiểm tra xung đột với lịch đặt sân định kỳ
        $dayOfWeek = Carbon::parse($startDateTime)->format('N') + 1; // 2-8 (2: Thứ hai, 8: Chủ nhật)
        
        $subscriptionConflicts = SubscriptionBooking::where('court_id', $courtId)
            ->where('day_of_week', $dayOfWeek)
            ->where('status', 'confirmed')
            ->where(function($query) use ($startDate) {
                $query->where('start_date', '<=', $startDate)
                      ->where('end_date', '>=', $startDate);
            })
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($q) use ($startTime, $endTime) {
                    $q->where('start_time', '>=', $startTime)
                      ->where('start_time', '<', $endTime);
                })
                ->orWhere(function($q) use ($startTime, $endTime) {
                    $q->where('end_time', '>', $startTime)
                      ->where('end_time', '<=', $endTime);
                })
                ->orWhere(function($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>=', $endTime);
                });
            })
            ->get();
            
        // Trả về kết quả xung đột
        return array_merge(
            $singleConflicts->map(function($booking) {
                return [
                    'type' => 'single',
                    'id' => $booking->id,
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time,
                    'court_id' => $booking->court_id
                ];
            })->toArray(),
            $subscriptionConflicts->map(function($booking) use ($startDate) {
                return [
                    'type' => 'subscription',
                    'id' => $booking->id,
                    'start_time' => $startDate . ' ' . $booking->start_time,
                    'end_time' => $startDate . ' ' . $booking->end_time,
                    'court_id' => $booking->court_id,
                    'day_of_week' => $booking->day_of_week
                ];
            })->toArray()
        );
    }
    
    /**
     * Kiểm tra xung đột khi đặt sân định kỳ
     *
     * @param int $courtId
     * @param int $dayOfWeek
     * @param string $startTime
     * @param string $endTime
     * @param string $startDate
     * @param string $endDate
     * @param int|null $excludeBookingId
     * @return array
     */
    public function checkSubscriptionBookingConflicts(
        $courtId, 
        $dayOfWeek, 
        $startTime, 
        $endTime, 
        $startDate, 
        $endDate, 
        $excludeBookingId = null
    ) {
        // Kiểm tra xung đột với lịch đặt sân định kỳ khác
        $subscriptionConflicts = SubscriptionBooking::where('court_id', $courtId)
            ->where('id', '!=', $excludeBookingId)
            ->where('day_of_week', $dayOfWeek)
            ->where('status', 'confirmed')
            ->where(function($query) use ($startDate, $endDate) {
                $query->where(function($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $endDate)
                      ->where('end_date', '>=', $startDate);
                });
            })
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($q) use ($startTime, $endTime) {
                    $q->where('start_time', '>=', $startTime)
                      ->where('start_time', '<', $endTime);
                })
                ->orWhere(function($q) use ($startTime, $endTime) {
                    $q->where('end_time', '>', $startTime)
                      ->where('end_time', '<=', $endTime);
                })
                ->orWhere(function($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>=', $endTime);
                });
            })
            ->get();
        
        // Kiểm tra xung đột với đặt sân đơn lẻ
        $currentDate = Carbon::parse($startDate);
        $endDateObj = Carbon::parse($endDate);
        $singleConflicts = [];
        
        // Kiểm tra từng ngày trong khoảng thời gian đặt sân định kỳ
        while ($currentDate <= $endDateObj) {
            if ((int)$currentDate->format('N') + 1 == $dayOfWeek) {
                $dateStr = $currentDate->format('Y-m-d');
                $fullStartTime = $dateStr . ' ' . $startTime;
                $fullEndTime = $dateStr . ' ' . $endTime;
                
                $conflicts = SingleBooking::where('court_id', $courtId)
                    ->whereDate('start_time', $dateStr)
                    ->where('status', 'confirmed')
                    ->where(function($query) use ($fullStartTime, $fullEndTime) {
                        $query->where(function($q) use ($fullStartTime, $fullEndTime) {
                            $q->where('start_time', '>=', $fullStartTime)
                              ->where('start_time', '<', $fullEndTime);
                        })
                        ->orWhere(function($q) use ($fullStartTime, $fullEndTime) {
                            $q->where('end_time', '>', $fullStartTime)
                              ->where('end_time', '<=', $fullEndTime);
                        })
                        ->orWhere(function($q) use ($fullStartTime, $fullEndTime) {
                            $q->where('start_time', '<=', $fullStartTime)
                              ->where('end_time', '>=', $fullEndTime);
                        });
                    })
                    ->get();
                    
                $singleConflicts = array_merge($singleConflicts, $conflicts->toArray());
            }
            
            $currentDate->addDay();
        }
        
        return array_merge(
            array_map(function($booking) {
                return [
                    'type' => 'single',
                    'id' => $booking['id'],
                    'start_time' => $booking['start_time'],
                    'end_time' => $booking['end_time'],
                    'court_id' => $booking['court_id']
                ];
            }, $singleConflicts),
            $subscriptionConflicts->map(function($booking) {
                return [
                    'type' => 'subscription',
                    'id' => $booking->id,
                    'day_of_week' => $booking->day_of_week,
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time,
                    'court_id' => $booking->court_id,
                    'start_date' => $booking->start_date,
                    'end_date' => $booking->end_date
                ];
            })->toArray()
        );
    }
    
    /**
     * Kiểm tra xung đột trong bảng giá (giá đã được định nghĩa cho khung giờ)
     *
     * @param int $dayOfWeek
     * @param string $startTime
     * @param string $endTime
     * @param int|null $excludeRateId
     * @return bool
     */
    public function checkRateTimeConflict($dayOfWeek, $startTime, $endTime, $excludeRateId = null)
    {
        $query = CourtRate::where('day_of_week', $dayOfWeek)
            ->overlappingTimeRange($startTime, $endTime);

        if ($excludeRateId) {
            $query->where('id', '!=', $excludeRateId);
        }

        return $query->exists();
    }
    
    /**
     * Tìm sân trống trong khung giờ
     *
     * @param string $startDateTime
     * @param string $endDateTime
     * @param array $excludeCourtIds
     * @return array
     */
    public function findAvailableCourts($startDateTime, $endDateTime, $excludeCourtIds = [])
    {
        $allCourts = Court::where('status', 'available')->get();
        $availableCourts = [];
            
        foreach ($allCourts as $court) {
            if (in_array($court->id, $excludeCourtIds)) {
                continue;
            }
            
            $conflicts = $this->checkSingleBookingConflicts($court->id, $startDateTime, $endDateTime);
            
            if (empty($conflicts)) {
                $availableCourts[] = [
                    'id' => $court->id,
                    'name' => $court->name,
                    'image' => $court->image
                ];
            }
        }
        
        return $availableCourts;
    }
    
    /**
     * Tìm sân trống cho đặt sân định kỳ
     *
     * @param int $dayOfWeek
     * @param string $startTime
     * @param string $endTime
     * @param string $startDate
     * @param string $endDate
     * @param array $excludeCourtIds
     * @return array
     */
    public function findAvailableCourtsForSubscription(
        $dayOfWeek, 
        $startTime, 
        $endTime, 
        $startDate, 
        $endDate, 
        $excludeCourtIds = []
    ) {
        $allCourts = Court::where('status', 'available')->get();
        $availableCourts = [];
            
        foreach ($allCourts as $court) {
            if (in_array($court->id, $excludeCourtIds)) {
                continue;
            }
            
            $conflicts = $this->checkSubscriptionBookingConflicts(
                $court->id, 
                $dayOfWeek, 
                $startTime, 
                $endTime, 
                $startDate, 
                $endDate
            );
            
            if (empty($conflicts)) {
                $availableCourts[] = [
                    'id' => $court->id,
                    'name' => $court->name,
                    'image' => $court->image
                ];
            }
        }
        
        return $availableCourts;
    }
} 