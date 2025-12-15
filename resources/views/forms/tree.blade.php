<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            isGlobalDisabled: {{ $isDisabled() ? 'true' : 'false' }},
            defaultExpanded: {{ $getDefaultExpanded() ? 'true' : 'false' }},

            toggleNode(id, descendants, isNodeDisabled) {
                // 如果全局禁用或节点被单独禁用，则不响应
                if (this.isGlobalDisabled || isNodeDisabled) return;

                if (!Array.isArray(this.state)) this.state = [];

                const isSelected = this.state.includes(id);
                // 级联逻辑：操作自己 + 所有子孙
                const idsToToggle = [id, ...descendants];

                if (isSelected) {
                    // 取消选中：移除自己和所有后代
                    this.state = this.state.filter(val => !idsToToggle.includes(val));
                } else {
                    // 选中：添加自己和所有后代 (利用 Set 去重)
                    const newState = new Set([...this.state, ...idsToToggle]);
                    this.state = Array.from(newState);
                }
            },

            isChecked(id) {
                return Array.isArray(this.state) && this.state.includes(id);
            },

            toggleExpandAll(expand) {
                this.$dispatch('tree-expand-event', { expand: expand });
            },

            selectAll(nodes) {
                if (this.isGlobalDisabled) return;

                const getAllIds = (items) => {
                    let ids = [];
                    items.forEach(item => {
                        if (!item._disabled) {
                            ids.push(item.id);
                        }
                        if (item.children && item.children.length) {
                            ids = ids.concat(getAllIds(item.children));
                        }
                    });
                    return ids;
                };

                const allIds = getAllIds(nodes);
                this.state = allIds;
            },

            deselectAll() {
                if (this.isGlobalDisabled) return;
                this.state = [];
            }
        }"
        class="fi-fo-tree border border-gray-200 dark:border-white/10 rounded-lg overflow-hidden bg-white dark:bg-white/5"
        :class="{ 'opacity-60 pointer-events-none bg-gray-50 dark:bg-white/5': isGlobalDisabled }"
    >
        {{-- 工具栏：仅在非禁用状态下显示交互 --}}
        @if($getShowToolbar() && !$isDisabled())
            <div class="bg-gray-50 dark:bg-white/5 px-4 py-2 border-b border-gray-200 dark:border-white/10 flex gap-3 text-xs">
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
                <span class="text-gray-300 dark:text-gray-600">|</span>
                <button
                    type="button"
                    @click="selectAll(@js($getTreeData()))"
                    class="text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 font-medium transition-colors"
                >
                    {{ __('Select All') }}
                </button>
                <span class="text-gray-300 dark:text-gray-600">|</span>
                <button
                    type="button"
                    @click="deselectAll()"
                    class="text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 font-medium transition-colors"
                >
                    {{ __('Deselect All') }}
                </button>
            </div>
        @endif

        <div class="p-4 max-h-[500px] overflow-y-auto">
            <ul class="space-y-1">
                @foreach($getTreeData() as $node)
                    @include('geekstek-filament-tree::forms.tree-item', [
                        'node' => $node,
                        'defaultExpanded' => $getDefaultExpanded(),
                    ])
                @endforeach
            </ul>
        </div>
    </div>
</x-dynamic-component>

