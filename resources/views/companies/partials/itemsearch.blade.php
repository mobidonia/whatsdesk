@if (config('settings.enable_search_bar',true))
    <div class="itemsSearchHolder">
        <select class="itemsSearch" id="itemsSearch" style="margin-right: 5px" placeholder="{{ __('Search') }}">
            <option></option>
            @if(!$company->categories->isEmpty())
            @foreach ( $company->categories as $key => $category)
                    @if(!$category->items->isEmpty())
                        <optgroup label="{{$category->name}}" >
                            @foreach ($category->aitems as $item)
                            <option value="{{$item->id}}" >{{$item->name}}</option>
                            @endforeach
                        </optgroup>
                    @endif
                @endforeach
            @endif 
        </select>
    </div>  
@endif
