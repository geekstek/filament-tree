<?php

namespace Geekstek\FilamentTree\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

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
     * 递归处理节点，添加元数据
     */
    protected function processNodes(array $nodes, array $disabledIds): array
    {
        $result = [];

        foreach ($nodes as $node) {
            $children = $node['children'] ?? [];
            $processedChildren = $this->processNodes($children, $disabledIds);

            // 收集所有后代 ID (用于级联全选)
            $childIds = array_column($children, 'id');
            foreach ($processedChildren as $child) {
                if (! empty($child['_descendants'])) {
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
