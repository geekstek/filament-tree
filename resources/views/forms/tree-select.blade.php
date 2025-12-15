<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    {{-- 加载 TreeselectJS 样式 --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/treeselectjs@0.11.0/dist/treeselectjs.css"
    />

    @php
        $maxHeight = $getMaxHeight();
        $options = $getOptions();
        $statePath = $getStatePath();
        // 生成基于 options 内容的唯一 key，当 options 变化时强制重新初始化
        $optionsKey = md5(json_encode($options));
        $config = [
            'statePath' => $statePath,
            'options' => $options,
            'isSingleSelect' => $isSingleSelect(),
            'showTags' => $getShowTags(),
            'isDisabled' => $isDisabled(),
            'placeholder' => $getPlaceholder() ?? __('geekstek-filament-tree::filament-tree.placeholder.select'),
            'clearable' => $isClearable(),
            'searchable' => $isSearchable(),
            'tagsCountText' => __('geekstek-filament-tree::filament-tree.status.items_selected'),
            'emptyText' => __('geekstek-filament-tree::filament-tree.empty.no_results'),
            'expandSelected' => $getExpandSelected(),
            'openLevel' => $getDefaultOpenLevel(),
            'maxHeight' => $maxHeight,
            'isLive' => $isLive(),
        ];
    @endphp

    {{-- 外层使用 wire:key 来响应 options 变化，触发重新初始化 --}}
    <div wire:key="tree-select-{{ $statePath }}-{{ $optionsKey }}">
        {{-- 内层使用 wire:ignore 来防止选择值时重新渲染 --}}
        <div
            wire:ignore
            x-data="{
                config: {{ json_encode($config) }},
                tree: null,
                initialized: false,
                TreeselectClass: null,

                async init() {
                    await this.$nextTick();
                    await this.loadTreeselectModule();
                    await this.initTreeselect();
                },

                async loadTreeselectModule() {
                    if (this.TreeselectClass) return;
                    try {
                        const module = await import('https://cdn.jsdelivr.net/npm/treeselectjs@0.11.0/+esm');
                        this.TreeselectClass = module.default;
                    } catch (error) {
                        console.error('Failed to load TreeSelect module:', error);
                    }
                },

                async initTreeselect() {
                    if (this.initialized || !this.TreeselectClass) return;

                    const container = this.$refs.treeContainer;
                    if (!container) return;

                    try {
                        const initialValue = $wire.get(this.config.statePath) ?? (this.config.isSingleSelect ? null : []);

                        this.tree = new this.TreeselectClass({
                            parentHtmlContainer: container,
                            value: initialValue,
                            options: this.config.options,
                            isSingleSelect: this.config.isSingleSelect,
                            showTags: this.config.showTags,
                            disabled: this.config.isDisabled,
                            placeholder: this.config.placeholder,
                            direction: 'auto',
                            clearable: this.config.clearable,
                            searchable: this.config.searchable,
                            tagsCountText: this.config.tagsCountText,
                            emptyText: this.config.emptyText,
                            expandSelected: this.config.expandSelected,
                            openLevel: this.config.openLevel,
                        });

                        this.tree.srcElement.addEventListener('input', (e) => {
                            $wire.set(this.config.statePath, e.detail);
                        });

                        this.initialized = true;
                    } catch (error) {
                        console.error('Failed to initialize TreeSelect:', error);
                    }
                }
            }"
            class="fi-fo-tree-select"
        >
        <div
            x-ref="treeContainer"
            class="treeselect-container"
            :class="{ 'opacity-60 pointer-events-none': config.isDisabled }"
        ></div>
    </div>

    <style>
        /* 基础样式 */
        .fi-fo-tree-select .treeselect-container .treeselect-input {
            border-radius: 0.5rem;
            min-height: 2.5rem;
            font-size: 0.875rem;
            transition: all 0.15s ease;
            border: 1px solid rgb(209 213 219);
            background-color: rgb(255 255 255);
            color: rgb(17 24 39);
        }

        .fi-fo-tree-select .treeselect-container .treeselect-input:focus-within {
            border-color: rgb(59 130 246);
            box-shadow: 0 0 0 1px rgb(59 130 246);
            outline: none;
        }

        .fi-fo-tree-select .treeselect-container .treeselect-input__tags-element {
            background-color: rgb(239 246 255);
            color: rgb(29 78 216);
            border-radius: 0.375rem;
            padding: 0.125rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .fi-fo-tree-select .treeselect-container .treeselect-input__clear,
        .fi-fo-tree-select .treeselect-container .treeselect-input__arrow {
            color: rgb(107 114 128);
        }

        .fi-fo-tree-select .treeselect-container .treeselect-input__clear:hover,
        .fi-fo-tree-select .treeselect-container .treeselect-input__arrow:hover {
            color: rgb(55 65 81);
        }

        .fi-fo-tree-select .treeselect-container .treeselect-list {
            border-radius: 0.5rem;
            border: 1px solid rgb(229 231 235);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            background-color: rgb(255 255 255);
            margin-top: 0.25rem;
            @if($maxHeight) max-height: {{ $maxHeight }}; overflow-y: auto; @endif
        }

        .fi-fo-tree-select .treeselect-container .treeselect-list__item {
            color: rgb(55 65 81);
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }

        .fi-fo-tree-select .treeselect-container .treeselect-list__item--focused {
            background-color: rgb(243 244 246);
        }

        .fi-fo-tree-select .treeselect-container .treeselect-list__item--checked {
            background-color: rgb(239 246 255);
            color: rgb(29 78 216);
        }

        .fi-fo-tree-select .treeselect-container .treeselect-list__item-checkbox {
            border-color: rgb(209 213 219);
            border-radius: 0.25rem;
        }

        .fi-fo-tree-select .treeselect-container .treeselect-list__item-checkbox--checked {
            background-color: rgb(59 130 246);
            border-color: rgb(59 130 246);
        }

        .fi-fo-tree-select .treeselect-container .treeselect-list__empty {
            color: rgb(107 114 128);
            font-size: 0.875rem;
            padding: 1rem;
            text-align: center;
        }

        .fi-fo-tree-select .treeselect-container .treeselect-input__edit {
            font-size: 0.875rem;
            color: rgb(17 24 39);
        }

        .fi-fo-tree-select .treeselect-container .treeselect-input__edit::placeholder {
            color: rgb(156 163 175);
        }

        /* 深色主题 */
        html.dark .fi-fo-tree-select .treeselect-container .treeselect-input,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-input {
            background-color: rgb(24 24 27);
            border-color: rgba(255, 255, 255, 0.1);
            color: rgb(243 244 246);
        }

        html.dark .fi-fo-tree-select .treeselect-container .treeselect-input:focus-within,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-input:focus-within {
            border-color: rgb(96 165 250);
            box-shadow: 0 0 0 1px rgb(96 165 250);
        }

        html.dark .fi-fo-tree-select .treeselect-container .treeselect-input__tags-element,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-input__tags-element {
            background-color: rgba(59, 130, 246, 0.2);
            color: rgb(147 197 253);
        }

        html.dark .fi-fo-tree-select .treeselect-container .treeselect-input__clear,
        html.dark .fi-fo-tree-select .treeselect-container .treeselect-input__arrow,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-input__clear,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-input__arrow {
            color: rgb(156 163 175);
        }

        html.dark .fi-fo-tree-select .treeselect-container .treeselect-input__clear:hover,
        html.dark .fi-fo-tree-select .treeselect-container .treeselect-input__arrow:hover,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-input__clear:hover,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-input__arrow:hover {
            color: rgb(229 231 235);
        }

        html.dark .fi-fo-tree-select .treeselect-container .treeselect-input__edit,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-input__edit {
            color: rgb(243 244 246);
        }

        html.dark .fi-fo-tree-select .treeselect-container .treeselect-input__edit::placeholder,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-input__edit::placeholder {
            color: rgb(107 114 128);
        }

        html.dark .fi-fo-tree-select .treeselect-container .treeselect-list,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-list {
            background-color: rgb(39 39 42);
            border-color: rgba(255, 255, 255, 0.1);
        }

        html.dark .fi-fo-tree-select .treeselect-container .treeselect-list__item,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-list__item {
            color: rgb(229 231 235);
        }

        html.dark .fi-fo-tree-select .treeselect-container .treeselect-list__item--focused,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-list__item--focused {
            background-color: rgb(63 63 70);
        }

        html.dark .fi-fo-tree-select .treeselect-container .treeselect-list__item--checked,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-list__item--checked {
            background-color: rgba(59, 130, 246, 0.2);
            color: rgb(147 197 253);
        }

        html.dark .fi-fo-tree-select .treeselect-container .treeselect-list__item-checkbox,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-list__item-checkbox {
            border-color: rgb(75 85 99);
            background-color: rgb(39 39 42);
        }

        html.dark .fi-fo-tree-select .treeselect-container .treeselect-list__item-checkbox--checked,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-list__item-checkbox--checked {
            background-color: rgb(59 130 246);
            border-color: rgb(59 130 246);
        }

        html.dark .fi-fo-tree-select .treeselect-container .treeselect-list__empty,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-list__empty {
            color: rgb(156 163 175);
        }

        /* 箭头图标样式 */
        .fi-fo-tree-select .treeselect-container .treeselect-list__item-icon {
            color: rgb(156 163 175);
            transition: transform 0.2s ease;
        }

        html.dark .fi-fo-tree-select .treeselect-container .treeselect-list__item-icon,
        .dark .fi-fo-tree-select .treeselect-container .treeselect-list__item-icon {
            color: rgb(107 114 128);
        }
    </style>
    </div>
</x-dynamic-component>
