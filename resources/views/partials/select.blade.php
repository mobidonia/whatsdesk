<div id="form-group-{{ $id }}" class="form-group {{ $errors->has($id) ? ' has-danger' : '' }}  @isset($class) {{$class}} @endisset">

    @isset($separator)
    @if (is_string($separator)&&!is_array($separator))
        <br />
        <h4 class="display-4 mb-0">{{ $separator }}</h4>
        <hr />
    @endif
@endisset

    <label class="form-control-label">{{ __($name) }}</label><br />

    <select @isset($multiple) multiple=" {{ "multiple" }}"   @endisset    @isset($disabled) {{ "disabled" }} @endisset  class="form-control form-control-alternative @isset($classselect) {{$classselect}} @endisset"  name="{{ $id }}" id="{{  $id }}">
        @if (!isset($multiple))
            <option disabled selected value> {{ __('Select')." ".__($name)}} </option>
        @endif
        @foreach ($data as $key => $item)

            @if (is_array(__($item)))
                <option value="{{ $key }}">{{ $item }}</option>
            @else
                @if (old($id)&&old($id).""==$key."")
                    <option  selected value="{{ $key }}">{{ __($item) }}</option>
                @elseif (isset($value)&&trim(strtoupper($value.""))==trim(strtoupper($key."")))
                    <option  selected value="{{ $key }}">{{ __($item) }}</option>
                @elseif (app('request')->input($id)&&strtoupper(app('request')->input($id)."")==strtoupper($key.""))
                    <option  selected value="{{ $key }}">{{ __($item) }}</option>
                @elseif (isset($multiple) && isset($multipleselected) && in_array($key,$multipleselected,false))
                    <option  selected value="{{ $key }}">{{ __($item) }}</option>
                @else
                    <option value="{{ $key }}">{{ __($item) }}</option>
                @endif
            @endif
            
        @endforeach
    </select>


    @isset($additionalInfo)
        <small class="text-muted"><strong>{!! $additionalInfo !!}</strong></small>
    @endisset
    @if ($errors->has($id))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first($id) }}</strong>
        </span>
    @endif
</div>
