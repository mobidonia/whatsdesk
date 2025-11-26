@extends('general.index', $setup)

@section('cardbody')
    @foreach ($setup['items'] as $item)
    @if ($item->template)
        <a href="{{ route('campaigns.show',$item->id)}}"><h3 class="mb-0">{{__('Campaign')}}: {{ $item->name }}</h3><br />
            @include('wpbox::campaigns.infoboxes',$item)
        <hr /> </a>  
    @endif
       
    @endforeach
    @if (count($setup['items'])==0)
        <div style="display: flex; justify-content: center; width:100%;">
            <div class="text-center">
                <div class="mb-4">
                    <dotlottie-player src="https://lottie.host/ff90657b-c74a-4325-9ac9-639e01d1e9de/F9NKBIxQ9k.lottie" background="transparent" speed="1" style="width: 300px; height: 300px; opacity: 0.6" loop autoplay></dotlottie-player>
                </div>
                <div class="mb-4">
                    <h4 class="text-muted">{{ __('There are no campaigns, send your first one!')}}</h4>
                </div>
                <div>
                    <a href="{{ route('campaigns.create') }}" class="btn btn-lg btn-primary">
                        <i class="fas fa-plus-circle mr-2"></i>{{__('Create your first campaign')}}
                    </a>
                </div>
            </div>
           
        </div>
    @endif
@endsection
@section('js')
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
@endsection