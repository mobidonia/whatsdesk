<script>
document.addEventListener('DOMContentLoaded', function(){
  console.log('WhatsApp Call script loaded');

  const logs = document.getElementById('waCallLogs');
  function logLine(obj, level='info'){
    const message = (typeof obj === 'string' ? obj : JSON.stringify(obj));
    console.log(`[WhatsApp Call] [${level}] ${message}`);
    
    if(!logs) return;
    const time = new Date().toISOString();
    const line = document.createElement('div');
    line.style.whiteSpace = 'pre-wrap';
    line.textContent = `[${time}] [${level}] ${message}`;
    logs.appendChild(line);
    logs.scrollTop = logs.scrollHeight;
  }

  // Initialize logs
  logLine('WhatsApp Call module initialized');

  const reqForm = document.getElementById('waRequestPermissionForm');
  console.log('Looking for form with ID: waRequestPermissionForm', reqForm);
  
  if(reqForm){
    logLine('Request permission form found');
    console.log("Request permission form found", reqForm);
    
    // Add click event to the button as well for debugging
    const submitBtn = reqForm.querySelector('button[type="submit"]');
    if(submitBtn){
      submitBtn.addEventListener('click', function(e){
        console.log('Submit button clicked');
        logLine('Submit button clicked');
      });
    }
    
    reqForm.addEventListener('submit', async function(e){
      console.log('Form submit event triggered');
      e.preventDefault();
      e.stopPropagation();
      
      try{
        logLine('Requesting call permission...');
        // For now, just log that permission was requested
        // TODO: Implement actual permission request endpoint
        logLine('Permission request feature not yet implemented');
        
        // Try different notification methods
        if(window.js && window.js.notify){ 
          window.js.notify('Permission request logged (feature pending)', 'info'); 
        } else if(window.toastr){
          window.toastr.info('Permission request logged (feature pending)');
        } else {
          alert('Permission request logged (feature pending)');
        }
      }catch(err){ 
        console.error('Error in form submission:', err);
        logLine(err?.message || err, 'error'); 
      }
      
      return false;
    });
  }else{
    logLine('Request permission form not found');
    console.log("Request permission form not found");
  }

  const startForm = document.getElementById('waStartCallForm');
  if(startForm){
    startForm.addEventListener('submit', async function(e){
      e.preventDefault();
      try{
        logLine('Starting business-initiated call...');
        const formData = new FormData(startForm);
        const res = await fetch(startForm.action, { method:'POST', body: formData, headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, credentials: 'same-origin' });
        const json = await res.json().catch(() => ({}));
        logLine({ status: res.status, body: json });
        if(json?.link){
          logLine('Call link: ' + json.link);
          if(window.open){ window.open(json.link, '_blank'); }
        }
        if(window.js && js.notify){ js.notify(res.ok ? 'Call initiated' : 'Call initiate failed', res.ok ? 'success' : 'danger'); }
      }catch(err){ logLine(err?.message || err, 'error'); }
    });
  }
});
</script>
