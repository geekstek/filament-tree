<?php

namespace Geekstek\FilamentTree\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;
use Illuminate\Contracts\Support\Htmlable;

class Tree extends Field
{
    protected string $view = 'geekstek-filament-tree::forms.tree';

    /**
     * 存储树形数据
     */
    protected array | Closure $options = [];

    /**
     * 存储哪些节点 ID 是禁用的（不可选）
     */
    protected array | Closure $disabledOptions = [];

    /**
     * 是否显示展开/收起工具栏
     */
    protected bool | Closure $showToolbar = true;

    /**
     * 默认展开状态
     */
    protected bool | Closure $defaultExpanded = true;

    /**
     * 是否展开已选中的节点
     */
    protected bool | Closure $expandSelected = false;

    /**
     * 默认展开层级 (0 = 全部收起, 1 = 只展开第一层, 以此类推)
     */
    protected int | Closure | null $defaultOpenLevel = null;

    /**
     * 最大高度 (支持 px, vh, rem 等单位)
     */
    protected string | int | Closure | null $maxHeight = '400px';

    /**
     * 是否只保存叶子节点的值
     */
    protected bool | Closure $leafOnly = false;

    /**
     * 是否可搜索
     */
    protected bool | Closure $isSearchable = false;

    /**
     * 搜索框占位符
     */
    protected string | Htmlable | Closure | null $searchPrompt = null;

    /**
     * 无搜索结果消息
     */
    protected string | Htmlable | Closure | null $noSearchResultsMessage = null;

    /**
     * 搜索防抖延迟 (毫秒)
     */
    protected int | Closure $searchDebounce = 300;

