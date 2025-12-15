# Geekstek Filament Tree

A Filament v4 plugin that provides tree form components including a cascading tree selector (Wunderbaum style) and a dropdown tree select.

## Requirements

- PHP 8.2+
- Filament 4.0+
- Laravel 11+

## Installation

```bash
composer require geekstek/filament-tree
```

The package will auto-register via Laravel's package discovery.

## Components

### 1. Tree (Cascading Tree Selector)

A flat tree component with checkbox selection and cascade behavior - when you select a parent, all children are automatically selected.

```php
use Geekstek\FilamentTree\Forms\Components\Tree;

Tree::make('permissions')
    ->label('Permissions')
    ->helperText('Click on a parent to select all children')
    ->options([
        [
            'id' => 1,
            'label' => 'System',
            'children' => [
                ['id' => 2, 'label' => 'Users'],
                ['id' => 3, 'label' => 'Roles'],
                ['id' => 4, 'label' => 'Settings'],
            ],
        ],
        [
            'id' => 5,
            'label' => 'Content',
            'children' => [
                ['id' => 6, 'label' => 'Posts'],
                ['id' => 7, 'label' => 'Pages'],
            ],
        ],
    ])
    ->disabledOptions([3]) // Disable specific node IDs
    ->live() // Enable reactive updates
    ->columnSpanFull()
```

#### Available Methods

| Method | Description |
|--------|-------------|
| `options(array\|Closure $options)` | Set the tree data structure |
| `disabledOptions(array\|Closure $ids)` | Disable specific node IDs |
| `showToolbar(bool\|Closure $show)` | Show/hide the expand/collapse toolbar |
| `hideToolbar()` | Hide the toolbar |
| `defaultExpanded(bool\|Closure $expanded)` | Set default expanded state |
| `collapsed()` | Start with all nodes collapsed |

### 2. TreeSelect (Dropdown Tree)

A dropdown component with tree structure support, powered by TreeselectJS.

```php
use Geekstek\FilamentTree\Forms\Components\TreeSelect;

TreeSelect::make('category_id')
    ->label('Category')
    ->placeholder('Select a category...')
    ->options([
        [
            'value' => 1,
            'name' => 'Electronics',
            'children' => [
                ['value' => 2, 'name' => 'Phones'],
                ['value' => 3, 'name' => 'Laptops'],
            ],
        ],
        [
            'value' => 4,
            'name' => 'Clothing',
            'children' => [
                ['value' => 5, 'name' => 'Men'],
                ['value' => 6, 'name' => 'Women'],
            ],
        ],
    ])
    ->required()
```

#### Available Methods

| Method | Description |
|--------|-------------|
| `options(array\|Closure $options)` | Set the tree data (uses `value` and `name` keys) |
| `single(bool\|Closure $isSingle)` | Enable single select mode |
| `showTags(bool\|Closure $show)` | Show selected items as tags |
| `clearable(bool\|Closure $clearable)` | Allow clearing selection |
| `searchable(bool\|Closure $searchable)` | Enable search functionality |

### 3. TreeEntry (Infolist Display)

Display tree data in infoolists with selected items highlighted.

```php
use Geekstek\FilamentTree\Infolists\Components\TreeEntry;

TreeEntry::make('permissions')
    ->label('Assigned Permissions')
    ->options([
        [
            'id' => 1,
            'label' => 'System',
            'children' => [
                ['id' => 2, 'label' => 'Users'],
                ['id' => 3, 'label' => 'Roles'],
            ],
        ],
    ])
    ->collapsed() // Start collapsed
```

#### Available Methods

| Method | Description |
|--------|-------------|
| `options(array\|Closure $options)` | Set the tree data structure |
| `defaultExpanded(bool\|Closure $expanded)` | Set default expanded state |
| `collapsed()` | Start with all nodes collapsed |

## Data Structure

### Tree & TreeEntry

The `Tree` and `TreeEntry` components expect data in this format:

```php
[
    [
        'id' => 1,           // Unique identifier
        'label' => 'Node 1', // Display text
        'children' => [      // Optional nested children
            [
                'id' => 2,
                'label' => 'Child 1',
            ],
        ],
    ],
]
```

### TreeSelect

The `TreeSelect` component uses TreeselectJS format:

```php
[
    [
        'value' => 1,         // Unique identifier
        'name' => 'Node 1',   // Display text
        'children' => [       // Optional nested children
            [
                'value' => 2,
                'name' => 'Child 1',
            ],
        ],
    ],
]
```

## Features

- ✅ Full Filament v4 compatibility
- ✅ Inherits all standard Field methods (`label()`, `helperText()`, `required()`, `live()`, etc.)
- ✅ Dark mode support
- ✅ Cascade selection (select parent → auto-select children)
- ✅ Individual node disabling
- ✅ Expand/collapse all functionality
- ✅ Select all / Deselect all
- ✅ Alpine.js powered (no jQuery dependency)

## License

MIT License. See [LICENSE](LICENSE) for more information.

