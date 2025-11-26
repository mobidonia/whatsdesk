<div id="waIncomingCall" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Incoming WhatsApp Call') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="d-flex align-items-center">
          <div class="flex-grow-1">
            <div class="h5 mb-1" id="waIncomingCallName"></div>
            <div class="text-muted" id="waIncomingCallNumber"></div>
          </div>
          <div>
            <span class="badge badge-success" id="waIncomingCallStatus">{{ __('Ringing') }}</span>
          </div>
        </div>
        <div class="mt-3" id="waCallLinkWrap" style="display:none;">
          <a id="waCallLink" href="#" target="_blank" class="btn btn-outline-primary btn-sm">{{ __('Open Call Link') }}</a>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
        <button type="button" class="btn btn-danger" id="waDeclineBtn">{{ __('Decline') }}</button>
        <button type="button" class="btn btn-success" id="waAcceptBtn">{{ __('Accept') }}</button>
      </div>
    </div>
  </div>
</div>

