@php
    $statePath = $getStatePath();
    $initialState = $getState() ?? [];
    $isSearchable = $isSearchable();
    $searchPrompt = $getSearchPrompt();
    $noSearchResultsMessage = $getNoSearchResultsMessage();
    $searchDebounce = $getSearchDebounce();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    {{-- SVG Symbols - 只定义一次，所有节点复用 --}}
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="fi-tree-chevron-right" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
        </symbol>
        <symbol id="fi-tree-chevron-down" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
        </symbol>
        <symbol id="fi-tree-folder-open" viewBox="0 0 20 20" fill="currentColor">
            <path d="M2 6a2 2 0 0 1 2-2h5l2 2h5a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6Z" />
        </symbol>
        <symbol id="fi-tree-folder-closed" viewBox="0 0 20 20" fill="currentColor">
            <path d="M3.75 3A1.75 1.75 0 0 0 2 4.75v3.26a3.235 3.235 0 0 1 1.75-.51h12.5c.644 0 1.245.188 1.75.51V6.75A1.75 1.75 0 0 0 16.25 5h-4.836a.25.25 0 0 1-.177-.073L9.823 3.513A1.75 1.75 0 0 0 8.586 3H3.75ZM3.75 9A1.75 1.75 0 0 0 2 10.75v4.5c0 .966.784 1.75 1.75 1.75h12.5A1.75 1.75 0 0 0 18 15.25v-4.5A1.75 1.75 0 0 0 16.25 9H3.75Z" />
        </symbol>
        <symbol id="fi-tree-file" viewBox="0 0 20 20" fill="currentColor">
            <path d="M3 3.5A1.5 1.5 0 0 1 4.5 2h6.879a1.5 1.5 0 0 1 1.06.44l4.122 4.12A1.5 1.5 0 0 1 17 7.622V16.5a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 3 16.5v-13Z" />
        </symbol>
        <symbol id="fi-tree-search" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
        </symbol>
        <symbol id="fi-tree-check" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
        </symbol>
        <symbol id="fi-tree-x" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16ZM8.28 7.22a.75.75 0 0 0-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 1 0 1.06 1.06L10 11.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L11.06 10l1.72-1.72a.75.75 0 0 0-1.06-1.06L10 8.94 8.28 7.22Z" clip-rule="evenodd" />
        </symbol>
    </svg>

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

        /* Search styles */
        .fi-fo-tree-search {
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        html.dark .fi-fo-tree-search,
        .dark .fi-fo-tree-search {
            border-color: rgba(255, 255, 255, 0.1);
            background-color: #27272a;
        }
        .fi-fo-tree-search-input-wrp {
            position: relative;
            display: flex;
            align-items: center;
        }
        .fi-fo-tree-search-icon {
            position: absolute;
            left: 0.625rem;
            width: 1rem;
            height: 1rem;
            color: #9ca3af;
            pointer-events: none;
        }
        html.dark .fi-fo-tree-search-icon,
        .dark .fi-fo-tree-search-icon { color: #71717a; }
        .fi-fo-tree-search-input {
            width: 100%;
            padding: 0.5rem 0.75rem 0.5rem 2rem;
            font-size: 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: #ffffff;
            color: #111827;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .fi-fo-tree-search-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px #3b82f6;
        }
        html.dark .fi-fo-tree-search-input,
        .dark .fi-fo-tree-search-input {
            border-color: #3f3f46;
            background-color: #18181b;
            color: #e4e4e7;
        }
        html.dark .fi-fo-tree-search-input:focus,
        .dark .fi-fo-tree-search-input:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 1px #60a5fa;
        }
        .fi-fo-tree-search-input::placeholder { color: #9ca3af; }
        html.dark .fi-fo-tree-search-input::placeholder,
        .dark .fi-fo-tree-search-input::placeholder { color: #71717a; }
        .fi-fo-tree-no-results {
            padding: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
        }
        html.dark .fi-fo-tree-no-results,
        .dark .fi-fo-tree-no-results { color: #a1a1aa; }
        .fi-fo-tree-node-hidden { display: none !important; }
    </style>

    <div
        x-data="{
            state: @js($initialState),
            search: '',
            isGlobalDisabled: {{ $isDisabled() ? 'true' : 'false' }},
            defaultExpanded: {{ $getDefaultExpanded() ? 'true' : 'false' }},
            expandSelected: {{ $getExpandSelected() ? 'true' : 'false' }},
            defaultOpenLevel: {{ $getDefaultOpenLevel() ?? 'null' }},
            leafOnly: {{ $isLeafOnly() ? 'true' : 'false' }},
            isSearchable: {{ $isSearchable ? 'true' : 'false' }},

            init() {
                // 监听 state 变化并同步到 Livewire
                this.$watch('state', (value) => {
                    $wire.set('{{ $statePath }}', value);
                });

                // 搜索时自动展开所有节点
                this.$watch('search', (value) => {
                    if (value && value.length > 0) {
                        this.toggleExpandAll(true);
                    }
                });
            },

            // 检查节点是否匹配搜索
            matchesSearch(label) {
                if (!this.search || this.search.length === 0) return true;
                return label.toLowerCase().includes(this.search.toLowerCase());
            },

            // 检查节点或其子节点是否匹配搜索
            nodeOrChildrenMatchSearch(node) {
                if (!this.search || this.search.length === 0) return true;
                
                // 检查当前节点
                if (this.matchesSearch(node.label)) return true;
                
                // 递归检查子节点
                if (node.children && node.children.length > 0) {
                    return node.children.some(child => this.nodeOrChildrenMatchSearch(child));
                }
                
                return false;
            },

            // 获取匹配的节点数量
            getMatchCount(nodes) {
                if (!this.search || this.search.length === 0) return -1;
                
                let count = 0;
                const countMatches = (items) => {
                    items.forEach(item => {
                        if (this.matchesSearch(item.label)) count++;
                        if (item.children) countMatches(item.children);
                    });
                };
                countMatches(nodes);
                return count;
            },

            toggleNode(id, selectableDescendants, allDescendants, isNodeDisabled, selectableLeafDescendants) {
                if (this.isGlobalDisabled || isNodeDisabled) return;

                if (!Array.isArray(this.state)) this.state = [];

                // 对于有子节点的父节点，检查是否所有可选择的子节点都已选中
                const hasSelectableDescendants = selectableDescendants.length > 0;
                let shouldSelect;

                // leafOnly 模式下使用叶子节点判断
                const checkIds = this.leafOnly ? selectableLeafDescendants : selectableDescendants;

                if (hasSelectableDescendants) {
                    // 父节点：根据可选择子节点的选中状态来决定
                    const allSelectableSelected = checkIds.every(d => this.state.includes(d));
                    shouldSelect = !allSelectableSelected;
                } else if (allDescendants.length > 0) {
                    // 有子节点但都是禁用的，不允许操作
                    return;
                } else {
                    // 叶子节点：切换自身状态
                    shouldSelect = !this.state.includes(id);
                }

                if (shouldSelect) {
                    // 选中：leafOnly 模式只添加叶子节点，否则添加自身和所有可选择的子节点
                    let idsToAdd;
                    if (this.leafOnly) {
                        // 如果是叶子节点（没有子节点），添加自身；否则只添加可选择的叶子后代
                        idsToAdd = selectableLeafDescendants.length > 0 ? selectableLeafDescendants : [id];
                    } else {
                        idsToAdd = [id, ...selectableDescendants];
                    }
                    const newState = new Set([...this.state, ...idsToAdd]);
                    this.state = Array.from(newState);
                } else {
                    // 取消选中：leafOnly 模式只移除叶子节点，否则移除自身和所有可选择的子节点
                    let idsToRemove;
                    if (this.leafOnly) {
                        idsToRemove = selectableLeafDescendants.length > 0 ? selectableLeafDescendants : [id];
                    } else {
                        idsToRemove = [id, ...selectableDescendants];
                    }
                    this.state = this.state.filter(val => !idsToRemove.includes(val));
                }
            },

            isChecked(id, selectableDescendants = [], selectableLeafDescendants = []) {
                if (!Array.isArray(this.state)) return false;

                // 转换为字符串进行比较（解决类型不匹配问题）
                const stateStrings = this.state.map(v => String(v));
                const idString = String(id);

                // leafOnly 模式下使用叶子节点判断
                const checkIds = this.leafOnly ? selectableLeafDescendants : selectableDescendants;

                // 如果有可选择的子节点，只有当所有可选择子节点都被选中时才返回 true
                if (checkIds.length > 0) {
                    return checkIds.every(d => stateStrings.includes(String(d)));
                }

                // 叶子节点：检查自身是否在 state 中
                return stateStrings.includes(idString);
            },

            isIndeterminate(id, selectableDescendants, selectableLeafDescendants = []) {
                // leafOnly 模式下使用叶子节点判断
                const checkIds = this.leafOnly ? selectableLeafDescendants : selectableDescendants;

                if (!Array.isArray(this.state) || checkIds.length === 0) return false;

                const checkedCount = checkIds.filter(d => this.state.includes(d)).length;
                // 部分选中：有选中但不是全部可选择的
                return checkedCount > 0 && checkedCount < checkIds.length;
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
                        const hasChildren = item.children && item.children.length > 0;

                        if (!isDisabled) {
                            // leafOnly 模式下只添加叶子节点
                            if (this.leafOnly) {
                                if (!hasChildren) {
                                    ids.push(item.id);
                                }
                            } else {
                                ids.push(item.id);
                            }
                        }

                        // 递归处理子节点，传递当前禁用状态
                        if (hasChildren) {
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
        {{-- 搜索框 --}}
        @if($isSearchable && !$isDisabled())
            <div class="fi-fo-tree-search">
                <div class="fi-fo-tree-search-input-wrp">
                    <svg class="fi-fo-tree-search-icon"><use href="#fi-tree-search"></use></svg>
                    <input
                        type="search"
                        x-model.debounce.{{ $searchDebounce }}ms="search"
                        placeholder="{{ $searchPrompt }}"
                        class="fi-fo-tree-search-input"
                    />
                </div>
            </div>
        @endif

        {{-- 工具栏 --}}
        @if($getShowToolbar() && !$isDisabled())
            <div class="fi-fo-tree-toolbar">
                <button type="button" @click="toggleExpandAll(true)" class="fi-fo-tree-toolbar-btn">
                    <svg class="fi-fo-tree-toolbar-icon"><use href="#fi-tree-chevron-down"></use></svg>
                    <span>{{ __('geekstek-filament-tree::filament-tree.toolbar.expand_all') }}</span>
                </button>

                <span class="fi-fo-tree-toolbar-separator">|</span>

                <button type="button" @click="toggleExpandAll(false)" class="fi-fo-tree-toolbar-btn">
                    <svg class="fi-fo-tree-toolbar-icon"><use href="#fi-tree-chevron-right"></use></svg>
                    <span>{{ __('geekstek-filament-tree::filament-tree.toolbar.collapse_all') }}</span>
                </button>

                <span class="fi-fo-tree-toolbar-separator">|</span>

                <button type="button" @click="selectAll(@js($getTreeData()))" class="fi-fo-tree-toolbar-btn">
                    <svg class="fi-fo-tree-toolbar-icon"><use href="#fi-tree-check"></use></svg>
                    <span>{{ __('geekstek-filament-tree::filament-tree.toolbar.select_all') }}</span>
                </button>

                <span class="fi-fo-tree-toolbar-separator">|</span>

                <button type="button" @click="deselectAll()" class="fi-fo-tree-toolbar-btn">
                    <svg class="fi-fo-tree-toolbar-icon"><use href="#fi-tree-x"></use></svg>
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
                {{-- 无搜索结果提示 --}}
                <template x-if="search && getMatchCount(@js($getTreeData())) === 0">
                    <p class="fi-fo-tree-no-results">{{ $noSearchResultsMessage }}</p>
                </template>

                <div class="fi-fo-tree-nodes">
                    @foreach($getTreeData() as $node)
                        @include('geekstek-filament-tree::forms.tree-item', [
                            'node' => $node,
                            'statePath' => $statePath,
                            'defaultExpanded' => $getDefaultExpanded(),
                            'expandSelected' => $getExpandSelected(),
                            'defaultOpenLevel' => $getDefaultOpenLevel(),
                            'level' => 0,
                            'parentDisabled' => false,
                            'isSearchable' => $isSearchable,
                        ])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-dynamic-component>