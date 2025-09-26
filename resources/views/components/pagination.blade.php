@props(['paginator'])

@if($paginator->hasPages())
<div class="flex items-center justify-between">
    <div>
        <p class="text-sm text-gray-700">
            Hiển thị từ <span class="font-medium">{{ $paginator->firstItem() }}</span>
            đến <span class="font-medium">{{ $paginator->lastItem() }}</span>
            trong <span class="font-medium">{{ $paginator->total() }}</span> kết quả
        </p>
    </div>
    <div class="flex items-center space-x-2">
        @if($paginator->onFirstPage())
            <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-md">Trước</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" 
               class="px-3 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md">Trước</a>
        @endif
        
        <div class="flex items-center space-x-1">
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                
                // Xác định phạm vi trang hiển thị (tối đa 5 nút phân trang)
                $startPage = max(1, $currentPage - 2);
                $endPage = min($lastPage, $startPage + 4);
                
                if ($endPage - $startPage < 4 && $lastPage > 4) {
                    if ($startPage == 1) {
                        $endPage = min(5, $lastPage);
                    } else {
                        $startPage = max(1, $lastPage - 4);
                    }
                }
            @endphp
            
            @if($startPage > 1)
                <a href="{{ $paginator->url(1) }}&{{ http_build_query(request()->except('page')) }}" 
                   class="px-3 py-1 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-md">1</a>
                
                @if($startPage > 2)
                    <span class="px-2 text-gray-500">...</span>
                @endif
            @endif
            
            @for($i = $startPage; $i <= $endPage; $i++)
                @if($i == $currentPage)
                    <span class="px-3 py-1 bg-blue-600 text-white rounded-md">{{ $i }}</span>
                @else
                    <a href="{{ $paginator->url($i) }}&{{ http_build_query(request()->except('page')) }}" 
                       class="px-3 py-1 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-md">{{ $i }}</a>
                @endif
            @endfor
            
            @if($endPage < $lastPage)
                @if($endPage < $lastPage - 1)
                    <span class="px-2 text-gray-500">...</span>
                @endif
                
                <a href="{{ $paginator->url($lastPage) }}&{{ http_build_query(request()->except('page')) }}" 
                   class="px-3 py-1 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-md">{{ $lastPage }}</a>
            @endif
        </div>
        
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" 
               class="px-3 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-md">Tiếp</a>
        @else
            <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-md">Tiếp</span>
        @endif
    </div>
</div>
@endif 