<div v-for="(message, indexMessage) in messages" class="row mb-4" :class="[ {'justify-content-start': message.is_message_by_contact==1}, {'justify-content-end': message.is_message_by_contact==0},{'text-right': (message.is_message_by_contact==0&& message.is_campign_messages==0)} ]">
    <div class="col-md-12" v-if="indexMessage==0 || momentDay(message.created_at)!=momentDay(messages[indexMessage-1].created_at)">
        <div class="message-separator col-md-12 text-center">
            <span class="badge">@{{ momentDay(message.created_at) }}</span>
        </div>
    </div>
    <div class="col-auto" :style="{ 'max-width': ( message.is_campign_messages==1?'440px':'65%'  )    }">
        <div class="card border-radius-xl"  :class="[ {'bg-white message-contact': message.is_message_by_contact==1}, {'bg-primary !important': message.is_note==1},{'bg-default message-agent': message.is_message_by_contact==0&& message.is_note==0} ] ">
            <div class="card-body py-2 px-3" >
                <img class="mb-2 inChatImage" v-if="message.header_image" :src="message.header_image" />
                <a v-if="message.header_document" :href="message.header_document" target="_blank" type="button" class="btn btn-secondary btn-lg btn-block">{{ __('Document link')}}</a>
                <a v-if="message.header_location" :href="message.header_location" target="_blank" type="button" class="btn btn-secondary btn-lg btn-block">{{ __('See location')}}</a>

                <video v-if="message.header_video" style="max-width: 300px" controls>
                    <source :src="message.header_video" type="video/mp4">
                </video>

                <audio v-if="message.header_audio" controls>
                    <source :src="message.header_audio" type="audio/mpeg">
                </audio>
                
                <h4 v-if="message.header_text" class="mb-2 text-white">@{{ message.header_text }}</h4>

                
                <p  v-html="formatIt(message.value)" class="mb-2 text-left" style="text-align: left !important;" :class="[ {'text-white': message.is_message_by_contact==0} ]" ></p>
                <p v-if="message.original_message.length>0"  v-html="formatIt('{{ __('Original:')}}'+' '+message.original_message)" class="mb-2 small text-right" style="opacity:0.7" :class="[ {'text-white': message.is_message_by_contact==0} ]" ></p>

                <p v-if="message.footer_text" class="text-muted text-xs text-white" style="opacity: 0.8">@{{ message.footer_text }}</p>

                <a :href="button.type=='URL'?button.url:( button.name=='cta_url'?button.parameters.url:( button.type=='reply'?'#':'')) " target="_blank" v-for="(button, indexButton) in parseJSON(message.buttons)" type="button" class="btn btn-secondary btn-lg btn-block">@{{  button.text? button.text:( button.name=='cta_url'?button.parameters.display_text:( button.type=='reply'?button.reply.title:''))  }}</a>

                <div   class="box-sizing: content-box; d-flex text-sm opacity-6 align-items-center" :class="[  {'text-white': message.is_message_by_contact==0} ,  {'justify-content-end': message.is_message_by_contact==0},{'text-right': message.is_message_by_contact==0} ]">
                    <svg class="mr-2" width="12" fill="currentColor"  viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7l233.4-233.3c12.5-12.5 32.8-12.5 45.3 0Z"/></svg>
                    <small> @{{ momentHM(message.created_at) }} </small>
                    <small class="ml-1" v-if="!message.is_message_by_contact&&message.sender_name&&message.sender_name.length>0">- @{{message.sender_name}} </small>
                </div>
            </div>
        </div>
        <div v-if="message.error" class="alert alert-danger" role="alert">
            <span class="alert-icon"><i class="ni ni-bell-55"></i></span>
            @{{ message.error }}
        </div>
    </div>
</div>