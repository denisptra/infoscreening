<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-[#3A07BB] border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#2d0594] focus:bg-[#2d0594] active:bg-[#24047a] focus:outline-none focus:ring-2 focus:ring-[#3A07BB] focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
