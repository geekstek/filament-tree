@props([
    'node',
    'selectedIds' => [],
    'defaultExpanded' => true,
    'level' => 0,
])

@php
    $isSelected = in_array($node['id'], $selectedIds);
    $hasChildren = !empty($node['children']);
    $indentPx = $level * 28;
@endphp

<div
    x-data="{ open: {{ $defaultExpanded ? 'true' : 'false' }} }"
    x-on:tree-entry-expand-event.window="open = $event.detail.expand"
    class="fi-in-tree-entry-node"
>
    <div
        class="fi-in-tree-entry-node-row {{ $isSelected ? 'fi-in-tree-entry-node-selected' : '' }}"
        style="padding-left: {{ $indentPx + 8 }}px"
    >
        {{-- 展开/收起按钮 --}}
        <div class="fi-in-tree-entry-node-toggle">
            @if($hasChildren)
                <button type="button" @click="open = !open" class="fi-in-tree-entry-node-toggle-btn">
                    <svg x-show="!open" class="fi-in-tree-entry-node-toggle-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                    <svg x-show="open" x-cloak class="fi-in-tree-entry-node-toggle-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>
            @endif
        </div>

        {{-- 选中状态指示器 --}}
        <div class="fi-in-tree-entry-node-icon">
            @if($isSelected)
                <svg class="fi-in-tree-entry-check-icon-selected" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                </svg>
            @else
                <svg class="fi-in-tree-entry-check-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm.75-11.25a.75.75 0 0 0-1.5 0v2.5h-2.5a.75.75 0 0 0 0 1.5h2.5v2.5a.75.75 0 0 0 1.5 0v-2.5h2.5a.75.75 0 0 0 0-1.5h-2.5v-2.5Z" clip-rule="evenodd" />
                </svg>
            @endif
        </div>

        {{-- 图标 --}}
        <div class="fi-in-tree-entry-node-icon">
            @if($hasChildren)
                <svg x-show="open" class="fi-in-tree-entry-folder-icon fi-in-tree-entry-folder-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 6a2 2 0 0 1 2-2h5l2 2h5a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Z" />
                </svg>
                <svg x-show="!open" x-cloak class="fi-in-tree-entry-folder-icon fi-in-tree-entry-folder-closed" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M3.75 3A1.75 1.75 0 0 0 2 4.75v3.26a3.235 3.235 0 0 1 1.75-.51h12.5c.644 0 1.245.188 1.75.51V6.75A1.75 1.75 0 0 0 16.25 5h-4.836a.25.25 0 0 1-.177-.073L9.823 3.513A1.75 1.75 0 0 0 8.586 3H3.75ZM3.75 9A1.75 1.75 0 0 0 2 10.75v4.5c0 .966.784 1.75 1.75 1.75h12.5A1.75 1.75 0 0 0 18 15.25v-4.5A1.75 1.75 0 0 0 16.25 9H3.75Z" />
                </svg>
            @else
                <svg class="fi-in-tree-entry-file-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M3 3.5A1.5 1.5 0 0 1 4.5 2h6.879a1.5 1.5 0 0 1 1.06.44l4.122 4.12A1.5 1.5 0 0 1 17 7.622V16.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 3 16.5v-13Z" />
                </svg>
            @endif
        </div>

        {{-- 标签 --}}
        <span class="fi-in-tree-entry-node-label {{ $isSelected ? 'fi-in-tree-entry-node-label-selected' : '' }}">
            {{ $node['label'] }}
        </span>

        {{-- 子节点数量 --}}
        @if($hasChildren)
            <span class="fi-in-tree-entry-node-count">({{ count($node['children']) }})</span>
        @endif
    </div>

    {{-- 子节点列表 --}}
    @if($hasChildren)
        <div x-show="open" x-collapse x-cloak class="fi-in-tree-entry-children">
            @foreach($node['children'] as $child)
                @include('geekstek-filament-tree::infolists.tree-entry-item', [
                    'node' => $child,
                    'selectedIds' => $selectedIds,
                    'defaultExpanded' => $defaultExpanded,
                    'level' => $level + 1,
                ])
            @endforeach
        </div>
    @endif
</div>
