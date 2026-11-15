@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-slate-300 focus:border-[#3A07BB] focus:ring-[#3A07BB] rounded-lg shadow-sm']) }}>
