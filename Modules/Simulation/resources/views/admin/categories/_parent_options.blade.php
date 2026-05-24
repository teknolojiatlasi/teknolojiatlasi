<option value="">(Yok)</option>
@php
    $renderCategoryOptions = function ($items, $level = 0) use (&$renderCategoryOptions) {
        foreach ($items as $item) {
            echo '<option value="'.$item->id.'">'.e($item->flattenedLabel($level)).'</option>';
            $renderCategoryOptions($item->childrenRecursive, $level + 1);
        }
    };
    $renderCategoryOptions($allCategories);
@endphp
