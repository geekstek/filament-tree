@props([
    'node',
    'defaultExpanded' => true,
    'expandSelected' => false,
    'defaultOpenLevel' => null,
    'level' => 0,
    'parentDisabled' => false,
])

@php
    $hasChildren = !empty($node['children']);
    // 如果父节点被禁用，子节点也应该被禁用
    $isDisabled = ($node['_disabled'] ?? false) || $parentDisabled;
    $indentPx = $level * 28;
    $selectableDescendants = $node['_selectableDescendants'] ?? [];
    $selectableLeafDescendants = $node['_selectableLeafDescendants'] ?? [];
    $allDescendants = $node['_descendants'] ?? [];

    // 计算初始展开状态
    // 优先级: defaultOpenLevel > expandSelected > defaultExpanded
    $initialOpen = $defaultExpanded;

    // 如果设置了 defaultOpenLevel，按层级展开
    if ($defaultOpenLevel !== null) {
        $initialOpen = $level < $defaultOpenLevel;
    }
@endphp

<div
    x-data="{
        open: {{ $initialOpen ? 'true' : 'false' }},
        expandSelected: {{ $expandSelected ? 'true' : 'false' }},
        allDescendants: @js($allDescendants),

        init() {
            // 如果启用了 expandSelected，检查是否有子节点被选中
            if (this.expandSelected && this.allDescendants.length > 0) {
                const currentState = $wire.get('{{ $getStatePath() }}') ?? [];
                const hasSelectedDescendant = this.allDescendants.some(id => currentState.includes(id));
                if (hasSelectedDescendant) {
                    this.open = true;
                }
            }
        }
    }"
    x-on:tree-expand-event.window="open = $event.detail.expand"
    class="fi-fo-tree-node"
>
    <div
        class="fi-fo-tree-node-row"
        :class="{
            'fi-fo-tree-node-checked': isChecked(@js($node['id']), @js($selectableDescendants), @js($selectableLeafDescendants))
        }"
        style="padding-left: {{ $indentPx + 8 }}px"
    >
        {{-- 展开/收起按钮 --}}
        <div class="fi-fo-tree-node-toggle">
            @if($hasChildren)
                <button type="button" @click.stop="open = !open" class="fi-fo-tree-node-toggle-btn">
                    <svg x-show="!open" class="fi-fo-tree-node-toggle-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                    <svg x-show="open" x-cloak class="fi-fo-tree-node-toggle-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </button>
            @endif
        </div>

        {{-- 复选框 --}}
        <input
            type="checkbox"
            :checked="isChecked(@js($node['id']), @js($selectableDescendants), @js($selectableLeafDescendants))"
            :indeterminate="isIndeterminate(@js($node['id']), @js($selectableDescendants), @js($selectableLeafDescendants))"
            :disabled="isGlobalDisabled || {{ $isDisabled ? 'true' : 'false' }}"
            @change="toggleNode(@js($node['id']), @js($selectableDescendants), @js($allDescendants), {{ $isDisabled ? 'true' : 'false' }}, @js($selectableLeafDescendants))"
            class="fi-checkbox-input fi-fo-tree-checkbox"
        >

        {{-- 图标 --}}
        <div class="fi-fo-tree-node-icon">
            @if($hasChildren)
                <svg x-show="open" class="fi-fo-tree-folder-icon fi-fo-tree-folder-open" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 6a2 2 0 0 1 2-2h5l2 2h5a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Z" />
                </svg>
                <svg x-show="!open" x-cloak class="fi-fo-tree-folder-icon fi-fo-tree-folder-closed" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M3.75 3A1.75 1.75 0 0 0 2 4.75v3.26a3.235 3.235 0 0 1 1.75-.51h12.5c.644 0 1.245.188 1.75.51V6.75A1.75 1.75 0 0 0 16.25 5h-4.836a.25.25 0 0 1-.177-.073L9.823 3.513A1.75 1.75 0 0 0 8.586 3H3.75ZM3.75 9A1.75 1.75 0 0 0 2 10.75v4.5c0 .966.784 1.75 1.75 1.75h12.5A1.75 1.75 0 0 0 18 15.25v-4.5A1.75 1.75 0 0 0 16.25 9H3.75Z" />
                </svg>
            @else
                <svg class="fi-fo-tree-file-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M3 3.5A1.5 1.5 0 0 1 4.5 2h6.879a1.5 1.5 0 0 1 1.06.44l4.122 4.12A1.5 1.5 0 0 1 17 7.622V16.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 3 16.5v-13Z" />
                </svg>
            @endif
        </div>

        {{-- 标签 --}}
        <label
            class="fi-fo-tree-node-label {{ $isDisabled ? 'fi-fo-tree-node-disabled' : '' }}"
            :class="{ 'fi-fo-tree-node-label-checked': isChecked(@js($node['id']), @js($selectableDescendants), @js($selectableLeafDescendants)) }"
            @if(!$isDisabled) @click="toggleNode(@js($node['id']), @js($selectableDescendants), @js($allDescendants), false, @js($selectableLeafDescendants))" @endif
        >
            {{ $node['label'] }}
            @if($isDisabled)
                <span class="fi-fo-tree-node-disabled-text">({{ __('geekstek-filament-tree::filament-tree.status.disabled') }})</span>
            @endif
        </label>

        {{-- 子节点数量 --}}
        @if($hasChildren)
            <span class="fi-fo-tree-node-count">({{ count($node['children']) }})</span>
        @endif
    </div>

    {{-- 子节点 --}}
    @if($hasChildren)
        <div x-show="open" x-collapse x-cloak class="fi-fo-tree-children">
            @foreach($node['children'] as $child)
                @include('geekstek-filament-tree::forms.tree-item', [
                    'node' => $child,
                    'defaultExpanded' => $defaultExpanded,
                    'expandSelected' => $expandSelected,
                    'defaultOpenLevel' => $defaultOpenLevel,
                    'level' => $level + 1,
                    'parentDisabled' => $isDisabled,
                ])
            @endforeach
        </div>
    @endif
</div>
