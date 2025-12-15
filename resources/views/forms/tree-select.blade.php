<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-ignore
        ax-load
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            tree: null,
            isDisabled: {{ $isDisabled() ? 'true' : 'false' }},

            init() {
                this.loadTreeselect();
            },

            async loadTreeselect() {
                try {
                    const { default: Treeselect } = await import('https://cdn.jsdelivr.net/npm/treeselectjs@0.11.0/dist/treeselectjs.mjs.js');

                    this.tree = new Treeselect({
                        parentHtmlContainer: this.$refs.treeContainer,
                        value: this.state ?? {{ $isSingleSelect() ? 'null' : '[]' }},
                        options: @js($getOptions()),
                        isSingleSelect: {{ $isSingleSelect() ? 'true' : 'false' }},
                        showTags: {{ $getShowTags() ? 'true' : 'false' }},
                        disabled: this.isDisabled,
                        placeholder: '{{ $getPlaceholder() ?? __('Select...') }}',
                        direction: 'auto',
                        clearable: {{ $isClearable() ? 'true' : 'false' }},
                        searchable: {{ $isSearchable() ? 'true' : 'false' }},
                        tagsCountText: '{{ __('items selected') }}',
                        emptyText: '{{ __('No results found') }}',
                    });

                    // 监听变化事件
                    this.tree.srcElement.addEventListener('input', (e) => {
                        this.state = e.detail;
                    });
                } catch (error) {
                    console.error('Failed to load Treeselect:', error);
                }
            }
        }"
        class="fi-fo-tree-select"
    >
        {{-- 动态加载 CSS --}}
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/treeselectjs@0.11.0/dist/treeselectjs.css"
            x-init="
                // 添加暗色模式样式
                const style = document.createElement('style');
                style.textContent = `
                    .dark .treeselect-input {
                        background-color: rgba(255, 255, 255, 0.05) !important;
                        border-color: rgba(255, 255, 255, 0.1) !important;
                        color: white !important;
                    }
                    .dark .treeselect-input__tags-element {
                        background-color: rgba(59, 130, 246, 0.2) !important;
                        color: rgb(147, 197, 253) !important;
                    }
                    .dark .treeselect-list {
                        background-color: rgb(31, 41, 55) !important;
                        border-color: rgba(255, 255, 255, 0.1) !important;
                    }
                    .dark .treeselect-list__item {
                        color: rgb(229, 231, 235) !important;
                    }
                    .dark .treeselect-list__item--focused {
                        background-color: rgba(59, 130, 246, 0.2) !important;
                    }
                    .dark .treeselect-list__item-checkbox {
                        border-color: rgba(255, 255, 255, 0.2) !important;
                    }
                    .dark .treeselect-input__operator {
                        color: rgb(156, 163, 175) !important;
                    }
                    .dark .treeselect-input__edit {
                        color: white !important;
                    }
                `;
                document.head.appendChild(style);
            "
        />

        <div
            x-ref="treeContainer"
            class="treeselect-wrapper text-sm"
            :class="{ 'opacity-60 pointer-events-none': isDisabled }"
        ></div>

        <style>
            .treeselect-wrapper .treeselect-input {
                border-color: rgb(209, 213, 219);
                border-radius: 0.5rem;
                background-color: transparent;
                min-height: 2.5rem;
            }
            .treeselect-wrapper .treeselect-input:focus-within {
                border-color: rgb(59, 130, 246);
                box-shadow: 0 0 0 1px rgb(59, 130, 246);
            }
            .treeselect-wrapper .treeselect-list {
                border-radius: 0.5rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
        </style>
    </div>
</x-dynamic-component>

