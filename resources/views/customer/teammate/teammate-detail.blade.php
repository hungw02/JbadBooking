@extends('layout.main-customer')

@section('title', 'Chi tiết đồng đội')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Main Content -->
    <div class="w-full bg-white rounded-lg shadow-lg p-6 max-w-3xl mx-auto">
        <div class="mb-6 border-b border-gray-200 pb-4">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-800">Chi tiết đồng đội</h1>
                <a href="{{ route('teammate.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition duration-200 ease-in-out shadow-sm text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Quay lại danh sách
                </a>
            </div>
        </div>
        
        <!-- Teammate Details -->
        <div class="space-y-6">
            <!-- Header Info -->
            <div class="flex flex-col md:flex-row justify-between bg-gray-50 p-5 rounded-xl">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800">{{ $teammate->full_name }}</h2>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Trình độ: {{ $teammate->skill_level }}
                        </span>
                        @if($teammate->play_time)
                        <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Thời gian chơi: {{ $teammate->play_time }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="mt-4 md:mt-0 text-right">
                    <div class="bg-white px-4 py-2 rounded-lg shadow-sm inline-block">
                        <p class="text-sm text-gray-500">Đăng tải lúc: {{ $teammate->created_at->format('d/m/Y') }}</p>
                        @if($teammate->created_at != $teammate->updated_at)
                            <p class="text-sm text-gray-500 mt-1">Cập nhật lúc: {{ $teammate->updated_at->format('d/m/Y') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <h3 class="text-lg font-medium text-gray-800 mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                    </svg>
                    Thông tin liên hệ
                </h3>
                <p class="bg-gray-50 p-4 rounded-lg text-gray-700">{{ $teammate->contact_info }}</p>
            </div>
            
            <!-- Expectations -->
            @if($teammate->expectations)
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <h3 class="text-lg font-medium text-gray-800 mb-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                    Mong muốn tìm đồng đội
                </h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="whitespace-pre-line text-gray-700">{{ $teammate->expectations }}</p>
                </div>
            </div>
            @endif
            
            <!-- Action Buttons -->
            <div class="mt-8 flex justify-end gap-4 pt-4 border-t border-gray-200">
                @if($teammate->user_id == Auth::id())
                    <a href="{{ route('teammate.edit') }}" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 ease-in-out shadow-md inline-flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Chỉnh sửa
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection