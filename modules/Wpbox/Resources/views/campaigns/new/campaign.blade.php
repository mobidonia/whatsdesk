<div class="campign_elements">
@if (!isset($_GET['contact_id']))
    @if ($isBot)
        @include('partials.input',['id'=>'name','name'=>'Bot name','placeholder'=>'Name for your bot', 'required'=>false])
    @elseif ($isReminder)
        @include('partials.input',['id'=>'name','name'=>'Reminder name','placeholder'=>'Name for your reminder', 'required'=>false])
    @else
        @include('partials.input',['id'=>'name','name'=>'Campaign name','placeholder'=>'Name for your campaign', 'required'=>false])
    @endif 
@endif

        
@include('partials.select',['id'=>'template_id','name'=>'Template','data'=>$templates, 'required'=>true])

@if (isset($_GET['contact_id']))
    @include('partials.select',['id'=>'contact_id','name'=>'Contact','data'=>$contacts, 'required'=>true])
@elseif($isBot)
    <input type="hidden" name="type" value="bot">
    @include('partials.select',['id'=>'reply_type','name'=>'Reply type','value'=>2,'data'=>['2'=>__('Reply bot: On exact match'),'3'=>__('Reply bot: When message contains')], 'required'=>true])  
    @include('partials.input',[ 'name'=>'Trigger', 'id'=>'trigger', 'placeholder'=>'Enter bot reply trigger', 'required'=>false])
@elseif($isAPI)
    <input type="hidden" name="type" value="api">
@elseif($isReminder)
    <input type="hidden" name="type" value="reminder">

    @include('partials.select',['value'=>'0','id'=>'source_id','name'=>'Source','data'=>$sources, 'required'=>true])
    
    <!-- Reminder type - 1: Before event, 2: After event -->
    @include('partials.select',['id'=>'reminder_type','value'=>1,'name'=>'Reminder type','data'=>['1'=>__('Before event'),'2'=>__('After event')], 'required'=>true])
    @include('partials.select',[
        'id'=>'reminder_unit',
        'name'=>'Reminder unit',
        'data'=>[
            'minutes'=>__('Minutes'),
            'hours'=>__('Hours'),
            'days'=>__('Days'),
            'weeks'=>__('Weeks'),
            'months'=>__('Months')
        ],
        'required'=>true
    ])
    @include('partials.input',['placeholder'=>'Enter reminder time','id'=>'reminder_time','name'=>'Reminder time','type'=>'number', 'required'=>true])
    
@else
    @include('partials.select',['id'=>'group_id','name'=>'Contacts','data'=>$groups, 'required'=>false])
    <div class="form-group">
        <label for="example-datetime-local-input" class="form-control-label">{{ __('Schedule send time') }}</label>
        <input class="form-control" type="datetime-local" @isset($_GET['send_time'])
            value="{{$_GET['send_time']}}"
        @endisset id="send_time" name="send_time" min="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i')}}">
        <small class="text-muted"><strong>{{ __('Per client, based on the contact timezone') }}</strong></small>
    </div>
    @include('partials.toggle',['dloff'=>'Schedule send','dlon'=>'Send now','dloff'=>'Schedule send','id'=>'send_now','name'=>'Ignore schedule time and send now', 'checked'=>(isset($_GET['send_now']))])
@endif

<button onclick="submitJustCampign()"  class="btn btn-success mt-4">{{ __('Apply') }}</button>    

</div>