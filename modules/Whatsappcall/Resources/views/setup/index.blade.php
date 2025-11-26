@extends('layouts.app', ['title' => __('WhatsApp Calling Setup')])
@section('content')
<div class="header pb-8 pt-2 pt-md-7">
    <div class="container-fluid">
        <div class="header-body">
            <h1 class="mb-3 mt--3">📞 {{ __('WhatsApp Calling Setup') }}</h1>
            <p class="text-muted">Enable calling, set business hours, and subscribe your app to the "calls" webhook per Meta docs. You also need Infobip WebRTC credentials.</p>
        </div>
    </div>
</div>
<div class="container-fluid mt--8">
    @include('partials.flash')
    <form method="POST" action="{{ route('whatsappcall.settings.store') }}" class="mt-3">
        @csrf
        <div class="card">
            <div class="card-header">Meta Calling</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="d-block">
                                <input type="checkbox" name="enabled" value="1" {{ $settings['enabled'] ? 'checked' : '' }}> {{ __('Enable calling') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('Call icon visibility') }}</label>
                            <select class="form-control" name="call_icon_visibility">
                                <option value="DEFAULT" {{ $settings['call_icon_visibility']==='DEFAULT'?'selected':'' }}>DEFAULT</option>
                                <option value="HIDDEN" {{ $settings['call_icon_visibility']==='HIDDEN'?'selected':'' }}>HIDDEN</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{ __('Callback permission') }}</label>
                            <select class="form-control" name="callback_permission_status">
                                <option value="DISABLED" {{ $settings['callback_permission_status']==='DISABLED'?'selected':'' }}>DISABLED</option>
                                <option value="ENABLED" {{ $settings['callback_permission_status']==='ENABLED'?'selected':'' }}>ENABLED</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __('Hours status') }}</label>
                            <select class="form-control" name="hours_status">
                                <option value="DISABLED" {{ $settings['hours_status']==='DISABLED'?'selected':'' }}>DISABLED</option>
                                <option value="ENABLED" {{ $settings['hours_status']==='ENABLED'?'selected':'' }}>ENABLED</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>{{ __('Timezone') }}</label>
                            <input type="text" class="form-control" name="timezone_id" value="{{ $settings['timezone_id'] }}" placeholder="e.g. America/Manaus">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="d-block">
                                <input type="checkbox" name="inbound_allowed" value="1" {{ $settings['inbound_allowed'] ? 'checked' : '' }}> {{ __('Allow inbound calls from profile') }}
                            </label>
                        </div>
                    </div>
                </div>

                <hr>
                <h5 class="mb-3">{{ __('Weekly operating hours') }}</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Day') }}</th>
                                <th>{{ __('Enabled') }}</th>
                                <th>{{ __('Open') }}</th>
                                <th>{{ __('Close') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($days=['MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY'])
                            @foreach($days as $d)
                                @php($row = collect($settings['weekly_operating_hours'])->firstWhere('day_of_week',$d) ?? [])
                                <tr>
                                    <td>{{ $d }}</td>
                                    <td><input type="checkbox" name="weekly[{{ $d }}][enabled]" value="1" {{ $row ? 'checked' : '' }}></td>
                                    <td><input type="time" class="form-control form-control-sm" name="weekly[{{ $d }}][open_time]" value="{{ isset($row['open_time']) ? substr($row['open_time'],0,2).':'.substr($row['open_time'],2,2) : '' }}"></td>
                                    <td><input type="time" class="form-control form-control-sm" name="weekly[{{ $d }}][close_time]" value="{{ isset($row['close_time']) ? substr($row['close_time'],0,2).':'.substr($row['close_time'],2,2) : '' }}"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <hr>
                <h5 class="mb-3">{{ __('Holiday schedule') }}</h5>
                <div id="holidays-wrapper">
                    @php($holidays = $settings['holiday_schedule'])
                    @forelse($holidays as $i=>$h)
                        <div class="form-row mb-2">
                            <div class="col-md-3"><input type="date" name="holidays[{{ $i }}][date]" class="form-control" value="{{ $h['date'] ?? '' }}"></div>
                            <div class="col-md-2"><input type="time" name="holidays[{{ $i }}][start_time]" class="form-control" value="{{ isset($h['start_time']) ? substr($h['start_time'],0,2).':'.substr($h['start_time'],2,2) : '' }}"></div>
                            <div class="col-md-2"><input type="time" name="holidays[{{ $i }}][end_time]" class="form-control" value="{{ isset($h['end_time']) ? substr($h['end_time'],0,2).':'.substr($h['end_time'],2,2) : '' }}"></div>
                            <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.parentElement.remove()">{{ __('Remove') }}</button></div>
                        </div>
                    @empty
                    @endforelse
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addHolidayRow()">{{ __('Add holiday') }}</button>

                <div class="alert alert-info mt-3">
                    {{ __('Remember to subscribe your Facebook app to the "calls" webhook field for this WABA.') }}
                </div>
            </div>
        </div>

        <div class="text-right mt-3">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
</div>
@endsection

@push('js')
<script>
function addHolidayRow(){
  const idx = document.querySelectorAll('#holidays-wrapper .form-row').length;
  const wrapper = document.getElementById('holidays-wrapper');
  const div = document.createElement('div');
  div.className='form-row mb-2';
  div.innerHTML = `
    <div class="col-md-3"><input type="date" name="holidays[${idx}][date]" class="form-control"></div>
    <div class="col-md-2"><input type="time" name="holidays[${idx}][start_time]" class="form-control"></div>
    <div class="col-md-2"><input type="time" name="holidays[${idx}][end_time]" class="form-control"></div>
    <div class="col-md-2"><button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.parentElement.remove()">{{ __('Remove') }}</button></div>
  `;
  wrapper.appendChild(div);
}
</script>
@endpush

