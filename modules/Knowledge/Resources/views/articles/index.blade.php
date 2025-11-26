@extends('general.index', $setup)
@section('thead')
    <th>{{ __('Article') }}</th>
    <th>{{ __('Category') }}</th>
    <th>{{ __('Status') }}</th>
    <th>{{ __('Views') }}</th>
    <th>{{ __('Date') }}</th>
    <th>{{ __('Actions') }}</th>
@endsection
@section('tbody')
@foreach ($setup['items'] as $item)
    <tr>
        <td style="width: 35%">
            <div class="d-flex align-items-center">
                <div>
                    <a href="{{ route('knowledgebase.articles.edit', ['article' => $item->id]) }}" class="text-decoration-none">
                        <strong>{{ $item->title }}</strong>
                    </a>
                    @if($item->excerpt)
                        <div class="text-muted small">{{ Str::limit($item->excerpt, 100) }}</div>
                    @endif
                    <div class="mt-1">
                        @if($item->is_featured)
                            <span class="badge bg-warning text-dark">{{ __('Featured') }}</span>
                        @endif
                        <span class="badge bg-light text-dark">{{ $item->formatted_read_time }}</span>
                    </div>
                </div>
            </div>
        </td>
        <td>
            @if($item->category)
                <span>{{ $item->category->name }}</span>
            @else
                <span class="text-muted">{{ __('No Category') }}</span>
            @endif
        </td>
        <td>
            <span class="badge text-white bg-{{ $item->status === 'published' ? 'success' : 'warning' }}">
                {{ ucfirst($item->status) }}
            </span>
        </td>
        <td>
            <div class="d-flex align-items-center">
                <i class="fas fa-eye text-muted me-1"></i>
                <span>{{ number_format($item->views_count) }}</span>
            </div>
        </td>
        <td>
            <div>{{ $item->created_at->format('M d, Y') }}</div>
            <small class="text-muted">{{ $item->created_at->format('h:i A') }}</small>
        </td>
        <td>
            <div class="btn-group" role="group">
                <a href="{{ route('knowledgebase.articles.edit', ['article' => $item->id]) }}" class="btn btn-sm btn-primary">
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('knowledgebase.articles.clone', ['article' => $item->id]) }}" class="btn btn-sm btn-info">
                    {{ __('Clone') }}
                </a>
                <a href="{{ route('knowledgebase.articles.delete', ['article' => $item->id]) }}" 
                   class="btn btn-sm btn-danger" 
                   onclick="return confirm('{{ __('Are you sure you want to delete this article?') }}')">
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
