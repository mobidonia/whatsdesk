

<div class="contacInfo border-radius-lg border p-4 mb-4">
    <div class="">
        <label class="form-label">{{ __('Recipient Phone Number') }}</label>
        <div class="form-control-plaintext"><strong>@{{ activeChat.phone }}</strong></div>
    </div>
    <!-- Permission Status Display -->
    <div v-show="getProperty('permissionStatus')" class="mt-3 p-3 alert alert-info border border-info rounded" style="background: rgba(45, 206, 137, 0.1); border-color: #2dce89 !important;">
        <div class="d-flex align-items-center">
            <div class="mr-2" style="width: 8px; height: 8px; border-radius: 50%;" :style="getProperty('permissionStatus')?.status === 'temporary' ? 'background-color: #2dce89;' : 'background-color: #f5365c;'"></div>
            <span class="mb-0 font-weight-medium text-dark" v-text="getPermissionStatusText()"></span>
        </div>
        <div v-show="getProperty('permissionStatus')?.expiration_time" class="mt-1">
            <small class="text-muted" v-text="getExpirationText()"></small>
        </div>
    </div>

    <!-- Request Permission Button -->
    <div v-show="getProperty('showRequestButton')" class="mt-3">
        <button type="button" 
                class="btn btn-outline-primary btn-block d-flex align-items-center justify-content-center" 
                @click="requestPermission()"
                :disabled="getProperty('isLoading')"
                style="transition: all 0.2s ease;">
            <i class="ni ni-lock-circle-open mr-2" style="font-size: 14px;"></i>
            <span v-text="getProperty('isLoading') ? 'Requesting...' : 'Request Call Permission'"></span>
        </button>
    </div>

    <!-- Make Call Button -->
    <div v-show="getProperty('showCallButton')" class="mt-3">
        <button type="button" 
                class="btn btn-success btn-block d-flex align-items-center justify-content-center" 
                @click="makeCall()"
                :disabled="getProperty('isLoading')"
                style="transition: all 0.2s ease;">
            <i class="ni ni-mobile-button mr-2" style="font-size: 14px;"></i>
            <span v-text="getProperty('isLoading') ? 'Calling...' : 'Start Call'"></span>
        </button>
    </div>
</div>

