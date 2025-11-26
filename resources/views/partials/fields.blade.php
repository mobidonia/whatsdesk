@foreach ($fields as $field)
    @if ($field['ftype']=="input")
        @include('partials.input',$field)
    @endif
    @if ($field['ftype']=="label")
        @include('partials.label',$field)
    @endif
    @if ($field['ftype']=="multiselect")
        @include('partials.multiselect',$field)
    @endif
    @if ($field['ftype']=="select")
        @include('partials.select',$field)
    @endif
    @if ($field['ftype']=="info")
        @include('partials.info',$field)
    @endif
    @if ($field['ftype']=="image")
        @include('partials.images',['image'=>['label'=>$field['name'], 'id'=>$field['id'], 'name'=>$field['id'], 'value'=>isset($field['value'])?$field['value']:config('global.company_details_image'), 'style'=>isset($field['style'])?$field['style']:'width: 290px; height:200' ]])
    @endif
    @if ($field['ftype']=="bool")
        @include('partials.bool',$field)
    @endif
    @if ($field['ftype']=="textarea")
        @include('partials.textarea',$field)
    @endif
    @if ($field['ftype']=="map")
        @include('partials.map',$field)
    @endif
    @if ($field['ftype']=="iconselect")
        @include('partials.iconselect',$field)
    @endif
@endforeach
