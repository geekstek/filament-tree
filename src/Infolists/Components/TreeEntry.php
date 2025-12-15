<?php

namespace Geekstek\FilamentTree\Infolists\Components;

use Closure;
use Filament\Infolists\Components\Entry;

class TreeEntry extends Entry
{
    protected string $view = 'geekstek-filament-tree::infolists.tree-entry';

    /**
     * 存储树形选项数据
     */
    protected array | Closure $options = [];

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
     * 获取树形选项数据
     */
    public function getTreeOptions(): array
    {
        return $this->evaluate($this->options);
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
     * 获取默认展开状态
     */
    public function getDefaultExpanded(): bool
    {
        return $this->evaluate($this->defaultExpanded);
    }
}
