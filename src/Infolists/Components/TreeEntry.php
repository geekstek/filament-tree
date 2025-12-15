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
     * 最大高度 (支持 px, vh, rem 等单位)
     */
    protected string | int | Closure | null $maxHeight = '400px';

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
}
