<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-maroon-700 border border-transparent rounded font-bold text-xs text-white uppercase tracking-wider hover:bg-maroon-800 focus:bg-maroon-800 active:bg-maroon-900 focus:outline-none focus:ring-2 focus:ring-maroon-700 focus:ring-offset-2 transition ease-in-out duration-150 shadow-xs']) }}>
    {{ $slot }}
</button>
