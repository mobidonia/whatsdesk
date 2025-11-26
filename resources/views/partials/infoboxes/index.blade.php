
<div class="row">
    @foreach ($cards as $card)
    <div class="col-xl-3 col-lg-6">
        @include('partials.infoboxes.box',$card)
    </div>
    @endforeach
</div>