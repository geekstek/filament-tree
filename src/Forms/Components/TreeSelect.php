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
     * 存储哪些节点 ID 是禁用的（不可选）
     */
    protected array | Closure $disabledOptions = [];

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
     * 占位符文本
     */
    protected string | Closure | null $customPlaceholder = null;

    /**
     * 是否展开已选中的节点
     */
    protected bool | Closure $expandSelected = false;

    /**
     * 默认展开层级 (0 = 全部收起, 1 = 只展开第一层, 以此类推)
     */
    protected int | Closure $defaultOpenLevel = 0;

    /**
     * 下拉列表最大高度 (支持 px, vh, rem 等单位)
     */
    protected string | int | Closure | null $maxHeight = '300px';

    /**
     * 设置树形选项数据
     */
    public function options(array | Closure $options): static
    {
        $this->options = $options;

        return $this;
    }

    /**
     * 获取树形选项数据（转换为 TreeselectJS 格式）
     */
    public function getOptions(): array
    {
        $options = $this->evaluate($this->options);
        $disabledIds = $this->getDisabledOptions();

        return $this->transformOptions($options, $disabledIds);
    }

    /**
     * 转换选项格式为 TreeselectJS 所需格式
     * 从 id/label 转换为 value/name
     */
    protected function transformOptions(array $options, array $disabledIds): array
    {
        $result = [];

        foreach ($options as $option) {
            $transformed = [
                'value' => $option['id'] ?? $option['value'],
                'name' => $option['label'] ?? $option['name'] ?? '',
            ];

            // 检查是否禁用
            if (in_array($transformed['value'], $disabledIds)) {
                $transformed['disabled'] = true;
            }

            // 递归处理子节点
            if (! empty($option['children'])) {
                $transformed['children'] = $this->transformOptions($option['children'], $disabledIds);
            }

            $result[] = $transformed;
        }

        return $result;
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

    /**
     * 设置禁用的选项 ID 列表
     */
    public function disabledOptions(array | Closure $options): static
    {
        $this->disabledOptions = $options;

        return $this;
    }

    /**
     * 获取禁用的选项 ID 列表
     */
    public function getDisabledOptions(): array
    {
        return $this->evaluate($this->disabledOptions) ?? [];
    }

    /**
     * 设置占位符文本
     */
    public function placeholder(string | Closure | null $placeholder): static
    {
        $this->customPlaceholder = $placeholder;

        return $this;
    }

    /**
     * 获取占位符文本
     */
    public function getPlaceholder(): ?string
    {
        return $this->evaluate($this->customPlaceholder);
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
     * 获取是否展开已选中的节点
     */
    public function getExpandSelected(): bool
    {
        return $this->evaluate($this->expandSelected);
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
     * 获取默认展开层级
     */
    public function getDefaultOpenLevel(): int
    {
        return $this->evaluate($this->defaultOpenLevel);
    }

    /**
     * 设置下拉列表最大高度
     *
     * @param  string|int  $height  高度值，如 '300px', '50vh', '20rem' 或整数 (将自动添加 px)
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
