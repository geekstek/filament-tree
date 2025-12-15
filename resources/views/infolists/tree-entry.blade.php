<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <style>
        /* Tree Entry Component Styles */
        .fi-in-tree-entry {
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background-color: #ffffff;
        }
        html.dark .fi-in-tree-entry,
        .dark .fi-in-tree-entry {
            border-color: rgba(255, 255, 255, 0.1);
            background-color: #18181b;
        }

        .fi-in-tree-entry-toolbar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-bottom: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        html.dark .fi-in-tree-entry-toolbar,
        .dark .fi-in-tree-entry-toolbar {
            border-color: rgba(255, 255, 255, 0.1);
            background-color: #27272a;
        }

        .fi-in-tree-entry-toolbar-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            color: #2563eb;
            transition: color 0.15s;
        }
        .fi-in-tree-entry-toolbar-btn:hover { color: #1d4ed8; }
        html.dark .fi-in-tree-entry-toolbar-btn,
        .dark .fi-in-tree-entry-toolbar-btn { color: #60a5fa; }
        html.dark .fi-in-tree-entry-toolbar-btn:hover,
        .dark .fi-in-tree-entry-toolbar-btn:hover { color: #93c5fd; }

        .fi-in-tree-entry-toolbar-icon { width: 1rem; height: 1rem; }
        .fi-in-tree-entry-toolbar-separator { color: #d1d5db; }
        html.dark .fi-in-tree-entry-toolbar-separator,
        .dark .fi-in-tree-entry-toolbar-separator { color: #3f3f46; }

        .fi-in-tree-entry-content { padding: 0.5rem; overflow-y: auto; }

        .fi-in-tree-entry-empty {
            padding: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            font-style: italic;
            color: #6b7280;
        }
        html.dark .fi-in-tree-entry-empty,
        .dark .fi-in-tree-entry-empty { color: #a1a1aa; }

        .fi-in-tree-entry-node-row {
            display: flex;
            align-items: center;
            min-height: 36px;
            border-radius: 0.375rem;
            transition: background-color 0.1s;
        }
        .fi-in-tree-entry-node-row:hover { background-color: #f3f4f6; }
        html.dark .fi-in-tree-entry-node-row:hover,
        .dark .fi-in-tree-entry-node-row:hover { background-color: #27272a; }
        .fi-in-tree-entry-node-row.fi-in-tree-entry-node-selected { background-color: #eff6ff; }
        html.dark .fi-in-tree-entry-node-row.fi-in-tree-entry-node-selected,
        .dark .fi-in-tree-entry-node-row.fi-in-tree-entry-node-selected { background-color: rgba(59, 130, 246, 0.15); }
        .fi-in-tree-entry-node-row.fi-in-tree-entry-node-selected:hover { background-color: #dbeafe; }
        html.dark .fi-in-tree-entry-node-row.fi-in-tree-entry-node-selected:hover,
        .dark .fi-in-tree-entry-node-row.fi-in-tree-entry-node-selected:hover { background-color: rgba(59, 130, 246, 0.25); }

        .fi-in-tree-entry-node-toggle {
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .fi-in-tree-entry-node-toggle-btn {
            width: 1.25rem;
            height: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.25rem;
            color: #6b7280;
            transition: all 0.15s;
        }
        .fi-in-tree-entry-node-toggle-btn:hover { color: #374151; background-color: #e5e7eb; }
        html.dark .fi-in-tree-entry-node-toggle-btn,
        .dark .fi-in-tree-entry-node-toggle-btn { color: #a1a1aa; }
        html.dark .fi-in-tree-entry-node-toggle-btn:hover,
        .dark .fi-in-tree-entry-node-toggle-btn:hover { color: #e4e4e7; background-color: #3f3f46; }
        .fi-in-tree-entry-node-toggle-icon { width: 1rem; height: 1rem; }

        .fi-in-tree-entry-node-icon {
            width: 1.25rem;
            height: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-right: 0.375rem;
        }
        .fi-in-tree-entry-folder-icon { width: 1rem; height: 1rem; }
        .fi-in-tree-entry-folder-open { color: #f59e0b; }
        html.dark .fi-in-tree-entry-folder-open,
        .dark .fi-in-tree-entry-folder-open { color: #fbbf24; }
        .fi-in-tree-entry-folder-closed { color: #d97706; }
        html.dark .fi-in-tree-entry-folder-closed,
        .dark .fi-in-tree-entry-folder-closed { color: #f59e0b; }
        .fi-in-tree-entry-file-icon { width: 1rem; height: 1rem; color: #9ca3af; }
        html.dark .fi-in-tree-entry-file-icon,
        .dark .fi-in-tree-entry-file-icon { color: #71717a; }

        .fi-in-tree-entry-check-icon { width: 1rem; height: 1rem; color: #d1d5db; }
        html.dark .fi-in-tree-entry-check-icon,
        .dark .fi-in-tree-entry-check-icon { color: #52525b; }
        .fi-in-tree-entry-check-icon-selected { width: 1rem; height: 1rem; color: #2563eb; }
        html.dark .fi-in-tree-entry-check-icon-selected,
        .dark .fi-in-tree-entry-check-icon-selected { color: #60a5fa; }

        .fi-in-tree-entry-node-label {
            flex: 1;
            padding: 0.375rem 0.5rem 0.375rem 0;
            font-size: 0.875rem;
            color: #374151;
        }
        html.dark .fi-in-tree-entry-node-label,
        .dark .fi-in-tree-entry-node-label { color: #e4e4e7; }
        .fi-in-tree-entry-node-label.fi-in-tree-entry-node-label-selected { font-weight: 500; color: #111827; }
        html.dark .fi-in-tree-entry-node-label.fi-in-tree-entry-node-label-selected,
        .dark .fi-in-tree-entry-node-label.fi-in-tree-entry-node-label-selected { color: #ffffff; }

        .fi-in-tree-entry-node-count {
            padding-right: 0.5rem;
            font-size: 0.75rem;
            font-variant-numeric: tabular-nums;
            color: #9ca3af;
        }
        html.dark .fi-in-tree-entry-node-count,
        .dark .fi-in-tree-entry-node-count { color: #71717a; }
    </style>

    <div
        x-data="{
            defaultExpanded: {{ $getDefaultExpanded() ? 'true' : 'false' }},
            toggleExpandAll(expand) {
                this.$dispatch('tree-entry-expand-event', { expand: expand });
            }
        }"
        class="fi-in-tree-entry"
    >
        {{-- 工具栏 --}}
        <div class="fi-in-tree-entry-toolbar">
            <button type="button" @click="toggleExpandAll(true)" class="fi-in-tree-entry-toolbar-btn">
                <svg class="fi-in-tree-entry-toolbar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>
                <span>{{ __('geekstek-filament-tree::filament-tree.toolbar.expand_all') }}</span>
            </button>

            <span class="fi-in-tree-entry-toolbar-separator">|</span>

            <button type="button" @click="toggleExpandAll(false)" class="fi-in-tree-entry-toolbar-btn">
                <svg class="fi-in-tree-entry-toolbar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                </svg>
                <span>{{ __('geekstek-filament-tree::filament-tree.toolbar.collapse_all') }}</span>
            </button>
        </div>

        <div class="fi-in-tree-entry-content" @if($getMaxHeight()) style="max-height: {{ $getMaxHeight() }}" @endif>
            @php
                $selectedIds = $getState() ?? [];
                $options = $getTreeOptions();
            @endphp

            @if(empty($options))
                <p class="fi-in-tree-entry-empty">
                    {{ __('geekstek-filament-tree::filament-tree.empty.no_data') }}
                </p>
            @else
                <div class="fi-in-tree-entry-nodes">
                    @foreach($options as $node)
                        @include('geekstek-filament-tree::infolists.tree-entry-item', [
                            'node' => $node,
                            'selectedIds' => $selectedIds,
                            'defaultExpanded' => $getDefaultExpanded(),
                            'level' => 0,
                        ])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-dynamic-component>
