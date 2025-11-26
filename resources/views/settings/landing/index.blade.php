@extends('layouts.app', ['title' => __('Landing Page')])
@section('admin_title')
@endsection
@section('content')
    <div class="header pb-6 pt-5 pt-md-8">
      <div class="container-fluid">
        <div class="header-body">
            <h1 class="mb-3 mt--3">🌐 {{__('Landing Page')}}</h1>
          <div class="row align-items-center pt-2">
          </div>
        </div>
    </div>
    </div>
    <div class="container-fluid mt--6">
      <div class="row">
        <div class="col">
          <div class="card">
           
            <!-- Light table -->
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col" class="sort" data-sort="name">{{ __('Sections') }}</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody class="list">
                    @foreach($sections as $key => $section)
                    <tr>
                        <th scope="row">
                            <div class="media align-items-center">
                                <div class="media-body">
                                    <a href="{{ route('admin.landing.posts',['type'=>strtolower($section)]) }}"><span class="name mb-0 text-sm">{{ __($key) }}</span></a>
                                </div>
                            </div>
                        </th>
                        <td class="text-right">
                            
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

