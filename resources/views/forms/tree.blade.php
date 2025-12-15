<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <style>
        /* Tree Component Styles */
        .fi-fo-tree {
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background-color: #ffffff;
        }
        html.dark .fi-fo-tree,
        .dark .fi-fo-tree {
            border-color: rgba(255, 255, 255, 0.1);
            background-color: #18181b;
        }
        .fi-fo-tree.fi-disabled { opacity: 0.5; pointer-events: none; }

        .fi-fo-tree-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            border-bottom: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        html.dark .fi-fo-tree-toolbar,
        .dark .fi-fo-tree-toolbar {
            border-color: rgba(255, 255, 255, 0.1);
            background-color: #27272a;
        }

        .fi-fo-tree-toolbar-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            color: #2563eb;
            transition: color 0.15s;
        }
        .fi-fo-tree-toolbar-btn:hover { color: #1d4ed8; }
        html.dark .fi-fo-tree-toolbar-btn,
        .dark .fi-fo-tree-toolbar-btn { color: #60a5fa; }
        html.dark .fi-fo-tree-toolbar-btn:hover,
        .dark .fi-fo-tree-toolbar-btn:hover { color: #93c5fd; }

        .fi-fo-tree-toolbar-icon { width: 1rem; height: 1rem; }
        .fi-fo-tree-toolbar-separator { color: #d1d5db; }
        html.dark .fi-fo-tree-toolbar-separator,
        .dark .fi-fo-tree-toolbar-separator { color: #3f3f46; }

        .fi-fo-tree-content { padding: 0.5rem; overflow-y: auto; }

        .fi-fo-tree-empty {
            padding: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            font-style: italic;
            color: #6b7280;
        }
        html.dark .fi-fo-tree-empty,
        .dark .fi-fo-tree-empty { color: #a1a1aa; }

        .fi-fo-tree-node-row {
            display: flex;
            align-items: center;
            min-height: 36px;
            border-radius: 0.375rem;
            transition: background-color 0.1s;
        }
        .fi-fo-tree-node-row:hover { background-color: #f3f4f6; }
        html.dark .fi-fo-tree-node-row:hover,
        .dark .fi-fo-tree-node-row:hover { background-color: #27272a; }
        .fi-fo-tree-node-row.fi-fo-tree-node-checked { background-color: #eff6ff; }
        html.dark .fi-fo-tree-node-row.fi-fo-tree-node-checked,
        .dark .fi-fo-tree-node-row.fi-fo-tree-node-checked { background-color: rgba(59, 130, 246, 0.15); }
        .fi-fo-tree-node-row.fi-fo-tree-node-checked:hover { background-color: #dbeafe; }
        html.dark .fi-fo-tree-node-row.fi-fo-tree-node-checked:hover,
        .dark .fi-fo-tree-node-row.fi-fo-tree-node-checked:hover { background-color: rgba(59, 130, 246, 0.25); }

        .fi-fo-tree-node-toggle {
            width: 1.5rem;
            height: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .fi-fo-tree-node-toggle-btn {
            width: 1.25rem;
            height: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.25rem;
            color: #6b7280;
            transition: all 0.15s;
        }
        .fi-fo-tree-node-toggle-btn:hover { color: #374151; background-color: #e5e7eb; }
        html.dark .fi-fo-tree-node-toggle-btn,
        .dark .fi-fo-tree-node-toggle-btn { color: #a1a1aa; }
        html.dark .fi-fo-tree-node-toggle-btn:hover,
        .dark .fi-fo-tree-node-toggle-btn:hover { color: #e4e4e7; background-color: #3f3f46; }
        .fi-fo-tree-node-toggle-icon { width: 1rem; height: 1rem; }

        .fi-fo-tree-checkbox { flex-shrink: 0; margin-right: 0.5rem; }

        .fi-fo-tree-node-icon {
            width: 1.25rem;
            height: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-right: 0.375rem;
        }
        .fi-fo-tree-folder-icon { width: 1rem; height: 1rem; }
        .fi-fo-tree-folder-open { color: #f59e0b; }
        html.dark .fi-fo-tree-folder-open,
        .dark .fi-fo-tree-folder-open { color: #fbbf24; }
        .fi-fo-tree-folder-closed { color: #d97706; }
        html.dark .fi-fo-tree-folder-closed,
        .dark .fi-fo-tree-folder-closed { color: #f59e0b; }
        .fi-fo-tree-file-icon { width: 1rem; height: 1rem; color: #9ca3af; }
        html.dark .fi-fo-tree-file-icon,
        .dark .fi-fo-tree-file-icon { color: #71717a; }

        .fi-fo-tree-node-label {
            flex: 1;
            padding: 0.375rem 0.5rem 0.375rem 0;
            font-size: 0.875rem;
            cursor: pointer;
            user-select: none;
            color: #374151;
        }
        html.dark .fi-fo-tree-node-label,
        .dark .fi-fo-tree-node-label { color: #e4e4e7; }
        .fi-fo-tree-node-label.fi-fo-tree-node-disabled {
            cursor: not-allowed;
            color: #9ca3af;
            text-decoration: line-through;
        }
        html.dark .fi-fo-tree-node-label.fi-fo-tree-node-disabled,
        .dark .fi-fo-tree-node-label.fi-fo-tree-node-disabled { color: #71717a; }
        .fi-fo-tree-node-label.fi-fo-tree-node-label-checked { font-weight: 500; color: #111827; }
        html.dark .fi-fo-tree-node-label.fi-fo-tree-node-label-checked,
        .dark .fi-fo-tree-node-label.fi-fo-tree-node-label-checked { color: #ffffff; }
        .fi-fo-tree-node-disabled-text { margin-left: 0.375rem; font-size: 0.75rem; color: #9ca3af; }
        html.dark .fi-fo-tree-node-disabled-text,
        .dark .fi-fo-tree-node-disabled-text { color: #71717a; }

        .fi-fo-tree-node-count {
            padding-right: 0.5rem;
            font-size: 0.75rem;
            font-variant-numeric: tabular-nums;
            color: #9ca3af;
        }
        html.dark .fi-fo-tree-node-count,
        .dark .fi-fo-tree-node-count { color: #71717a; }
    </style>

    <div
        x-data="{
            state: $wire.entangle('{{ $getStatePath() }}'){{ $isLive() ? '.live' : '' }},
            isGlobalDisabled: {{ $isDisabled() ? 'true' : 'false' }},
            defaultExpanded: {{ $getDefaultExpanded() ? 'true' : 'false' }},
            expandSelected: {{ $getExpandSelected() ? 'true' : 'false' }},
            defaultOpenLevel: {{ $getDefaultOpenLevel() ?? 'null' }},

            toggleNode(id, selectableDescendants, allDescendants, isNodeDisabled) {
                if (this.isGlobalDisabled || isNodeDisabled) return;

                if (!Array.isArray(this.state)) this.state = [];

                // 对于有子节点的父节点，检查是否所有可选择的子节点都已选中
                const hasSelectableDescendants = selectableDescendants.length > 0;
                let shouldSelect;

                if (hasSelectableDescendants) {
                    // 父节点：根据可选择子节点的选中状态来决定
                    const allSelectableSelected = selectableDescendants.every(d => this.state.includes(d));
                    shouldSelect = !allSelectableSelected;
                } else if (allDescendants.length > 0) {
                    // 有子节点但都是禁用的，不允许操作
                    return;
                } else {
                    // 叶子节点：切换自身状态
                    shouldSelect = !this.state.includes(id);
                }

                if (shouldSelect) {
                    // 选中：添加自身和所有可选择的子节点（排除禁用项）
                    const idsToAdd = [id, ...selectableDescendants];
                    const newState = new Set([...this.state, ...idsToAdd]);
                    this.state = Array.from(newState);
                } else {
                    // 取消选中：移除自身和所有可选择的子节点
                    const idsToRemove = [id, ...selectableDescendants];
                    this.state = this.state.filter(val => !idsToRemove.includes(val));
                }
            },

            isChecked(id, selectableDescendants = []) {
                if (!Array.isArray(this.state)) return false;

                // 如果有可选择的子节点，只有当所有可选择子节点都被选中时才返回 true
                if (selectableDescendants.length > 0) {
                    return selectableDescendants.every(d => this.state.includes(d));
                }

                // 叶子节点：检查自身是否在 state 中
                return this.state.includes(id);
            },

            isIndeterminate(id, selectableDescendants) {
                if (!Array.isArray(this.state) || selectableDescendants.length === 0) return false;

                const checkedCount = selectableDescendants.filter(d => this.state.includes(d)).length;
                // 部分选中：有选中但不是全部可选择的
                return checkedCount > 0 && checkedCount < selectableDescendants.length;
            },

            toggleExpandAll(expand) {
                this.$dispatch('tree-expand-event', { expand: expand });
            },

            selectAll(nodes) {
                if (this.isGlobalDisabled) return;

                const getAllIds = (items, parentDisabled = false) => {
                    let ids = [];
                    items.forEach(item => {
                        // 如果当前节点被禁用或父节点被禁用，则跳过
                        const isDisabled = item._disabled || parentDisabled;

                        if (!isDisabled) {
                            ids.push(item.id);
                        }

                        // 递归处理子节点，传递当前禁用状态
                        if (item.children && item.children.length) {
                            ids = ids.concat(getAllIds(item.children, isDisabled));
                        }
                    });
                    return ids;
                };

                const allIds = getAllIds(nodes, false);
                this.state = allIds;
            },

            deselectAll() {
                if (this.isGlobalDisabled) return;
                this.state = [];
            }
        }"
        class="fi-fo-tree"
        :class="{ 'fi-disabled': isGlobalDisabled }"
    >
        {{-- 工具栏 --}}
        @if($getShowToolbar() && !$isDisabled())
            <div class="fi-fo-tree-toolbar">
                <button type="button" @click="toggleExpandAll(true)" class="fi-fo-tree-toolbar-btn">
                    <svg class="fi-fo-tree-toolbar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ __('geekstek-filament-tree::filament-tree.toolbar.expand_all') }}</span>
                </button>

                <span class="fi-fo-tree-toolbar-separator">|</span>

                <button type="button" @click="toggleExpandAll(false)" class="fi-fo-tree-toolbar-btn">
                    <svg class="fi-fo-tree-toolbar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ __('geekstek-filament-tree::filament-tree.toolbar.collapse_all') }}</span>
                </button>

                <span class="fi-fo-tree-toolbar-separator">|</span>

                <button type="button" @click="selectAll(@js($getTreeData()))" class="fi-fo-tree-toolbar-btn">
                    <svg class="fi-fo-tree-toolbar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ __('geekstek-filament-tree::filament-tree.toolbar.select_all') }}</span>
                </button>

                <span class="fi-fo-tree-toolbar-separator">|</span>

                <button type="button" @click="deselectAll()" class="fi-fo-tree-toolbar-btn">
                    <svg class="fi-fo-tree-toolbar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ __('geekstek-filament-tree::filament-tree.toolbar.deselect_all') }}</span>
                </button>
            </div>
        @endif

        <div class="fi-fo-tree-content" @if($getMaxHeight()) style="max-height: {{ $getMaxHeight() }}" @endif>
            @if(empty($getTreeData()))
                <p class="fi-fo-tree-empty">
                    {{ __('geekstek-filament-tree::filament-tree.empty.no_data') }}
                </p>
            @else
                <div class="fi-fo-tree-nodes">
                    @foreach($getTreeData() as $node)
                        @include('geekstek-filament-tree::forms.tree-item', [
                            'node' => $node,
                            'defaultExpanded' => $getDefaultExpanded(),
                            'expandSelected' => $getExpandSelected(),
                            'defaultOpenLevel' => $getDefaultOpenLevel(),
                            'level' => 0,
                            'parentDisabled' => false,
                        ])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-dynamic-component>
