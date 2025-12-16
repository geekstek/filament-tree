@php
    $statePath = $getStatePath();
    $isSearchable = $isSearchable();
    $searchPrompt = $getSearchPrompt();
    $noSearchResultsMessage = $getNoSearchResultsMessage();
    $searchDebounce = $getSearchDebounce();
    $maxHeight = $getMaxHeight();
    $showToolbar = $getShowToolbar();
    $isDisabled = $isDisabled();
    $leafOnly = $isLeafOnly();
    $expandSelected = $getExpandSelected();
    $jsTreeData = $getJsTreeData();
    $leafNodeIds = $getLeafNodeIds();

    // 提取树结构（不包含选中状态）用于生成稳定的 wire:key
    // 这样当只有选中状态变化时，不会触发组件完全替换
    $extractStructure = function($nodes) use (&$extractStructure) {
        return array_map(function($node) use (&$extractStructure) {
            $structure = [
                'id' => $node['id'],
                'text' => $node['text'] ?? '',
            ];
            if (!empty($node['children'])) {
                $structure['children'] = $extractStructure($node['children']);
            }
            return $structure;
        }, $nodes);
    };
    
    // 结构哈希 - 只在树结构变化时改变（节点增删）
    $structureHash = md5(json_encode($extractStructure($jsTreeData)));
    $uniqueId = 'jstree-' . $structureHash;
    
    // 数据哈希 - 包含完整数据（含选中状态），用于检测是否需要更新
    $dataHash = md5(json_encode($jsTreeData));
    
    // 将配置编码为 base64 避免任何引号问题
    $configJson = json_encode([
        'containerId' => $uniqueId,
        'statePath' => $statePath,
        'isDisabled' => $isDisabled,
        'leafOnly' => $leafOnly,
        'expandSelected' => $expandSelected,
        'isSearchable' => $isSearchable,
        'searchDebounce' => $searchDebounce,
        'treeData' => $jsTreeData,
        'leafNodeIds' => $leafNodeIds,
        'structureHash' => $structureHash,  // 用于 wire:key
        'dataHash' => $dataHash,             // 用于检测数据变化
    ]);
    $configBase64 = base64_encode($configJson);
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <style>
        .fi-jstree-container {
            border-radius: 0.5rem;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            background-color: #ffffff;
        }
        html.dark .fi-jstree-container, .dark .fi-jstree-container {
            border-color: rgba(255, 255, 255, 0.1);
            background-color: #18181b;
        }
        .fi-jstree-container.fi-disabled {
            opacity: 0.5;
            pointer-events: none;
        }
        .fi-jstree-toolbar {
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
        html.dark .fi-jstree-toolbar, .dark .fi-jstree-toolbar {
            border-color: rgba(255, 255, 255, 0.1);
            background-color: #27272a;
        }
        .fi-jstree-toolbar-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            color: #2563eb;
            transition: color 0.15s;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            font: inherit;
        }
        .fi-jstree-toolbar-btn:hover { color: #1d4ed8; }
        html.dark .fi-jstree-toolbar-btn, .dark .fi-jstree-toolbar-btn { color: #60a5fa; }
        html.dark .fi-jstree-toolbar-btn:hover, .dark .fi-jstree-toolbar-btn:hover { color: #93c5fd; }
        .fi-jstree-toolbar-icon { width: 1rem; height: 1rem; }
        .fi-jstree-toolbar-separator { color: #d1d5db; }
        html.dark .fi-jstree-toolbar-separator, .dark .fi-jstree-toolbar-separator { color: #3f3f46; }
        .fi-jstree-content { padding: 0.5rem; overflow-y: auto; }
        .fi-jstree-empty {
            padding: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
            font-style: italic;
            color: #6b7280;
        }
        html.dark .fi-jstree-empty, .dark .fi-jstree-empty { color: #a1a1aa; }
        .fi-jstree-content .jstree-default .jstree-node,
        .fi-jstree-content .jstree-default .jstree-icon { background-image: none; }
        .fi-jstree-content .jstree-default .jstree-anchor {
            color: #374151;
            font-size: 0.875rem;
            line-height: 1.5;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }
        html.dark .fi-jstree-content .jstree-default .jstree-anchor,
        .dark .fi-jstree-content .jstree-default .jstree-anchor { color: #e4e4e7; }
        .fi-jstree-content .jstree-default .jstree-anchor:hover {
            background-color: #f3f4f6;
            box-shadow: none;
        }
        html.dark .fi-jstree-content .jstree-default .jstree-anchor:hover,
        .dark .fi-jstree-content .jstree-default .jstree-anchor:hover { background-color: #27272a; }
        .fi-jstree-content .jstree-default .jstree-clicked {
            background-color: #eff6ff !important;
            color: #1d4ed8;
            box-shadow: none;
        }
        html.dark .fi-jstree-content .jstree-default .jstree-clicked,
        .dark .fi-jstree-content .jstree-default .jstree-clicked {
            background-color: rgba(59, 130, 246, 0.15) !important;
            color: #93c5fd;
        }
        .fi-jstree-content .jstree-default .jstree-checkbox {
            background-image: none;
            width: 1rem;
            height: 1rem;
            margin-right: 0.25rem;
            position: relative;
            border: 2px solid #d1d5db;
            border-radius: 0.25rem;
            background-color: #ffffff;
        }
        html.dark .fi-jstree-content .jstree-default .jstree-checkbox,
        .dark .fi-jstree-content .jstree-default .jstree-checkbox {
            border-color: #52525b;
            background-color: #27272a;
        }
        .fi-jstree-content .jstree-default .jstree-checked > .jstree-checkbox {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        .fi-jstree-content .jstree-default .jstree-checked > .jstree-checkbox::after {
            content: '';
            position: absolute;
            left: 3px;
            top: 0px;
            width: 5px;
            height: 9px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }
        /* Undetermined 状态 - 部分子节点选中时显示横线 */
        /* 注意：jstree-undetermined 类是应用在 checkbox <i> 元素本身 */
        .fi-jstree-content .jstree-default .jstree-checkbox.jstree-undetermined {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
        }
        .fi-jstree-content .jstree-default .jstree-checkbox.jstree-undetermined::after {
            content: '' !important;
            position: absolute !important;
            left: 2px !important;
            top: 5px !important;
            width: 8px !important;
            height: 2px !important;
            background-color: white !important;
            border: none !important;
            transform: none !important;
        }
        .fi-jstree-content .jstree-default .jstree-icon.jstree-ocl {
            background-image: none;
            width: 1.25rem;
            height: 1.25rem;
            position: relative;
        }
        .fi-jstree-content .jstree-default .jstree-icon.jstree-ocl::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            width: 0;
            height: 0;
            border-left: 5px solid #6b7280;
            border-top: 4px solid transparent;
            border-bottom: 4px solid transparent;
            transition: transform 0.15s;
        }
        html.dark .fi-jstree-content .jstree-default .jstree-icon.jstree-ocl::before,
        .dark .fi-jstree-content .jstree-default .jstree-icon.jstree-ocl::before { border-left-color: #a1a1aa; }
        .fi-jstree-content .jstree-default .jstree-open > .jstree-icon.jstree-ocl::before {
            transform: translate(-50%, -50%) rotate(90deg);
        }
        .fi-jstree-content .jstree-default .jstree-disabled {
            color: #9ca3af !important;
            text-decoration: line-through;
        }
        html.dark .fi-jstree-content .jstree-default .jstree-disabled,
        .dark .fi-jstree-content .jstree-default .jstree-disabled { color: #71717a !important; }
        .fi-jstree-search-wrapper {
            padding: 0.5rem 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }
        html.dark .fi-jstree-search-wrapper, .dark .fi-jstree-search-wrapper {
            border-color: rgba(255, 255, 255, 0.1);
        }
        .fi-jstree-search-input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            background-color: #ffffff;
            color: #374151;
        }
        html.dark .fi-jstree-search-input, .dark .fi-jstree-search-input {
            border-color: #52525b;
            background-color: #27272a;
            color: #e4e4e7;
        }
        .fi-jstree-search-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 1px #2563eb;
        }
        .fi-jstree-no-results {
            padding: 1rem;
            text-align: center;
            font-size: 0.875rem;
            color: #6b7280;
            display: none;
        }
        html.dark .fi-jstree-no-results, .dark .fi-jstree-no-results { color: #a1a1aa; }
    </style>

    {{-- 配置数据 (Base64 编码) --}}
    {{-- structureHash: 只在树结构变化时改变，用于判断是否需要重新初始化 --}}
    <input type="hidden" id="{{ $uniqueId }}-config" data-structure-hash="{{ $structureHash }}" value="{{ $configBase64 }}" />

    {{-- Alpine.js 组件 - 使用 dataHash 作为 key 来触发重新初始化 --}}
    <div
        x-data="{
            tree: null,
            cfg: null,
            leafSet: null,
            initialized: false,
            currentHash: null,
            searchTimeout: null,
            
            init() {
                var self = this;
                self.loadConfig();
            },
            
            loadConfig() {
                var self = this;
                var configEl = document.getElementById('{{ $uniqueId }}-config');
                if (!configEl) { 
                    console.log('[jsTree] 找不到配置元素，等待 DOM 更新...');
                    return; 
                }
                
                var newHash = configEl.dataset.structureHash;
                
                // 检查树容器是否存在且 jsTree 实例是否还在
                var containerId = self.cfg ? self.cfg.containerId : '{{ $uniqueId }}';
                var containerEl = document.getElementById(containerId);
                var treeStillExists = containerEl && 
                    typeof jQuery !== 'undefined' && 
                    jQuery(containerEl).hasClass('jstree');
                
                // 如果哈希相同且树仍然存在于 DOM 中，跳过初始化
                if (self.currentHash === newHash && self.initialized && treeStillExists) {
                    console.log('[jsTree] 数据未变化且树存在，跳过初始化');
                    return;
                }
                
                // 如果树不存在了（Livewire 重新渲染导致 DOM 被替换），重置状态
                if (!treeStillExists) {
                    self.initialized = false;
                    self.tree = null;
                }
                
                try {
                    self.cfg = JSON.parse(atob(configEl.value));
                    self.leafSet = new Set((self.cfg.leafNodeIds || []).map(function(x) { return String(x); }));
                    self.currentHash = newHash;
                } catch(e) { 
                    console.error('[jsTree] 配置解析失败:', e); 
                    return; 
                }
                
                if (self.cfg.treeData.length === 0) { 
                    self.destroyTree();
                    return; 
                }
                
                self.loadDependencies();
            },
            
            destroyTree() {
                var self = this;
                if (self.tree) {
                    try {
                        jQuery('#' + self.cfg.containerId).jstree('destroy');
                    } catch(e) {}
                    self.tree = null;
                    self.initialized = false;
                }
            },
            
            loadDependencies() {
                var self = this;
                var jqUrl = 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js';
                var jsTreeCss = 'https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.17/themes/default/style.min.css';
                var jsTreeJs = 'https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.17/jstree.min.js';
                
                self.loadCss(jsTreeCss);
                
                if (typeof jQuery === 'undefined') {
                    // 检查是否有其他组件正在加载 jQuery
                    var jqScriptExists = Array.from(document.scripts).some(function(s) { 
                        return s.src && s.src.indexOf('jquery') > -1; 
                    });
                    
                    if (jqScriptExists) {
                        self.waitForJQuery(function() {
                            self.loadJsTreeLib(jsTreeJs);
                        });
                    } else {
                        self.loadJs(jqUrl, function() {
                            self.waitForJQuery(function() {
                                self.loadJsTreeLib(jsTreeJs);
                            });
                        });
                    }
                } else {
                    self.loadJsTreeLib(jsTreeJs);
                }
            },
            
            loadCss(url) {
                var exists = Array.from(document.styleSheets).some(function(s) { 
                    return s.href && s.href.indexOf('jstree') > -1; 
                });
                if (exists) return;
                var l = document.createElement('link');
                l.rel = 'stylesheet';
                l.href = url;
                document.head.appendChild(l);
            },
            
            loadJs(url, cb) {
                var exists = Array.from(document.scripts).some(function(s) { return s.src === url; });
                if (!exists) {
                    var s = document.createElement('script');
                    s.src = url;
                    s.onload = cb;
                    document.head.appendChild(s);
                } else {
                    // 脚本已存在，等待它加载完成
                    cb();
                }
            },
            
            waitForJQuery(cb) {
                var self = this;
                if (typeof jQuery !== 'undefined') {
                    cb();
                } else {
                    // jQuery 还没加载完，等待
                    setTimeout(function() { self.waitForJQuery(cb); }, 50);
                }
            },
            
            waitForJsTree(cb) {
                var self = this;
                if (typeof jQuery !== 'undefined' && typeof jQuery.fn.jstree !== 'undefined') {
                    cb();
                } else {
                    // jsTree 还没加载完，等待
                    setTimeout(function() { self.waitForJsTree(cb); }, 50);
                }
            },
            
            loadJsTreeLib(url) {
                var self = this;
                // 先确保 jQuery 已加载
                self.waitForJQuery(function() {
                    if (typeof jQuery.fn.jstree === 'undefined') {
                        self.loadJs(url, function() {
                            // 等待 jsTree 真正可用
                            self.waitForJsTree(function() {
                                self.initJsTree();
                            });
                        });
                    } else {
                        self.initJsTree();
                    }
                });
            },
            
            initJsTree() {
                var self = this;
                
                var $ = jQuery;
                var $container = $('#' + self.cfg.containerId);
                
                if ($container.length === 0) { 
                    console.error('[jsTree] 容器不存在:', self.cfg.containerId); 
                    return; 
                }
                
                // 如果已有 jsTree 实例，先销毁
                if ($container.hasClass('jstree')) {
                    $container.jstree('destroy');
                }
                
                var plugins = ['checkbox', 'wholerow'];
                if (self.cfg.isSearchable) plugins.push('search');
                
                $container.jstree({
                    core: { 
                        data: self.cfg.treeData, 
                        themes: { dots: false, icons: true }, 
                        multiple: true, 
                        animation: 150 
                    },
                    checkbox: { 
                        three_state: true, 
                        // up: 所有子节点选中时父节点自动选中
                        // down: 选中父节点时所有子节点自动选中
                        // undetermined: 部分子节点选中时父节点显示横线标记
                        cascade: 'up+down+undetermined', 
                        tie_selection: false,  // 分离选中和勾选状态
                        whole_node: false 
                    },
                    search: {
                        show_only_matches: true,
                        show_only_matches_children: true
                    },
                    plugins: plugins
                });
                
                self.tree = $container.jstree(true);
                self.initialized = true;
                
                // ready 事件 - 直接设置内部状态（最快方法）
                $container.on('ready.jstree', function() {
                    // 从 treeData 中提取所有 selected=true 的节点 ID
                    var selectedIds = [];
                    function collectSelected(nodes) {
                        nodes.forEach(function(node) {
                            if (node.state && node.state.selected) {
                                selectedIds.push(node.id);
                            }
                            if (node.children) {
                                collectSelected(node.children);
                            }
                        });
                    }
                    collectSelected(self.cfg.treeData);
                                        
                    if (selectedIds.length > 0) {
                        var model = self.tree._model.data;
                        var startTime = performance.now();
                        
                        // 关键: 获取 checkbox 插件的内部选中数组
                        var checkboxSelected = self.tree._data.checkbox.selected;
                        
                        // 1. 直接设置 checked 状态 + 更新内部追踪数组
                        selectedIds.forEach(function(id) {
                            if (model[id]) {
                                model[id].state.checked = true;
                                // 同时更新 checkbox 插件的内部数组（get_checked 依赖这个）
                                if (checkboxSelected.indexOf(id) === -1) {
                                    checkboxSelected.push(id);
                                }
                            }
                        });
                        
                        // 2. 从下往上计算父节点状态
                        var parentsToCheck = new Set();
                        selectedIds.forEach(function(id) {
                            if (model[id]) {
                                var parentId = model[id].parent;
                                while (parentId && parentId !== '#') {
                                    parentsToCheck.add(parentId);
                                    parentId = model[parentId] ? model[parentId].parent : null;
                                }
                            }
                        });
                        
                        // 按深度排序（从深到浅处理）
                        var sortedParents = Array.from(parentsToCheck).sort(function(a, b) {
                            var depthA = 0, depthB = 0;
                            var pa = a, pb = b;
                            while (model[pa] && model[pa].parent !== '#') { depthA++; pa = model[pa].parent; }
                            while (model[pb] && model[pb].parent !== '#') { depthB++; pb = model[pb].parent; }
                            return depthB - depthA;
                        });
                        
                        // 检查每个父节点：如果所有子节点都勾选，则父节点也勾选
                        sortedParents.forEach(function(parentId) {
                            var parent = model[parentId];
                            if (parent && parent.children && parent.children.length > 0) {
                                var allChildrenChecked = parent.children.every(function(childId) {
                                    return model[childId] && model[childId].state.checked;
                                });
                                if (allChildrenChecked) {
                                    parent.state.checked = true;
                                    // 同时更新内部数组
                                    if (checkboxSelected.indexOf(parentId) === -1) {
                                        checkboxSelected.push(parentId);
                                    }
                                }
                            }
                        });
                        
                        // 3. 计算 undetermined 状态
                        self.tree._undetermined();
                        
                        // 4. 重绘树
                        self.tree.redraw(true);
                        
                        var endTime = performance.now();
                        console.log('[jsTree] 勾选完成，耗时:', Math.round(endTime - startTime), 'ms');
                        console.log('[jsTree] checkbox.selected 数组长度:', checkboxSelected.length);
                    }
                });
                
                // checkbox 变化事件 - 同步到 Livewire (不触发重新渲染)
                $container.on('check_node.jstree uncheck_node.jstree', function(e, data) {
                    if (self.cfg.isDisabled) return;
                    
                    var ids = self.tree.get_checked();
                    if (self.cfg.leafOnly) {
                        ids = ids.filter(function(x) { return self.leafSet.has(String(x)); });
                    }
                    
                    // 更新 Livewire 状态，允许重新渲染以支持 live() 
                    // 注意：loadConfig() 会检测树是否需要重新初始化
                    if (typeof $wire !== 'undefined') {
                        $wire.$set(self.cfg.statePath, ids);
                    }
                });
            },
            
            expandAll() {
                if (this.tree) this.tree.open_all();
            },
            
            collapseAll() {
                if (this.tree) this.tree.close_all();
            },
            
            selectAll() {
                if (this.tree) this.tree.check_all();
            },
            
            deselectAll() {
                if (this.tree) this.tree.uncheck_all();
            },
            
            doSearch(query) {
                var self = this;
                if (!self.tree || !self.cfg.isSearchable) return;
                
                clearTimeout(self.searchTimeout);
                self.searchTimeout = setTimeout(function() {
                    self.tree.search(query);
                    
                    var noResultsEl = document.getElementById(self.cfg.containerId + '-no-results');
                    if (noResultsEl) {
                        var hasResults = jQuery('#' + self.cfg.containerId + ' .jstree-search').length > 0;
                        noResultsEl.style.display = (query && !hasResults) ? 'block' : 'none';
                    }
                }, self.cfg.searchDebounce || 300);
            }
        }"
        x-init="init()"
        wire:key="jstree-{{ $structureHash }}"
        class="fi-jstree-wrapper"
    >
        <div class="fi-jstree-container {{ $isDisabled ? 'fi-disabled' : '' }}">
            @if($isSearchable)
                <div class="fi-jstree-search-wrapper">
                    <input 
                        type="text" 
                        class="fi-jstree-search-input"
                        placeholder="{{ $searchPrompt }}"
                        x-on:input="doSearch($event.target.value)"
                    />
                </div>
            @endif
            
            @if($showToolbar && !$isDisabled)
                <div class="fi-jstree-toolbar">
                    <button type="button" x-on:click="expandAll()" class="fi-jstree-toolbar-btn">
                        <span>▼</span>
                        <span>{{ __('geekstek-filament-tree::filament-tree.toolbar.expand_all') }}</span>
                    </button>
                    <span class="fi-jstree-toolbar-separator">|</span>
                    <button type="button" x-on:click="collapseAll()" class="fi-jstree-toolbar-btn">
                        <span>▶</span>
                        <span>{{ __('geekstek-filament-tree::filament-tree.toolbar.collapse_all') }}</span>
                    </button>
                    <span class="fi-jstree-toolbar-separator">|</span>
                    <button type="button" x-on:click="selectAll()" class="fi-jstree-toolbar-btn">
                        <span>✓</span>
                        <span>{{ __('geekstek-filament-tree::filament-tree.toolbar.select_all') }}</span>
                    </button>
                    <span class="fi-jstree-toolbar-separator">|</span>
                    <button type="button" x-on:click="deselectAll()" class="fi-jstree-toolbar-btn">
                        <span>✗</span>
                        <span>{{ __('geekstek-filament-tree::filament-tree.toolbar.deselect_all') }}</span>
                    </button>
                </div>
            @endif

            {{-- wire:ignore 防止 Livewire 在选中状态变化时破坏 jsTree 的 DOM --}}
            <div class="fi-jstree-content" wire:ignore @if($maxHeight) style="max-height: {{ $maxHeight }}" @endif>
                @if(empty($jsTreeData))
                    <p class="fi-jstree-empty">
                        {{ __('geekstek-filament-tree::filament-tree.empty.no_data') }}
                    </p>
                @else
                    <div id="{{ $uniqueId }}"></div>
                    @if($isSearchable)
                        <div id="{{ $uniqueId }}-no-results" class="fi-jstree-no-results">
                            {{ $noSearchResultsMessage }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-dynamic-component>
