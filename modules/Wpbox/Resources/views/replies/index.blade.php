@extends('general.index', $setup)
@section('thead')
    <th>{{ __('Name') }}</th>
    <th>{{ __('Type') }}</th>
    <th>{{ __('crud.actions') }}</th>
@endsection
@section('tbody')
    @foreach ($setup['items'] as $item)
        <tr>
            <td>{{ $item->name }}</td>
            @if (isset($item->template_id))
                <td>{{ $item->bot_type==2?__('Template bot: On exact match'):__('Template bot: When message contains') }}</td>
            @else
                <td>{{ $item->type==1?__('Quick reply'):($item->type==2?__('Text bot: On exact match'):($item->type==4?__('Text bot: Welcome'):__('Text bot: When message contains'))) }}</td>
            @endif
           
            @if (isset($item->template_id))
                <td>
                

                    <!-- ANALYTICS -->
                    <a href="{{ route('campaigns.show',$item->id) }}" class="btn btn-info btn-sm">
                        {{ __('Analytics') }}
                    </a>
                    
                    <!-- Activate and Deactivate -->
                    @if ($item->is_bot_active)
                        <a href="{{ route('campaigns.deactivatebot',$item->id) }}" class="btn btn-warning btn-sm">
                            {{ __('Deactivate') }}  
                        </a>
                    @else
                        <a href="{{ route('campaigns.activatebot',$item->id) }}" class="btn btn-success btn-sm">
                            {{ __('Activate') }}
                        </a>
                    @endif


                    <!-- DELETE -->
                    <a href="{{ route('campaigns.delete',$item->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this bot?')">
                        {{ __('Delete') }}
                    </a>
  
        
                    
                </td>
            @else
                <td>
                    <!-- EDIT -->
                    <a href="{{ route('replies.edit',['reply'=>$item->id]) }}" class="btn btn-primary btn-sm">
                        <i class="ni ni-ruler-pencil"></i>
                    </a>

                    <!-- DELETE -->
                    <a href="{{ route('replies.delete',['reply'=>$item->id]) }}" class="btn btn-danger btn-sm">
                        <i class="ni ni ni-fat-remove"></i>
                    </a>
                </td>
            @endif
        </tr> 
    @endforeach
@endsection