<div id="conversationTrafficReport">
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

        <!-- Traffic Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-default shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-light ls-1 mb-1">{{ __('Overview') }}</h6>
                                <h2 class="text-white mb-0">{{ __('Conversation Traffic') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart" style="position: relative; height: 350px;">
                            <canvas id="conversationTrafficChart" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Total Messages') }}</h5>
                                <span class="h2 font-weight-bold mb-0">@{{ stats.totalMessages }}</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                                    <i class="ni ni-chat-round"></i>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-sm">
                            <span class="text-success mr-2">@{{ stats.messagesGrowth > 0 ? '+' : '' }}@{{ stats.messagesGrowth }}%</span>
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
                                <h5 class="card-title text-uppercase text-muted mb-0">{{ __('New Conversations') }}</h5>
                                <span class="h2 font-weight-bold mb-0">@{{ stats.newConversations }}</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
                                    <i class="ni ni-single-02"></i>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-sm">
                            <span class="text-success mr-2">@{{ stats.conversationsGrowth > 0 ? '+' : '' }}@{{ stats.conversationsGrowth }}%</span>
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
                                <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Avg Response Time') }}</h5>
                                <span class="h2 font-weight-bold mb-0">@{{ stats.avgResponseTime }}</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-gradient-primary text-white rounded-circle shadow">
                                    <i class="ni ni-time-alarm"></i>
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

            <div class="col-xl-3 col-md-6">
                <div class="card card-stats">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Resolution Rate') }}</h5>
                                <span class="h2 font-weight-bold mb-0">@{{ stats.resolutionRate }}%</span>
                            </div>
                            <div class="col-auto">
                                <div class="icon icon-shape bg-gradient-success text-white rounded-circle shadow">
                                    <i class="ni ni-check-bold"></i>
                                </div>
                            </div>
                        </div>
                        <p class="mt-3 mb-0 text-sm">
                            <span class="text-success mr-2">@{{ stats.resolvedConversations }}</span>
                            <span class="text-nowrap">{{ __('resolved') }}</span>
                        </p>
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
        <span class="alert-text">{{ __('Error loading report data') }}: @{{ error }}</span>
    </div>
</div>
