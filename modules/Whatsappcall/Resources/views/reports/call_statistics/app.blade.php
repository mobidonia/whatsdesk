@hasrole('owner')
<div id="callStatisticsReport">
    <div class="row" v-if="!loading && stats && stats.totalCalls > 0">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Total Calls') }}</h5>
                            <span class="h2 font-weight-bold mb-0">@{{ stats.totalCalls }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                                <i class="ni ni-mobile-button"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-success mr-2">@{{ stats.callsGrowth > 0 ? '+' : '' }}@{{ stats.callsGrowth }}%</span>
                        <span class="text-nowrap">{{ __('vs previous period') }}</span>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Answered') }}</h5>
                            <span class="h2 font-weight-bold mb-0">@{{ stats.answeredCalls }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
                                <i class="ni ni-check-bold"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-success mr-2">@{{ stats.answerRate }}%</span>
                        <span class="text-nowrap">{{ __('answer rate') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Missed') }}</h5>
                            <span class="h2 font-weight-bold mb-0">@{{ stats.missedCalls }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-warning text-white rounded-circle shadow">
                                <i class="ni ni-time-alarm"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-warning mr-2">@{{ stats.missedRate }}%</span>
                        <span class="text-nowrap">{{ __('missed rate') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Avg Duration') }}</h5>
                            <span class="h2 font-weight-bold mb-0">@{{ stats.avgDuration }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-primary text-white rounded-circle shadow">
                                <i class="ni ni-sound-wave"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-primary mr-2">{{ __('minutes') }}</span>
                        <span class="text-nowrap">{{ __('average') }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Call Direction Breakdown -->
    <div class="row mt-4" v-if="!loading && stats && stats.totalCalls > 0">
        <div class="col-xl-6 col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Call Direction') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart" style="position: relative; height: 300px;">
                        <canvas id="callDirectionChart" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-12">
            <div class="card shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">{{ __('Call Status Distribution') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart" style="position: relative; height: 300px;">
                        <canvas id="callStatusChart" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-if="loading" class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">{{ __('Loading...') }}</span>
        </div>
    </div>

    <div v-if="error" class="alert alert-danger" role="alert">
        <span class="alert-icon"><i class="ni ni-support-16"></i></span>
        <span class="alert-text">{{ __('Error loading call statistics') }}: @{{ error }}</span>
    </div>
</div>
@endhasrole