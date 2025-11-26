@extends('layouts.app', ['title' => __('Web Support Widget Configuration')])

@section('content')
<div class="header pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <h1 class="mb-3 mt--3">🎛️ {{ __('Web Support Widget Configuration') }}</h1>
            <p class="text-muted">Create a modern, multi-tab support widget for your website with Chat, Email, and Help functionality.</p>
        </div>
    </div>
</div>

<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-12">
            @include('partials.flash')
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <h4>Validation Errors:</h4>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        
        <div class="col-lg-8">
            <form action="{{ route('websupportwidget.store') }}" method="POST" enctype="multipart/form-data" id="widgetForm">
                @csrf
                
                <!-- General Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h3 class="mb-0">{{ __('General Settings') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                @include('partials.input', [
                                    'name' => 'Company Name',
                                    'id' => 'company_name',
                                    'placeholder' => 'Your Company Name',
                                    'required' => true,
                                    'value' => $widget['company_name']
                                ])
                            </div>
                            <div class="col-md-6">
                                @include('partials.select', [
                                    'name' => 'Widget Position',
                                    'id' => 'position',
                                    'data' => [
                                        'bottom-right' => 'Bottom Right',
                                        'bottom-left' => 'Bottom Left'
                                    ],
                                    'required' => true,
                                    'value' => $widget['position']
                                ])
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                @include('partials.textarea', [
                                    'name' => 'Welcome Message',
                                    'id' => 'welcome_message',
                                    'placeholder' => 'Hi there! 👋 How can we help you today?',
                                    'required' => true,
                                    'value' => $widget['welcome_message']
                                ])
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                @include('partials.colorpicker', [
                                    'name' => 'Primary Color',
                                    'id' => 'primary_color',
                                    'required' => true,
                                    'value' => $widget['primary_color']
                                ])
                            </div>
                            <div class="col-md-6">
                                @include('partials.colorpicker', [
                                    'name' => 'Secondary Color',
                                    'id' => 'secondary_color',
                                    'required' => true,
                                    'value' => $widget['secondary_color']
                                ])
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                @include('partials.images', [
                                    'image' => [
                                        'name' => 'logo',
                                        'label' => __('Company Logo'),
                                        'value' => $widget['logo'],
                                        'style' => 'width: 100px; height: 100px; border-radius: 50%;'
                                    ]
                                ])
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Tab Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">💬 {{ __('Chat Tab Settings') }}</h3>
                            </div>
                            <div class="col-auto">
                                @include('partials.toggle', [
                                    'id' => 'chat_enabled',
                                    'name' => 'Enable Chat Tab',
                                    'checked' => $widget['chat_enabled']
                                ])
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="chatSettings">
                        <div class="row">
                            <div class="col-md-6">
                                @include('partials.input', [
                                    'name' => 'WhatsApp Number',
                                    'id' => 'whatsapp_number',
                                    'placeholder' => '+1234567890',
                                    'required' => true,
                                    'value' => $widget['whatsapp_number']
                                ])
                            </div>
                            <div class="col-md-6">
                                @include('partials.input', [
                                    'name' => 'Chat Button Text',
                                    'id' => 'chat_button_text',
                                    'placeholder' => 'Start WhatsApp Chat',
                                    'required' => true,
                                    'value' => $widget['chat_button_text']
                                ])
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                @include('partials.textarea', [
                                    'name' => 'Chat Welcome Message',
                                    'id' => 'chat_welcome_message',
                                    'placeholder' => 'Hi! Let us know how we can help you.',
                                    'required' => true,
                                    'value' => $widget['chat_welcome_message']
                                ])
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Tab Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">📧 {{ __('Email Tab Settings') }}</h3>
                            </div>
                            <div class="col-auto">
                                @include('partials.toggle', [
                                    'id' => 'email_enabled',
                                    'name' => 'Enable Email Tab',
                                    'checked' => $widget['email_enabled']
                                ])
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="emailSettings">
                        <div class="row">
                            <div class="col-md-6">
                                @include('partials.input', [
                                    'name' => 'Email Recipient',
                                    'id' => 'email_recipient',
                                    'type' => 'email',
                                    'placeholder' => 'support@company.com',
                                    'required' => true,
                                    'value' => $widget['email_recipient']
                                ])
                            </div>
                            <div class="col-md-6">
                                @include('partials.input', [
                                    'name' => 'Email Subject Prefix',
                                    'id' => 'email_subject_prefix',
                                    'placeholder' => 'Contact Form',
                                    'required' => true,
                                    'value' => $widget['email_subject_prefix']
                                ])
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                @include('partials.textarea', [
                                    'name' => 'Email Welcome Message',
                                    'id' => 'email_welcome_message',
                                    'placeholder' => 'Send us a message and we\'ll get back to you as soon as possible.',
                                    'required' => true,
                                    'value' => $widget['email_welcome_message']
                                ])
                            </div>
                            <div class="col-md-6">
                                @include('partials.textarea', [
                                    'name' => 'Email Success Message',
                                    'id' => 'email_success_message',
                                    'placeholder' => 'Thank you! We will get back to you soon.',
                                    'required' => true,
                                    'value' => $widget['email_success_message']
                                ])
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help Tab Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">❓ {{ __('Help Tab Settings') }}</h3>
                            </div>
                            <div class="col-auto">
                                @include('partials.toggle', [
                                    'id' => 'help_enabled',
                                    'name' => 'Enable Help Tab',
                                    'checked' => $widget['help_enabled']
                                ])
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="helpSettings">
                        <div class="row">
                            <div class="col-md-8">
                                @include('partials.textarea', [
                                    'name' => 'Help Welcome Message',
                                    'id' => 'help_welcome_message',
                                    'placeholder' => 'Browse our help articles to find answers quickly.',
                                    'required' => true,
                                    'value' => $widget['help_welcome_message']
                                ])
                            </div>
                            <div class="col-md-4">
                                @include('partials.input', [
                                    'name' => 'Articles Limit',
                                    'id' => 'help_articles_limit',
                                    'type' => 'number',
                                    'placeholder' => '5',
                                    'required' => true,
                                    'value' => $widget['help_articles_limit'],
                                    'min' => '1',
                                    'max' => '10'
                                ])
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                @include('partials.toggle', [
                                    'id' => 'help_show_search',
                                    'name' => 'Show Search Box',
                                    'checked' => $widget['help_show_search']
                                ])
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Call Tab Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0">📞 {{ __('Call Tab') }}</h3>
                            </div>
                            <div class="col-auto">
                                @include('partials.toggle', [
                                    'id' => 'call_enabled',
                                    'name' => 'Enable Call Tab',
                                    'checked' => $widget['call_enabled'] ?? false
                                ])
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="callSettings">
                        <div class="row">
                            <div class="col-md-12">
                                @include('partials.textarea', [
                                    'name' => 'Call Information Text',
                                    'id' => 'call_info_message',
                                    'placeholder' => 'We are available Mon-Fri 9:00-17:00 UTC. Tap the button below to call us on WhatsApp.',
                                    'required' => false,
                                    'value' => $widget['call_info_message'] ?? ''
                                ])
                                <small class="text-muted d-block mb-3">{{ __('WhatsApp number is taken from Chat Tab -> WhatsApp Number') }}</small>
                            </div>
                            <div class="col-md-12">
                                @include('partials.input', [
                                    'name' => 'WhatsApp Call Link URL',
                                    'id' => 'call_link_url',
                                    'placeholder' => 'e.g. whatsapp://call?number=1234567890 or https://wa.me/c/<your-call-link>',
                                    'required' => false,
                                    'value' => $widget['call_link_url'] ?? ''
                                ])
                                <small class="text-muted">{{ __('Need help? Follow the official WhatsApp guide:') }} <a href="https://faq.whatsapp.com/456694046556486/?helpref=uf_share" target="_blank">{{ __('Create a WhatsApp call link') }}</a></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h3 class="mb-0">⚙️ {{ __('Advanced Settings') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                @include('partials.toggle', [
                                    'id' => 'show_company_logo',
                                    'name' => 'Show Company Logo',
                                    'checked' => $widget['show_company_logo']
                                ])
                            </div>
                            <div class="col-md-6">
                                @include('partials.toggle', [
                                    'id' => 'show_agent_status',
                                    'name' => 'Show Online Status',
                                    'checked' => $widget['show_agent_status']
                                ])
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                @include('partials.input', [
                                    'name' => 'Offline Message',
                                    'id' => 'offline_message',
                                    'placeholder' => 'We\'re currently offline. Please leave a message!',
                                    'value' => $widget['offline_message']
                                ])
                            </div>
                            <div class="col-md-6">
                                @include('partials.select', [
                                    'name' => 'Timezone',
                                    'id' => 'timezone',
                                    'data' => [
                                        'UTC' => 'UTC',
                                        'America/New_York' => 'Eastern Time',
                                        'America/Chicago' => 'Central Time',
                                        'America/Denver' => 'Mountain Time',
                                        'America/Los_Angeles' => 'Pacific Time',
                                        'Europe/London' => 'London',
                                        'Europe/Paris' => 'Paris',
                                        'Europe/Berlin' => 'Berlin',
                                        'Asia/Tokyo' => 'Tokyo',
                                        'Asia/Shanghai' => 'Shanghai',
                                        'Australia/Sydney' => 'Sydney'
                                    ],
                                    'required' => true,
                                    'value' => $widget['timezone']
                                ])
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="ni ni-check-bold"></i>
                        {{ __('Save Widget Configuration') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview and Embed Code -->
        <div class="col-lg-4">
            @if(isset($widget['embed_url']))

                <!-- Embed Code -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h3 class="mb-0">{{ __('Embed Code') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-control-label">{{ __('Copy this code to your website') }}</label>
                            <textarea class="form-control" id="embedCode" rows="4" readonly><script src="{{ $widget['embed_url'] }}"></script></textarea>
                            <button type="button" class="btn btn-primary btn-sm mt-2" onclick="copyEmbedCode()">
                                <i class="ni ni-single-copy-04"></i>
                                {{ __('Copy Code') }}
                            </button>
                        </div>
                        
                        <div class="alert alert-info">
                            <strong>{{ __('How to use:') }}</strong><br>
                            {{ __('Paste this code before the closing body tag on any page where you want the widget to appear.') }}
                        </div>
                        
                        
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function copyEmbedCode() {
    const embedCode = document.getElementById('embedCode');
    embedCode.select();
    embedCode.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        
        // Show success notification
        if (typeof js !== 'undefined' && js.notify) {
            js.notify('{{ __("Embed code copied to clipboard!") }}', 'success');
        } else {
            alert('{{ __("Embed code copied to clipboard!") }}');
        }
    } catch (err) {
        console.error('Failed to copy embed code:', err);
        alert('{{ __("Failed to copy. Please select and copy manually.") }}');
    }
}

// Debug form submission
document.getElementById('widgetForm').addEventListener('submit', function(e) {
    console.log('Form submitting...');
    
    // Log all form data
    const formData = new FormData(this);
    console.log('Form fields:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    // Check required fields
    const requiredFields = ['company_name', 'welcome_message', 'primary_color', 'secondary_color', 'position', 'timezone'];
    const missingFields = [];
    
    requiredFields.forEach(field => {
        if (!formData.get(field)) {
            missingFields.push(field);
        }
    });
    
    // Debug checkbox values
    const checkboxFields = ['chat_enabled', 'email_enabled', 'help_enabled', 'help_show_search', 'show_company_logo', 'show_agent_status'];
    console.log('Checkbox values:');
    checkboxFields.forEach(field => {
        const checkbox = document.querySelector(`input[name="${field}"]`);
        console.log(`${field}: checked=${checkbox?.checked}, value=${formData.get(field)}`);
    });
    
    if (missingFields.length > 0) {
        console.error('Missing required fields:', missingFields);
        e.preventDefault();
        alert('Missing required fields: ' + missingFields.join(', '));
        return;
    }
    
    // Show loading state
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="ni ni-settings-gear-65 fa-spin"></i> Saving...';
    submitButton.disabled = true;
    
    // Re-enable after 10 seconds as fallback
    setTimeout(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    }, 10000);
});

// Toggle settings visibility based on tab enablement
document.addEventListener('DOMContentLoaded', function() {
    const chatToggle = document.querySelector('input[name="chat_enabled"]');
    const emailToggle = document.querySelector('input[name="email_enabled"]');
    const helpToggle = document.querySelector('input[name="help_enabled"]');
    
    function toggleSettings(toggle, settingsId) {
        const settings = document.getElementById(settingsId);
        if (settings) {
            settings.style.opacity = toggle.checked ? '1' : '0.5';
            const inputs = settings.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.disabled = !toggle.checked;
            });
        }
    }
    
    if (chatToggle) {
        chatToggle.addEventListener('change', () => toggleSettings(chatToggle, 'chatSettings'));
        toggleSettings(chatToggle, 'chatSettings'); // Initial state
    }
    
    if (emailToggle) {
        emailToggle.addEventListener('change', () => toggleSettings(emailToggle, 'emailSettings'));
        toggleSettings(emailToggle, 'emailSettings'); // Initial state
    }
    
    if (helpToggle) {
        helpToggle.addEventListener('change', () => toggleSettings(helpToggle, 'helpSettings'));
        toggleSettings(helpToggle, 'helpSettings'); // Initial state
    }
});
</script>

@if(isset($widget['embed_url']))
    <!-- Load the actual widget for testing -->
    <script src="{{ $widget['embed_url'] }}"></script>
@endif

@endsection
