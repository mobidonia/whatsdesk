<!-- Temmplate Preview -->
<div class="col-xl-4 mt-2">
    <div class="card shadow">
        <div class="card-header bg-white border-0">
            <div class="row align-items-center">
                <div class="col-8">
                    <h3 class="mb-0">{{__('Template Preview')}}</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            @include('wpbox::templates.partials.message')
            <!-- button to save the template -->
            <br />  
            <!-- button to save the template - disable in demo -->
            @if ($isDemo)
                <button type="button" style="opacity:0.8"  @click="showDisabledInDemo()" class="btn btn-primary mt-3">{{__('Save Template')}}</button>
            @else
                <div v-if="isSending" class="loading"></div>
                <button  v-if="!isSending" @click="submitTemplate()" type="button"   class="btn btn-primary mt-3">{{__('Save Template')}}</button>
            @endif
            
        </div>
    </div>
</div>