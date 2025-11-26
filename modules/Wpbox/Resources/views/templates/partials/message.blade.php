<div class="card-body" style="justify-content: flex-end; text-align: right; background: url('{{ asset('default/wpbox/bg.png') }}');">
                        

    
        <div class="card" id="previewElement" style="min-width: 18rem; text-align: left; border-top-left-radius: 0;">

            <div class="card-body" style="">
                <img v-if="headerType=='image'" :src="headerImage" class="card-img-top" style="">
                <video v-if="headerType=='video'&&headerVideo.length>4" width="100%" height="200" controls>
                    <source :src="headerVideo" type="video/mp4">
                </video>
                <img v-if="headerType=='pdf'" class="card-img-top" style="" src="{{ asset('default/wpbox/pdf.png') }}" alt="Card image cap">
                <h4 v-if="headerType=='text'" class="card-title mb-2">@{{headerReplacedWithExample}}</h4>
                <pre class="card-text" style="font-family: inherit; margin: 0; white-space: pre-wrap; background: none; border: none;">@{{bodyReplacedWithExample}}</pre>
                <span class="text-muted text-xs">@{{footerText}}</span>  
            </div>
            <div class="card" style="min-width: 18rem; text-align: center;">
                <div  v-for="(v, index) in quickReplies" class="card-body" style="padding:1rem">
                    <img style="height: 18px" src="{{ asset('uploads').'/default/wpbox/reply.png' }}"/>
                    <span class="" style="color: #00a5f4" >@{{quickReplies[index]}}</span>
                </div>
            </div>

            <div class="card" style="min-width: 18rem; text-align: center;">
                <div  v-for="(vw, index) in vistiWebsite" class="card-body" style="padding:1rem">
                    <img style="height: 18px" src="{{ asset('uploads').'/default/wpbox/open.png' }}"/>
                    <span class="" style="color: #00a5f4" >@{{vistiWebsite[index].title}}</span>
                </div>
            </div>

            <div v-if="hasPhone" class="card" style="min-width: 18rem; text-align: center;">
                <div  class="card-body" style="padding:1rem">
                    <img style="height: 18px" src="{{ asset('uploads').'/default/wpbox/open.png' }}"/>
                    <span class="" style="color: #00a5f4" >@{{callPhoneButtonText}}</span>
                </div>
            </div>

            <div  v-if="copyOfferCode" class="card" style="min-width: 18rem; text-align: center;">
                <div  class="card-body" style="padding:1rem">
                    <img style="height: 18px" src="{{ asset('uploads').'/default/wpbox/open.png' }}"/>
                    <span class="" style="color: #00a5f4" >{{__('Copy offer code')}}</span>
                </div>
            </div>
            
        </div>

    


</div>