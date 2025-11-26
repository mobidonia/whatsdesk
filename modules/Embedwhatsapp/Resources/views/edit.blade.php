@extends('layouts.app', ['title' =>  __("Whatsapp Web Widget creator") ])


@section('content')
    <div class="header  pb-8 pt-5 pt-md-8">
    </div>


    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-12">
                @include('partials.flash')
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __("Whatsapp Web Widget creator") }}</h3>
                  
   
                                
                            </div>
                            
                               
                        </div>
                       
                    </div>

                   

                   
                       <div class="card-body">
                            <form action="{{ route('embedwhatsapp.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <!-- Header image partials.images -->
                                @include('partials.images',['image'=>['name'=>'logo','label'=>__('Logo'),'value'=>$widget['logo'],'style'=>'width: 200px;']])

                                <!-- Phone number -->
                                @include('partials.input',['class'=>'col-md-12','name'=>"WhatsApp Phone number",'id'=>'phone_number','type'=>'text','placeholder'=>"Phone number",'required'=>true, 'value'=>$widget['phone_number']])

                                <!-- Header text -->
                                @include('partials.input',['class'=>'col-md-12','name'=>"Header text",'id'=>'header_text','type'=>'text','placeholder'=>"Header text",'required'=>true, 'value'=>$widget['header_text']])

                                <!-- Header subtext -->
                                @include('partials.input',['class'=>'col-md-12','name'=>"Header subtext",'id'=>'header_subtext','type'=>'text','placeholder'=>"Header subtext",'required'=>true, 'value'=>$widget['header_subtext']])

                                <!-- Text Area -->
                                @include('partials.textarea',['class'=>'col-md-12','name'=>"Widget text",'id'=>'widget_text','placeholder'=>"Widget text",'required'=>true, 'value'=>$widget['widget_text']])

                                <!-- Button text -->
                                @include('partials.input',['class'=>'col-md-12','name'=>"Button text",'id'=>'button_text','type'=>'text','placeholder'=>"Button text",'required'=>true, 'value'=>$widget['button_text']])

                                <!-- Widget type -->
                                @include('partials.select',['class'=>'col-md-12','name'=>"Widget type",'id'=>'widget_type','data'=>['1'=>'With start chat button','2'=>'With input field'],'required'=>true, 'value'=>$widget['widget_type']])

                                <!-- Input field placeholder -->
                                @include('partials.input',['class'=>'col-md-12','name'=>"Input field placeholder",'id'=>'input_field_placeholder','type'=>'text','placeholder'=>"Input field placeholder",'required'=>true, 'value'=>$widget['input_field_placeholder']])

                                <!-- Button color -->
                                @include('partials.colorpicker',['class'=>'col-md-12','name'=>"Button color",'id'=>'button_color','placeholder'=>"Button color",'required'=>true, 'value'=>$widget['button_color']])

                                <!-- Header color -->
                                @include('partials.colorpicker',['class'=>'col-md-12','name'=>"Header color",'id'=>'header_color','placeholder'=>"Header color",'required'=>true, 'value'=>$widget['header_color']])
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success ml-3 mt-2" >{{ __('Save')}}</button>
                                </div>
                                
                            </form>
                        </div>
                   
                    
     
         


                </div>
            </div>
            @if (isset($widget['url']))
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __("Embedde widget code") }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                           <div class="col-md-12">
                                <textarea class="form-control" id="code" rows="2" readonly>
<script src="{{$widget['url']}}"></script>
<div id="embed-whatsapp-chat"></div>
                                </textarea>
                                


                                <!-- copy code button -->
                                <button class="btn btn-primary mt-2" onclick="copyToClipboard('code');">{{ __('Copy code') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <script>
            function copyToClipboard(elementId) {
                var aux = document.createElement("input");
                aux.setAttribute("value", document.getElementById(elementId).value);
                document.body.appendChild(aux);
                aux.select();
                document.execCommand("copy");
                document.body.removeChild(aux);
                $.notify("Widget code copied to clipboard",{
                    position: "top right",
                    className: 'success',
                });
                //js.notify('Copyed to clipboaar')
            }
        </script>


        @include('layouts.footers.auth')
        @if (isset($widget['url']))
            <script src="<?php echo $widget['url'];  ?>"></script>
        @endif
        
        <div id="embed-whatsapp-chat"></div>
    </div>
@endsection
