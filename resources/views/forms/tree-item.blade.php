@props([
    'node',
    'defaultExpanded' => true,
])

<li
    x-data="{ open: {{ $defaultExpanded ? 'true' : 'false' }} }"
    x-on:tree-expand-event.window="open = $event.detail.expand"
    class="relative"
>
    <div
        class="flex items-center gap-1.5 py-1.5 px-2 rounded-md transition duration-150 hover:bg-gray-50 dark:hover:bg-white/5"
        :class="{ 'bg-primary-50 dark:bg-primary-500/10': isChecked({{ $node['id'] }}) }"
    >
        {{-- 展开/收起按钮 --}}
        <div class="w-5 flex justify-center shrink-0">
            @if(!empty($node['children']))
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

        {{-- 复选框 --}}
        <label class="flex items-center gap-2 cursor-pointer flex-1 select-none">
            <input
                type="checkbox"
                :checked="isChecked({{ $node['id'] }})"
                :disabled="isGlobalDisabled || {{ $node['_disabled'] ? 'true' : 'false' }}"
                {{-- 核心点击事件 --}}
                @change="toggleNode({{ $node['id'] }}, @js($node['_descendants']), {{ $node['_disabled'] ? 'true' : 'false' }})"
                class="fi-checkbox-input rounded border-gray-300 dark:border-white/20 text-primary-600 dark:text-primary-500 shadow-sm focus:ring-primary-500 dark:focus:ring-primary-400 focus:ring-offset-0 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-white/5 dark:checked:bg-primary-500"
            >
            <span
                class="text-sm text-gray-700 dark:text-gray-200"
                :class="{
                    'text-gray-400 dark:text-gray-500': {{ $node['_disabled'] ? 'true' : 'false' }},
                    'font-medium': isChecked({{ $node['id'] }})
                }"
            >
                {{ $node['label'] }}
            </span>
        </label>
    </div>

    @if(!empty($node['children']))
        <ul
            x-show="open"
            x-collapse
            class="pl-6 ml-2.5 border-l border-dashed border-gray-200 dark:border-white/10 space-y-0.5"
        >
            @foreach($node['children'] as $child)
                @include('geekstek-filament-tree::forms.tree-item', [
                    'node' => $child,
                    'defaultExpanded' => $defaultExpanded,
                ])
            @endforeach
        </ul>
    @endif
</li>

