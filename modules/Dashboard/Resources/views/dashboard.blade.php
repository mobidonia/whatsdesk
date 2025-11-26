@hasrole('admin')
<div class="row">
    @if (config('settings.admin_companies_enabled',true))
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">

                <div class="card-body shadow-lg">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Users')}}</h5>
                            <span class="h2 font-weight-bold mb-0">{{ $dashboard['total_users'] }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                                <i class="ni ni-single-02"></i>
                            </div>
                        </div>

                    </div>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-success mr-2"><i class="fa fa-users"></i>
                            {{ $dashboard['users_this_month'] }}</span>
                        <span class="text-nowrap">{{ __('this month') }}</span>
                    </p>
                </div>
            </div>
        </div>
    @endif
    @if (config('settings.enable_pricing'))
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats shadow-lg">

                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Paying clients')}}</h5>
                            <span class="h2 font-weight-bold mb-0">{{ $dashboard['total_paying_users'] }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
                                <i class="ni ni-trophy"></i>
                            </div>
                        </div>

                    </div>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-success mr-2"><i class="fa fa-arrow-up"></i>
                            {{ $dashboard['total_paying_users_this_month'] }}</span>
                        <span class="text-nowrap">{{ __('this month') }}</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats shadow-lg">

                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('MRR')}}</h5>
                            <span class="h2 font-weight-bold mb-0">{{ $dashboard['mrr'] }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                                <i class="ni ni-chart-bar-32"></i>
                            </div>
                        </div>

                    </div>
                    
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats shadow-lg ">

                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('ARR')}}</h5>
                            <span class="h2 font-weight-bold mb-0">{{ $dashboard['arr'] }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                                <i class="ni ni-chart-bar-32"></i>
                            </div>
                        </div>

                    </div>
                    
                </div>
            </div>
        </div> 
    @endif
    
</div>
@endhasrole

@hasrole('admin')
@section('dashboard_content2')
@if (config('settings.admin_companies_enabled',true))
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header border-0">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="mb-0">{{ __('Latest companies') }}</h3>
                    </div>
                    <div class="col text-right">
                        <a href="{{ route('admin.companies.index') }}"
                            class="btn btn-sm btn-primary">{{ __('See all') }}</a>
                    </div>
                </div>
            </div>
            <div class="table-responsive">

                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">{{ __('Company') }}</th>
                            <th scope="col">{{ __('Creation Date') }}</th>
                            <th scope="col">{{ __('Name') }}</th>
                            <th scope="col">{{ __('Email') }}</th>
                            @if (config('settings.enable_pricing'))
                                <th scope="col">{{ __('Plan') }}</th>
                            @endif
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ( $dashboard['clients'] as $client )
                            @if ($client->user)
                                <tr>
                                    <td scope="row">
                                        <a href="{{ route('admin.companies.edit',$client->id)}}">{{ $client->name }}</a>
                                    </td>
                                    <td>{{ $client->created_at->locale(Config::get('app.locale'))->isoFormat('LLLL') }}</td>
                                    <td>
                                        {{ $client->user->name }}
                                    </td>
                                    <td>
                                        {{ $client->user->email }}
                                    </td>
                                    @if (config('settings.enable_pricing'))
                                    <td>
                                        @isset($dashboard['plans'])
                                            @isset($dashboard['plans'][$client->user->plan_id])
                                                {{ $dashboard['plans'][$client->user->plan_id] }}
                                            @endisset
                                        @endisset
                                        
                                    </td>
                                    @endif
                                    <td>
                                        <a class="btn btn-sm btn-primary text-white" href="{{ route('admin.companies.loginas',  $client)}}">{{ __('Login as') }}</a>
                                        @if (config('settings.show_company_page',true))
                                            <a target="_blank" href="{{ $client->getLinkAttribute() }}" class="btn btn-sm btn-success">{{ __('View it') }}</a>
                                        @endif
                                        
                                    </td>
                                </tr>
                            @endif
                        
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
    </div>
</div>
@endif
@endsection
@endhasrole