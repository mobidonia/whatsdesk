@extends('general.index', $setup)
@section('thead')
    <th>{{ __('Name') }}</th>
    <th>{{ __('Campaing ID') }}</th>
    <th>{{ __('crud.actions') }}</th>
@endsection
@section('tbody')
    @foreach ($setup['items'] as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->id }}</td>
       
                <td>
                

                    <!-- ANALYTICS -->
                    <a href="{{ route('campaigns.show',$item->id) }}" class="btn btn-info btn-sm">
                        {{ __('Analytics') }}
                    </a>
                    

                    <!-- DELETE -->
                    <a href="{{ route('campaigns.delete',$item->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?')">
                        {{ __('Delete') }}
                    </a>
  
        
                    
                </td>
            
        </tr> 
    @endforeach
@endsection