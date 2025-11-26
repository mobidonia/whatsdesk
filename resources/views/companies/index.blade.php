@extends('general.index', $setup)

@section('thead')
    <th>{{ __('Name') }}</th>
    @if (config('settings.show_company_logo'))
        <th>{{ __('Logo') }}</th>
    @endif
    <th>{{ __('Owner') }}</th>
    <th>{{ __('Owner email') }}</th>
    <th>{{ __('Phone') }}</th>
    <th>{{ __('Active') }}</th>
    <th>{{ __('Plan') }}</th>
    <th>{{ __('crud.actions') }}</th>
@endsection

@section('tbody')
    @foreach ($setup['items'] as $company)
        <tr>
            <td>
                @if(auth()->user()->hasRole('manager'))
                    <a href="{{ route('admin.companies.loginas', $company) }}">{{ $company->name }}</a>
                @else
                    <a href="{{ route('admin.companies.edit', $company) }}">{{ $company->name }}</a>
                @endif
            </td>
            
            @if (config('settings.show_company_logo'))
                <td><img class="rounded" src={{ $company->icon }} width="50px" height="50px"></img></td>
            @endif
            
            <td>{{ $company->user?$company->user->name:__('Deleted') }}</td>
            <td><a href="mailto: {{ $company->user?$company->user->email:"" }}">{{ $company->user?$company->user->email:__('Deleted') }}</a></td>
            <td><a href="tel:{{ $company->phone }}">{{ $company->phone }}</a></td>
            <td>
                @if($company->active == 1)
                    <span class="badge badge-success">{{ __('Active') }}</span>
                @else
                    <span class="badge badge-warning">{{ __('Not active') }}</span>
                @endif
            </td>
            <td>{{ isset($plans) && isset($company->user) && isset($company->user->plan_id) && isset($plans[$company->user->plan_id]) ? $plans[$company->user->plan_id] : '' }}</td>
            <td>
                <a class="btn btn-sm btn-outline-primary me-2" href="{{ route('admin.companies.edit', $company) }}">{{ __('Edit') }}</a>
                <a class="btn btn-sm btn-outline-info me-2" href="{{ route('admin.companies.loginas', $company) }}">{{ __('Login as') }}</a>
                @if ($hasCloner)
                    <a class="btn btn-sm btn-outline-secondary me-2" href="{{ route('admin.companies.create')."?cloneWith=".$company->id }}">{{ __('Clone it') }}</a>
                @endif
                <form action="{{ route('admin.companies.destroy', $company) }}" method="post" class="d-inline me-2">
                    @csrf
                    @method('delete')
                    @if($company->active == 0)
                        <a class="btn btn-sm btn-outline-success" href="{{ route('admin.company.activate', $company) }}">{{ __('Activate') }}</a>
                        <a class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this Company from Database? This will aslo delete all data related to it. This is irreversible step.')" href="{{ route('admin.company.remove',$company)}}">{{ __('Delete') }}</a>
           
                    @else
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="confirm('{{ __("Are you sure you want to deactivate this company?") }}') ? this.parentElement.submit() : ''">
                            {{ __('Deactivate') }}
                        </button>
                    @endif
                </form>
             </td>
        </tr>
    @endforeach
@endsection
