<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Form Submission</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #374151;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            padding: 32px 24px;
            text-align: center;
        }
        .header h1 {
            color: white;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .header p {
            color: rgba(255, 255, 255, 0.9);
            margin: 8px 0 0 0;
            font-size: 16px;
        }
        .content {
            padding: 32px 24px;
        }
        .field {
            margin-bottom: 24px;
        }
        .field-label {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .field-value {
            background: #f8fafc;
            padding: 16px;
            border-radius: 8px;
            border-left: 4px solid #4f46e5;
            font-size: 16px;
            line-height: 1.5;
        }
        .message-field {
            white-space: pre-wrap;
        }
        .footer {
            background: #f8fafc;
            padding: 24px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }
        .timestamp {
            color: #9ca3af;
            font-size: 12px;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Contact Form Submission</h1>
            <p>{{ $companyName }}</p>
        </div>
        
        <div class="content">
            <div class="field">
                <span class="field-label">From</span>
                <div class="field-value">
                    <strong>{{ $customerName }}</strong><br>
                    <a href="mailto:{{ $customerEmail }}" style="color: #4f46e5; text-decoration: none;">{{ $customerEmail }}</a>
                </div>
            </div>
            
            <div class="field">
                <span class="field-label">Message</span>
                <div class="field-value message-field">{{ $customerMessage }}</div>
            </div>
        </div>
        
        <div class="footer">
            <p>This message was sent via your Web Support Widget</p>
            <div class="timestamp">{{ now()->format('F j, Y \a\t g:i A') }}</div>
        </div>
    </div>
</body>
</html>
