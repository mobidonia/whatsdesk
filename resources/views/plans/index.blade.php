@extends('layouts.app', ['title' => __('Pages')])

@section('content')
    <div class="header pb-8 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
                <h1 class="mb-3 mt--3">💰 {{__('Pricing plans')}}</h1>
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
                                
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('plans.create') }}" class="btn btn-sm btn-primary">{{ __('Add plan') }}</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        @include('partials.flash')
                    </div>
                    @if(count($plans))
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">{{ __('Price') }}</th>
                                    @if (config('settings.enable_credits'))
                                        <th scope="col">{{ __('Credit amount') }}</th>
                                    @endif
                                    <th scope="col">{{ __('Period') }}</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($plans as $plan)
                                <tr>
                                    <td><a href="{{ route('plans.edit', $plan) }}">{{ $plan->name }} </a></td>
                                    <td>@money($plan->price, config('settings.site_currency','usd'),config('settings.site_do_currency',true))/{{ $plan->period==1?__('m'):__('y') }}</td>
                                    @if (config('settings.enable_credits'))
                                        <td>{{ $plan->credit_amount }}</td>
                                    @endif
                                    <td>{{ $plan->period == 1 ? __("Monthly") : __("Anually") }}</td>
                                   
                                    
                                    <td class="text-right">
                                        <div class="d-flex">
                                            <a href="{{ route('plans.edit', $plan) }}" class="btn btn-sm btn-info mr-2">{{ __('Edit') }}</a>
                                            <form action="{{ route('plans.destroy', $plan) }}" method="post">
                                                @csrf
                                                @method('delete')
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirm('{{ __("Are you sure you want to delete this plan?") }}') ? this.parentElement.submit() : ''">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    <div class="card-footer py-4">
                        @if(count($plans))
                            <nav class="d-flex justify-content-end" aria-label="...">
                                {{ $plans->links() }}
                            </nav>
                        @else
                            <h4>{{ __('You don`t have any plans') }} ...</h4>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
