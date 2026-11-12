<script>
    window.location.href = "{{ url('/admin') }}";
</script>
<noscript>
    <meta http-equiv="refresh" content="0;url={{ url('/admin') }}">
</noscript>
<div style="font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; background: #f6f6f9; color: #32324d;">
    <div style="text-align: center;">
        <div style="width: 40px; height: 40px; border: 3px solid #eaeaef; border-top-color: #4945ff; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 16px;"></div>
        <h2 style="font-size: 16px; font-weight: 600; margin-bottom: 6px;">Membuka Strapi CMS...</h2>
        <p style="font-size: 12px; color: #8e8ea9;">Mengalihkan ke Admin Content Manager Infoscreening</p>
        <p style="margin-top: 14px;"><a href="{{ url('/admin') }}" style="color: #4945ff; font-size: 12px; text-decoration: underline;">Klik di sini jika tidak otomatis dialihkan</a></p>
    </div>
</div>
<style>
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
