@extends('layouts.app', ['title' => __('Settings')])
@section('admin_title')
    {{__('Site Settings')}}
@endsection
@section('content')
<div class="header  pb-7 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <h1 class="mb-3 mt--3">⚙️ {{__('Settings Management')}}</h1>
          <div class="row align-items-center pt-2">
          </div>
        </div>
    </div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card bg-secondary shadow">
                
                <div class="card-body">

                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if (session('results'))
                        <div class="alert alert-success" role="alert">
                            
                            <?php print_r(session('results')); ?>
                            
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                        <form id="settings" method="post" action="{{ route('admin.settings.update',1)}}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            @method('put')

                            <div class="nav-wrapper">
                                <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
                                    @foreach ($envConfigs as $groupConfig)
                                        <li class="nav-item ">
                                            <a class="nav-link mb-sm-3 mb-md-0  @if ($groupConfig['slug']=="setup") active @endif" id="tabs-icons-text-2-tab" data-toggle="tab" href="#{{$groupConfig['slug']}}" role="tab" aria-controls="tabs-icons-text-2" aria-selected="@if ($groupConfig['slug']=="setup") true @else false @endif"><i class="{{$groupConfig['icon']}}"></i> {{ __ ($groupConfig['name']) }}</a>
                                        </li>
                                    @endforeach
                                    <li class="nav-item">
                                        <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-2-tab" data-toggle="tab" href="#tabs-icons-text-2" role="tab" aria-controls="tabs-icons-text-2" aria-selected="false"><i class="ni ni-image mr-2"></i>{{ __ ('Images') }}</a>
                                    </li>



                                    


                                    <li class="nav-item">
                                        <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-2-tab" data-toggle="tab" href="#cssjs" role="tab" aria-controls="tabs-icons-text-2" aria-selected="false"><i class="ni ni-palette mr-2"></i>{{ __ ('CSS & JS') }}</a>
                                    </li>

                                   




                                </ul>
                            </div>
                            <br/>
                                <div class="tab-content" id="myTabContent">
                                
                                    <div class="tab-pane fade" id="tabs-icons-text-2" role="tabpanel" aria-labelledby="tabs-icons-text-2-tab">
                                        <div class="row">
                                            <?php
                                                $images=[
                                                    ['name'=>'site_logo','label'=>__('Site Logo'),'value'=>config('settings.logo'),'style'=>'width: 200px;'],
                                                    ['help'=>"256,256px",'name'=>'favicons','label'=>__('Favicon'),'value'=>'/apple-touch-icon.png','style'=>'width: 120px; height: 120px;']
                                                 ]; 

                                            ?>
                                            @foreach ($images as $image)
                                                <div class="col-md-4">
                                                    @include('partials.images',$image)
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>



                                    <div class="tab-pane fade" id="cssjs" role="tabpanel" aria-labelledby="cssjs">
                                        @include('partials.textarea',['id'=>'jsfront','name'=>'JavaScript - Frontend','placeholder'=>'JavaScript - Frontend','value'=>$jsfront, 'required'=>false])
                                        @include('partials.textarea',['id'=>'jsback','name'=>'JavaScript - Backend','placeholder'=>'JavaScript - Backend','value'=>$jsback, 'required'=>false])
                                        @include('partials.textarea',['id'=>'cssfront','name'=>'CSS - Frontend','placeholder'=>'CSS - Frontend','value'=>$cssfront, 'required'=>false])
                                        @include('partials.textarea',['id'=>'cssback','name'=>'CSS - Backend','placeholder'=>'CSS - Backend','value'=>$cssback, 'required'=>false])
                                    </div>

                                    @foreach ($envConfigs as $groupConfig)
                                        <div class="tab-pane  @if ($groupConfig['slug']=="setup") active @else fade @endif " id="{{ $groupConfig['slug'] }}"  role="tabpanel" aria-labelledby="{{ $groupConfig['slug'] }}">
                                            
                                            @if($groupConfig['slug']!="plugins")
                                                <div class="">
                                                    @include('partials.fields',['fields'=>$groupConfig['fields']])
                                                </div>
                                            @endif
                                            @if($groupConfig['slug']=="plugins")
                                                <div class="">
                                                    @include('settings.plugins',['fields'=>$groupConfig['fields']])
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach


                            </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<br/><br/>
</div>
@endsection
@section('js')
    <script>
        $('#settings').submit(function() {
            $('form textarea').each(function(){
                this.value = this.value.replace(/script/g, 'tagscript');
            });
        });
    </script>
@endsection