@props([
    'node',
    'selectedIds' => [],
    'defaultExpanded' => true,
])

@php
    $isSelected = in_array($node['id'], $selectedIds);
    $hasChildren = !empty($node['children']);
@endphp

<li
    x-data="{ open: {{ $defaultExpanded ? 'true' : 'false' }} }"
    x-on:tree-entry-expand-event.window="open = $event.detail.expand"
    class="relative"
>
    <div
        class="flex items-center gap-1.5 py-1.5 px-2 rounded-md transition duration-150"
        :class="{
            'bg-primary-50 dark:bg-primary-500/10': {{ $isSelected ? 'true' : 'false' }}
        }"
    >
        {{-- 展开/收起按钮 --}}
        <div class="w-5 flex justify-center shrink-0">
            @if($hasChildren)
                <button
                    type="button"
                    @click="open = !open"
                    class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors p-0.5"
                >
                    <svg
                        x-show="!open"
                        class="w-3 h-3"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                    </svg>
                    <svg
                        x-show="open"
                        class="w-3 h-3"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
            @endif
        </div>

        {{-- 选中状态指示器 --}}
        <div class="flex items-center gap-2 flex-1">
            @if($isSelected)
                <svg
                    class="w-4 h-4 text-primary-600 dark:text-primary-400 shrink-0"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
            @else
                <svg
                    class="w-4 h-4 text-gray-300 dark:text-gray-600 shrink-0"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                >
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd" />
                </svg>
            @endif

            <span
                class="text-sm {{ $isSelected ? 'text-gray-900 dark:text-gray-100 font-medium' : 'text-gray-600 dark:text-gray-400' }}"
            >
                {{ $node['label'] }}
            </span>
        </div>
    </div>

    @if($hasChildren)
        <ul
            x-show="open"
            x-collapse
            class="pl-6 ml-2.5 border-l border-dashed border-gray-200 dark:border-white/10 space-y-0.5"
        >
            @foreach($node['children'] as $child)
                @include('geekstek-filament-tree::infolists.tree-entry-item', [
                    'node' => $child,
                    'selectedIds' => $selectedIds,
                    'defaultExpanded' => $defaultExpanded,
                ])
            @endforeach
        </ul>
    @endif
</li>

