 <!-- Temmplate Basics -->
 <div class="col-xl-3 mt-2">
    <div class="card shadow">
        <div class="card-header bg-white border-0">
            <div class="row align-items-center">
                <div class="col-8">
                    <h3 class="mb-0">{{__('Template basics')}}</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Template Name -->
            <div class="form-group">
                <label for="name">{{__('Name')}}</label>
                <input v-model="template_name" type="text" name="name" id="name" class="form-control" placeholder="{{__('Name')}}" required>
            </div>

            <!-- Template Category - Marketing,Utility -->
            <div class="form-group">
                <label for="category">{{__('Category')}}</label>
                <select v-model="category" name="category" id="category" class="form-control">
                    <option value="MARKETING">{{__('Marketing')}}</option>
                    <option value="UTILITY">{{__('Utility')}}</option>
                </select>
            </div>

            <!-- Template Language -->
            <div class="form-group">
                <label for="language">{{__('Language')}}</label>
                <select v-model="language" name="language" id="language" class="form-control">
                    <option value="">{{__('Select language')}}</option>
                    @foreach ($languages as $language)
                     <option value="{{ $language[1] }}">{{ $language[0] }}</option>
                    @endforeach
                   
                  
                    
                </select>
            </div>
        </div>

    </div>
</div>