@extends('layouts.app', ['title' => __('Send new campaign')])
@section('head')
@endsection

@section('content')
<div class="header  pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <h1 class="mb-3 mt--3">{{__('Create new template')}}</h1>
            <div class="row align-items-center pt-2">
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('campaigns.store') }}" id="template_creator" enctype="multipart/form-data">
    @csrf
    <div class="container-fluid mt--7" id="tempplate_managment">
        <div class="row">
            @include('wpbox::templates.partials.basic')
            @include('wpbox::templates.partials.form')
            @include('wpbox::templates.partials.preview')
        </div>
    </div>
</form>
@include('wpbox::templates.scripts')
@endsection

