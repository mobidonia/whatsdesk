<!-- Global site tag (gtag.js) - Google Analytics -->
@if (config('settings.google_analytics'))
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo config('settings.google_analytics'); ?>"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '<?php echo config('settings.google_analytics'); ?>');
</script>
@endif