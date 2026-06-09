@props([
    'headers' => [],
    'searchable' => false,
    'searchPlaceholder' => 'Search...',
])

<div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-2xl rounded-[2rem] border border-white/60 dark:border-zinc-800/50 shadow-xl shadow-zinc-200/50 overflow-hidden transition-all duration-300" x-data="{ searchQuery: '' }">
    @if($searchable)
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-400">
                    <span class="material-symbols-rounded text-xl">search</span>
                </div>
                <input type="text" x-model="searchQuery" class="bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 p-2.5 transition-colors font-outfit" placeholder="{{ $searchPlaceholder }}">
            </div>
        </div>
    @endif

    <div class="overflow-x-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
        <table class="w-full text-sm text-left text-zinc-600 dark:text-zinc-400 font-outfit">
            <thead class="text-xs text-zinc-500 dark:text-zinc-400 uppercase bg-transparent border-b border-zinc-200/50 dark:border-zinc-700/50 font-cabinet">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col" class="px-6 py-4 font-semibold tracking-wider">
                            @if(is_array($header))
                                {{ $header['label'] ?? '' }}
                            @else
                                {{ $header }}
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if(isset($empty))
        <div class="p-8 text-center" x-show="!$el.previousElementSibling.querySelector('tbody tr')">
            {{ $empty }}
        </div>
    @endif

    @if(isset($pagination))
        <div class="p-4 border-t border-zinc-200/50 dark:border-zinc-800/50">
            {{ $pagination }}
        </div>
    @endif
</div>
