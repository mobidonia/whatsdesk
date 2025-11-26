@extends('layouts.app', ['title' => __('Organizations')])
@section('content')
    <div class="header pb-6 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
                <div class="row align-items-center">
                    <div class="col mb-3">
                        <h1 class="mb-0">🏢 {{__('Organizations')}}</h1>
                    </div>
                    <div class="col text-right">
                        @if(config('settings.enable_create_company', true))
                            <button type="button" class="btn btn-sm btn-primary shadow-sm rounded-lg hover:shadow-md transition-all duration-200" data-toggle="modal" data-target="#createOrgModal">
                                {{ __('New Organization') }}
                            </button>

                            <!-- Create Organization Modal -->
                            <div class="modal fade" id="createOrgModal" tabindex="-1" role="dialog" aria-labelledby="createOrgModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.organizations.create') }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="createOrgModalLabel">{{ __('Create New Organization') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="organization_name">{{ __('Organization Name') }}</label>
                                                    <input type="text" class="form-control" id="organization_name" name="name" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                                <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt--6">      
        <div class="row">
            <div class="col">
                <div class="card">
                    @include('partials.flash')
                    
                    <div class="table-responsive">
                        <table class="table align-items-center">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('Name') }}</th>
                                    @if (config('settings.show_company_logo'))
                                        <th scope="col">{{ __('Logo') }}</th>
                                    @endif
                                    <th scope="col">{{ __('Status') }}</th>
                                    <th scope="col" class="text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (auth()->user()->companies->where('active', 1) as $company)
                                    <tr>
                                        <td class="table-user">
                                            <a href="{{ route('admin.companies.edit', $company) }}" class="text-decoration-none"><b>{{ $company->name }}</b></a>
                                        </td>
                                       
                                        @if (config('settings.show_company_logo'))
                                            <td>
                                                <img src="{{ $company->icon }}" class="avatar rounded-circle mr-3">
                                            </td>
                                        @endif
                                        
                                        <td>
                                            <span class="badge badge-dot mr-4">
                                                <i class="bg-success"></i>
                                                <span class="status">{{ __('Active') }}</span>
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> {{ __('Edit') }}
                                            </a>
                                            <a href="{{ route('admin.companies.switch', $company) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-random"></i> {{ __('Switch') }}
                                            </a>
                                            @if($company->id != auth()->user()->company_id)
                                                <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure you want to delete this organization?') }}')">
                                                        <i class="fas fa-trash"></i> {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
