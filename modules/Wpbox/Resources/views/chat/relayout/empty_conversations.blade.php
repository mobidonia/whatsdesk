<div  class="card shadow d-flex justify-content-center align-items-center w-100 p-6 border-radius-lg h-100" >
    <div class="d-flex justify-content-center align-items-center">
        <div class="d-flex justify-content-center align-items-center flex-column">
            <dotlottie-player src="https://lottie.host/ff90657b-c74a-4325-9ac9-639e01d1e9de/F9NKBIxQ9k.lottie" background="transparent" speed="1" style="width: 300px; height: 300px; opacity: 0.6" loop autoplay></dotlottie-player>
            <h2>{{__('Start your first chat')}}</h2>
            <p class="text-muted mb-4">{{__('Start a chat by sending a template message to contact.')}}</p>
            <a href="{{ route('contacts.index') }}" class="btn btn-primary">{{__('Go to Contacts')}}</a>
        </div>
    </div>
</div>