<h6 class="heading-small text-muted mb-4">{{ __('Owner information') }}</h6>
<div class="pl-lg-4">
    <div class="form-group{{ $errors->has('name_owner') ? ' has-danger' : '' }}">
        <label class="form-control-label" for="name_owner">{{ __('Owner Name') }}</label>
        <input type="text" name="name_owner" id="name_owner" class="form-control form-control-alternative" placeholder="{{ __('Owner Name') }}" value="{{ old('name', $company->user->name) }}" readonly>
    </div>
    <div class="form-group{{ $errors->has('email_owner') ? ' has-danger' : '' }}">
        <label class="form-control-label" for="email_owner">{{ __('Owner Email') }}</label>
        <input type="text" name="email_owner" id="email_owner" class="form-control form-control-alternative" placeholder="{{ __('Owner Email') }}" value="{{ old('name', $company->user->email) }}" readonly>
    </div>
    <div class="form-group{{ $errors->has('phone_owner') ? ' has-danger' : '' }}">
        <label class="form-control-label" for="phone_owner">{{ __('Owner Phone') }}</label>
        <input type="text" name="phone_owner" id="phone_owner" class="form-control form-control-alternative" placeholder="{{ __('Owner Phone') }}" value="{{ old('name', $company->user->phone) }}" readonly>
    </div>
</div>
