@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-flex; align-items: center; gap: 10px; text-decoration: none;">
<img src="{{ asset('apple-touch-icon.png') }}" alt="Logo {{ config('app.name', 'Infoscreening') }}" style="width: 36px; height: 36px; border-radius: 8px; vertical-align: middle;">
<span style="font-size: 20px; font-weight: 700; color: #3A07BB; letter-spacing: -0.02em; vertical-align: middle;">{{ config('app.name', 'Infoscreening') }}</span>
</a>
</td>
</tr>
