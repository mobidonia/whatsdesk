@extends('general.index', $setup)
@section('cardbody')
<form action="{{ $setup['action'] }}" method="POST" enctype="multipart/form-data">
        @csrf
        @isset($setup['isupdate'])
            @method('PUT')
        @endisset
        @isset($setup['inrow'])
            <div class="row">
        @endisset
            @include('partials.fields',['fiedls'=>$fields])
        @isset($setup['inrow'])
            </div>
        @endisset
        @if (isset($setup['isupdate']))
            <button type="submit" class="btn btn-primary">{{ __('Update')}}</button>  
        @else
            <button type="submit" class="btn btn-primary">{{ __('Insert')}}</button>  
        @endif
    </form>
@endsection

@section('js')
<script type="text/javascript">
setTimeout(() => {
    $('.select2init').select2({
        }); 
}, 1000);
</script>
@endsection