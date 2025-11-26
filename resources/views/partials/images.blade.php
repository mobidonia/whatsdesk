<div class="form-group text-center">
    <label class="form-control-label" for="input-name">{{ $image['label'] }}</label>
    @isset($image['help'])
       <br /> <span class="small">{{ $image['help'] }}</span>
    @endisset
    <div class="text-center">
        <div class="fileinput fileinput-new" data-provides="fileinput">
            <div class="fileinput-preview img-thumbnail" data-trigger="fileinput" style="{{ $image['style'] }}">
            @if (isset($image['value']) && !empty($image['value']) && strlen($image['value']) > 4  && $image['value']!=config('app.url'))
                <img src="{{ $image['value'] }}" alt="..."/>
            @endif
                
        </div>
            <div>
                <span class="btn btn-outline-secondary btn-file">
                <span class="fileinput-new">{{ __('Select image') }}</span>
                <span class="fileinput-exists">{{ __('Change') }}</span>
                
               
                <input type="file" name="{{ $image['name'] }}" accept="image/x-png,image/png,image/gif,image/jpeg">
                </span>
                <a href="#" class="btn btn-outline-secondary fileinput-exists" data-dismiss="fileinput">{{ __('Remove') }}</a>
            </div>
        </div>
       
        
    </div>
    @if ($errors->has($image['name']))
        <span class="text-danger"><strong>{{ $errors->first($image['name']) }}</strong></span>
    @endif
</div>