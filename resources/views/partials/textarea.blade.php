@isset($separator)
    <br />
    <h4 class="display-4 mb-0">{{ __($separator) }}</h4>
    <hr />
@endisset
<div class="form-group{{ $errors->has($id) ? ' has-danger' : '' }} @isset($class) {{$class}} @endisset">
    <label class="form-control-label" for="{{ $id }}">{{ __($name) }}</label>
    <textarea  class="form-control form-control-alternative{{ $errors->has($id) ? ' is-invalid' : '' }}" name="{{ $id }}" id="{{ $id }}"  @if (isset($rows)) rows="{{ $rows }}" @else  rows="4" @endif  cols="50">{{ old($id, isset($value)?$value:'') }}</textarea>
    @if ($errors->has($id))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first($id) }}</strong>
        </span>
    @endif
    @isset($additionalInfo)
        <small class="text-muted"><strong>{{ __($additionalInfo) }}</strong></small>
    @endisset
</div>
    
