<?php

namespace Geekstek\FilamentTree\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;

class TreeSelect extends Field
{
    protected string $view = 'geekstek-filament-tree::forms.tree-select';

    /**
     * 存储树形选项数据
     */
    protected array | Closure $options = [];

    /**
     * 是否单选模式
     */
    protected bool | Closure $isSingleSelect = false;

    /**
     * 是否显示标签
     */
    protected bool | Closure $showTags = true;

    /**
     * 是否可清除
     */
    protected bool | Closure $isClearable = true;

    /**
     * 是否可搜索
     */
    protected bool | Closure $isSearchable = true;

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
    public function getOptions(): array
    {
        return $this->evaluate($this->options);
    }

    /**
     * 设置为单选模式
     */
    public function single(bool | Closure $isSingle = true): static
    {
        $this->isSingleSelect = $isSingle;

        return $this;
    }

    /**
     * 获取是否单选模式
     */
    public function isSingleSelect(): bool
    {
        return $this->evaluate($this->isSingleSelect);
    }

    /**
     * 设置是否显示标签
     */
    public function showTags(bool | Closure $show = true): static
    {
        $this->showTags = $show;

        return $this;
    }

    /**
     * 获取是否显示标签
     */
    public function getShowTags(): bool
    {
        return $this->evaluate($this->showTags);
    }

    /**
     * 设置是否可清除
     */
    public function clearable(bool | Closure $clearable = true): static
    {
        $this->isClearable = $clearable;

        return $this;
    }

    /**
     * 获取是否可清除
     */
    public function isClearable(): bool
    {
        return $this->evaluate($this->isClearable);
    }

    /**
     * 设置是否可搜索
     */
    public function searchable(bool | Closure $searchable = true): static
    {
        $this->isSearchable = $searchable;

        return $this;
    }

    /**
     * 获取是否可搜索
     */
    public function isSearchable(): bool
    {
        return $this->evaluate($this->isSearchable);
    }
}
