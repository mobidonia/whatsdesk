<div class="row align-items-center">
    <div class="col-lg-6 col-7">
      <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
          <li class="breadcrumb-item"><a href="/home"><i class="fas fa-home"></i></a></li>
          @foreach ($breadcrumbs as $breadcrumb)
              @if ($breadcrumb[1])
                <li class="breadcrumb-item"><a href="{{ $breadcrumb[1] }}">{{ __($breadcrumb[0])}}</a></li>
              @else
                <li class="breadcrumb-item active" aria-current="page">{{ __($breadcrumb[0])}}</li>
              @endif
          @endforeach
        </ol>
      </nav>
    </div>
  </div>