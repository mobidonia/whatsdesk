@extends('layouts.app', ['title' => __('Share')])
@section('admin_title')
    {{__('Share')}}
@endsection
@section('title')
<title>{{$name}}</title>
@endsection

@section('head')
<!-- Try multiple QR libraries for better compatibility -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.js"></script>
<script>
// Backup QR generation using QRious library if main one fails
if (typeof QRCode === 'undefined') {
    console.log('Loading backup QR library...');
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js';
    script.onload = function() {
        console.log('Backup QR library loaded');
        if (typeof generateQRCode === 'function') {
            generateQRCode();
        }
    };
    document.head.appendChild(script);
}
</script>
<style>
  
    
    .share-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        border: none;
        overflow: hidden;
    }
    
    .share-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }
    
    .share-body {
        padding: 3rem 2rem;
    }
    
    .url-container {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .url-container:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.1);
    }
    
    .url-input {
        border: none;
        background: transparent;
        font-size: 1.1rem;
        color: #495057;
        outline: none;
        width: 100%;
        padding: 0.5rem 0;
    }
    
    .copy-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        color: white;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }
    
    .copy-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .copy-btn.copied {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
    }
    
    .qr-container {
        text-align: center;
        margin: 2rem 0;
        padding: 2rem;
        background: #f8f9fa;
        border-radius: 15px;
        border: 2px dashed #dee2e6;
    }
    
    .social-share {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }
    
    .social-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white;
        gap: 0.5rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .social-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        color: white;
        text-decoration: none;
    }
    
    .social-btn.facebook { background: #1877f2; }
    .social-btn.twitter { background: #1da1f2; }
    .social-btn.linkedin { background: #0077b5; }
    .social-btn.whatsapp { background: #25d366; }
    .social-btn.telegram { background: #0088cc; }
    .social-btn.email { background: #ea4335; }
    
    .feature-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .feature-item {
        text-align: center;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 15px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .feature-item:hover {
        border-color: #667eea;
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.1);
    }
    
    .feature-icon {
        font-size: 3rem;
        color: #667eea;
        margin-bottom: 1rem;
    }
    
    .stats-container {
        display: flex;
        justify-content: space-around;
        margin: 2rem 0;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .stat-item {
        text-align: center;
        padding: 1rem;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #667eea;
    }
    
    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    @media (max-width: 768px) {
        .share-body {
            padding: 2rem 1rem;
        }
        
        .social-share {
            gap: 0.5rem;
        }
        
        .social-btn {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
    }
</style>
@endsection

@section('content')
<div class="share-container pt-7">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12">
                <div class="card share-card">
                    <div class="share-header">
                        <h1 class="mb-3 text-white">
                            <i class="fas fa-share-alt mr-3"></i>
                            {{ __('Share Your profile') }}
                        </h1>
                        <p class="mb-0 opacity-90">{{ __('Share your profile with your audience.') }}</p>
                    </div>
                    
                    <div class="share-body">
                        <!-- URL Section -->
                        <div class="url-container">
                            <div class="d-flex align-items-center">
                                <input type="text" id="companyUrl" class="url-input" value="{{ $url }}" readonly>
                                <button type="button" id="copyBtn" class="copy-btn ml-3" onclick="copyToClipboard()">
                                    <i id="copyIcon" class="fas fa-copy"></i>
                                    <span id="copyText">{{ __('Copy Link') }}</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- QR Code Section -->
                        <div class="qr-container">
                            <h4 class="mb-3">
                                <i class="fas fa-qrcode mr-2"></i>
                                {{ __('QR Code') }}
                            </h4>
                            <div id="qrcode" class="mb-3"></div>
                            <p class="text-muted mb-0">{{ __('Scan this QR code to quickly access your company profile') }}</p>
                            <button type="button" class="btn btn-outline-primary mt-3" onclick="downloadQR()">
                                <i class="fas fa-download mr-2"></i>
                                {{ __('Download QR Code') }}
                            </button>
                        </div>
                        
                        <!-- Social Share Buttons -->
                        <div class="text-center">
                            <h4 class="mb-4">
                                <i class="fas fa-share mr-2"></i>
                                {{ __('Share on Social Media') }}
                            </h4>
                            <div class="social-share">
                                <a href="#" onclick="shareOnFacebook()" class="social-btn facebook">
                                    <i class="fab fa-facebook-f"></i>
                                    Facebook
                                </a>
                                <a href="#" onclick="shareOnTwitter()" class="social-btn twitter">
                                    <i class="fab fa-twitter"></i>
                                    Twitter
                                </a>
                                <a href="#" onclick="shareOnLinkedIn()" class="social-btn linkedin">
                                    <i class="fab fa-linkedin-in"></i>
                                    LinkedIn
                                </a>
                                <a href="#" onclick="shareOnWhatsApp()" class="social-btn whatsapp">
                                    <i class="fab fa-whatsapp"></i>
                                    WhatsApp
                                </a>
                                <a href="#" onclick="shareOnTelegram()" class="social-btn telegram">
                                    <i class="fab fa-telegram-plane"></i>
                                    Telegram
                                </a>
                                <a href="#" onclick="shareViaEmail()" class="social-btn email">
                                    <i class="fas fa-envelope"></i>
                                    Email
                                </a>
                            </div>
                        </div>
                        
                       
                        
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Global variables
    const companyUrl = "{{ $url }}";
    console.log(companyUrl);
    const companyName = "{{ $name }}";
    let qrCodeCanvas = null;
    
    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        generateQRCode();
        loadStats();
        
        // Check if Web Share API is supported
        if (navigator.share) {
            addNativeShareButton();
        }
    });
    
    // Generate QR Code
    function generateQRCode() {
        const qrContainer = document.getElementById('qrcode');
        if (!qrContainer) {
            console.error('QR container not found');
            return;
        }
        
        // Show loading state
        qrContainer.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 text-muted">{{ __("Generating QR Code...") }}</p></div>';
        
        // Debug logging
        console.log('QRCode library available:', typeof QRCode);
        console.log('Company URL:', companyUrl);
        
        // Check if QRCode library is loaded
        if (typeof QRCode === 'undefined') {
            console.error('QRCode library not loaded');
            qrContainer.innerHTML = '<p class="text-danger">{{ __("QR Code library not loaded. Please refresh the page.") }}</p>';
            return;
        }
        
        // Check if URL is valid
        if (!companyUrl || companyUrl.trim() === '') {
            console.error('Company URL is empty');
            qrContainer.innerHTML = '<p class="text-danger">{{ __("Invalid URL for QR Code generation") }}</p>';
            return;
        }
        
        try {
            // Method 1: Try toCanvas with canvas element
            if (typeof QRCode.toCanvas === 'function') {
                console.log('Using QRCode.toCanvas method');
                
                // Create canvas element first
                const canvas = document.createElement('canvas');
                
                QRCode.toCanvas(canvas, companyUrl, {
                    width: 200,
                    height: 200,
                    color: {
                        dark: '#000000',
                        light: '#FFFFFF'
                    },
                    margin: 2,
                    errorCorrectionLevel: 'M'
                }, function (error) {
                    if (error) {
                        console.error('QR Code generation failed:', error);
                        qrContainer.innerHTML = '<p class="text-danger"><i class="fas fa-exclamation-triangle mr-2"></i>{{ __("QR Code generation failed") }}: ' + error.message + '</p>';
                    } else {
                        qrContainer.innerHTML = ''; // Clear loading state
                        qrCodeCanvas = canvas;
                        canvas.style.border = '2px solid #dee2e6';
                        canvas.style.borderRadius = '10px';
                        canvas.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
                        qrContainer.appendChild(canvas);
                        
                        console.log('QR Code generated successfully with toCanvas');
                    }
                });
            } 
            // Method 2: Try creating canvas manually
            else if (typeof QRCode.create === 'function') {
                console.log('Using QRCode.create method');
                QRCode.create(companyUrl, {
                    width: 200,
                    margin: 2,
                    color: {
                        dark: '#000000',
                        light: '#FFFFFF'
                    },
                    errorCorrectionLevel: 'M'
                }, function (error, qr) {
                    if (error) {
                        console.error('QR Code generation failed:', error);
                        qrContainer.innerHTML = '<p class="text-danger"><i class="fas fa-exclamation-triangle mr-2"></i>{{ __("QR Code generation failed") }}: ' + error.message + '</p>';
                    } else {
                        // Create canvas manually
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        canvas.width = 200;
                        canvas.height = 200;
                        
                        // Draw QR code (this is a simplified version)
                        const modules = qr.modules;
                        const size = modules.size;
                        const scale = 200 / size;
                        
                        ctx.fillStyle = '#FFFFFF';
                        ctx.fillRect(0, 0, 200, 200);
                        ctx.fillStyle = '#000000';
                        
                        for (let i = 0; i < size; i++) {
                            for (let j = 0; j < size; j++) {
                                if (modules.get(i, j)) {
                                    ctx.fillRect(j * scale, i * scale, scale, scale);
                                }
                            }
                        }
                        
                        qrContainer.innerHTML = '';
                        qrCodeCanvas = canvas;
                        canvas.style.border = '2px solid #dee2e6';
                        canvas.style.borderRadius = '10px';
                        canvas.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
                        qrContainer.appendChild(canvas);
                        
                        console.log('QR Code generated successfully with create method');
                    }
                });
            }
            // Method 3: Try simple QRCode.toDataURL approach
            else if (typeof QRCode.toDataURL === 'function') {
                console.log('Using QRCode.toDataURL method');
                QRCode.toDataURL(companyUrl, {
                    width: 200,
                    margin: 2,
                    color: {
                        dark: '#000000',
                        light: '#FFFFFF'
                    },
                    errorCorrectionLevel: 'M'
                }, function (error, url) {
                    if (error) {
                        console.error('QR Code generation failed:', error);
                        qrContainer.innerHTML = '<p class="text-danger"><i class="fas fa-exclamation-triangle mr-2"></i>{{ __("QR Code generation failed") }}: ' + error.message + '</p>';
                    } else {
                        qrContainer.innerHTML = '';
                        const img = document.createElement('img');
                        img.src = url;
                        img.style.border = '2px solid #dee2e6';
                        img.style.borderRadius = '10px';
                        img.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
                        img.style.width = '200px';
                        img.style.height = '200px';
                        qrContainer.appendChild(img);
                        
                        // Create canvas from image for download functionality
                        const canvas = document.createElement('canvas');
                        canvas.width = 200;
                        canvas.height = 200;
                        const ctx = canvas.getContext('2d');
                        img.onload = function() {
                            ctx.drawImage(img, 0, 0);
                            qrCodeCanvas = canvas;
                        };
                        
                        console.log('QR Code generated successfully with toDataURL');
                    }
                });
            }
            // Method 4: Try QRious library if available
            else if (typeof QRious !== 'undefined') {
                console.log('Using QRious library as fallback');
                const canvas = document.createElement('canvas');
                const qr = new QRious({
                    element: canvas,
                    value: companyUrl,
                    size: 200,
                    level: 'M',
                    background: '#FFFFFF',
                    foreground: '#000000'
                });
                
                qrContainer.innerHTML = '';
                qrCodeCanvas = canvas;
                canvas.style.border = '2px solid #dee2e6';
                canvas.style.borderRadius = '10px';
                canvas.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
                qrContainer.appendChild(canvas);
                
                console.log('QR Code generated successfully with QRious');
            }
            // Method 5: Last resort fallback
            else {
                console.log('No compatible QR library found');
                qrContainer.innerHTML = '<div class="text-center"><p class="text-warning"><i class="fas fa-exclamation-triangle mr-2"></i>{{ __("QR Code library not compatible") }}</p><p class="small text-muted">{{ __("Please try refreshing the page or contact support") }}</p></div>';
            }
            
        } catch (error) {
            console.error('QR Code generation error:', error);
            qrContainer.innerHTML = '<p class="text-danger"><i class="fas fa-exclamation-triangle mr-2"></i>{{ __("QR Code generation failed") }}: ' + error.message + '</p>';
        }
    }
    
    // Copy to clipboard with modern API
    async function copyToClipboard() {
        const copyBtn = document.getElementById('copyBtn');
        const copyIcon = document.getElementById('copyIcon');
        const copyText = document.getElementById('copyText');
        
        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(companyUrl);
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = companyUrl;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
            }
            
            // Visual feedback
            copyBtn.classList.add('copied');
            copyIcon.className = 'fas fa-check mr-2';
            copyText.textContent = '{{ __("Copied!") }}';
            
            // Track share
            trackShare('copy');
            
            // Reset after 3 seconds
            setTimeout(() => {
                copyBtn.classList.remove('copied');
                copyIcon.className = 'fas fa-copy mr-2';
                copyText.textContent = '{{ __("Copy Link") }}';
            }, 3000);
            
        } catch (error) {
            console.error('Copy failed:', error);
            //alert('{{ __("Failed to copy link. Please copy manually.") }}');
        }
    }
    
    // Download QR Code
    function downloadQR() {
        if (!qrCodeCanvas) {
            alert('{{ __("QR Code is not ready yet. Please wait for it to generate.") }}');
            return;
        }
        
        try {
            const link = document.createElement('a');
            const fileName = companyName ? 
                `${companyName.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_qr_code.png` : 
                'company_qr_code.png';
            
            link.download = fileName;
            link.href = qrCodeCanvas.toDataURL('image/png');
            
            // Trigger download
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            trackShare('qr_download');
            
            // Show success feedback
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check mr-2"></i>{{ __("Downloaded!") }}';
            btn.style.background = '#10b981';
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = '';
            }, 2000);
            
        } catch (error) {
            console.error('Download failed:', error);
            //alert('{{ __("Failed to download QR Code. Please try again.") }}');
        }
    }
    
    // Social sharing functions
    function shareOnFacebook() {
        const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(companyUrl)}`;
        openShareWindow(url);
        trackShare('facebook');
    }
    
    function shareOnTwitter() {
        const text = `{{ __('Check out this company profile:') }} ${companyName}`;
        const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(companyUrl)}`;
        openShareWindow(url);
        trackShare('twitter');
    }
    
    function shareOnLinkedIn() {
        const url = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(companyUrl)}`;
        openShareWindow(url);
        trackShare('linkedin');
    }
    
    function shareOnWhatsApp() {
        const text = `{{ __('Check out this company profile:') }} ${companyName} - ${companyUrl}`;
        const url = `https://wa.me/?text=${encodeURIComponent(text)}`;
        window.open(url, '_blank');
        trackShare('whatsapp');
    }
    
    function shareOnTelegram() {
        const text = `{{ __('Check out this company profile:') }} ${companyName}`;
        const url = `https://t.me/share/url?url=${encodeURIComponent(companyUrl)}&text=${encodeURIComponent(text)}`;
        window.open(url, '_blank');
        trackShare('telegram');
    }
    
    function shareViaEmail() {
        const subject = `{{ __('Company Profile:') }} ${companyName}`;
        const body = `{{ __('I wanted to share this company profile with you:') }}\n\n${companyName}\n${companyUrl}`;
        const url = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        window.location.href = url;
        trackShare('email');
    }
    
    // Add native share button if supported
    function addNativeShareButton() {
        const socialShare = document.querySelector('.social-share');
        const nativeShareBtn = document.createElement('button');
        nativeShareBtn.className = 'social-btn btn btn-primary';
        nativeShareBtn.style.background = '#6c63ff';
        nativeShareBtn.innerHTML = '<i class="fas fa-share mr-2"></i>{{ __("Share") }}';
        nativeShareBtn.onclick = shareNative;
        socialShare.insertBefore(nativeShareBtn, socialShare.firstChild);
    }
    
    // Native share API
    async function shareNative() {
        if (navigator.share) {
            try {
                await navigator.share({
                    title: companyName,
                    text: `{{ __('Check out this company profile:') }} ${companyName}`,
                    url: companyUrl
                });
                trackShare('native');
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error('Share failed:', error);
                }
            }
        }
    }
    
    // Helper function to open share windows
    function openShareWindow(url) {
        window.open(url, 'share', 'width=600,height=400,scrollbars=yes,resizable=yes');
    }
    
    // Track share events (you can integrate with your analytics)
    function trackShare(platform) {
        // Update local count
        const shareCountEl = document.getElementById('shareCount');
        let currentCount = parseInt(shareCountEl.textContent);
        shareCountEl.textContent = currentCount + 1;
        
        // You can add analytics tracking here
        console.log(`Shared via ${platform}`);
        
        // Optional: Send to server for tracking
        // fetch('/api/track-share', {
        //     method: 'POST',
        //     headers: {'Content-Type': 'application/json'},
        //     body: JSON.stringify({platform, url: companyUrl})
        // });
    }
    
    // Load stats (placeholder function)
    function loadStats() {
        // You can implement real stats loading here
        document.getElementById('viewCount').textContent = Math.floor(Math.random() * 1000);
        document.getElementById('shareCount').textContent = Math.floor(Math.random() * 50);
    }
    
    // Add some interactive animations
    document.querySelectorAll('.feature-item, .social-btn').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
</script>
@endsection
