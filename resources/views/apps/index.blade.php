@extends('layouts.app', ['title' => __('Settings'), 'hideActions'=>true])
@section('js')
<script src="https://cdn.paddle.com/paddle/paddle.js"></script>
<script>
    "use strict";
    function showPlugins(filterPlugins){
        $('.plugin-all').hide();
        var toShow='.plugin-'+filterPlugins;
        $(toShow).show();
    }
</script>
@endsection
@section('content')
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
</div>
<div class="container-fluid mt--9">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h3 class="mb-0">{{ __('Apps') }}</h3>
                            
                        </div>
                        @if (config('settings.is_demo') | config('settings.is_demo')) 
                        <div class="col-6 text-right">
                            <a  onclick="alert('Disabled in demo')" class="btn btn-success text-white">{{ __('Upload plugin') }}</a>
                        </div>
                        @else
                            <div class="col-6 text-right">
                                <a  onclick="$('#appupload').click();" class="btn btn-success text-white">{{ __('Upload plugin') }}</a>
                            </div>
                        @endif
                        
                    </div>
                </div>
                <div class="card-body">
                    
                   
                    
                    @include('partials.flash')
                    <form method="post" enctype="multipart/form-data">
                        @csrf
                        <div style="display: none">
                            <input name="appupload" type="file" class="" id="appupload" accept=".zip,.rar,.7zip"   onchange="form.submit()">
                        </div>
                    </form>

                    <div class="row">
                        @if(empty($apps))
                        <p>
                            {{ __("There are no apps at the moment")}}
                        </p>
                        @endif
                        @foreach ($apps as $app)
                        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mt-3 plugin-all @if ($app->installed) {{ 'plugin-installed'}} @endif <?php  foreach ($app->category as $cat){echo "plugin-".$cat." ";} ?>">
                            <div class="card" style="width: 18rem;">
                                <img class="card-img-top" src="{{ $app->image }}" alt="{{ $app->name }}">
                                <div class="card-body">
                                <h5 class="card-title">{{ $app->name }} - {{ $app->price }} @if ($app->installed)<span class="small text-green">{{ __('installed')}} v{{$app->version}}</span>@endif</h5> @if ($app->installed&&!$app->updateAvailable) <span class="badge badge-success">{{ __('Latest version')}}</span> @endif
                                <p class="card-text">{{ $app->description }}</p>
                                
                                @if ($app->installed)
                                    <!-- APP IS IN LOCAL SYSTEM -->
                                    <a href="{{ route('admin.settings.index') }}" class="mt-1 btn btn-sm btn-outline-success"><span class="btn-inner--icon"><i class="fas fa-cog"></i></span>   {{ __('Settings')}}</a>
                                    @if ($app->updateAvailable&&isset($app->file))
                                        @if (!config('settings.is_demo',false))
                                            <a target="_blank" href="{{ $app->file }}"  class="mt-1 btn btn-sm btn-outline-primary"><span class="btn-inner--icon"><i class="fas fa-refresh"></i></span> ⬇️  {{ __('New update')}} v{{$app->latestVersion}}</a>
                                        @endif
                                    @endif
                                    @if ($app->updateAvailable&&isset($app->link))
                                        @if (!config('settings.is_demo',false))
                                            <a target="_blank" href="{{ $app->link }}" class="mt-1 btn btn-sm btn-outline-primary"><span class="btn-inner--icon"><i class="fas fa-refresh"></i></span> ⬇️  {{ __('New update')}} v{{$app->latestVersion}}</a>
                                        @endif
                                    @endif
                                    
                                    <!-- @if ($app->price=="Free")
                                        <p class="card-text mt-2">{{__('Price')}}: 0$</p>
                                    @else
                                        <p class="card-text mt-2">{{__('Price')}}: {{ $app->price }}</p>
                                    @endif -->
                                @else
                                
                                    <!-- APP IS NOT IN LOCAL SYSTEM -->
                                    
                                    @if (isset($app->file))
                                        @if (!config('settings.is_demo',false))
                                            <!-- WE HAVE Access to the file. This is all access pass -->
                                            <a href="{{$app->file}}" target="_blank"  class="mt-1 btn btn-sm btn-outline-primary"><span class="btn-inner--icon"><i class="fas fa-download"></i></span>   {{ __('Download') }}</a>  
                                        @endif
                                        
                                    @else
                                        <!-- WE don't HAVE Access to the file.  -->
                                        @if (isset($app->paddle))
                                            @if (isset($app->discount_code))
                                                <a  onclick="openPaddleDiscount('{{$app->paddle}}','{{$app->discount_code}}')" class="mt-1 btn btn-sm btn-outline-primary"><i class="fas fa-shopping-cart"></i></span>  {{ __('Buy with discount')." - ".$app->price }}</a>
                                            @else
                                                <a  onclick="openPaddle({{$app->paddle}})" class="mt-1 btn btn-sm btn-outline-primary"><span class="btn-inner--icon"><i class="fas fa-shopping-cart"></i></span>  {{ __('Buy now')." - ".$app->price }}</a>  
                                            @endif
                                        @elseif(isset($app->link)&&strlen($app->link)>3)
                                            <!-- Outside link like CodeCanyon -->
                                            <a target="_blank" href="{{ $app->link }}" class="mt-1 btn btn-sm btn-outline-primary"><span class="btn-inner--icon"><i class="fas fa-shopping-cart"></i></span> {{ __('Buy now')." - ".$app->price }}</a>
                                        @endif


                                        @if (isset($app->ppfolder))
                                            <a target="_blank"  href="{{$app->ppfolder}}" class="mt-1 btn btn-sm btn-outline-primary"><span class="btn-inner--icon"><i class="fas fa-download"></i></span>  {{ __('Download update') }}</a>  
                                        @endif

                                        
                                        
                                    @endif
                               
                                @endif

                                @if (isset($app->docs))
                                    <a href="{{$app->docs}}" target="_blank" class="mt-1 btn btn-sm btn-outline-info"><span class="btn-inner--icon"><i class="fas fa-file-pdf"></i></span> {{ __('Docs') }}</a>
                                @endif

                                @if (isset($app->video))
                                    <a href="{{$app->video}}" target="_blank" class="mt-1 btn btn-sm btn-outline-danger"><span class="btn-inner--icon"><i class="fas fa-video"></i></span> {{ __('Video') }}</a>
                                @endif

                                @if (isset($app->demo))
                                    <a href="{{$app->demo}}" target="_blank" class="mt-1 btn btn-sm btn-outline-primary"><span class="btn-inner--icon"><i class="fas fa-eye"></i></span> {{ __('Demo') }}</a>
                                @endif
                                
                                </div>
                            </div>  
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection