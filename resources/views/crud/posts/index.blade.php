@extends('general.index', $setup)

@section('cardbody')
<div class="container-fluid">
    <div class="row">
    @foreach ($setup['items'] as $item)
        <div class="col-sm-12 col-md-6 col-lg-4 col-xl-4 mb-4">
            <br/>
            <a href="{{ route( (isset($setup['edit_link'])?$setup['edit_link']:"admin.landing.posts.edit"),["post"=>$item->id]) }}">
                <div class="card cardWithShadow cardWithShadowAnimated shadow" data-toggle="tooltip" data-placement="top" title="{{ __('Edit this item')}}">
                    <div class="card-body">
                        @if (strlen($item->image)>2)
                            <div class="imgHolderInCard">
                                <img 
                                    class="image-in-card" 
                                    src='{{ $item->image_link }}'
                                />
                            </div>
                        @endif
                        <h3 class="card-title">{{ $item->title }}</h3>
                        <p class="card-text">{{ $item->description }}</p>
                    </div>
                </div>
            </a>
            <br/>
            <div class="text-center">
                <a onclick="return confirm('Are you sure?')" href="{{ route((isset($setup['delete_link'])?$setup['delete_link']: "admin.landing.posts.delete"),["post"=>$item->id]) }}" class="btn btn-outline-danger btn-sm">{{ __('Delete') }}</a>
            </div>
        </div>
    @endforeach
    </div>
</div>

@endsection