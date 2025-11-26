@extends('layouts.app')
@section('admin_title')
    {{__('Dashboard')}}
@endsection

@section('content')
<div class="header pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">

            <h1 class="mb-3 mt--3">{{__('Welcome back')}}, {{ auth()->user()->name}}.</h1>
            @if (count($tasks)>0)
                @include('dashboard::tasks')
            @endif

            @foreach (config('global.modulesWithDashboardInfo') as $moduleWithDashboardInfo)
                @include($moduleWithDashboardInfo.'::dashboard')
            @endforeach

            @hasrole('owner')
            @foreach (config('global.reportsOnDashboard') as $reportView)
               @include($reportView[0])
               @include($reportView[1])
               <br />
            @endforeach
            @endhasrole
            
            @if(auth()->user()->hasRole('owner') && config('settings.enable_credits'))
                <?php
                    $authUser=auth()->user();
                    $company=auth()->user()->currentCompany();
                    $totalCreditsAndPercentageUsed=$company->getTotalRemainingCreditsAndPercentageUsed();
                    $availableCredits=$totalCreditsAndPercentageUsed[0];
                    $percentageUsed=$totalCreditsAndPercentageUsed[1][0];
                ?>
                <!-- Credits -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Credits</h5>
                                <div class="d-flex align-items-center">
                                    <div style="width: 80px; height: 80px;">
                                        <canvas id="creditsChart"></canvas>
                                    </div>
                                    <div class="ml-3">
                                        <p class="mb-1">Available Credits: <strong>{{ $availableCredits }}</strong></p>
                                        <p class="mb-0">Used: <strong>{{ $percentageUsed }}%</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                        <script>
                            const ctx = document.getElementById('creditsChart');
                            new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: ['Used', 'Available'],
                                    datasets: [{
                                        data: [{{ $percentageUsed }}, {{ 100 - $percentageUsed }}],
                                        backgroundColor: ['#ff6384', '#36a2eb']
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: true,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    }
                                }
                            });
                        </script>
                    </div>
                </div>
            @endif

           
        </div>
    </div>
</div>

<div class="container-fluid mt--6">
    @yield('dashboard_content')
    @yield('dashboard_content2')
    @yield('dashboard_content3')
    @yield('dashboard_content4')
</div>
@endsection

@section('js')
<script src="{{ asset('vendor/chart/chart.js') }}"></script>
@endsection