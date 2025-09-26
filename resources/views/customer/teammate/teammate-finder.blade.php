@extends('layout.main-customer')

@section('title', 'Tìm đồng đội')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Main Content -->
    <div class="w-full bg-white rounded-lg shadow-lg p-6 max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8 border-b border-gray-200 pb-4">
            <h1 class="text-3xl font-bold text-gray-800">Tìm đồng đội</h1>
            @if(App\Models\TeammateFinder::where('user_id', Auth::id())->where('is_active', true)->exists())
                <a href="{{ route('teammate.edit') }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-200 ease-in-out shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Chỉnh sửa hồ sơ
                </a>
            @else
                <a href="{{ route('teammate.create') }}" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-200 ease-in-out shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Tạo hồ sơ lông thủ
                </a>
            @endif
        </div>

        <!-- Search and Filter -->
        <div class="mb-8 p-5 bg-gray-50 rounded-xl shadow-inner">
            <form id="search-form" class="flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-2/5">
                    <label for="search-input" class="block mb-2 text-sm font-medium text-gray-700">Tìm kiếm theo tên</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="search-input" name="search" class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200" placeholder="Nhập tên cần tìm...">
                    </div>
                </div>

                <div class="w-full md:w-2/5">
                    <label for="skill-level" class="block mb-2 text-sm font-medium text-gray-700">Trình độ</label>
                    <select id="skill-level" name="skill_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-200 appearance-none bg-white">
                        <option value="all">Tất cả</option>
                        <option value="yếu">Yếu</option>
                        <option value="trung bình yếu">Trung bình yếu</option>
                        <option value="trung bình">Trung bình</option>
                        <option value="trung bình khá">Trung bình khá</option>
                        <option value="khá">Khá</option>
                    </select>
                </div>

                <div class="w-full md:w-1/5 flex items-end">
                    <div class="flex gap-2 w-full">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 ease-in-out flex-1 shadow-md">
                            <span class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Lọc
                            </span>
                        </button>
                        <button type="button" id="clear-filters" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition duration-200 ease-in-out flex-1 shadow-md">
                            <span class="flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Xóa
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Teammate List -->
        <div class="space-y-5">
            @if($teammates->isEmpty())
                <div class="text-center p-10 bg-gray-50 rounded-xl border border-gray-200">
                    <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p class="mt-4 text-gray-500 text-lg">Không tìm thấy hồ sơ nào phù hợp với tìm kiếm.</p>
                </div>
            @else
                @foreach($teammates as $teammate)
                <div class="border border-gray-200 rounded-xl p-5 hover:shadow-lg transition-shadow duration-300 bg-white">
                    <div class="flex flex-col md:flex-row justify-between">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-800">{{ $teammate->full_name }}</h3>
                            <div class="my-3 flex flex-wrap gap-2">
                                <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Trình độ: {{ $teammate->skill_level }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $teammate->play_time ?: 'Thời gian: Linh hoạt' }}
                                </span>
                            </div>
                            <p class="text-gray-600 line-clamp-2 mt-2">
                                {{ Str::limit($teammate->expectations, 150) ?: 'Không có thông tin mong muốn' }}
                            </p>
                        </div>
                        <div class="mt-4 md:mt-0 md:ml-6 flex items-start">
                            <a href="{{ route('teammate.show', $teammate->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200 ease-in-out shadow-md">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $teammates->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
