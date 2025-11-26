@extends('layouts.app', ['title' => __('Plan')])

@section('content')
<div class="header pb-8 pt-5 pt-md-8">
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">{{ __('Plans Management') }}</h3>
                        </div>
                        <div class="col-4 text-right">
                            <a href="{{ route('plans.index') }}" class="btn btn-sm btn-primary">{{ __('Back to plans') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="heading-small text-muted mb-4">{{ __('Plan information') }}</h6>
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif
                    <div class="pl-lg-4">
                        <form method="post" action="{{ route('plans.update', $plan) }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            @method('put')
                            @include('plans.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footers.auth')
</div>
@endsection
