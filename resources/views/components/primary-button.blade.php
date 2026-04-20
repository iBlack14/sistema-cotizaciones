<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#5F1BF2] via-[#8704BF] to-[#F2059F] border-0 rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-md shadow-[#8704BF]/30 hover:shadow-lg hover:shadow-[#8704BF]/40 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#BF1F6A] focus:ring-offset-white/10 transition ease-in-out duration-200']) }}>
    {{ $slot }}
</button>
