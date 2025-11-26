// Web Support Widget v2.0 - Modern Multi-Tab Support Widget
(function() {
    'use strict';
    
    // Widget configuration
    const config = {
        id: '{{ $id }}',
        companyName: '{{ $company_name }}',
        welcomeMessage: `{{ $welcome_message }}`,
        primaryColor: '{{ $primary_color }}',
        secondaryColor: '{{ $secondary_color }}',
        position: '{{ $position }}',
        logo: '{{ $logo }}',
        isOnline: {{ $is_online ? 'true' : 'false' }},
        
        // Tab configurations
        chat: {
            enabled: {{ $chat_enabled ? 'true' : 'false' }},
            whatsappNumber: '{{ $whatsapp_number }}',
            welcomeMessage: `{{ $chat_welcome_message }}`,
            buttonText: '{{ $chat_button_text }}',
            whatsappUrl: '{{ $whatsapp_url }}'
        },
        email: {
            enabled: {{ $email_enabled ? 'true' : 'false' }},
            welcomeMessage: `{{ $email_welcome_message }}`,
            successMessage: `{{ $email_success_message }}`
        },
        help: {
            enabled: {{ $help_enabled ? 'true' : 'false' }},
            welcomeMessage: `{{ $help_welcome_message }}`,
            showSearch: {{ $help_show_search ? 'true' : 'false' }},
            articlesLimit: {{ $help_articles_limit }}
        },
        call: {
            enabled: {{ ($call_enabled ?? false) ? 'true' : 'false' }},
            whatsappNumber: '{{ $whatsapp_number }}',
            welcomeMessage: `{{ $call_info_message }}`,
            buttonText: 'Call us on WhatsApp'
        },
        
        // Advanced settings
        showLogo: {{ $show_company_logo ? 'true' : 'false' }},
        showStatus: {{ $show_agent_status ? 'true' : 'false' }},
        offlineMessage: `{{ $offline_message }}`,
        
        // API endpoints
        apiBase: '{{ config('app.url') }}',
        assetsUrl: '{{ $widget_url }}',
        callLinkUrl: '{{ $call_link_url ?? '' }}'
    };

    // CSS Styles
    const styles = `
        <style>
        :root {
            --ws-primary: ${config.primaryColor};
            --ws-secondary: ${config.secondaryColor};
            --ws-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --ws-shadow-lg: 0 35px 60px -12px rgba(0, 0, 0, 0.35);
            --ws-border-radius: 24px;
            --ws-border-radius-sm: 16px;
        }
        
        .ws-widget-container {
            position: fixed;
            ${config.position === 'bottom-left' ? 'left: 24px;' : 'right: 24px;'}
            bottom: 24px;
            z-index: 999999;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .ws-widget-button {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--ws-primary) 0%, var(--ws-secondary) 100%);
            border-radius: 50%;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--ws-shadow);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .ws-widget-button:hover {
            transform: scale(1.05);
            box-shadow: var(--ws-shadow-lg);
        }
        
        .ws-widget-button:active {
            transform: scale(0.95);
        }
        
        .ws-widget-button::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .ws-widget-button:hover::before {
            opacity: 1;
        }
        
        .ws-widget-icon {
            width: 28px;
            height: 28px;
            fill: white;
            transition: transform 0.3s ease;
        }
        
        .ws-widget-button.active .ws-widget-icon {
            transform: rotate(45deg);
        }
        
        .ws-widget-panel {
            position: absolute;
            bottom: 80px;
            ${config.position === 'bottom-left' ? 'left: 0;' : 'right: 0;'}
            width: 420px;
            max-width: calc(100vw - 48px);
            background: white;
            border-radius: var(--ws-border-radius);
            box-shadow: var(--ws-shadow);
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px) scale(0.95);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        
        .ws-widget-panel.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }
        
        .ws-widget-header {
            background: #f3f4f6;
            padding: 24px;
            position: relative;
        }
        
        .ws-widget-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        }
        
        .ws-widget-header-content {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .ws-widget-logo {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            overflow: hidden;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .ws-widget-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .ws-widget-logo-fallback {
            color: white;
            font-size: 20px;
            font-weight: 600;
        }
        
        .ws-widget-info {
            flex: 1;
        }
        
        .ws-widget-company-name {
            color: #303030;
            font-size: 18px;
            font-weight: 600;
            margin: 0;
            line-height: 1.2;
        }
        
        .ws-widget-status {
            color: #303030;
            font-size: 14px;
            margin: 4px 0 0 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .ws-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            animation: pulse 2s infinite;
        }
        
        .ws-status-dot.offline {
            background: #ef4444;
            animation: none;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .ws-widget-close {
            background: none;
            border: none;
            color: rgba(255,255,255,0.8);
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        
        .ws-widget-close:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .ws-widget-tabs {
            display: flex;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .ws-widget-tab {
            flex: 1;
            padding: 16px 12px;
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            position: relative;
        }
        
        .ws-widget-tab:hover {
            background: #f1f5f9;
        }
        
        .ws-widget-tab.active {
            background: white;
            color: var(--ws-primary);
        }
        
        .ws-widget-tab.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--ws-primary);
            border-radius: 2px 2px 0 0;
        }
        
        .ws-tab-icon {
            width: 20px;
            height: 20px;
            fill: #64748b;
            transition: fill 0.2s ease;
        }
        
        .ws-widget-tab.active .ws-tab-icon {
            fill: var(--ws-primary);
        }
        
        .ws-tab-label {
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
            transition: color 0.2s ease;
        }
        
        .ws-widget-tab.active .ws-tab-label {
            color: var(--ws-primary);
        }
        
        .ws-widget-content {
            min-height: 320px;
            max-height: 480px;
            overflow-y: auto;
        }
        
        .ws-tab-content {
            display: none;
        }
        
        .ws-tab-content.active {
            display: block;
        }
        
        .ws-welcome-message {
            font-size: 16px;
            color: #374151;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        /* Chat Tab Styles */
        .ws-chat-container {
            background: #e5ddd5 url('${config.assetsUrl}vendor/meta/bg.png') repeat;
            background-size: 420px auto;
            background-position: top left;
            padding: 20px 16px;
            min-height: 400px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        
        .ws-chat-messages {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .ws-chat-message {
            max-width: 85%;
            animation: messageSlideIn 0.3s ease-out;
        }
        
        @keyframes messageSlideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .ws-chat-bubble {
            background: white;
            padding: 8px 12px 6px 12px;
            border-radius: 8px;
            box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
            position: relative;
        }
        
        .ws-chat-bubble::before {
            
        }
        
        .ws-chat-bubble-text {
            color: #303030;
            font-size: 14px;
            line-height: 1.5;
            margin: 0 0 4px 0;
            word-wrap: break-word;
            white-space: pre-wrap;
        }
        
        .ws-chat-time {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
            font-size: 11px;
            color: #667781;
            margin-top: 2px;
        }
        
        .ws-chat-checkmarks {
            width: 16px;
            height: 12px;
            display: inline-flex;
            align-items: center;
        }
        
        .ws-chat-checkmarks svg {
            width: 16px;
            height: 12px;
            fill: #53bdeb;
        }
        
        .ws-chat-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #dfe5e7;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .ws-chat-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .ws-chat-avatar-text {
            color: #8696a0;
            font-size: 14px;
            font-weight: 500;
        }
        
        .ws-chat-message-wrapper {
            display: flex;
            gap: 8px;
            align-items: flex-end;
        }
        
        .ws-chat-sender-name {
            color: #667781;
            font-size: 12px;
            font-weight: 500;
            margin: 0 0 4px 0;
        }
        
        .ws-chat-button {
            width: 100%;
            background: #25d366;
            color: white;
            border: none;
            border-radius: 24px;
            padding: 12px 24px;
            font-size: 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.15);
        }
        
        .ws-chat-button:hover {
            background: #20ba5a;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .ws-chat-button:active {
            transform: scale(0.98);
        }
        
        .ws-chat-icon {
            width: 20px;
            height: 20px;
            fill: white;
        }
        
        .ws-typing-indicator {
            display: flex;
            gap: 4px;
            padding: 12px 16px;
            background: white;
            border-radius: 8px;
            width: fit-content;
            box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
        }
        
        .ws-typing-dot {
            width: 8px;
            height: 8px;
            background: #90949c;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }
        
        .ws-typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        .ws-typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }
        
        @keyframes typing {
            0%, 60%, 100% {
                opacity: 0.3;
                transform: translateY(0);
            }
            30% {
                opacity: 1;
                transform: translateY(-4px);
            }
        }
        
        /* Email Tab Styles */
        .ws-form-group {
            margin-bottom: 20px;
        }
        
        .ws-form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }
        
        .ws-form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: var(--ws-border-radius-sm);
            font-size: 14px;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }
        
        .ws-form-input:focus {
            outline: none;
            border-color: var(--ws-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .ws-form-textarea {
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }
        
        .ws-submit-button {
            width: 100%;
            background: var(--ws-primary);
            color: white;
            border: none;
            border-radius: var(--ws-border-radius-sm);
            padding: 14px 24px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .ws-submit-button:hover {
            background: var(--ws-secondary);
            transform: translateY(-1px);
        }
        
        .ws-submit-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Help Tab Styles */
        .ws-search-box {
            position: relative;
            margin-bottom: 20px;
        }
        
        .ws-search-input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 2px solid #e2e8f0;
            border-radius: var(--ws-border-radius-sm);
            font-size: 14px;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }
        
        .ws-search-input:focus {
            outline: none;
            border-color: var(--ws-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .ws-search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            fill: #9ca3af;
        }
        
        .ws-article-list {
            space-y: 12px;
        }
        
        .ws-article-item {
            display: block;
            padding: 16px;
            background: #f8fafc;
            border-radius: var(--ws-border-radius-sm);
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }
        
        .ws-article-item:hover {
            background: #f1f5f9;
            border-color: var(--ws-primary);
            transform: translateY(-1px);
        }
        
        .ws-article-title {
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 8px 0;
            line-height: 1.4;
        }
        
        .ws-article-excerpt {
            font-size: 13px;
            color: #6b7280;
            margin: 0 0 8px 0;
            line-height: 1.4;
        }
        
        .ws-article-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12px;
            color: #9ca3af;
        }
        
        .ws-category-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 16px;
        }
        
        .ws-category-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 16px 12px;
            background: #f8fafc;
            border-radius: var(--ws-border-radius-sm);
            text-decoration: none;
            color: inherit;
            transition: all 0.2s ease;
            text-align: center;
            cursor: pointer;
        }
        
        .ws-category-item:hover {
            background: #f1f5f9;
            transform: translateY(-2px);
        }
        
        .ws-category-icon {
            width: 32px;
            height: 32px;
            background: var(--ws-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
        }
        
        .ws-category-name {
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin: 0;
        }
        
        .ws-back-button {
            display: flex;
            align-items: center;
            gap: 8px;
            background: none;
            border: none;
            color: var(--ws-primary);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            padding: 8px 12px;
            margin-bottom: 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        
        .ws-back-button:hover {
            background: #f1f5f9;
            transform: translateX(-2px);
        }
        
        .ws-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        
        .ws-spinner {
            width: 24px;
            height: 24px;
            border: 3px solid #e2e8f0;
            border-top: 3px solid var(--ws-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .ws-success-message {
            text-align: center;
            padding: 40px 20px;
            color: #059669;
        }
        
        .ws-success-icon {
            width: 48px;
            height: 48px;
            fill: #059669;
            margin: 0 auto 16px;
        }
        
        .ws-error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: var(--ws-border-radius-sm);
            font-size: 14px;
            margin-bottom: 16px;
        }
        
        @media (max-width: 480px) {
            .ws-widget-container {
                ${config.position === 'bottom-left' ? 'left: 16px;' : 'right: 16px;'}
                bottom: 16px;
            }
            
            .ws-widget-panel {
                width: calc(100vw - 32px);
                ${config.position === 'bottom-left' ? 'left: 0;' : 'right: 0;'}
            }
            
            .ws-category-list {
                grid-template-columns: 1fr;
            }
        }
        </style>
    `;

    // Widget HTML
    const widgetHTML = `
        <div class="ws-widget-container" id="webSupportWidget">
            <button class="ws-widget-button" id="wsToggleButton">
                <svg class="ws-widget-icon" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </button>
            
            <div class="ws-widget-panel" id="wsPanel">
                <div class="ws-widget-header">
                    <div class="ws-widget-header-content">
                        ${config.showLogo ? `
                            <div class="ws-widget-logo">
                                ${config.logo ? `<img src="${config.logo}" alt="${config.companyName}">` : `<span class="ws-widget-logo-fallback">${config.companyName.charAt(0)}</span>`}
                            </div>
                        ` : ''}
                        <div class="ws-widget-info">
                            <h3 class="ws-widget-company-name">${config.companyName}</h3>
                            ${config.showStatus ? `
                                <div class="ws-widget-status">
                                    <span class="ws-status-dot ${config.isOnline ? '' : 'offline'}"></span>
                                    <span>${config.isOnline ? 'Online' : 'Offline'}</span>
                                </div>
                            ` : ''}
                        </div>
                        <button class="ws-widget-close" id="wsCloseButton">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="ws-widget-tabs">
                    ${config.chat.enabled ? `
                        <button class="ws-widget-tab active" data-tab="chat">
                            <svg class="ws-tab-icon" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.479 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                            <span class="ws-tab-label">Chat</span>
                        </button>
                    ` : ''}
                    ${config.call.enabled ? `
                        <button class="ws-widget-tab ${!config.chat.enabled ? 'active' : ''}" data-tab="call">
                            <svg class="ws-tab-icon" viewBox="0 0 24 24">
                                <path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 011 1V21a1 1 0 01-1 1C10.4 22 2 13.6 2 3a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.46.57 3.58a1 1 0 01-.25 1.01l-2.2 2.2z"/>
                            </svg>
                            <span class="ws-tab-label">Call</span>
                        </button>
                    ` : ''}
                    ${config.email.enabled ? `
                        <button class="ws-widget-tab ${!config.chat.enabled && !config.call.enabled ? 'active' : ''}" data-tab="email">
                            <svg class="ws-tab-icon" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            <span class="ws-tab-label">Email</span>
                        </button>
                    ` : ''}
                    ${config.help.enabled ? `
                        <button class="ws-widget-tab ${!config.chat.enabled && !config.call.enabled && !config.email.enabled ? 'active' : ''}" data-tab="help">
                            <svg class="ws-tab-icon" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
                            </svg>
                            <span class="ws-tab-label">Help</span>
                        </button>
                    ` : ''}
                </div>
                
                <div class="ws-widget-content" style="padding: 0;">
                    ${config.chat.enabled ? `
                        <div class="ws-tab-content active" id="chatTab">
                            <div class="ws-chat-container">
                                <div class="ws-chat-messages">
                                    <div class="ws-chat-message">
                                        <div class="ws-chat-message-wrapper">
                                            <div class="ws-chat-avatar">
                                                ${config.logo ? `<img src="${config.logo}" alt="${config.companyName}">` : `<span class="ws-chat-avatar-text">${config.companyName.charAt(0)}</span>`}
                                            </div>
                                            <div style="flex: 1;">
                                                <p class="ws-chat-sender-name">${config.companyName}</p>
                                                <div class="ws-chat-bubble">
                                                    <p class="ws-chat-bubble-text">${config.chat.welcomeMessage}</p>
                                                    <div class="ws-chat-time">
                                                        <span id="wsChatTime"></span>
                                                        <span class="ws-chat-checkmarks">
                                                            <svg viewBox="0 0 18 18">
                                                                <path d="M17.394 5.035l-.57-.444a.434.434 0 0 0-.609.076l-6.39 8.198a.38.38 0 0 1-.577.039l-.427-.388a.381.381 0 0 0-.578.038l-.451.576a.497.497 0 0 0 .043.645l1.575 1.51a.38.38 0 0 0 .577-.039l7.483-9.602a.436.436 0 0 0-.076-.609zm-4.892 0l-.57-.444a.434.434 0 0 0-.609.076l-6.39 8.198a.38.38 0 0 1-.577.039l-2.614-2.556a.435.435 0 0 0-.614.007l-.505.516a.435.435 0 0 0 .007.614l3.887 3.8a.38.38 0 0 0 .577-.039l7.483-9.602a.435.435 0 0 0-.075-.609z"/>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a href="${config.chat.whatsappUrl}" target="_blank" class="ws-chat-button">
                                    <svg class="ws-chat-icon" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.479 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                    </svg>
                                    <span>${config.chat.buttonText}</span>
                                </a>
                            </div>
                        </div>
                    ` : ''}
                    ${config.call.enabled ? `
                        <div class="ws-tab-content ${!config.chat.enabled ? 'active' : ''}" id="callTab">
                            <div class="ws-chat-container">
                                <div class="ws-welcome-message">${config.call.welcomeMessage}</div>
                                ${(config.callLinkUrl || '').length > 0 ? `
                                    <div style="padding: 24px; position: absolute; bottom: 24px; left: 0; right: 0; display: flex; justify-content: center;">
                                        <a href="${config.callLinkUrl}" class="ws-chat-button">
                                            <svg class="ws-chat-icon" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.479 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                            </svg>
                                            <span>${config.call.buttonText}</span>
                                        </a>
                                    </div>
                                ` : `
                                    <div class="ws-error-message" style="background:#eef2ff;color:#3730a3;border-color:#c7d2fe">Please set your WhatsApp call link in settings. See the <a href="https://faq.whatsapp.com/456694046556486/?helpref=uf_share" target="_blank" rel="noopener">official guide</a>.</div>
                                `}
                            </div>
                        </div>
                    ` : ''}
                    
                    ${config.email.enabled ? `
                        <div class="ws-tab-content ${!config.chat.enabled ? 'active' : ''}" id="emailTab" style="padding: 24px;">
                            <div class="ws-welcome-message">${config.email.welcomeMessage}</div>
                            <form id="wsEmailForm">
                                <div class="ws-form-group">
                                    <label class="ws-form-label">Name *</label>
                                    <input type="text" class="ws-form-input" name="name" required>
                                </div>
                                <div class="ws-form-group">
                                    <label class="ws-form-label">Email *</label>
                                    <input type="email" class="ws-form-input" name="email" required>
                                </div>
                                <div class="ws-form-group">
                                    <label class="ws-form-label">Message *</label>
                                    <textarea class="ws-form-input ws-form-textarea" name="message" required placeholder="How can we help you?"></textarea>
                                </div>
                                <button type="submit" class="ws-submit-button">Send Message</button>
                            </form>
                        </div>
                    ` : ''}
                    
                    ${config.help.enabled ? `
                        <div class="ws-tab-content ${!config.chat.enabled && !config.email.enabled ? 'active' : ''}" id="helpTab" style="padding: 24px;">
                            <div class="ws-welcome-message">${config.help.welcomeMessage}</div>
                            ${config.help.showSearch ? `
                                <div class="ws-search-box">
                                    <input type="text" class="ws-search-input" id="wsHelpSearch" placeholder="Search help articles...">
                                    <svg class="ws-search-icon" viewBox="0 0 24 24">
                                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                                    </svg>
                                </div>
                            ` : ''}
                            <div id="wsHelpContent">
                                <div class="ws-loading">
                                    <div class="ws-spinner"></div>
                                </div>
                            </div>
                        </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;

    // Initialize widget
    function initWidget() {
        // Inject styles
        const styleElement = document.createElement('div');
        styleElement.innerHTML = styles;
        document.head.appendChild(styleElement.firstElementChild);

        // Inject HTML
        const widgetContainer = document.createElement('div');
        widgetContainer.innerHTML = widgetHTML;
        document.body.appendChild(widgetContainer.firstElementChild);

        // Bind events
        bindEvents();
        
        // Set chat message time
        if (config.chat.enabled) {
            setChatMessageTime();
        }
        
        // Load initial help content if help tab is enabled
        if (config.help.enabled) {
            loadHelpContent();
        }
    }
    
    // Set current time on chat message
    function setChatMessageTime() {
        const chatTimeElement = document.getElementById('wsChatTime');
        if (chatTimeElement) {
            const now = new Date();
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            chatTimeElement.textContent = `${hours}:${minutes}`;
        }
    }

    // Bind event listeners
    function bindEvents() {
        const toggleButton = document.getElementById('wsToggleButton');
        const closeButton = document.getElementById('wsCloseButton');
        const panel = document.getElementById('wsPanel');
        const tabs = document.querySelectorAll('.ws-widget-tab');
        const emailForm = document.getElementById('wsEmailForm');
        const helpSearch = document.getElementById('wsHelpSearch');

        // Toggle widget
        toggleButton.addEventListener('click', () => {
            const isActive = panel.classList.contains('active');
            if (isActive) {
                closeWidget();
            } else {
                openWidget();
            }
        });

        // Close widget
        if (closeButton) {
            closeButton.addEventListener('click', closeWidget);
        }

        // Tab switching
        tabs.forEach(tab => {
            tab.addEventListener('click', () => switchTab(tab.dataset.tab));
        });

        // Email form submission
        if (emailForm) {
            emailForm.addEventListener('submit', handleEmailSubmit);
        }

        // Help search
        if (helpSearch) {
            let searchTimeout;
            helpSearch.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchHelpArticles(e.target.value);
                }, 300);
            });
        }

        // Close on outside click
        document.addEventListener('click', (e) => {
            const widget = document.getElementById('webSupportWidget');
            if (widget && !widget.contains(e.target)) {
                closeWidget();
            }
        });
    }

    // Widget control functions
    function openWidget() {
        document.getElementById('wsPanel').classList.add('active');
        document.getElementById('wsToggleButton').classList.add('active');
    }

    function closeWidget() {
        document.getElementById('wsPanel').classList.remove('active');
        document.getElementById('wsToggleButton').classList.remove('active');
    }

    function switchTab(tabName) {
        // Update tab buttons
        document.querySelectorAll('.ws-widget-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');

        // Update tab content
        document.querySelectorAll('.ws-tab-content').forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById(tabName + 'Tab').classList.add('active');
    }

    // Email form handler
    async function handleEmailSubmit(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('.ws-submit-button');
        
        // Add widget ID
        formData.append('widget_id', config.id);
        
        // Show loading state
        submitButton.disabled = true;
        submitButton.textContent = 'Sending...';
        
        try {
            const response = await fetch(config.apiBase + '/websupport/send-email', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            
            if (result.status === 'success') {
                // Show success message
                document.getElementById('emailTab').innerHTML = `
                    <div class="ws-success-message">
                        <svg class="ws-success-icon" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <h3 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 600;">Message Sent!</h3>
                        <p style="margin: 0; color: #6b7280;">${result.message}</p>
                    </div>
                `;
            } else {
                throw new Error(result.message || 'Failed to send email');
            }
        } catch (error) {
            // Show error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'ws-error-message';
            errorDiv.textContent = error.message;
            form.insertBefore(errorDiv, form.firstChild);
            
            // Remove error after 5 seconds
            setTimeout(() => errorDiv.remove(), 5000);
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Send Message';
        }
    }

    // Help content functions
    async function loadHelpContent() {
        try {
            const response = await fetch(`${config.apiBase}/websupport/knowledge-categories?widget_id=${config.id}`);
            const result = await response.json();
            
            if (result.status === 'success') {
                renderHelpCategories(result.categories);
            } else {
                document.getElementById('wsHelpContent').innerHTML = '<div style="text-align: center; color: #6b7280; padding: 20px;">Knowledge base not available</div>';
            }
        } catch (error) {
            console.error('Failed to load help content:', error);
            document.getElementById('wsHelpContent').innerHTML = '<div style="text-align: center; color: #6b7280; padding: 20px;">Help content unavailable</div>';
        }
    }

    async function searchHelpArticles(query) {
        const helpContent = document.getElementById('wsHelpContent');
        helpContent.innerHTML = '<div class="ws-loading"><div class="ws-spinner"></div></div>';
        
        try {
            const response = await fetch(`${config.apiBase}/websupport/knowledge-articles?widget_id=${config.id}&search=${encodeURIComponent(query)}&limit=${config.help.articlesLimit}`);
            const result = await response.json();
            
            if (result.status === 'success') {
                renderHelpArticles(result.articles);
            }
        } catch (error) {
            console.error('Failed to search articles:', error);
            helpContent.innerHTML = '<div style="text-align: center; color: #ef4444; padding: 20px;">Failed to load articles</div>';
        }
    }

    function renderHelpCategories(categories) {
        const helpContent = document.getElementById('wsHelpContent');
        
        if (categories.length === 0) {
            helpContent.innerHTML = '<div style="text-align: center; color: #6b7280; padding: 20px;">No help articles available</div>';
            return;
        }
        
        const categoriesHTML = categories.map(category => `
            <div class="ws-category-item" data-category-slug="${category.slug}">
                <div class="ws-category-icon">
                    ${category.icon ? `<i class="${category.icon}" style="color: white; font-size: 16px;"></i>` : `
                        <svg width="16" height="16" fill="white" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                    `}
                </div>
                <div class="ws-category-name">${category.name}</div>
                <div style="font-size: 11px; color: #9ca3af; margin-top: 4px;">${category.published_articles_count || 0} articles</div>
            </div>
        `).join('');
        
        helpContent.innerHTML = `<div class="ws-category-list">${categoriesHTML}</div>`;
        
        // Add click listeners to category items using event delegation
        const categoryList = helpContent.querySelector('.ws-category-list');
        if (categoryList) {
            categoryList.addEventListener('click', (e) => {
                const categoryItem = e.target.closest('.ws-category-item');
                if (categoryItem) {
                    e.stopPropagation(); // Prevent event from bubbling to widget close handler
                    const slug = categoryItem.dataset.categorySlug;
                    if (slug) {
                        loadCategoryArticles(slug);
                    }
                }
            });
        }
    }

    function renderHelpArticles(articles, showBackButton = false) {
        const helpContent = document.getElementById('wsHelpContent');
        
        if (articles.length === 0) {
            helpContent.innerHTML = `
                ${showBackButton ? '<button class="ws-back-button" id="wsBackToCategories">← Back to Categories</button>' : ''}
                <div style="text-align: center; color: #6b7280; padding: 20px;">No articles found</div>
            `;
            if (showBackButton) {
                document.getElementById('wsBackToCategories').addEventListener('click', (e) => {
                    e.stopPropagation();
                    loadHelpContent();
                });
            }
            return;
        }
        
        const articlesHTML = articles.map(article => `
            <a href="${config.apiBase}/knowledge/${config.companyName.toLowerCase()}/article/${article.slug}" target="_blank" class="ws-article-item">
                <h4 class="ws-article-title">${article.title}</h4>
                ${article.excerpt ? `<p class="ws-article-excerpt">${article.excerpt}</p>` : ''}
                <div class="ws-article-meta">
                    <span>${article.category ? article.category.name : 'General'}</span>
                    <span>•</span>
                    <span>${article.read_time} min read</span>
                </div>
            </a>
        `).join('');
        
        helpContent.innerHTML = `
            ${showBackButton ? '<button class="ws-back-button" id="wsBackToCategories">← Back to Categories</button>' : ''}
            <div class="ws-article-list" style="display: flex; flex-direction: column; gap: 12px;">${articlesHTML}</div>
        `;
        
        // Add event listener for back button
        if (showBackButton) {
            const backButton = document.getElementById('wsBackToCategories');
            if (backButton) {
                backButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    loadHelpContent();
                });
            }
        }
    }

    async function loadCategoryArticles(categorySlug) {
        const helpContent = document.getElementById('wsHelpContent');
        helpContent.innerHTML = '<div class="ws-loading"><div class="ws-spinner"></div></div>';
        
        try {
            const response = await fetch(`${config.apiBase}/websupport/knowledge-articles?widget_id=${config.id}&category=${categorySlug}&limit=${config.help.articlesLimit}`);
            const result = await response.json();
            
            if (result.status === 'success') {
                renderHelpArticles(result.articles, true); // Show back button when viewing category articles
            }
        } catch (error) {
            console.error('Failed to load category articles:', error);
            helpContent.innerHTML = '<button class="ws-back-button" onclick="loadHelpContent()">← Back to Categories</button><div style="text-align: center; color: #ef4444; padding: 20px;">Failed to load articles</div>';
        }
    }

    // Expose functions globally for onclick handlers
    window.loadCategoryArticles = loadCategoryArticles;
    window.loadHelpContent = loadHelpContent;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWidget);
    } else {
        initWidget();
    }

})();
