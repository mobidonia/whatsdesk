@extends('general.index', $setup)
@section('thead')
    <th>{{ __('Name') }}</th>
    <th>{{ __('Status') }}</th>
    <th>{{ __('Category') }}</th>
    <th>{{ __('Language') }}</th>
    <th>{{ __('Actions') }}</th>
@endsection
@section('tbody')
    @foreach ($setup['items'] as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>
                @if ($item->status == 'APPROVED') 
                    <span class="badge badge-success">{{ __($item->status) }}</span>
                @else
                    <span class="badge badge-warning">{{ __($item->status) }}</span>
                @endif
            </td>
            <td>{{ __($item->category) }}</td>
            <td>{{ __($item->language) }}</td>
            <td>
                <form action="{{ route('templates.destroy', $item->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submitt" class="btn btn-outline-danger btn-sm" onclick="return confirm('{{ __('Are you sure you want to delete this template?') }}')">{{ __('Delete') }}</button>
                </form>
            </td>
        </tr> 
    @endforeach
@endsection