    /**
     * 设置树形选项数据
     */
    public function options(array | Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * 设置禁用的选项 ID 列表
     */
    public function disabledOptions(array | Closure $options): static
    {
        $this->disabledOptions = $options;

        return $this;
    }

    /**
     * 是否显示工具栏
     */
    public function showToolbar(bool | Closure $show = true): static
    {
        $this->showToolbar = $show;

        return $this;
    }

    /**
     * 隐藏工具栏
     */
    public function hideToolbar(): static
    {
        $this->showToolbar = false;

        return $this;
    }

    /**
     * 设置默认展开状态
     */
    public function defaultExpanded(bool | Closure $expanded = true): static
    {
        $this->defaultExpanded = $expanded;

        return $this;
    }

    /**
     * 默认收起所有节点
     */
    public function collapsed(): static
    {
        $this->defaultExpanded = false;

        return $this;
    }

    /**
     * 设置是否展开已选中的节点
     */
    public function expandSelected(bool | Closure $expand = true): static
    {
        $this->expandSelected = $expand;

        return $this;
    }

    /**
     * 设置默认展开层级
     *
     * @param  int  $level  0 = 全部收起, 1 = 展开第一层, 2 = 展开前两层, 以此类推
     */
    public function defaultOpenLevel(int | Closure $level): static
    {
        $this->defaultOpenLevel = $level;

        return $this;
    }

    /**
     * 获取处理后的树形数据
     */
    public function getTreeData(): array
    {
        $options = $this->evaluate($this->options);
        $disabledIds = $this->evaluate($this->disabledOptions) ?? [];

        // 预处理数据：计算后代 ID，标记禁用状态
        return $this->processNodes($options, $disabledIds);
    }

    /**
     * 获取是否显示工具栏
     */
    public function getShowToolbar(): bool
    {
        return $this->evaluate($this->showToolbar);
    }

    /**
     * 获取默认展开状态
     */
    public function getDefaultExpanded(): bool
    {
        return $this->evaluate($this->defaultExpanded);
    }

    /**
     * 获取是否展开已选中的节点
     */
    public function getExpandSelected(): bool
    {
        return $this->evaluate($this->expandSelected);
    }

    /**
     * 获取默认展开层级
     */
    public function getDefaultOpenLevel(): ?int
    {
        return $this->evaluate($this->defaultOpenLevel);
    }

    /**
     * 设置是否只保存叶子节点的值
     * 当启用时，选中父节点只会保存其下所有叶子节点的 ID
     */
    public function leafOnly(bool | Closure $leafOnly = true): static
    {
        $this->leafOnly = $leafOnly;

        return $this;
    }

    /**
     * 获取是否只保存叶子节点的值
     */
    public function isLeafOnly(): bool
    {
        return $this->evaluate($this->leafOnly);
    }

    /**
     * 设置最大高度
     *
     * @param  string|int  $height  高度值，如 '400px', '50vh', '20rem' 或整数 (将自动添加 px)
     */
    public function maxHeight(string | int | Closure | null $height): static
    {
        $this->maxHeight = $height;

        return $this;
    }

    /**
     * 获取最大高度 (带单位的字符串)
     */
    public function getMaxHeight(): ?string
    {
        $height = $this->evaluate($this->maxHeight);

        if ($height === null) {
            return null;
        }

        // 如果是整数，添加 px 单位
        if (is_int($height)) {
            return $height . 'px';
        }

        return $height;
    }

    /**
     * 设置是否可搜索
     */
    public function searchable(bool | Closure $condition = true): static
    {
        $this->isSearchable = $condition;

        return $this;
    }

    /**
     * 获取是否可搜索
     */
    public function isSearchable(): bool
    {
        return (bool) $this->evaluate($this->isSearchable);
    }

    /**
     * 设置搜索框占位符
     */
    public function searchPrompt(string | Htmlable | Closure | null $prompt): static
    {
        $this->searchPrompt = $prompt;

        return $this;
    }

    /**
     * 获取搜索框占位符
     */
    public function getSearchPrompt(): string | Htmlable
    {
        return $this->evaluate($this->searchPrompt) ?? __('geekstek-filament-tree::filament-tree.search.placeholder');
    }

    /**
     * 设置无搜索结果消息
     */
    public function noSearchResultsMessage(string | Htmlable | Closure | null $message): static
    {
        $this->noSearchResultsMessage = $message;

        return $this;
    }

    /**
     * 获取无搜索结果消息
     */
    public function getNoSearchResultsMessage(): string | Htmlable
    {
        return $this->evaluate($this->noSearchResultsMessage) ?? __('geekstek-filament-tree::filament-tree.search.no_results');
    }

    /**
     * 设置搜索防抖延迟
     */
    public function searchDebounce(int | Closure $debounce): static
    {
        $this->searchDebounce = $debounce;

        return $this;
    }

    /**
     * 获取搜索防抖延迟
     */
    public function getSearchDebounce(): int
    {
        return (int) $this->evaluate($this->searchDebounce);
    }

    /**
     * 递归处理节点，添加元数据
     */
    protected function processNodes(array $nodes, array $disabledIds): array
    {
        $result = [];

        foreach ($nodes as $node) {
            $children = $node['children'] ?? [];
            $processedChildren = $this->processNodes($children, $disabledIds);

            // 收集所有后代 ID (用于判断选中状态)
            $allDescendantIds = [];
            // 收集可选择的后代 ID (用于级联选中，排除禁用项)
            $selectableDescendantIds = [];
            // 收集可选择的叶子后代 ID (用于 leafOnly 模式)
            $selectableLeafDescendantIds = [];

            foreach ($children as $index => $child) {
                $childId = $child['id'];
                $allDescendantIds[] = $childId;

                // 只有非禁用的才加入可选择列表
                if (! in_array($childId, $disabledIds)) {
                    $selectableDescendantIds[] = $childId;

                    // 如果子节点是叶子节点（没有children），加入叶子列表
                    $childHasChildren = ! empty($processedChildren[$index]['children']);
                    if (! $childHasChildren) {
                        $selectableLeafDescendantIds[] = $childId;
                    }
                }
            }

            // 递归收集子节点的后代
            foreach ($processedChildren as $child) {
                if (! empty($child['_descendants'])) {
                    $allDescendantIds = array_merge($allDescendantIds, $child['_descendants']);
                }
                if (! empty($child['_selectableDescendants'])) {
                    $selectableDescendantIds = array_merge($selectableDescendantIds, $child['_selectableDescendants']);
                }
                if (! empty($child['_selectableLeafDescendants'])) {
                    $selectableLeafDescendantIds = array_merge($selectableLeafDescendantIds, $child['_selectableLeafDescendants']);
                }
            }

            // 判断当前节点是否被特定禁用
            $isNodeDisabled = in_array($node['id'], $disabledIds);

            $node['children'] = $processedChildren;
            $node['_descendants'] = $allDescendantIds;
            $node['_selectableDescendants'] = $selectableDescendantIds;
            $node['_selectableLeafDescendants'] = $selectableLeafDescendantIds;
            $node['_disabled'] = $isNodeDisabled;

            $result[] = $node;
        }

        return $result;
    }
}
