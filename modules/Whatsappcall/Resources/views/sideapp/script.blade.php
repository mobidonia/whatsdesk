<script>
    "use strict";

    //Add this function to the Vue instance of chatList
    window.addEventListener('load', function() {

       

      const pollUrl = '{{ route('whatsappcall.chat.active') }}';
      const preAcceptUrl = '{{ route('whatsappcall.uic.pre_accept') }}';
      const requestPermissionUrl = '{{ route('whatsappcall.bic.request') }}';
      const makeCallUrl = '{{ route('whatsappcall.bic.start') }}';
      const finalAcceptUrl = '{{ route('whatsappcall.uic.accept') }}';
      const terminateUrl = '{{ route('whatsappcall.uic.terminate') }}';
      const permissionStatusUrl = '{{ route('whatsappcall.bic.permission_status') }}';
      const ringToneUrl = '{{ config('app.whatsapp_ring_url', '/vendor/meta/pling.mp3') }}';
      let showing = false;
      let activeCall = null;
      let peerConnection = null;
      let localStream = null;
      let preAcceptStarted = false;
      let readyToAccept = false;
      let ringAudio = null;

      var activeChatID = null;
      var permissionStatus = null;
      var isLoading = false;

      function normalizeLineEndings(text){
        if(!text) return '';
        // Ensure CRLF per SDP expectations
        return text.replace(/\r?\n/g, '\r\n');
      }

      function sanitizeRemoteSdp(sdp){
        try{
          const original = normalizeLineEndings(sdp || '');
          const lines = original.split('\r\n');

          // Identify payload types to remove: telephone-event (DTMF) and CN comfort noise
          const removePayloads = new Set();
          for(const line of lines){
            const rtpmap = line.match(/^a=rtpmap:(\d+)\s+([^\/]+)\/(\d+)/i);
            if(rtpmap){
              const pt = rtpmap[1];
              const codec = (rtpmap[2] || '').toLowerCase();
              if(codec === 'telephone-event' || codec === 'cn'){
                removePayloads.add(pt);
                console.warn('[WebRTC] SDP mark payload for removal', { pt, codec, line });
              }
            }
          }

          // Filter lines
          const filtered = [];
          let audioMLineIndex = -1;
          for(let i=0;i<lines.length;i++){
            const line = lines[i];
            if(!line){ filtered.push(line); continue; }
            if(/^m=audio /i.test(line)){
              audioMLineIndex = filtered.length; // position in filtered array
              filtered.push(line); // temporary, will be rewritten later
              continue;
            }
            // Drop SSRC attribute lines
            if(/^a=ssrc:\d+\s+/i.test(line)) { console.warn('[WebRTC] SDP remove line', line); continue; }
            // Drop ptime/maxptime
            if(/^a=ptime:\d+/i.test(line)) { console.warn('[WebRTC] SDP remove line', line); continue; }
            if(/^a=maxptime:\d+/i.test(line)) { console.warn('[WebRTC] SDP remove line', line); continue; }
            // Drop rtpmap/fmtp/rtcp-fb lines that reference removed payloads
            if(/^a=(rtpmap|fmtp|rtcp-fb):(\d+)/i.test(line)){
              const m = line.match(/^a=(rtpmap|fmtp|rtcp-fb):(\d+)/i);
              if(m && removePayloads.has(m[2])){ console.warn('[WebRTC] SDP remove line', line); continue; }
            }
            filtered.push(line);
          }

          // Rewrite m=audio line to exclude removed payloads
          if(audioMLineIndex >= 0){
            const orig = filtered[audioMLineIndex];
            const parts = orig.split(' ');
            // m=audio <port> <proto> <pt> <pt> ...
            if(parts.length >= 4){
              const header = parts.slice(0,3); // m=audio, port, proto
              const pts = parts.slice(3).filter(pt => !removePayloads.has(pt));
              const rebuilt = [...header, ...pts].join(' ');
              filtered[audioMLineIndex] = rebuilt;
              console.warn('[WebRTC] SDP rewrite m=audio', { from: orig, to: rebuilt });
            }
          }

          // Join back
          let sanitized = filtered.join('\r\n');
          if(!sanitized.endsWith('\r\n')){ sanitized += '\r\n'; }
          console.warn('[WebRTC] SDP sanitized', { originalLen: (sdp||'').length, sanitizedLen: sanitized.length });
          return sanitized;
        }catch(e){ console.error('[WebRTC] SDP sanitize error', e); return sdp; }
      }

      // Toast UI container (top-right, stacked)
      function ensureToastContainer(){
        if(document.getElementById('waCallToastContainer')) return;
        const container = document.createElement('div');
        container.id = 'waCallToastContainer';
        container.style.position = 'fixed';
        container.style.top = '16px';
        container.style.right = '16px';
        container.style.zIndex = '20000';
        container.style.display = 'flex';
        container.style.flexDirection = 'column';
        container.style.gap = '10px';
        container.style.alignItems = 'flex-end';
        document.body.appendChild(container);
      }

      function startRinging(forceUserGesture){
        try{
          if(!ringAudio){
            ringAudio = new Audio(ringToneUrl);
            ringAudio.loop = true;
          }
          const playPromise = ringAudio.play();
          if(playPromise !== undefined){
            playPromise.then(() => {
              // hide any enable buttons if playing
              document.querySelectorAll('[id^="waEnableSound_"]').forEach(btn => btn.style.display = 'none');
            }).catch(() => {
              // show enable buttons to allow user gesture
              document.querySelectorAll('[id^="waEnableSound_"]').forEach(btn => btn.style.display = 'inline-block');
              if(forceUserGesture){ ringAudio.play().catch(()=>{}); }
            });
          }
        }catch(_){ }
      }

      function showCallToast(call){
        ensureToastContainer();
        const container = document.getElementById('waCallToastContainer');
        const cardId = `waCallToast_${call.id}`;
        if(document.getElementById(cardId)) return; // already shown
        const card = document.createElement('div');
        card.id = cardId;
        card.style.minWidth = '320px';
        card.style.maxWidth = '360px';
        card.style.background = '#fff';
        card.style.boxShadow = '0 8px 20px rgba(0,0,0,0.15)';
        card.style.borderRadius = '12px';
        card.style.overflow = 'hidden';
        card.style.fontFamily = 'inherit';
        card.innerHTML = `
          <div style="display:flex; align-items:center; padding:12px 14px; gap:10px;">
            <img id="waCallAvatar_${call.id}" src="" alt="" style="width:40px;height:40px;border-radius:50%;object-fit:cover;background:#f0f0f0;">
            <div style="flex:1;min-width:0;">
              <div id="waCallName_${call.id}" style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"></div>
              <div id="waCallNumber_${call.id}" style="color:#6b7280;font-size:12px"></div>
            </div>
            <span id="waCallBadge_${call.id}" style="background:#10b981;color:#fff;border-radius:999px;padding:2px 8px;font-size:12px;">${'{{ __('Ringing') }}'}</span>
          </div>
          <div id="waCallActions_${call.id}" style="display:flex;gap:8px;justify-content:flex-end;padding:10px 14px;border-top:1px solid #f3f4f6;">
            <button id="waCallDecline_${call.id}" class="btn btn-sm btn-outline-danger">${'{{ __('Decline') }}'}</button>
            <button id="waCallAccept_${call.id}" class="btn btn-sm btn-success">${'{{ __('Accept') }}'}</button>
            <button id="waEnableSound_${call.id}" class="btn btn-sm btn-outline-secondary" style="display:none;">${'{{ __('Enable sound') }}'}</button>
          </div>`;
        container.appendChild(card);

        // Populate
        const name = call.contact_name || '{{ __('Unknown') }}';
        const number = call.wa_user_id || '';
        const avatar = call.contact_avatar || '/img/placeholder-user.png';
        document.getElementById(`waCallName_${call.id}`).innerText = name;
        document.getElementById(`waCallNumber_${call.id}`).innerText = number;
        document.getElementById(`waCallAvatar_${call.id}`).src = avatar;

        // Bind
        document.getElementById(`waCallDecline_${call.id}`).addEventListener('click', () => {
          // just remove the card; do not terminate on decline from toast
          document.getElementById(cardId)?.remove();
          const cont = document.getElementById('waCallToastContainer');
          if(cont && cont.childElementCount === 0 && ringAudio){ try{ ringAudio.pause(); ringAudio = null; }catch(_){} }
        });
        document.getElementById(`waCallAccept_${call.id}`).addEventListener('click', async () => {
          try{
            // Set active call and kick off accept flow
            activeCall = call;
            if(!preAcceptStarted){
              preAcceptStarted = true;
              const answer = await setupPeerAndCreateAnswer(call.offer || {});
              const pre = await preAcceptWithSdp(answer.sdp);
              if(!(pre && pre.ok)){
                console.error('[UIC] Pre-accept failed (card)', pre);
                return;
              }
            }
            readyToAccept = true;
            // If already connected, finalize accept immediately
            if(peerConnection && peerConnection.connectionState === 'connected' && peerConnection.localDescription){
              await acceptWithSdp(peerConnection.localDescription.sdp);
            }
          }catch(e){ console.error('[UIC] Accept error (card)', e); }
        });
        const enableBtn = document.getElementById(`waEnableSound_${call.id}`);
        if(enableBtn){ enableBtn.addEventListener('click', () => { startRinging(true); }); }

        // Attempt to start ringing
        startRinging(false);
      }

      function switchToastToConnected(callId){
        const badge = document.getElementById(`waCallBadge_${callId}`);
        const actions = document.getElementById(`waCallActions_${callId}`);
        if(badge){ badge.innerText = '{{ __('Connected') }}'; badge.style.background = '#3b82f6'; }
        if(actions){
          actions.innerHTML = `<button id="waCallStop_${callId}" class="btn btn-sm btn-danger">{{ __('Stop') }}</button>`;
          if (ringAudio) { try { ringAudio.pause(); ringAudio = null; }catch(e){
            console.error('[UIC] Switch toast to connected error', e);
          } }
          document.getElementById(`waCallStop_${callId}`).addEventListener('click', async () => {
            try{
              if(activeCall?.id){
                const form = new FormData();
                form.append('call_id', activeCall.id);
                await fetch(terminateUrl, { method:'POST', body: form, headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}, credentials: 'same-origin' });
              }
            }catch(_){ }
            cleanupCall();
            document.getElementById(`waCallToast_${callId}`)?.remove();
          });
        }
      }

      // Close UI on ended broadcast
      window.wpCallEnded = function(call){
        try{
          console.info('[UIC] Call ended broadcast', call);
          const cardId = call?.id ? `waCallToast_${call.id}` : null;
          if(cardId){ document.getElementById(cardId)?.remove(); }
          if (ringAudio) { try { ringAudio.pause(); ringAudio = null; }catch(e){
            console.error('[UIC] Switch toast to connected error', e);
          } }
          cleanupCall();
        }catch(e){
          console.error('[UIC] Call ended broadcast error', e);
        }
      };

      // Close UI on claimed by another agent
      window.wpCallClaimed = function(call){
        try{
          console.info('[UIC] Call claimed by another agent', call);
          // If this call matches our active toast, remove it
          const cardId = call?.id ? `waCallToast_${call.id}` : null;
          if(cardId){ document.getElementById(cardId)?.remove(); }
            // keep cleanup minimal; we didn't start media if not accepted
            readyToAccept = false;
            activeCall = false;
            if (ringAudio) { try { ringAudio.pause(); ringAudio = null; }catch(e){
              console.error('[UIC] Call claimed by another agent error', e);
            } 
          }
        }catch(e){
          console.error('[UIC] Call claimed by another agent error', e);
        }
      };

      // Expose handler for UIC Pusher event
      window.wpIncomingCall = function(call){
        console.info('[UIC] Incoming call via Pusher', call);
        // Always show as individual card
        showCallToast(call);
      };

      async function pollIncoming(){
        try{
          console.debug('[UIC] Polling incoming calls...');
          const res = await fetch(pollUrl, {credentials: 'same-origin'});
          const data = await res.json();
          console.debug('[UIC] Poll result', { ok: data?.ok, count: Array.isArray(data?.calls) ? data.calls.length : 0 });
          if(data && data.ok && Array.isArray(data.calls) && data.calls.length > 0){
            const call = data.calls[0];
            console.info('[UIC] Active call detected', {
              id: call?.id,
              status: call?.status,
              wa_call_id: call?.wa_call_id,
              offerType: call?.offer?.type,
              offerSdpLen: (call?.offer?.sdp || '').length
            });
            if(!showing){
              activeCall = call;
              document.getElementById('waIncomingCallName').innerText = call.contact_name || '{{ __('Unknown') }}';
              document.getElementById('waIncomingCallNumber').innerText = call.wa_user_id || '';
              document.getElementById('waIncomingCallStatus').innerText = '{{ __('Ringing') }}';
              $('#waIncomingCall').modal('show');
              console.info('[UIC] Showing incoming call modal');
              showing = true;
              // Auto start pre-accept as recommended by Meta docs
              try{
                /*if(activeCall?.offer?.sdp){
                  document.getElementById('waIncomingCallStatus').innerText = '{{ __('Preparing') }}';
                  preAcceptStarted = true;
                  setupPeerAndCreateAnswer(activeCall.offer || {})
                    .then(async (answer) => {
                      document.getElementById('waIncomingCallStatus').innerText = '{{ __('Pre-accepting') }}';
                      const pre = await preAcceptWithSdp(answer.sdp);
                      if(pre && pre.ok){
                        console.info('[UIC] Pre-accept OK, awaiting connection...');
                        document.getElementById('waIncomingCallStatus').innerText = '{{ __('Connecting') }}';
                      }else{
                        console.error('[UIC] Pre-accept failed (auto)', pre);
                        document.getElementById('waIncomingCallStatus').innerText = '{{ __('Failed') }}';
                      }
                    })
                    .catch(err => { console.error('[UIC] Auto pre-accept error', err); document.getElementById('waIncomingCallStatus').innerText = '{{ __('Failed') }}'; });
                } else {
                  console.warn('[UIC] No SDP offer in activeCall.offer; cannot pre-accept');
                }*/
              }catch(err){ console.error('[UIC] Error starting auto pre-accept', err); }
            }
          }
        }catch(e){ /* ignore */ }
      }

      //setInterval(pollIncoming, 4000);

      function cleanupCall() {
        console.debug('[WebRTC] Cleaning up call');

        // Stop ringing
        if (ringAudio) {
            try { ringAudio.pause(); } catch(_) {}
            try { ringAudio.currentTime = 0; } catch(_) {}
            ringAudio = null;
        }

        // Stop local audio
        if (localStream) {
            localStream.getTracks().forEach(track => {
                track.stop();
            });
            localStream = null;
        }

        // Close peer connection
        if (peerConnection) {
            peerConnection.ontrack = null;
            peerConnection.onicecandidate = null;
            peerConnection.onconnectionstatechange = null;
            peerConnection.oniceconnectionstatechange = null;
            peerConnection.close();
            peerConnection = null;
        }

        // Reset call state variables
        readyToAccept = false;
        activeCall = false;

        // Remove any dynamic <audio> elements
        document.querySelectorAll('audio[autoplay]').forEach(audio => audio.remove());

        console.debug('[WebRTC] Cleanup complete');
    }



      async function setupPeerAndCreateAnswer(remoteOffer){
        console.info('[WebRTC] Setting up peer; remote offer', { type: remoteOffer?.type, sdpLen: (remoteOffer?.sdp || '').length });
        // Create peer
       // cleanupCall();

        peerConnection = new RTCPeerConnection({
          iceServers: [{ urls: 'stun:stun.l.google.com:19302' }]
        });
        peerConnection.onicecandidate = (event) => {
          // No external signaling toward WhatsApp; trickle ICE is handled via SDP where supported
          if(event.candidate){
            console.debug('[WebRTC] ICE candidate');
          } else {
            console.debug('[WebRTC] ICE gathering complete');
          }
        };
        peerConnection.oniceconnectionstatechange = () => {
          console.debug('[WebRTC] iceConnectionState', peerConnection.iceConnectionState);
          if (['failed', 'disconnected', 'closed'].includes(peerConnection.iceConnectionState)) {
              console.warn('[WebRTC] ICE state ended, cleaning up...');
              cleanupCall();
          }
      };

      peerConnection.onconnectionstatechange = () => {
          console.debug('[WebRTC] connectionState', peerConnection.connectionState);
          if (peerConnection.connectionState === 'connected') {
              if(activeCall?.id){ switchToastToConnected(activeCall.id); }
              if (readyToAccept && activeCall && peerConnection.localDescription) {
                  console.info('[UIC] Calling final accept...');
                  acceptWithSdp(peerConnection.localDescription.sdp).catch(() => {});
              }
          } else if (['failed', 'disconnected', 'closed'].includes(peerConnection.connectionState)) {
              console.warn('[WebRTC] Connection ended, cleaning up...');
              cleanupCall();
          }
      };
        peerConnection.ontrack = (event) => {
          console.info('[WebRTC] ontrack: incoming stream', { tracks: event.streams?.[0]?.getTracks()?.length || 0 });
          const audio = document.createElement('audio');
          audio.srcObject = event.streams[0];
          audio.autoplay = true;
          audio.playsInline = true;
          document.body.appendChild(audio);
        };

        const remoteSdp = sanitizeRemoteSdp(remoteOffer.sdp);
        try{
          await peerConnection.setRemoteDescription(new RTCSessionDescription({type: 'offer', sdp: remoteSdp}));
        }catch(e){
          console.warn('[WebRTC] setRemoteDescription failed, retrying with minimal sanitized SDP');
          // Fallback: keep only essential m=audio block with RTP map lines
          const blocks = remoteSdp.split(/\r\n(?=m=)/);
          let audioBlock = blocks.find(b => /^m=audio /m.test(b)) || '';
          // Also remove any a=rtpmap lines for telephone-event in audioBlock if present
          audioBlock = audioBlock
            .split('\r\n')
            .filter(l => !/^a=rtpmap:\d+\s+telephone-event\//i.test(l))
            .join('\r\n');
          const headerLines = remoteSdp.split('\r\n').filter(l => /^v=|^o=|^s=|^t=|^a=group:|^a=msid-semantic:/.test(l));
          const parts = [ ...headerLines, audioBlock ].filter(Boolean);
          let minimal = parts.join('\r\n');
          if(!minimal.endsWith('\r\n')){ minimal += '\r\n'; }
          await peerConnection.setRemoteDescription(new RTCSessionDescription({ type: 'offer', sdp: minimal }));
        }
        console.debug('[WebRTC] setRemoteDescription done');
        try{
          localStream = await navigator.mediaDevices.getUserMedia({ audio: { echoCancellation: true, noiseSuppression: true, channelCount: 1 } });
          console.debug('[WebRTC] getUserMedia success', { tracks: localStream.getTracks().length });
          localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));
        }catch(err){
          console.error('getUserMedia failed', err);
        }

        const answer = await peerConnection.createAnswer({ offerToReceiveAudio: true, voiceActivityDetection: true });
        console.debug('[WebRTC] createAnswer done', { sdpLen: (answer?.sdp || '').length });
        // Normalize line endings in local SDP too
        const normalizedLocal = new RTCSessionDescription({ type: 'answer', sdp: normalizeLineEndings(answer.sdp) });
        await peerConnection.setLocalDescription(normalizedLocal);
        console.debug('[WebRTC] setLocalDescription done');
        return answer;
      }

      async function preAcceptWithSdp(answerSdp){
        const form = new FormData();
        form.append('call_id', activeCall.id);
        form.append('sdp', answerSdp);
        console.log("Prepare for pre accept");
        console.log(preAcceptUrl+" <---  preAcceptUrl");
        console.log(form);
        const res = await fetch(preAcceptUrl, {method:'POST', body: form, headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}, credentials: 'same-origin'});
        const json = await res.json();
        console.info('[UIC] Pre-accept response', { status: res.status, json });
        return json;
      }

      async function acceptWithSdp(answerSdp){
        const form = new FormData();
        form.append('call_id', activeCall.id);
        form.append('sdp', answerSdp);
        const res = await fetch(finalAcceptUrl, {method:'POST', body: form, headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}, credentials: 'same-origin'});
        const json = await res.json();
        console.info('[UIC] Accept response', { status: res.status, json });
        return json;
      }

      document.getElementById('waAcceptBtn').addEventListener('click', async function(){
        if(!activeCall || !activeCall.id){ console.warn('[UIC] Accept clicked but no activeCall'); return; }
        try{
          console.info('[UIC] Accept clicked', { id: activeCall?.id, wa_call_id: activeCall?.wa_call_id, offerType: activeCall?.offer?.type, offerSdpLen: (activeCall?.offer?.sdp || '').length });
          // If pre-accept hasn't started for any reason, start it now
          if(!preAcceptStarted){
            document.getElementById('waIncomingCallStatus').innerText = '{{ __('Preparing') }}';
            preAcceptStarted = true;
            if (peerConnection || localStream) {
                cleanupCall();
            }
            const answer = await setupPeerAndCreateAnswer(activeCall.offer || {});
            document.getElementById('waIncomingCallStatus').innerText = '{{ __('Pre-accepting') }}';
            const pre = await preAcceptWithSdp(answer.sdp);
            if(!(pre && pre.ok)){
              console.error('[UIC] Pre-accept failed (on click)', pre);
              document.getElementById('waIncomingCallStatus').innerText = '{{ __('Failed') }}';
              return;
            }
          }else{
            console.info('[UIC] Pre-accept already started');
          }
          // Mark that agent approved to accept when connected
          readyToAccept = true;
          document.getElementById('waIncomingCallStatus').innerText = '{{ __('Connecting') }}';
          // If already connected by now, proceed to accept immediately
          if(peerConnection && peerConnection.connectionState === 'connected' && peerConnection.localDescription){
            console.info('[UIC] Already connected, sending final accept...');
            await acceptWithSdp(peerConnection.localDescription.sdp);
          }
        }catch(e){ console.error('[UIC] Accept flow error', e); document.getElementById('waIncomingCallStatus').innerText = '{{ __('Failed') }}'; }
      });

      document.getElementById('waDeclineBtn').addEventListener('click', function(){
        $('#waIncomingCall').modal('hide');
        showing = false;
        activeCall = null;
        try{ if(peerConnection){ peerConnection.close(); peerConnection = null; } if(localStream){ localStream.getTracks().forEach(t=>t.stop()); localStream=null; } console.info('[UIC] Call declined and cleaned up'); }catch(_){}
      });


      
      async function checkPermissionStatus(){
        if (!activeChatID) return;
        
        try {
          isLoading = true;
          chatList.updateProperty('isLoading', true);
          
          const url = new URL(permissionStatusUrl, window.location.origin);
          url.searchParams.append('contact_id', activeChatID);
          
          const res = await fetch(url, {
            method: 'GET',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            credentials: 'same-origin'
          });
          
          const json = await res.json();
          console.log("Permission status response:", json);
          
          if (json.ok && json.permission) {
            permissionStatus = json.permission;
            chatList.updateProperty('permissionStatus', permissionStatus);
            
            // Check if we can perform actions
            const actions = json.actions || [];
            const startCallAction = actions.find(a => a.action_name === 'start_call');
            const requestPermissionAction = actions.find(a => a.action_name === 'send_call_permission_request');
            
            // Show call button if we have temporary permission and can start calls
            chatList.updateProperty('showCallButton', permissionStatus.status === 'temporary' && 
                                     startCallAction && startCallAction.can_perform_action);
            
            // Show request button if we don't have permission and can request it
            chatList.updateProperty('showRequestButton', permissionStatus.status !== 'temporary' && 
                                       requestPermissionAction && requestPermissionAction.can_perform_action);
          } else {
            // No permission data - assume we can request permission
            permissionStatus = null;
            chatList.updateProperty('permissionStatus', null);
            chatList.updateProperty('showRequestButton', true);
            chatList.updateProperty('showCallButton', false);
          }
        } catch (error) {
          console.error("Error checking permission status:", error);
          // On error, show request permission button as fallback
          permissionStatus = null;
          chatList.updateProperty('permissionStatus', null);
          chatList.updateProperty('showRequestButton', true);
          chatList.updateProperty('showCallButton', false);
        } finally {
          isLoading = false;
          chatList.updateProperty('isLoading', false);
        }
      }

      async function requestPermission(){
        console.log("Requesting permission");
        isLoading = true;
        chatList.updateProperty('isLoading', true);
        
        try {
          const form = new FormData();
          form.append('contact_id', activeChatID);
          const res = await fetch(requestPermissionUrl, {method:'POST', body: form, headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}, credentials: 'same-origin'});
          const json = await res.json();
          
          if(window.js && js.notify){ 
            js.notify(res.ok ? 'Permission request sent' : 'Failed to send permission request', res.ok ? 'success' : 'danger'); 
          }
          
          // Refresh permission status after request
          setTimeout(() => {
            checkPermissionStatus();
          }, 5000);

          setTimeout(() => {
            checkPermissionStatus();
          }, 10000);
        } catch (error) {
          console.error("Error requesting permission:", error);
          if(window.js && js.notify){ js.notify('Failed to send permission request', 'danger'); }
        } finally {
          isLoading = false;
          chatList.updateProperty('isLoading', false);
        }
      }

      async function makeCall(){
        console.log("Making call");
        isLoading = true;
        chatList.updateProperty('isLoading', true);
        
        try {
          const form = new FormData();
          form.append('contact_id', activeChatID);
          var sdp = await createMicSDPOffer();
          form.append('sdp', sdp);
          const res = await fetch(makeCallUrl, {method:'POST', body: form, headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}, credentials: 'same-origin'});
        
          const json = await res.json();
          
          //Notify the user that the call has been initiated
          if(window.js && js.notify){ js.notify(res.ok ? 'Call initiated' : 'Call initiate failed', res.ok ? 'success' : 'danger'); }
        } catch (error) {
          console.error("Error making call:", error);
          if(window.js && js.notify){ js.notify('Call initiate failed', 'danger'); }
        } finally {
          isLoading = false;
          chatList.updateProperty('isLoading', false);
        }
      }

      /**
       * BIC -  GET MIC SDP OFFER
       * @return {string} SDP offer
       * */
      async function createMicSDPOffer() {
        // Create a PeerConnection
        const pc = new RTCPeerConnection();

        // Capture your microphone audio
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        const track = stream.getTracks()[0];
        pc.addTrack(track, stream);

        // Create an SDP offer
        const offer = await pc.createOffer();

        // Apply it as the local description
        await pc.setLocalDescription(offer);

        // Wait for ICE gathering to complete
        await new Promise(resolve => {
          if (pc.iceGatheringState === "complete") {
            resolve();
          } else {
            pc.onicegatheringstatechange = () => {
              if (pc.iceGatheringState === "complete") {
                resolve();
              }
            };
          }
        });

        // Final SDP offer with audio from your mic
        console.log("=== Your Microphone SDP Offer ===");
        console.log(pc.localDescription.sdp);
        return pc.localDescription.sdp;
      }

       // Add helper functions for permission status display
       function getPermissionStatusText() {
         const permission = chatList.getProperty('permissionStatus');
         if (!permission) return '';
         
         switch (permission.status) {
           case 'temporary':
             return 'Call permission granted';
           case 'permanent':
             return 'Permanent call permission';
           default:
             return 'No call permission';
         }
       }

       function getExpirationText() {
         const permission = chatList.getProperty('permissionStatus');
         if (!permission || !permission.expiration_time) return '';
         
         const expirationDate = new Date(permission.expiration_time * 1000);
         const now = new Date();
         const diffMs = expirationDate - now;
         
         if (diffMs <= 0) {
           return 'Permission expired';
         }
         
         const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
         const diffMins = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
         
         if (diffHours > 0) {
           return `Expires in ${diffHours}h ${diffMins}m`;
         } else {
           return `Expires in ${diffMins}m`;
         }
       }

       // Add methods to chatList
       chatList.requestPermission = requestPermission;
       chatList.makeCall = makeCall;
       chatList.getPermissionStatusText = getPermissionStatusText;
       chatList.getExpirationText = getExpirationText;
       
       // Initialize reactive data using addProperty
       chatList.addProperty('permissionStatus', null);
       chatList.addProperty('showRequestButton', false);
       chatList.addProperty('showCallButton', false);
       chatList.addProperty('isLoading', false);

      //Watch for changes in activeChat
      chatList.$watch('activeChat', function(newVal, oldVal) {
        if(newVal !== oldVal) {
          if(newVal.id){
              console.log("activeChat changed", newVal.phone);
              activeChatID = newVal.id;
              
              // Check permission status when chat changes
              checkPermissionStatus();
          } else {
            // Reset state when no active chat
            activeChatID = null;
            permissionStatus = null;
            chatList.updateProperty('permissionStatus', null);
            chatList.updateProperty('showRequestButton', false);
            chatList.updateProperty('showCallButton', false);
          }
        }
      });




      $('#waIncomingCall').on('hidden.bs.modal', function(){ showing = false; activeCall = null; document.getElementById('waCallLinkWrap').style.display='none';});
    });

  </script>