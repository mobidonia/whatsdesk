@extends('general.index', $setup)

@section('customheading')
    @if (config('wpbox.google_maps_enabled',true))
      @include('wpbox::campaigns.map',$item)
    @endif
   <div class="mt-4">
    @include('wpbox::campaigns.infoboxes',$item)
   </div>
   
@endsection

@section('thead')
    <th>{{ __('Phone') }}</th>
    <th>{{ __('Name') }}</th>
    <th>{{ __('Message') }}</th>
    <th>{{ __('Status') }}</th>
@endsection
@section('tbody')
    @foreach ($setup['items'] as $item)
      @isset($item->contact)
        <tr>
          <td>{{ $item->contact->phone }}</td>
          <td>{{ $item->contact->name }}</td>
          <td>{{ $item->value }}</td>
          <td>
              @if ( $item->status==0)
              <span class="badge badge-dot mr-4">
                  <i class="bg-warning"></i>
                  <span class="status">{{ __('PENDING SENT')}} {{ __( $item->error)}}</span>
                </span> 
              @elseif ( $item->status==1)
              <span class="badge badge-dot mr-4">
                  <i class="bg-warning"></i>
                  <span class="status">{{ __('SENT')}} {{ __( $item->error)}}</span>
                </span>
              @elseif( $item->status==2)
                  {{ __('SENT')}} 
              @elseif( $item->status==3)
              <span class="badge badge-dot mr-4">
                  <i class="bg-info"></i>
                  <span class="status">{{ __('DELIVERED')}} {{ __( $item->error)}}</span>
                </span>
              @elseif( $item->status==4)
              <span class="badge badge-dot mr-4">
                  <i class="bg-success"></i>
                  <span class="status">{{ __('READ')}} {{ __( $item->error)}}</span>
                </span>
              @elseif( $item->status==5)
              <span class="badge badge-dot mr-4">
                  <i class="bg-danger"></i>
                  <span class="status">{{ __('FAILED')}} : {{ __( $item->error)}} </span>
                </span>
              @endif
          </td>
        </tr>
      @endisset
         
    @endforeach
@endsection