<?php

use Geekstek\FilamentTree\Forms\Components\Tree;
use Geekstek\FilamentTree\Forms\Components\TreeSelect;
use Geekstek\FilamentTree\Infolists\Components\TreeEntry;

it('can create tree component', function () {
    $tree = Tree::make('permissions');

    expect($tree)->toBeInstanceOf(Tree::class);
    expect($tree->getName())->toBe('permissions');
});

it('can set tree options', function () {
    $options = [
        ['id' => 1, 'label' => 'System', 'children' => [
            ['id' => 2, 'label' => 'Users'],
            ['id' => 3, 'label' => 'Roles'],
        ]],
    ];

    $tree = Tree::make('permissions')->options($options);
    $treeData = $tree->getTreeData();

    expect($treeData)->toBeArray();
    expect($treeData[0]['id'])->toBe(1);
    expect($treeData[0]['label'])->toBe('System');
    expect($treeData[0]['children'])->toHaveCount(2);
});

it('can process descendants correctly', function () {
    $options = [
        ['id' => 1, 'label' => 'Parent', 'children' => [
            ['id' => 2, 'label' => 'Child 1', 'children' => [
                ['id' => 4, 'label' => 'Grandchild'],
            ]],
            ['id' => 3, 'label' => 'Child 2'],
        ]],
    ];

    $tree = Tree::make('test')->options($options);
    $treeData = $tree->getTreeData();

    // 验证后代 ID 被正确计算
    expect($treeData[0]['_descendants'])->toContain(2, 3, 4);
});

it('can disable specific options', function () {
    $options = [
        ['id' => 1, 'label' => 'Node 1'],
        ['id' => 2, 'label' => 'Node 2'],
    ];

    $tree = Tree::make('test')
        ->options($options)
        ->disabledOptions([2]);

    $treeData = $tree->getTreeData();

    expect($treeData[0]['_disabled'])->toBeFalse();
    expect($treeData[1]['_disabled'])->toBeTrue();
});

it('can toggle toolbar visibility', function () {
    $tree = Tree::make('test');
    expect($tree->getShowToolbar())->toBeTrue();

    $tree->hideToolbar();
    expect($tree->getShowToolbar())->toBeFalse();

    $tree->showToolbar(true);
    expect($tree->getShowToolbar())->toBeTrue();
});

it('can set default expanded state', function () {
    $tree = Tree::make('test');
    expect($tree->getDefaultExpanded())->toBeTrue();

    $tree->collapsed();
    expect($tree->getDefaultExpanded())->toBeFalse();

    $tree->defaultExpanded(true);
    expect($tree->getDefaultExpanded())->toBeTrue();
});

it('can create tree select component', function () {
    $treeSelect = TreeSelect::make('category_id');

    expect($treeSelect)->toBeInstanceOf(TreeSelect::class);
    expect($treeSelect->getName())->toBe('category_id');
});

it('can configure tree select options', function () {
    $treeSelect = TreeSelect::make('category')
        ->single()
        ->clearable(false)
        ->searchable(false)
        ->showTags(false);

    expect($treeSelect->isSingleSelect())->toBeTrue();
    expect($treeSelect->isClearable())->toBeFalse();
    expect($treeSelect->isSearchable())->toBeFalse();
    expect($treeSelect->getShowTags())->toBeFalse();
});

it('can create tree entry component', function () {
    $entry = TreeEntry::make('permissions');

    expect($entry)->toBeInstanceOf(TreeEntry::class);
    expect($entry->getName())->toBe('permissions');
});

it('can configure tree entry options', function () {
    $options = [
        ['id' => 1, 'label' => 'Test'],
    ];

    $entry = TreeEntry::make('test')
        ->options($options)
        ->collapsed();

    expect($entry->getTreeOptions())->toBe($options);
    expect($entry->getDefaultExpanded())->toBeFalse();
});
