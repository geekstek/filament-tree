<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div
        x-data="{
            defaultExpanded: {{ $getDefaultExpanded() ? 'true' : 'false' }},
            toggleExpandAll(expand) {
                this.$dispatch('tree-entry-expand-event', { expand: expand });
            }
        }"
        class="fi-in-tree-entry border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden bg-gray-50 dark:bg-white/5"
    >
        {{-- 工具栏 --}}
        <div class="bg-gray-100 dark:bg-white/5 px-4 py-2 border-b border-gray-200 dark:border-white/10 flex gap-3 text-xs">
            <button
                type="button"
                @click="toggleExpandAll(true)"
                class="text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 font-medium transition-colors"
            >
                [+] {{ __('Expand All') }}
            </button>
            <span class="text-gray-300 dark:text-gray-600">|</span>
            <button
                type="button"
                @click="toggleExpandAll(false)"
                class="text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 font-medium transition-colors"
            >
                [-] {{ __('Collapse All') }}
            </button>
        </div>

        <div class="p-4 max-h-[400px] overflow-y-auto">
            @php
                $selectedIds = $getState() ?? [];
                $options = $getTreeOptions();
            @endphp

            @if(empty($options))
                <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                    {{ __('No data available') }}
                </p>
            @else
                <ul class="space-y-1">
                    @foreach($options as $node)
                        @include('geekstek-filament-tree::infolists.tree-entry-item', [
                            'node' => $node,
                            'selectedIds' => $selectedIds,
                            'defaultExpanded' => $getDefaultExpanded(),
                        ])
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-dynamic-component>

