@extends('layout.main-owner')

@section('title', 'Quản lý giá thuê')

@section('content')
    <div class="py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold text-gray-800">Quản lý giá thuê</h2>
                        <a href="{{ route('court-rates.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            Thêm giá thuê mới
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @php
                        $ratesByDay = $courtRates->groupBy('day_of_week');
                        $dayOrder = [2, 3, 4, 5, 6, 7, 8];
                    @endphp

                    <div class="space-y-4">
                        @foreach($dayOrder as $dayNum)
                            @if($ratesByDay->has($dayNum))
                                @php
                                    $firstRate = $ratesByDay[$dayNum]->first();
                                    $dayName = $firstRate->day_name;
                                @endphp
                                <div class="border rounded-lg overflow-hidden shadow">
                                    <div class="bg-gray-100 px-6 py-4 cursor-pointer flex justify-between items-center day-header" 
                                         data-day="{{ $dayNum }}">
                                        <h3 class="font-medium text-gray-800">{{ $dayName }}</h3>
                                        <span class="text-gray-600">
                                            <i class="fas fa-chevron-down day-icon-{{ $dayNum }}"></i>
                                        </span>
                                    </div>
                                    <div class="day-content day-content-{{ $dayNum }}" style="display: none">

                                        <table class="min-w-full bg-white">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                                        Giờ bắt đầu
                                                    </th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                                        Giờ kết thúc
                                                    </th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                                        Giá/giờ
                                                    </th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">
                                                        Thao tác
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-200">
                                                @foreach($ratesByDay[$dayNum] as $rate)
                                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                                            {{ \Carbon\Carbon::parse($rate->start_time)->format('H:i') }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                                            {{ \Carbon\Carbon::parse($rate->end_time)->format('H:i') }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                                            {{ number_format($rate->price_per_hour) }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                            <div class="flex space-x-2">
                                                                <a href="{{ route('court-rates.edit', $rate) }}" class="text-indigo-600 hover:text-indigo-900">
                                                                    <i class="fas fa-edit"></i> Sửa
                                                                </a>
                                                                <form action="{{ route('court-rates.destroy', $rate) }}" method="POST" class="inline-block">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="text-red-600 hover:text-red-900" 
                                                                        onclick="return confirm('Bạn có chắc chắn muốn xóa giá thuê này?')">
                                                                        <i class="fas fa-trash"></i> Xóa
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dayHeaders = document.querySelectorAll('.day-header');
            dayHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const day = this.getAttribute('data-day');
                    toggleDay(day);
                });
            });
        });

        function toggleDay(dayNum) {
            const content = document.querySelector(`.day-content-${dayNum}`);
            const icon = document.querySelector(`.day-icon-${dayNum}`);
            
            if (content.style.display === 'none') {
                content.style.display = 'block';
                icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
            } else {
                content.style.display = 'none';
                icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
            }
        }
    </script>
@endsection
