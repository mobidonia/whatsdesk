
<!-- AI Translate Section -->


@if ($company->getConfig('translation_enabled', false))
<h5 class="text-muted mt-4">{{ __('Automatic AI Translate')}}</h5>
<b-dropdown  id="dropdown-right" split right :text="translationLanguage(activeChat)" variant="primary" class="m-2">
    <b-dropdown-item v-for="lang in languages" :key="key" @click="setLanguage(lang, activeChat)">
        @{{lang}}
    </b-dropdown-item>
</b-dropdown>
    
@endif



<!-- AI Message Style Section -->
<h5 class="text-muted mt-4">{{ __('Message Style')}}</h5>
<div class="contacInfo border-radius-lg border p-4 mb-4">
    <div class="form-group">
        <textarea class="form-control" v-model="originalMessage" rows="4" placeholder="{{ __('Enter your message here...') }}"></textarea>
    </div>

    <div class="btn-group mb-3">
        <button class="btn btn-sm btn-primary mr-2" @click="convertStyle('professional')">{{ __('Professional') }}</button>
        <button class="btn btn-sm btn-info mr-2" @click="convertStyle('relaxed')">{{ __('Relaxed') }}</button>
        <button class="btn btn-sm btn-success mr-2" @click="convertStyle('friendly')">{{ __('Friendly') }}</button>
        <button class="btn btn-sm btn-warning" @click="convertStyle('formal')">{{ __('Formal') }}</button>
    </div>

    <div v-if="convertedMessage" class="mt-3">
        <div class="border rounded p-3 bg-light">
            <p class="mb-2">@{{ convertedMessage }}</p>
            <button class="btn btn-sm btn-secondary" @click="copyToClipboard">
                <i class="ni ni-single-copy-04"></i> {{ __('Copy') }}
            </button>
        </div>
    </div>
</div>

<!-- AI Chat Summary Section -->
<h5 class="text-muted mt-4">{{ __('Chat Summary')}}</h5>
<div class="contacInfo border-radius-lg border p-4 mb-4">
    <button class="btn btn-primary w-100" @click="summarizeChat">
        <i class="ni ni-bulb-61 mr-2"></i> {{ __('Generate Chat Summary') }}
    </button>

    <div v-if="dynamicProperties.isGeneratingSummary" class="mt-3 text-center">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">{{ __('Loading...') }}</span>
        </div>
        <p class="mt-2">{{ __('Generating summary...') }}</p>
    </div>

    <div v-if="activeChat && activeChat.summary" class="mt-3">
        <div class="border rounded p-3 bg-light">
            <h6 class="font-weight-bold mb-3">{{ __('Summary') }}</h6>
            <p class="mb-3" style="white-space: pre-line">@{{ activeChat.summary }}</p>
        </div>
    </div>

    <div v-if="activeChat && activeChat.summaryError" class="mt-3">
        <div class="alert alert-danger">
            @{{ activeChat.summaryError }}
        </div>
    </div>
</div>

<!-- Ask AI Section -->
<h5 class="text-muted mt-4">{{ __('Ask AI')}}</h5>
<div class="contacInfo border-radius-lg border p-4 mb-4">
    <div class="form-group">
        <textarea 
            class="form-control" 
            v-model="aiQuestion" 
            rows="3" 
            placeholder="{{ __('Ask AI a question, Also question related to the conversation...') }}"
        ></textarea>
    </div>

    <button 
        class="btn btn-primary w-100" 
        @click="askAI"
        :disabled="dynamicProperties.isAskingAI"
    >
        <i class="ni ni-chat-round mr-2"></i> {{ __('Ask AI') }}
    </button>

    <div v-if="dynamicProperties.isAskingAI" class="mt-3 text-center">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">{{ __('Loading...') }}</span>
        </div>
        <p class="mt-2">{{ __('AI is analyzing the conversation...') }}</p>
    </div>
        
        <div v-if="dynamicProperties.aiResponse" class="mt-3">
        <div class="border rounded p-3 bg-light">
            <h6 class="font-weight-bold mb-3">{{ __('AI Response') }}</h6>
            <p class="mb-3" style="white-space: pre-line">@{{ dynamicProperties.aiResponse }}</p>
            <button class="btn btn-sm btn-secondary" @click="copyAIResponseToClipboard">
                <i class="ni ni-single-copy-04"></i> {{ __('Copy') }}
            </button>
        </div>
    </div>

    <div v-if="aiError" class="mt-3">
        <div class="alert alert-danger">
            @{{ aiError }}
        </div>
    </div>
</div>





