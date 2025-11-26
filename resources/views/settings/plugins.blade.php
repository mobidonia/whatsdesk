@php
    $tabs = [];
    $currentTab = 'default';

    //Emojis
    $emojis = [
        'OPEN AI' => '🤖',
        'Landing page links' => '🔗',
        'Social links' => '👥',
        'Embedded Login setup' => '🔐',
        'Lemonsqueezy configuration' => '🍋',
        'Blog settings' => '📰',
        'Mercado Pago Subscribe configuration' => '💳',
        'Paddle Subscribe configuration' => '🚣',
        'Paddle Billing Subscribe configuration' => '⛵',
        'PayPal Subscribe configuration' => '💰',
        'Paystack Subscribe configuration' => '💸',
        'Razorpay configuration' => '💵',
        'Razorpay subscription configuration' => '💷',
        'Stripe Hosted Checkout configuration' => '💴',
        'Notification settings' => '🔔',
        'Twilio SMS Notification settings' => '📱',
        'WhatsApp Notifications' => '💬',
        'Links that appear in the side menu of the vendor navigation' => '📑',
        'Menu Links' => '📑',
        'One Signal' => '📡',
        'Google Maps' => '🗺️',
        'Sending messages settings' => '✉️',
        'Mobile app settings' => '📱',
        'Tools' => '🛠️',
        'Pusher live notifications' => '📢',
        'Share this' => '🔄',
        'Credit costs' => '💰'
    ];
    
    // Group fields by separator
    foreach ($fields as $field) {
        if (isset($field['separator'])) {
            $currentTab = $field['separator'];
        }
        $tabs[$currentTab][] = $field;

       
        
    }

     //If there is a tab with the name 'Credit costs', he needs to be the first one
     if(isset($tabs['Credit costs'])){
        $tabs = array_merge(['Credit costs' => $tabs['Credit costs']], $tabs);
       // unset($tabs['Credit costs']);
    }

    //Loop through the tabs and make sure that only the first one has a separator
    foreach($tabs as $tabName => $tabFields){
        
        //Loop through the fields and check if the separator is set
        foreach($tabFields as $key => $field){

            //If the field is the first one and has a separator, add it to the tabs
            if($key == 0 && isset($field['separator'])){
                //Add the icon to the tab name
                if(isset($emojis[$field['separator']])) {
                    $tabs[$tabName][$key]['separator'] = $emojis[$field['separator']].' '.$field['separator'];
                }
            }

            if($key != 0 && isset($field['separator'])){
                unset($tabs[$tabName][$key]);
            }
        }
    }

   
    
@endphp

<div class="d-flex">
    {{-- Tabs navigation --}}
    <div class="flex-shrink-0" style="max-height: 80vh; overflow-y: auto;">
        <div class="nav flex-column nav-pills mr-3" role="tablist" >
            @foreach($tabs as $tabName => $tabFields)
                <a class="nav-link @if($loop->first) active @endif" 
                id="tab-{{ Str::slug($tabName) }}-tab"
                data-toggle="pill" 
                href="#tab-{{ Str::slug($tabName) }}" 
                role="tab"
                aria-controls="tab-{{ Str::slug($tabName) }}"
                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                    @if(isset($emojis[$tabName])){{ $emojis[$tabName] }} @endif
                    {{ str_replace(['configuration', 'settings','Setup'], '', $tabName) }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Tabs content --}}
    <div class="tab-content flex-grow-1">
        @foreach($tabs as $tabName => $tabFields)
            <div class="p-3 tab-pane fade @if($loop->first) show active @endif" 
                 id="tab-{{ Str::slug($tabName) }}" 
                 role="tabpanel"
                 aria-labelledby="tab-{{ Str::slug($tabName) }}-tab">
                @include('partials.fields', ['fields' => $tabFields])
            </div>
        @endforeach
    </div>
</div>