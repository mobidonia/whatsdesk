@if (config('wpbox.one_signal_app_id') && config('wpbox.one_signal_app_id',"")!="")
    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script>
        window.OneSignalDeferred = window.OneSignalDeferred || [];
        OneSignalDeferred.push(function(OneSignal) {

             // Set the user ID
             setTimeout(() => {
                var userId = "{{ auth()->user()->id }}"; // Replace this with how you get the user ID in your project
                OneSignal.login(userId);
             }, 1000);
          

            OneSignal.init({
                allowLocalhostAsSecureOrigin: true,
                appId: "{{ config('wpbox.one_signal_app_id',"") }}",
            });

          
        });
    </script>
    
@endif