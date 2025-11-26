@if ($variables!=null)
   
@foreach ($variables as $key => $itemBox)
    <h2>{{ __(ucfirst ($key)    ) }}</h2> 
    @if ($key=="header"||$key=="body")
        @foreach ($itemBox as $item)
            @include('partials.input',['onvuechange'=>"setPreviewValue",'id'=> "paramvalues[".$key."][".$item['id']."]",'name'=>__('Variable')." ".$item['id'],'placeholder'=>$item['exampleValue'], 'value'=>$item['exampleValue'], 'required'=>false])
            @include('partials.select',['id'=>"parammatch[".$key."][".$item['id']."]",'name'=>__('Match with a contact field'),'data'=>$contactFields, 'required'=>true,'value'=>"-2",'additionalInfos'=>"Use contact filed, as a value for the variable, or use static value."])
        @endforeach
    @elseif ($key=="document")
        @include('partials.input',['id'=> "pdf",'name'=>__('PDF Document'),'placeholder'=>'','type'=>"file", 'required'=>true,'accept'=>"application/pdf"])
    @elseif ($key=="image")
        @include('partials.input',['id'=> "imageupload", "changevue"=>"handleImageUpload" ,'name'=>__('Select image'),'placeholder'=>'','type'=>"file", 'required'=>true,'accept'=>".jpg, .jpeg, .png"])
    @elseif ($key=="video")
        @include('partials.input',['id'=> "imageupload", "changevue"=>"handleVideoUpload" ,'name'=>__('Select video'),'placeholder'=>'','type'=>"file", 'required'=>true,'accept'=>".mp4"])
    @elseif ($key=="buttons")

        @foreach ($itemBox as $button)
            @foreach ($button as $keybtn => $item)
                @if ($item['type']=="URL")
                    <!-- 1 paramters for buttons only, that is why 0 can be used -->
                    @include('partials.input',['prepend'=>$item['exampleValue'],'id'=> "paramvalues[".$key."][".$keybtn."][".$item['id']."]",'name'=>$item['text'],'placeholder'=>"",'value'=>"", 'required'=>false])
                    @include('partials.select',['id'=>"parammatch[".$key."][".$keybtn."][".$item['id']."]",'name'=>__('Match with a contact field'),'data'=>$contactFields, 'required'=>true,'value'=>"-2",'additionalInfos'=>"Use contact filed, as a value for the variable, or use static value."])
             
                @elseif($item['type']=="COPY_CODE")
                    <!-- 1 paramters for buttons only, that is why 0 can be used -->
                    @include('partials.input',['id'=> "paramvalues[".$key."][".$keybtn."][".$item['id']."]",'name'=>$item['text'],'placeholder'=>$item['exampleValue'],'value'=>"", 'required'=>false])
                    @include('partials.select',['id'=>"parammatch[".$key."][".$keybtn."][".$item['id']."]",'name'=>__('Match with a contact field'),'data'=>$contactFields, 'required'=>true,'value'=>"-2",'additionalInfos'=>"Use contact filed, as a value for the variable, or use static value."])
             
                @endif
            @endforeach
        @endforeach
       
    @endif
    
    
   
    <hr />
@endforeach
@endif
