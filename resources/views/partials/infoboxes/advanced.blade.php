<div class="row">

    @foreach ($collection as $item)
    @if ($item['main_value'])
    <div class="col-xl-3 col-md-6 mt-4">

        <div class="card card-stats">
           @if (isset($item['href']))
               <a href="{{ $item['href'] }}">
           @endif
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">{{ __($item['title']) }}</h5>
                        <span class="h2 font-weight-bold mb-0">{{ $item['main_value'] }}</span>
                    </div>
                    <div class="col-auto">
                        <div class="icon icon-shape {{ $item['icon_color'] }} text-white rounded-circle shadow">
                            <i class="ni  {{ $item['icon'] }}"></i>
                        </div>
                    </div>

                </div>
                <p class="mt-3 mb-0 text-sm">
                    <span class="{{ $item['sub_value_color'] }} mr-2">{{ $item['sub_value'] }}</span>
                    <span class="text-nowrap">{{ __($item['sub_title']) }}</span>
                </p>
            </div>
            @if (isset($item['href']))
               </a>
           @endif
        </div>
    </div>
    @endif
    @endforeach
</div>