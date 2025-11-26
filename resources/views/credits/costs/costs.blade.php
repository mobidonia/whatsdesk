@extends('layouts.app', ['title' => __('Credits')])

@section('content')
    <div class="header pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
                <h1 class="mb-3 mt--3">💰 {{__('Credits')}}</h1>
                <div class="row align-items-center pt-2">
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Costs per action') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('credits.costs') }}">
                            @csrf
                            @if(count($actions) == 0)
                                <div class="text-center">
                                    <p>{{ __('There are no modules with credit costs defined.') }}</p>
                                </div>
                            @endif

                            @if(count($actions) > 0)
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%">{{ __('Action') }}</th>
                                            <th style="width: 25%">{{ __('Type') }}</th>
                                            <th style="width: 25%">{{ __('Cost in credits') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($actions as $action)
                                            <tr>
                                                <td>{{ $action['name'] }}</td>
                                                <td>
                                                    <select class="form-control action_type custom-select" name="{{ $action['action'] }}_type">
                                                        <option value="-1" {{ $action['cost'] == -1 ? 'selected' : '' }}>
                                                            <i class="fas fa-chart-line mr-2"></i>
                                                            {{ __('Usage based') }}
                                                        </option>
                                                        <option value="1" {{ $action['cost'] != -1 ? 'selected' : '' }}>
                                                            <i class="fas fa-lock mr-2"></i>
                                                            {{ __('Fixed amount') }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="{{ $action['action'] }}_cost" class="form-control" value="{{ $action['cost'] == -1 ? '' : $action['cost'] }}" style="{{ $action['cost'] == -1 ? 'display: none;' : '' }}">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                
                                <div class="text-right mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save mr-2"></i>{{ __('Save Changes') }}
                                    </button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')

    <script type="text/javascript">
            $(document).ready(function() {
                $('select.action_type').on('change', function() {
                if($(this).val() == "-1"){
                    $(this).parent().parent().find('input').hide();
                }else{
                    $(this).parent().parent().find('input').show();
                }
                });
            });

    </script>
@endsection
