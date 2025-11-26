@isset($separator)
    <br />
    <h4 id="sep{{ $id }}" class="display-4 mb-0">{{ __($separator) }}</h4>
    <hr />
@endisset

<div id="form-group-{{ $id }}" class="form-group @isset($class) {{$class}} @endisset">
    <div class="alert alert-info" role="alert">
        <h5>{{ $name }}</h5>
        <p class="mb-0 text-muted text-white">{{ __($text) }}</p>
        @isset($button)
            <a href="{{ $button['link'] }}" class="btn btn-sm btn-default mt-2">{{ $button['text'] }}</a>
        @endisset
    </div>
</div>
