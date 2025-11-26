<div class="card card-stats mb-4 mb-xl-0 mt-2">
    <div class="card-body">
        <div class="row">
            <div class="col">
                <h5 class="card-title text-uppercase text-muted mb-0">{{ __($title) }}</h5>
                <span class="h2 font-weight-bold mb-0">{{ isset($isMoney)&&$isMoney?money($value, config('settings.cashier_currency'),config('settings.do_convertion')):$value }}</span>
            </div>
            @if (isset($icon))
                <div class="col-auto">
                    <div class="icon icon-shape {{ isset($icon_color)?$icon_color:""}} bg-yellow text-white rounded-circle shadow">
                        <i class="{{$icon}}"></i>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>