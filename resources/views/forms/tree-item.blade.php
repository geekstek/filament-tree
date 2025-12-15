@props([
    'node',
    'statePath',
    'defaultExpanded' => true,
    'expandSelected' => false,
    'defaultOpenLevel' => null,
    'level' => 0,
    'parentDisabled' => false,
    'isSearchable' => false,
])

@php
    $hasChildren = !empty($node['children']);
    // 如果父节点被禁用，子节点也应该被禁用
    $isDisabled = ($node['_disabled'] ?? false) || $parentDisabled;
    $indentPx = $level * 28;
    $selectableDescendants = $node['_selectableDescendants'] ?? [];
    $selectableLeafDescendants = $node['_selectableLeafDescendants'] ?? [];
    $allDescendants = $node['_descendants'] ?? [];

    // 使用 PHP 预计算的展开状态（已在 Tree.php processNodes 中计算）
    $initialOpen = $node['_initialOpen'] ?? $defaultExpanded;
@endphp

<div
    x-data="{
        open: {{ $initialOpen ? 'true' : 'false' }},
        @if($isSearchable)
        nodeData: @js($node),
        isVisible() {
            // 如果没有搜索功能或没有搜索内容，始终显示
            if (!search || search.length === 0) return true;
            // 使用父级的 nodeOrChildrenMatchSearch 方法
            return nodeOrChildrenMatchSearch(this.nodeData);
        }
        @endif
    }"
    x-on:tree-expand-event.window="open = $event.detail.expand"
    @if($isSearchable) x-show="isVisible()" @endif
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
                    <svg x-show="!open" class="fi-fo-tree-node-toggle-icon"><use href="#fi-tree-chevron-right"></use></svg>
                    <svg x-show="open" x-cloak class="fi-fo-tree-node-toggle-icon"><use href="#fi-tree-chevron-down"></use></svg>
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
                <svg x-show="open" class="fi-fo-tree-folder-icon fi-fo-tree-folder-open"><use href="#fi-tree-folder-open"></use></svg>
                <svg x-show="!open" x-cloak class="fi-fo-tree-folder-icon fi-fo-tree-folder-closed"><use href="#fi-tree-folder-closed"></use></svg>
            @else
                <svg class="fi-fo-tree-file-icon"><use href="#fi-tree-file"></use></svg>
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
                    'statePath' => $statePath,
                    'defaultExpanded' => $defaultExpanded,
                    'expandSelected' => $expandSelected,
                    'defaultOpenLevel' => $defaultOpenLevel,
                    'level' => $level + 1,
                    'parentDisabled' => $isDisabled,
                    'isSearchable' => $isSearchable,
                ])
            @endforeach
        </div>
    @endif
</div>
