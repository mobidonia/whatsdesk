<div class="card-body" style="justify-content: flex-end; text-align: right; background: url('{{ asset('default/wpbox/bg.png') }}');">
                        
    @if($selectedTemplate)
    
        <div class="card" id="previewElement" style="min-width: 18rem; text-align: left; border-top-left-radius: 0;">
            @foreach ($selectedTemplateComponents as $component)
                @if ($component['type']=="HEADER"&&$component['format']=="DOCUMENT")
                    <img class="card-img-top" style="" src="{{ asset('default/wpbox/pdf.png') }}" alt="Card image cap">
                @endif
                @if ($component['type']=="HEADER"&&$component['format']=="IMAGE")
                    <img :src="imagePreview" class="card-img-top" style="">
                @endif

                @if ($component['type']=="HEADER"&&$component['format']=="VIDEO")
                    <video width="100%" height="200" controls>
                        <source src="{{$component['example']['header_handle'][0]}}" type="video/mp4">
                    </video>
                @endif

                
           
             @endforeach
        
            <div class="card-body" style="">
                @foreach ($selectedTemplateComponents as $component)
                    @if ($component['type']=="HEADER"&&$component['format']=="TEXT")
                        <h4 class="card-title mb-2">{{ str_replace('{{','{{header_',$component['text'])  }}</h4>
                    @elseif ($component['type']=="FOOTER")
                        <span class="text-muted text-xs">{{ $component['text']  }}</span>
                    @endif
                    @if ($component['type']=="BODY")
                        <pre class="card-text" style="font-family: inherit; margin: 0; white-space: pre-wrap; background: none; border: none;">{{ str_replace(['{{'], ['{{body_'], $component['text']) }}</pre>
                    @endif
                @endforeach
            </div>
            
        </div>
        @foreach ($selectedTemplateComponents as $component)
        @if ($component['type']=="BUTTONS")
        @foreach ($component['buttons'] as $button)
            <div class="card" style="min-width: 18rem; text-align: center;">
                <div class="card-body" style="padding:1rem">
                    @if ($button['type']=="URL")
                        <img style="height: 18px" src="{{ asset('uploads').'/default/wpbox/open.png' }}"/>
                    @else
                        <img style="height: 18px" src="{{ asset('uploads').'/default/wpbox/reply.png' }}"/>
                    @endif
                    
                    <span class="" style="color: #00a5f4" >{{ $button['text'] }}</span>
                </div>
            </div>
            @endforeach
       @endif
        @endforeach
    @endif
    


</div>