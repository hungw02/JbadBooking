@props(['paginator'])

@if($paginator->hasPages())
<div class="flex items-center justify-between">
    <div>
        <p class="text-sm text-gray-400">
            Hiển thị từ <span class="font-medium text-gray-300">{{ $paginator->firstItem() }}</span>
            đến <span class="font-medium text-gray-300">{{ $paginator->lastItem() }}</span>
            trong <span class="font-medium text-gray-300">{{ $paginator->total() }}</span> kết quả
        </p>
    </div>
    <div class="flex items-center space-x-2">
        @if($paginator->onFirstPage())
            <span class="px-3 py-1 bg-gray-700 text-gray-400 rounded-md border border-gray-600">Trước</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" 
               class="px-3 py-1 bg-cyan-900 text-cyan-300 hover:bg-cyan-800 rounded-md border border-cyan-700">Trước</a>
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
                   class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-md border border-gray-600">1</a>
                
                @if($startPage > 2)
                    <span class="px-2 text-gray-500">...</span>
                @endif
            @endif
            
            @for($i = $startPage; $i <= $endPage; $i++)
                @if($i == $currentPage)
                    <span class="px-3 py-1 bg-cyan-800 text-cyan-100 rounded-md border border-cyan-700">{{ $i }}</span>
                @else
                    <a href="{{ $paginator->url($i) }}&{{ http_build_query(request()->except('page')) }}" 
                       class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-md border border-gray-600">{{ $i }}</a>
                @endif
            @endfor
            
            @if($endPage < $lastPage)
                @if($endPage < $lastPage - 1)
                    <span class="px-2 text-gray-500">...</span>
                @endif
                
                <a href="{{ $paginator->url($lastPage) }}&{{ http_build_query(request()->except('page')) }}" 
                   class="px-3 py-1 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-md border border-gray-600">{{ $lastPage }}</a>
            @endif
        </div>
        
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" 
               class="px-3 py-1 bg-cyan-900 text-cyan-300 hover:bg-cyan-800 rounded-md border border-cyan-700">Tiếp</a>
        @else
            <span class="px-3 py-1 bg-gray-700 text-gray-400 rounded-md border border-gray-600">Tiếp</span>
        @endif
    </div>
</div>
@endif 