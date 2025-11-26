<div class="form-group{{ $errors->has($id) ? ' has-danger' : '' }}">
    <br />
    <label class="form-control-label">{{ __($name) }}</label>
    <div class="row">
        
        @foreach ($data as  $select)
            <div class="col-md-4">
                <br />
                <label class="form-control-label">{{ __($select['name']) }}</label>
                <select class="form-control col-sm"  name="{{ $id."[".$select['id']."]" }}" id="{{  $id."[".$select['id']."]" }}">
                    <option disabled selected value> {{ __('Select')." ".$select['name']}} </option>
                    @foreach ($select['data'] as $key => $item)
                        @if (isset($select['value'])&&$key==$select['value'])
                            <option value="{{ $key }}" selected>{{$item }}</option>
                        @else
                            <option value="{{ $key }}">{{$item }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        @endforeach
        
        
    </div>
    
    
    @isset($additionalInfo)
        <small class="text-muted"><strong>{{ __($additionalInfo) }}</strong></small>
    @endisset
    @if ($errors->has($id))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first($id) }}</strong>
        </span>
    @endif
</div>
