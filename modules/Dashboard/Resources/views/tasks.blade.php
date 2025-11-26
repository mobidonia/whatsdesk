<br />
<h2>{{ __('Tasks to be done!')}}</h2>
@foreach ($tasks as $task)
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span class="alert-icon"><a target="_blank" href="{{ $task['task_docs']}}" class="btn btn-secondary btn-sm">{{ __('Docs')}}</a></span>
        <span class="alert-text">{{ $task['task']}}</span>
        @if (strlen($task['task_info'])>0)
            <br />
            <span class="alert-text ml-6">{{ $task['task_info']}}</span>
        @endif
       
        <a onclick="return confirm('Have you completed this task?')"  href="?task_done={{$task['id']}}" class="close">
            <span aria-hidden="true">&times;</span>
        </a>
    </div> 
@endforeach

<br />