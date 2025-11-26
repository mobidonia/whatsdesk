
@extends('layouts.app', ['title' => __('Apps')])

@section('content')
<div class="header bg-gradient-default pb-6 pt-5 pt-md-8"">
</div>
<div class="container-fluid mt--7">
    <div class="row">
       
        <div class="col-xl-3 flex-row">
                <div class="nav-wrapper flex-row">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <ul class="nav nav-pills nav-fill flex-column" id="tabs-icons-text" role="tablist">

                                        @foreach ($separators as $separator)
                                            <li class="nav-item pb-2">
                                                <a class="nav-link mb-sm-3 mb-md-0 @if ($loop->first) active @endif" id="{{$separator['snake']."_tab"}}" data-toggle="tab" href="#{{$separator['snake']}}" role="tab" aria-controls="{{$separator['snake']}}" aria-selected="true">{{ $separator['icon'] }} {{$separator['name']}}</a>
                                            </li>
                                        @endforeach
                                        
                
                                        
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
        </div>
        <div class="col-xl-9 mt-3">
            @include('partials.flash')
            <form id="restorant-apps-form" method="post" autocomplete="off" enctype="multipart/form-data" action="{{ route('admin.owner.updateApps',$company) }}">
                @csrf
                @method('put')
                    <div class="card shadow">
                    
                        <div class="card-body">
                        
                                <div class="tab-content" id="myTabContent">
                                    @foreach ($separators as $separator)
                                        <div class="tab-pane fade show @if ($loop->first) active @endif" id="{{ $separator['snake'] }}" role="tabpanel" aria-labelledby="{{ $separator['snake'] }}">
                                            @include('partials.fields',['fields'=>$separator['fields']])
                                        </div>
                                    @endforeach
                                
                                    
                                </div>
                                
                        </div>
                    
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection