<div id="conversationByAgentReport">
    <div v-if="!loading">
        <!-- Agent Performance Overview -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-info shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-light ls-1 mb-1">{{ __('Performance') }}</h6>
                                <h2 class="text-white mb-0">{{ __('Agent Statistics') }}</h2>
                            </div>
                            <div class="col-auto">
                                <div class="form-group mb-0">
                                    <select v-model="selectedPeriod" @change="loadReportData" class="form-control form-control-sm">
                                        <option value="7">{{ __('Last 7 days') }}</option>
                                        <option value="30">{{ __('Last 30 days') }}</option>
                                        <option value="90">{{ __('Last 90 days') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart" style="position: relative; height: 300px;">
                            <canvas id="agentPerformanceChart" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Agent Details Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <h3 class="mb-0">{{ __('Agent Performance Details') }}</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('Agent') }}</th>
                                    <th scope="col">{{ __('Conversations') }}</th>
                                    <th scope="col">{{ __('Messages Sent') }}</th>
                                    <th scope="col">{{ __('Avg Response Time') }}</th>
                                    <th scope="col">{{ __('Resolution Rate') }}</th>
                                    <th scope="col">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="agent in agentStats" :key="agent.id">
                                    <td>
                                        <div class="media align-items-center">
                                            <div class="media-body">
                                                <span class="mb-0 text-sm font-weight-bold">@{{ agent.name }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-dot mr-4">
                                            <i class="bg-success"></i>
                                            @{{ agent.conversations }}
                                        </span>
                                    </td>
                                    <td>
                                        @{{ agent.messagesSent }}
                                    </td>
                                    <td>
                                        <span class="text-sm">@{{ agent.avgResponseTime }} {{ __('min') }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="mr-2">@{{ agent.resolutionRate }}%</span>
                                            <div class="progress" style="width: 50px;">
                                                <div class="progress-bar bg-success" :style="'width: ' + agent.resolutionRate + '%'"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" :class="agent.status === 'Online' ? 'badge-success' : (agent.status === 'Busy' ? 'badge-warning' : 'badge-secondary')">
                                            @{{ agent.status }}
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
        <span class="alert-text">{{ __('Error loading report data') }}: @{{ error }}</span>
    </div>
</div>
