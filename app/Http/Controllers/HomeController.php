<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotion;

class HomeController
{
    public function index()
    {
        $promotions = Promotion::where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->take(4)
            ->get();

        $defaultSlides = [
            [
                'name' => 'Sân cầu lông cao cấp',
                'description' => 'Trải nghiệm sân cầu lông tiêu chuẩn quốc tế với hệ thống đèn LED hiện đại và mặt sân chuyên nghiệp.',
                'image' => 'image/slide1.jpeg'
            ],
            [
                'name' => 'Giá ưu đãi hấp dẫn',
                'description' => 'Đặt sân định kỳ để nhận ưu đãi giảm giá lên đến 10%. Đặc biệt ưu đãi cho khách hàng thân thiết.',
                'image' => 'image/slide2.jpeg'
            ],
            [
                'name' => 'Dịch vụ đi kèm',
                'description' => 'Cung cấp đầy đủ dịch vụ từ cho thuê vợt, bán cầu đến nước uống và phòng thay đồ tiện nghi.',
                'image' => 'image/slide3.jpeg'
            ],
            [
                'name' => 'Đặt sân trực tuyến dễ dàng',
                'description' => 'Chỉ với vài thao tác đơn giản, bạn có thể đặt sân cầu lông mọi lúc mọi nơi qua hệ thống.',
                'image' => 'image/slide4.jpeg'
            ]
        ];

        $sliderItems = [];
        $promotionCount = $promotions->count();

        foreach ($promotions as $promotion) {
            $sliderItems[] = [
                'type' => 'promotion',
                'data' => $promotion
            ];
        }

        if ($promotionCount < 4) {
            for ($i = 0; $i < (4 - $promotionCount); $i++) {
                $sliderItems[] = [
                    'type' => 'default',
                    'data' => (object)$defaultSlides[$i]
                ];
            }
        }

        return view('home', [
            'sliderItems' => $sliderItems
        ]);
    }
}
