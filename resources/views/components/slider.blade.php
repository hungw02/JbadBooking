<section class="slider-container">
    <div class="main-slider">
        @foreach($sliderItems as $index => $item)
            <div class="slider-content {{ $index === 0 ? 'active' : '' }}" id="slide{{ $index + 1 }}">
                <div class="slider-image">
                    @if($item['type'] === 'promotion' && $item['data']->image)
                        <img src="{{ asset($item['data']->image) }}" alt="{{ $item['data']->name }}">
                    @else
                        <img src="{{ asset($item['data']->image) }}" alt="{{ $item['data']->name }}">
                    @endif
                </div>
                <div class="slider-text animate__animated {{ $index === 0 ? 'animate__fadeInUp' : '' }}">
                    <h2>{{ $item['data']->name }}</h2>
                    <p>{{ $item['data']->description }}</p>
                    @if($item['type'] === 'promotion' && $item['data']->start_date)
                        <span class="promotion-badge">Bắt đầu từ {{ $item['data']->start_date->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Slide phụ -->
    <div class="slide-tabs">
        @foreach($sliderItems as $index => $item)
            <div class="slide-tab" data-slide="slide{{ $index + 1 }}">
                @if($item['type'] === 'promotion' && $item['data']->image)
                    <img src="{{ asset($item['data']->image) }}" alt="{{ $item['data']->name }}">
                @else
                    <img src="{{ asset($item['data']->image) }}" alt="{{ $item['data']->name }}">
                @endif
            </div>
        @endforeach
    </div>

    <!-- Slide navigation dots -->
    <div class="slide-dots">
        @foreach($sliderItems as $index => $item)
            <span class="dot {{ $index === 0 ? 'active' : '' }}" data-slide="slide{{ $index + 1 }}"></span>
        @endforeach
    </div>
</section>