@extends('layouts.app', ['title' => __($title)])


@section('content')
    <div class="header  pb-8 pt-5 pt-md-8">
       @isset($breadcrumbs)
           @include('general.breadcrumbs')
       @endisset
    </div>
    <div class="container-fluid mt--7">
        @yield('customheading')
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __($title) }}</h3>
                                @isset($subtitle)
                                <p class="mb-0">{{ $subtitle }}</p>
                                @endisset
                                
                            </div>
                            
                                <div class="col-4 text-right">
                                    @isset($action_link)
                                        <a href="{{ $action_link }}" class="btn btn-sm btn-primary">{{ __($action_name) }}</a>
                                    @endisset
                                    @isset($action_link2) 
                                            <a href="{{ $action_link2 }}" class="btn btn-sm btn-primary">{{ __($action_name2) }}</a>
                                    @endisset
                                    @isset($action_link3) 
                                            <a href="{{ $action_link3 }}" class="btn btn-sm btn-primary">{{ __($action_name3) }}</a>
                                    @endisset
                                    @isset($action_link4) 
                                            <a href="{{ $action_link4 }}" class="btn btn-sm btn-primary">{{ __($action_name4) }}</a>
                                    @endisset
                                    @isset($action_link5) 
                                        <a target="_blank" href="{{ $action_link5 }}" class="btn btn-sm btn-primary">{{ __($action_name5) }}</a>
                                    @endisset
                                    @isset($usefilter)
                                        <button id="show-hide-filters" class="btn btn-icon btn-1 btn-sm btn-outline-secondary" type="button">
                                            <span class="btn-inner--icon"><i id="button-filters" class="ni ni-bold-down"></i></span>
                                        </button>
                                    @endisset
                                </div>
                        </div>
                        @isset($usefilter)
                            @include('general.filters')
                        @endisset
                    </div>

                    <div class="col-12">
                        @include('partials.flash')
                    </div>

                    @yield('contenttop')
                   @if (isset($iscontent))
                       <div class="card-body">
                            @yield('cardbody')
                       </div>
                   @else
                    @if(count($items))
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    @if(isset($custom_table))
                                        @yield('thead')
                                    @else
                                        @if(isset($fields))
                                            @foreach ($fields as $field)
                                                <th>{{ __( $field['name'] ) }}</th>
                                            @endforeach 
                                            <th>{{ __('crud.actions') }}</th>
                                        @else
                                            @yield('thead')
                                        @endif
                                    @endif
                                   
                                   
                                       
                                </thead>
                                <tbody>
                                    @yield('tbody')
                                </tbody>
                            </table>
                        </div>
                    @endif
                    <div class="card-footer py-4">
                        @if(count($items))
                        
                            @unless(isset($hidePaging) && $hidePaging)
                                <nav class="d-flex justify-content-end" aria-label="...">
                                    {{ $items->appends(request()->query())->links() }}
                                </nav>
                            @endunless
                        @else
                            <h4>{{__('crud.no_items',['items'=>$item_names])}}</h4>
                        @endif
                    </div>
                   @endif


                </div>
            </div>
        </div>

        @include('layouts.footers.auth')
    </div>
@endsection
