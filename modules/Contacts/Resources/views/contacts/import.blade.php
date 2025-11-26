@extends('layouts.app', ['title' =>  __("CSV contacts Import ") ])


@section('content')
    <div class="header  pb-8 pt-5 pt-md-8">
    </div>
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __("CSV contacts Import ") }}</h3>
                  
   
                                
                            </div>
                            
                               
                        </div>
                       
                    </div>

                    <div class="col-12">
                        @include('partials.flash')
                    </div>

                   
                       <div class="card-body">
                            <form action="{{ route('contacts.import.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @include('partials.input',['additionalInfo'=>"Headers phone,name,custom_field_name_1,custom_field_name_2",'class'=>'col-md-4','name'=>"CSV file",'id'=>'csv','type'=>'file','placeholder'=>"",'required'=>true,'accept'=>".csv"])
                                @include('partials.select',['class'=>'col-md-4','name'=>"Group to insert into",'id'=>'group','placeholder'=>"",'required'=>false,'data'=>$groups])
                                <div class="form-group">
                                    <button type="submit" class="btn btn-success ml-3 mt-2" >{{ __('Import contact')}}</button>
                                </div>
                                
                            </form>
                        </div>
                   
                    
     
         


                </div>
            </div>
        </div>

        @include('layouts.footers.auth')
    </div>
@endsection
