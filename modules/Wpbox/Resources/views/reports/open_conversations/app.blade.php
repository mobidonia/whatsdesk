@hasrole('owner')
<div id="openConversationsReport">
    <div class="row" v-if="!loading">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Open') }}</h5>
                            <span class="h2 font-weight-bold mb-0">@{{ stats.open }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                                <i class="ni ni-chat-round"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-success mr-2">@{{ stats.openPercentage }}%</span>
                        <span class="text-nowrap">{{ __('of total conversations') }}</span>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Unattended') }}</h5>
                            <span class="h2 font-weight-bold mb-0">@{{ stats.unattended }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-warning text-white rounded-circle shadow">
                                <i class="ni ni-bell-55"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-warning mr-2">@{{ stats.unattendedPercentage }}%</span>
                        <span class="text-nowrap">{{ __('need attention') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Unassigned') }}</h5>
                            <span class="h2 font-weight-bold mb-0">@{{ stats.unassigned }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-danger text-white rounded-circle shadow">
                                <i class="ni ni-single-02"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-danger mr-2">@{{ stats.unassignedPercentage }}%</span>
                        <span class="text-nowrap">{{ __('without agent') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Pending') }}</h5>
                            <span class="h2 font-weight-bold mb-0">@{{ stats.pending }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="icon icon-shape bg-gradient-primary text-white rounded-circle shadow">
                                <i class="ni ni-time-alarm"></i>
                            </div>
                        </div>
                    </div>
                    <p class="mt-3 mb-0 text-sm">
                        <span class="text-primary mr-2">@{{ stats.pendingPercentage }}%</span>
                        <span class="text-nowrap">{{ __('awaiting response') }}</span>
                    </p>
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
@endhasrole