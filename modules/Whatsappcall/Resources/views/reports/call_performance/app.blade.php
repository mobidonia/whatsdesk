@hasrole('owner')
<div id="callPerformanceReport">
    <div v-if="!loading">
        <!-- Time Period Selector -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-control-label">{{ __('Time Period') }}</label>
                    <select v-model="selectedPeriod" @change="loadReportData" class="form-control">
                        <option value="7">{{ __('Last 7 days') }}</option>
                        <option value="30">{{ __('Last 30 days') }}</option>
                        <option value="90">{{ __('Last 90 days') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Call Performance Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-success shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-light ls-1 mb-1">{{ __('Performance') }}</h6>
                                <h2 class="text-white mb-0">{{ __('Call Activity Over Time') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart" style="position: relative; height: 350px;">
                            <canvas id="callPerformanceChart" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="row">
            <div class="col-xl-4 col-md-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Peak Call Time') }}</h5>
                                <span class="h2 font-weight-bold mb-0">@{{ stats.peakCallTime }}</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                                    <i class="ni ni-watch-time"></i>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-sm">
                            <span class="text-info mr-2">@{{ stats.peakCallCount }}</span>
                            <span class="text-nowrap">{{ __('calls at peak') }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Success Rate') }}</h5>
                                <span class="h2 font-weight-bold mb-0">@{{ stats.successRate }}%</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
                                    <i class="ni ni-trophy"></i>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-sm">
                            <span class="text-success mr-2">@{{ stats.successfulCalls }}</span>
                            <span class="text-nowrap">{{ __('successful calls') }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Avg Wait Time') }}</h5>
                                <span class="h2 font-weight-bold mb-0">@{{ stats.avgWaitTime }}</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-gradient-warning text-white rounded-circle shadow">
                                    <i class="ni ni-time-alarm"></i>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-sm">
                            <span class="text-warning mr-2">{{ __('seconds') }}</span>
                            <span class="text-nowrap">{{ __('to answer') }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Calls Table -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <h3 class="mb-0">{{ __('Recent Calls') }}</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('Time') }}</th>
                                    <th scope="col">{{ __('Contact') }}</th>
                                    <th scope="col">{{ __('Direction') }}</th>
                                    <th scope="col">{{ __('Duration') }}</th>
                                    <th scope="col">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="call in recentCalls" :key="call.id">
                                    <td>
                                        <span class="text-sm">@{{ formatDateTime(call.started_at) }}</span>
                                    </td>
                                    <td>
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="mb-0 text-sm font-weight-bold">@{{ call.contact_name || call.wa_user_id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" :class="call.direction === 'UIC' ? 'badge-info' : 'badge-primary'">
                                            @{{ call.direction === 'UIC' ? 'Inbound' : 'Outbound' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-sm">@{{ call.duration || '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge" :class="getStatusBadgeClass(call.status)">
                                            @{{ formatStatus(call.status) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
        <span class="alert-text">{{ __('Error loading call performance data') }}: @{{ error }}</span>
    </div>
</div>
@endhasrole