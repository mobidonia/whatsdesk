@hasrole('owner')

@if(config('settings.app_code_name')=='wpbox')


<div class="mt-4">
    <a href="{{ route('campaigns.create') }}" type="button" class="btn btn-outline-primary ">📢 {{ __('Send campaign')}}</a>
    <a href="{{ route('contacts.create') }}" type="button" class="btn btn-outline-primary ">🪪 {{ __('Create contact')}}</a>
    <a href="{{ route('replies.create') }}" type="button" class="btn btn-outline-primary ">📤 {{ __('Create reply bot')}}</a>
    <a target="_blank" href="https://business.facebook.com/wa/manage/message-templates/" type="button" class="btn btn-outline-primary ">🔖 {{ __('Create template')}}</a>
</div>
    <div class="mb-4">
        @include('partials.infoboxes.advanced',['collection'=>$wpbox])
    </div>

    @if(config('settings.is_demo',false))
    <div class="modal" id="modal-notification" tabindex="-1" role="dialog" aria-labelledby="modal-notification" aria-hidden="true" >
        <div class="modal-dialog modal-danger modal-dialog-centered modal-" role="document">
            <div class="modal-content bg-gradient-danger">
                
                <div class="modal-header">
                    <h6 class="modal-title" id="modal-title-notification"></h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    
                    <div class="py-3 text-center">
                        <i class="ni ni-bell-55 ni-3x"></i>
                        <h4 class="heading mt-4">You should read this!</h4>
                        <p>Hey there! 👋 This is a demo version with a test phone number for you to try out. Just please use it responsibly! 😉</p>
                        <p>The demo resets daily. Feel free to add your phone number as a contact to test messaging - it'll automatically disappear after a few minutes.</p>
                        <p>Want full access? Create your own account to test everything, including Facebook API integration and WhatsApp setup. The system resets hourly for unlimited testing.</p>
                        <p>We've included some premium plugins like AI Chats, Journies, Shopify etc..  that you can purchase separately if you like them! 🚀</p>
                    </div>
                    
                </div>
                
                <div class="modal-footer">
                  
                    <button type="button" class="btn btn-link text-white ml-auto" data-dismiss="modal">OK, I Agree</button>
                </div>
                
            </div>
        </div>
    </div>
    <script>
        window.onload = function () {
            $('#modal-notification').modal('show');
        }
       
    </script>
    @endif
@endif
@endhasrole
