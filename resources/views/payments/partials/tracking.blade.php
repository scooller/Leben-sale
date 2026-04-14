@php
    $resolvedTagManagerId = trim((string) \App\Models\SiteSetting::get('tag_manager_id', ''));
    $resolvedPaymentId = $payment ?? null;
@endphp

@if($resolvedTagManagerId !== '')
    <script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            'gtm.start': Date.now(),
            event: 'gtm.js',
        });
    </script>
    <script async src="https://www.googletagmanager.com/gtm.js?id={{ urlencode($resolvedTagManagerId) }}"></script>
@endif

<script>
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
        event: @json($eventName),
        payment_id: @json($resolvedPaymentId),
        payment_status: @json($paymentStatus),
        page_type: 'payment_return',
        page_url: window.location.href,
    });
</script>
