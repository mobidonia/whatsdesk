<div class="row">
    <div class="col-md-6 mt-3">
        <h6 class="heading-small text-muted mb-4">{{ __('Plugins') }}</h6>
        <label for="pluginsSelector">{{ __('Select available plugins or leave empty for all') }}</label><br />
        <select style="height: 200px" name="pluginsSelector[]" multiple="multiple" class="form-control noselecttwo"  id="pluginsSelector">
            <?php
                $perviousPlans=isset($plan)?json_decode($plan->getConfig('plugins','[]'),false):[];
                
            ?>
            @foreach ($allplugins as $plugin)
                <option @if (is_array($perviousPlans)&&in_array($plugin->alias,$perviousPlans))
                    selected
                @endif id="plugin{{$plugin->alias}}" value="{{$plugin->alias}}">{{ strlen($plugin->name)>0?$plugin->name:ucfirst($plugin->alias) }}</option>
            @endforeach
        </select>
    </div>
</div>
