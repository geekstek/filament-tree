在 Filament v4 (Laravel 11 + Livewire 3 + Tailwind 4) 中开发此插件完全可行。

1.  **原生方法继承**：
    *   通过继承 `Filament\Forms\Components\Field`，你的组件会自动获得 `label()`, `helperText()`, `hint()`, `required()`, `visible()`, `live()` 等所有标准能力。
    *   关键在于 Blade 视图中必须使用 `<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">` 包裹，这样 Filament 会自动渲染 Label 和错误信息。
2.  **禁用与占位符**：
    *   `disabled()`: 可以通过 `$isDisabled()` 方法在视图中获取状态，并应用到 input 或 div 上。
    *   `placeholder()`: 可以通过 `$getPlaceholder()` 获取。
3.  **Wunderbaum 级联效果**：
    *   使用 **Alpine.js** 处理前端交互（点击父级 -> 递归查找子级 ID -> 更新 Livewire `entangle` 数据），这既能实现 Wunderbaum 的效果，又能保持 Filament 的原生性能（无需引入 jQuery）。

---

以下是为您定制的 **Filament v4 插件开发指南**。

# 📦 Geekstek/FilamentTree 插件开发指南

**目标**：创建一个 Filament v4 专用插件，包含下拉树和类似 [Wunderbaum Demo](https://mar10.github.io/wunderbaum/demo/#demo-select) 的级联选择器。

**包名**：`geekstek/filament-tree`

## 1. 目录结构与配置

建议目录结构：

```text
/geekstek-filament-tree
├── composer.json
├── src
│   ├── FilamentTreeServiceProvider.php
│   ├── Forms
│   │   └── Components
│   │       ├── TreeSelect.php       // 下拉树
│   │       └── Tree.php             // 级联树 (Wunderbaum 风格)
│   └── Infolists
│       └── Components
│           └── TreeEntry.php        // 详情页展示
└── resources
    └── views
        ├── forms
        │   ├── tree-select.blade.php
        │   ├── tree.blade.php
        │   └── tree-item.blade.php
        └── infolists
            ├── tree-entry.blade.php
            └── tree-entry-item.blade.php
```

### `composer.json`
定义命名空间映射：

```json
{
    "name": "geekstek/filament-tree",
    "description": "Tree form components for Filament v4",
    "require": {
        "php": "^8.2",
        "filament/filament": "^4.0" 
    },
    "autoload": {
        "psr-4": {
            "Geekstek\\FilamentTree\\": "src/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "Geekstek\\FilamentTree\\FilamentTreeServiceProvider"
            ]
        }
    }
}
```

### `src/FilamentTreeServiceProvider.php`

```php
<?php

namespace Geekstek\FilamentTree;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentTreeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('geekstek-filament-tree')
            ->hasViews('geekstek-filament-tree');
    }
}
```

---

## 2. Form 组件：Tree (级联平铺树)

实现类似 Wunderbaum/Dcat 的效果，支持 `disabled` 和 `live`。

### PHP 类: `src/Forms/Components/Tree.php`

```php
<?php

namespace Geekstek\FilamentTree\Forms\Components;

use Filament\Forms\Components\Field;
use Closure;

class Tree extends Field
{
    protected string $view = 'geekstek-filament-tree::forms.tree';
    
    // 存储树形数据
    protected array | Closure $options = [];
    
    // 存储哪些节点 ID 是禁用的（不可选）
    protected array | Closure $disabledOptions = [];

    public function options(array | Closure $options): static
    {
        $this->options = $options;
        return $this;
    }

    public function disabledOptions(array | Closure $options): static
    {
        $this->disabledOptions = $options;
        return $this;
    }

    public function getTreeData(): array
    {
        $options = $this->evaluate($this->options);
        $disabledIds = $this->evaluate($this->disabledOptions) ?? [];
        
        // 预处理数据：计算后代 ID，标记禁用状态
        return $this->processNodes($options, $disabledIds);
    }

    protected function processNodes(array $nodes, array $disabledIds): array
    {
        $result = [];
        foreach ($nodes as $node) {
            $children = $node['children'] ?? [];
            $processedChildren = $this->processNodes($children, $disabledIds);
            
            // 收集所有后代 ID (用于级联全选)
            $childIds = array_column($children, 'id');
            foreach ($processedChildren as $child) {
                if (!empty($child['_descendants'])) {
                    $childIds = array_merge($childIds, $child['_descendants']);
                }
            }
            
            // 判断当前节点是否被特定禁用
            $isNodeDisabled = in_array($node['id'], $disabledIds);

            $node['children'] = $processedChildren;
            $node['_descendants'] = $childIds; 
            $node['_disabled'] = $isNodeDisabled;
            
            $result[] = $node;
        }
        return $result;
    }
}
```

### Blade 视图: `resources/views/forms/tree.blade.php`

**重点功能实现**：
1.  **Wrapper**: 使用 `$getFieldWrapperView()` 自动支持 label, helperText, error 等。
2.  **Disabled**: 全局禁用 `$isDisabled()` 和单项禁用 `_disabled` 共同作用。
3.  **Live**: 使用 `$wire.$entangle` 自动支持 live/reactive。
4.  **Wunderbaum Effect**: `toggleNode` 函数实现级联逻辑。

```html
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            isGlobalDisabled: {{ $isDisabled() ? 'true' : 'false' }},

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
            }
        }"
        class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-900"
        :class="{ 'opacity-60 pointer-events-none bg-gray-50': isGlobalDisabled }"
    >
        {{-- 工具栏：仅在非禁用状态下显示交互 --}}
        @unless($isDisabled())
        <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex gap-3 text-xs">
            <button type="button" @click="toggleExpandAll(true)" class="text-primary-600 hover:text-primary-500 font-medium">[+] 全部展开</button>
            <span class="text-gray-300">|</span>
            <button type="button" @click="toggleExpandAll(false)" class="text-primary-600 hover:text-primary-500 font-medium">[-] 全部收起</button>
        </div>
        @endunless

        <div class="p-4 max-h-[500px] overflow-y-auto">
            <ul class="space-y-1">
                @foreach($getTreeData() as $node)
                    @include('geekstek-filament-tree::forms.tree-item', ['node' => $node])
                @endforeach
            </ul>
        </div>
    </div>
</x-dynamic-component>
```

### Blade 子视图: `resources/views/forms/tree-item.blade.php`

```html
@props(['node'])

<li 
    x-data="{ open: true }" 
    x-on:tree-expand-event.window="open = $event.detail.expand"
    class="relative"
>
    <div class="flex items-center gap-1.5 py-1 px-2 rounded transition duration-150 hover:bg-gray-50 dark:hover:bg-gray-800">
        {{-- 展开/收起按钮 --}}
        <div class="w-5 flex justify-center shrink-0">
            @if(!empty($node['children']))
                <button type="button" @click="open = !open" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <span x-show="!open" class="text-[10px]">▶</span>
                    <span x-show="open" class="text-[10px]">▼</span>
                </button>
            @endif
        </div>

        {{-- 复选框 --}}
        <label class="flex items-center gap-2 cursor-pointer flex-1 select-none">
            <input 
                type="checkbox" 
                :checked="isChecked({{ $node['id'] }})"
                :disabled="isGlobalDisabled || {{ $node['_disabled'] ? 'true' : 'false' }}"
                {{-- 核心点击事件 --}}
                @change="toggleNode({{ $node['id'] }}, @js($node['_descendants']), {{ $node['_disabled'] ? 'true' : 'false' }})"
                class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
            <span class="text-sm text-gray-700 dark:text-gray-200" :class="{ 'text-gray-400': {{ $node['_disabled'] ? 'true' : 'false' }} }">
                {{ $node['label'] }}
            </span>
        </label>
    </div>

    @if(!empty($node['children']))
        <ul x-show="open" x-collapse class="pl-6 ml-2.5 border-l border-dashed border-gray-200 dark:border-gray-700">
            @foreach($node['children'] as $child)
                @include('geekstek-filament-tree::forms.tree-item', ['node' => $child])
            @endforeach
        </ul>
    @endif
</li>
```

---

## 3. Form 组件：TreeSelect (下拉树)

封装 TreeselectJS，并适配 Filament 属性。

### PHP 类: `src/Forms/Components/TreeSelect.php`

```php
<?php

namespace Geekstek\FilamentTree\Forms\Components;

use Filament\Forms\Components\Field;
use Closure;

class TreeSelect extends Field
{
    protected string $view = 'geekstek-filament-tree::forms.tree-select';

    protected array | Closure $options = [];

    public function options(array | Closure $options): static
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->evaluate($this->options);
    }
}
```

### Blade 视图: `resources/views/forms/tree-select.blade.php`

```html
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-ignore
        ax-load
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}'),
            tree: null,
            init() {
                import('https://cdn.jsdelivr.net/npm/treeselectjs@0.11.0/dist/treeselectjs.mjs.js')
                    .then(({default: Treeselect}) => {
                        this.tree = new Treeselect({
                            parentHtmlContainer: this.$refs.treeContainer,
                            value: this.state,
                            options: @js($getOptions()),
                            isSingleSelect: false,
                            showTags: true,
                            disabled: {{ $isDisabled() ? 'true' : 'false' }}, // 支持 disabled
                            placeholder: '{{ $getPlaceholder() ?? '请选择...' }}', // 支持 placeholder
                            direction: 'auto',
                        });

                        this.tree.srcElement.addEventListener('input', (e) => {
                            this.state = e.detail;
                        });
                        
                        // 监听外部 disabled 变化 (例如 Livewire 更新)
                        this.$watch('{{ $isDisabled() ? "true" : "false" }}', (val) => {
                            // Treeselectjs 可能需要销毁重建或调用特定API，这里视库的支持而定
                            // 简单起见，可以切换 pointer-events-none 样式
                        });
                    });
            }
        }"
    >
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/treeselectjs@0.11.0/dist/treeselectjs.css" />
        
        <div 
            x-ref="treeContainer" 
            class="treeselect-wrapper text-sm"
            :class="{ 'opacity-60 pointer-events-none': {{ $isDisabled() ? 'true' : 'false' }} }"
        ></div>
        
        <style>
            .treeselect-wrapper .treeselect-input { 
                border-color: var(--gray-300, #d1d5db); 
                border-radius: 0.5rem; 
                background-color: transparent;
            }
            .dark .treeselect-wrapper .treeselect-input {
                border-color: var(--gray-700, #374151);
                color: white;
            }
        </style>
    </div>
</x-dynamic-component>
```

---

## 4. Infolist 组件：TreeEntry (展示)

### PHP 类: `src/Infolists/Components/TreeEntry.php`

```php
<?php

namespace Geekstek\FilamentTree\Infolists\Components;

use Filament\Infolists\Components\Entry;
use Closure;

class TreeEntry extends Entry
{
    protected string $view = 'geekstek-filament-tree::infolists.tree-entry';

    protected array | Closure $options = [];

    public function options(array | Closure $options): static
    {
        $this->options = $options;
        return $this;
    }

    public function getTreeOptions(): array
    {
        return $this->evaluate($this->options);
    }
}
```

### Blade 视图: `resources/views/infolists/tree-entry.blade.php`

```html
<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="text-sm border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-900/50">
        <ul class="space-y-1">
            @foreach($getTreeOptions() as $node)
                @include('geekstek-filament-tree::infolists.tree-entry-item', [
                    'node' => $node, 
                    'selectedIds' => $getState() ?? []
                ])
            @endforeach
        </ul>
    </div>
</x-dynamic-component>
```

---

## 5. 使用示例 (Resource)

在您的 Filament 项目中：

```php
use Geekstek\FilamentTree\Forms\Components\Tree;
use Geekstek\FilamentTree\Forms\Components\TreeSelect;

public static function form(Form $form): Form
{
    return $form->schema([
        // 1. Wunderbaum 风格级联树
        Tree::make('permissions')
            ->label('权限设置')
            ->helperText('点击父级可全选子级')
            ->options([
                ['id' => 1, 'label' => 'System', 'children' => [
                    ['id' => 2, 'label' => 'Users'],
                    ['id' => 3, 'label' => 'Roles'],
                ]]
            ])
            ->disabledOptions([3]) // 禁止选择 ID 为 3 的节点
            ->live() // 启用实时响应
            ->columnSpanFull(),

        // 2. 下拉树
        TreeSelect::make('category_id')
            ->label('分类选择')
            ->placeholder('请选择一个分类')
            ->options([/* ... */])
            ->required(), // 支持必填验证
    ]);
}
```

### 特性总结

1.  **Filament 4 原生支持**：
    *   继承了 `live()`, `disabled()`, `required()`, `helperText()`。
    *   视图包裹在 `<x-dynamic-component>` 中，自动适配布局。
2.  **级联选择 (Wunderbaum 风格)**：
    *   在 `Tree` 组件的 AlpineJS 逻辑中，实现了 `toggleNode` 方法，点击父级自动勾选所有子级。
3.  **禁用逻辑**：
    *   **全局禁用**: `disabled()` 方法会使整个组件变灰且不可点击。
    *   **单项禁用**: `disabledOptions([id, id])` 方法可禁止特定树节点的选择，同时父级全选时也会跳过这些节点（视逻辑而定，当前逻辑为界面变灰，点击无效）。