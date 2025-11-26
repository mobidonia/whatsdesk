@extends('general.index', $setup)
@section('thead')
    <th>{{ __('Category') }}</th>
    <th>{{ __('Articles') }}</th>
    <th>{{ __('Status') }}</th>
    <th>{{ __('Order') }}</th>
    <th>{{ __('Actions') }}</th>
@endsection
@section('tbody')
@foreach ($setup['items'] as $item)
    <tr>
        <td style="width: 40%">
            <div class="d-flex align-items-center">
                <div>
                    <a href="{{ route('knowledgebase.categories.edit', ['category' => $item->id]) }}" class="text-decoration-none">
                        <strong>{{ $item->name }}</strong>
                    </a>
                    @if($item->description)
                        <div class="text-muted small">{{ Str::limit($item->description, 80) }}</div>
                    @endif
                </div>
            </div>
        </td>
        <td>
            <div>
                <span class="badge bg-primary text-white">{{ $item->published_articles_count ?? 0 }} {{ __('Published') }}</span>
                @if(($item->articles_count ?? 0) > ($item->published_articles_count ?? 0))
                    <span class="badge bg-secondary text-white">{{ ($item->articles_count ?? 0) - ($item->published_articles_count ?? 0) }} {{ __('Draft') }}</span>
                @endif
            </div>
        </td>
        <td>
            <span class="badge text-white bg-{{ $item->is_active ? 'success' : 'secondary' }}">
                {{ $item->is_active ? __('Active') : __('Inactive') }}
            </span>
        </td>
        <td>
            <span class="badge text-black bg-light">{{ $item->sort_order }}</span>
        </td>
        <td>
            <div class="btn-group" role="group">
                <a href="{{ route('knowledgebase.categories.edit', ['category' => $item->id]) }}" class="btn btn-sm btn-primary">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('knowledgebase.categories.clone', ['category' => $item->id]) }}" class="btn btn-sm btn-info">
                    {{ __('Clone') }}
                </a>
                <a href="{{ route('knowledgebase.categories.delete', ['category' => $item->id]) }}" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('{{ __('Are you sure you want to delete this category?') }}')">
                    {{ __('Delete') }}
                </a>
            </div>
        </td>
    </tr> 
@endforeach
@endsection

@push('styles')
<style>
    .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }
    .badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
    }
    .btn-group {
        gap: 0.25rem;
    }
    .btn-group .btn {
        border-radius: 0.25rem !important;
    }
</style>
@endpush
