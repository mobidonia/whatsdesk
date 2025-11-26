<div class="form-group{{ $errors->has($id) ? ' has-danger' : '' }}">
    
    <label class="form-control-label mr-2" for="{{ $id }}">{{ __($name) }} </label>
    <label class="custom-toggle" style="float: right">
        <input type="checkbox"  name="{{ $id }}" id="{{ $id }}" <?php if($checked){echo "checked";}?>>
        <span class="custom-toggle-slider rounded-circle"    @isset($dlo)  data-label-off="{{ __($dlo) }}" @endisset   @isset($dloff)  data-label-off="{{ __($dloff) }}" @endisset @isset($dlon) data-label-on="{{ __($dlon) }}"  @endisset        ></span>
    </label>
    @isset($additionalInfo)
        <br /><small class="text-muted"><strong>{{ __($additionalInfo) }}</strong></small>
    @endisset
    @if ($errors->has($id))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first($id) }}</strong>
        </span>
    @endif
</div>