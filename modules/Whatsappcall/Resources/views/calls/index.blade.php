@extends('general.index', $setup)

@section('thead')
    <th>{{ __('Time') }}</th>
    <th>{{ __('Direction') }}</th>
    <th>{{ __('Status') }}</th>
    <th>{{ __('WA User') }}</th>
    <th>{{ __('Call ID') }}</th>
@endsection

@section('tbody')
    @forelse ($setup['items'] as $call)
        <tr>
            <td>{{ $call->created_at->format('Y-m-d H:i') }}</td>
            <td>{{ strtoupper($call->direction) }}</td>
            <td>{{ $call->status }}</td>
            <td>{{ $call->wa_user_id }}</td>
            <td>{{ $call->wa_call_id ?? '-' }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center py-4">{{ __('No calls yet') }}</td>
        </tr>
    @endforelse
@endsection

