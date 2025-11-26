@extends('layouts.app', ['title' => __('Organization')])
@section('admin_title')
    {{ $company->name }}
@endsection
@section('content')



<div class="header pb-6 pt-5 pt-md-8">
    <div class="container-fluid">


        <div class="nav-wrapper">
            <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="res_menagment" role="tablist">

                @if(count($appFields)>0 || (auth()->user()->hasRole('admin')  && config('settings.enable_pricing',true)))
                    <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0 active " id="tabs-menagment-main" data-toggle="tab" href="#menagment" role="tab" aria-controls="menagment" aria-selected="true"><i class="ni ni-badge mr-2"></i>{{ __('Organization Management')}}</a>
                    </li>
                @endif
                @if(count($appFields)>0)
                    <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-menagment-apps" data-toggle="tab" href="#apps" role="tab" aria-controls="apps" aria-selected="falae"><i class="ni ni-spaceship mr-2"></i>{{ __('Apps')}}</a>
                    </li>
                @endif
                
                @if(auth()->user()->hasRole('admin')  && config('settings.enable_pricing',true) )
                    <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0" id="tabs-menagment-plan" data-toggle="tab" href="#plan" role="tab" aria-controls="plan" aria-selected="false"><i class="ni ni-money-coins mr-2"></i>{{ __('Plans')}}</a>
                    </li>
                @endif
            </ul>
        </div>

    </div>
</div>



<div class="container-fluid mt--7">

   


    <div class="row">
        <div class="col-12">
            <br />

            @include('partials.flash')

            <div class="tab-content" id="tabs">


                <!-- Tab Managment -->
                <div class="tab-pane fade show active" id="menagment" role="tabpanel" aria-labelledby="menagment">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">{{ __('Organization Management') }}</h3>
                                    @if (config('settings.wildcard_domain_ready'))
                                    <span class="blockquote-footer">{{ $company->getLinkAttribute() }}</span>
                                    @endif
                                </div>
                                <div class="col-4 text-right">
                                    @if(auth()->user()->hasRole('admin'))
                                        <a href="{{ route('admin.companies.index') }}" class="btn btn-sm btn-info">{{ __('Back to list') }}</a>
                                    @endif
                                    @if (config('settings.show_company_page',true))
                                        @if (config('settings.wildcard_domain_ready'))
                                        <a target="_blank" href="{{ $company->getLinkAttribute() }}"
                                            class="btn btn-sm btn-success">{{ __('View it') }}</a>
                                        @else
                                        <a target="_blank" href="{{ route('vendor',$company->subdomain) }}"
                                            class="btn btn-sm btn-success">{{ __('View it') }}</a>
                                        @endif
                                        <a class="btn btn-sm btn-success text-white" href="{{ route('admin.companies.loginas', $company) }}">{{ __('Login as') }}</a>
                                       
                                    @endif
                                    @if ($hasCloner)
                                        <a href="{{ route('admin.companies.create')."?cloneWith=".$company->id }}" class="btn btn-sm btn-warning text-white">{{ __('Clone it') }}</a>
                                    @endif
                                   
                                        

                                </div>

                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="heading-small text-muted mb-4">{{ __('Organization information') }}</h6>
                            
                            @include('companies.partials.info')
                            <hr />
                            @include('companies.partials.owner')
                        </div>
                    </div>
                </div>

                <!-- Tab Apps -->
                @if(count($appFields)>0)
                    <div class="tab-pane fade show" id="apps" role="tabpanel" aria-labelledby="apps">
                        @include('companies.partials.apps') 
                    </div>
                @endif


              

                <!-- Tab Plans -->
                @if(auth()->user()->hasRole('admin') && config('settings.enable_pricing',true) )
                    <div class="tab-pane fade show" id="plan" role="tabpanel" aria-labelledby="plan">
                        @include('companies.partials.plan')
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
