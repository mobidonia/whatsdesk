<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Support Widget Demo - {{ $widget['company_name'] }}</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 40px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        
        .demo-container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }
        
        .demo-header {
            margin-bottom: 60px;
        }
        
        .demo-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .demo-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 32px;
        }
        
        .demo-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 60px;
        }
        
        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 32px 24px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .feature-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        
        .feature-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .feature-description {
            opacity: 0.8;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        
        .demo-instructions {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 32px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 40px;
        }
        
        .demo-instructions h3 {
            margin-bottom: 16px;
            font-size: 1.5rem;
        }
        
        .demo-instructions p {
            opacity: 0.9;
            margin-bottom: 16px;
        }
        
        .widget-preview-note {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 16px;
            font-size: 0.875rem;
            opacity: 0.8;
        }
        
        @media (max-width: 768px) {
            .demo-title {
                font-size: 2rem;
            }
            
            .demo-features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="demo-container">
        <div class="demo-header">
            <h1 class="demo-title">Web Support Widget</h1>
            <p class="demo-subtitle">Modern multi-tab support widget for {{ $widget['company_name'] }}</p>
        </div>
        
        <div class="demo-features">
            @if($widget['chat_enabled'])
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="24" height="24" fill="white" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.479 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z"/>
                    </svg>
                </div>
                <h3 class="feature-title">WhatsApp Chat</h3>
                <p class="feature-description">Direct connection to your WhatsApp Business account for instant messaging.</p>
            </div>
            @endif
            
            @if($widget['email_enabled'])
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="24" height="24" fill="white" viewBox="0 0 24 24">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                </div>
                <h3 class="feature-title">Email Contact</h3>
                <p class="feature-description">Professional contact form that sends emails directly to your inbox.</p>
            </div>
            @endif
            
            @if($widget['help_enabled'])
            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="24" height="24" fill="white" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
                    </svg>
                </div>
                <h3 class="feature-title">Self-Service Help</h3>
                <p class="feature-description">Integrated knowledge base with searchable articles and categories.</p>
            </div>
            @endif
        </div>
        
        <div class="demo-instructions">
            <h3>🚀 Try the Widget!</h3>
            <p>The support widget is now active on this page. Look for the floating button in the bottom-{{ $widget['position'] === 'bottom-left' ? 'left' : 'right' }} corner.</p>
            <div class="widget-preview-note">
                <strong>Widget Features:</strong><br>
                • Modern, responsive design<br>
                • Three integrated support channels<br>
                • Knowledge base integration<br>
                • Mobile-friendly interface<br>
                • Customizable colors and branding
            </div>
        </div>
        
        <div style="margin-top: 40px;">
            <a href="{{ route('websupportwidget.edit') }}" style="display: inline-block; background: rgba(255,255,255,0.2); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);">
                ← Back to Configuration
            </a>
        </div>
    </div>

    <!-- Load the widget -->
    <script src="{{ config('app.url') }}/websupport/widget?id={{ $widget['id'] }}"></script>
</body>
</html>
