<li id="{{ $id }}_{{ $item['id'] }}" data-jstree='{"opened":false @if (in_array($item['id'], $selected)),"selected":true @endif @if ($item['id'] == $currentId),"disabled":true @endif}'>
    {{ $item['name'] }}
    @if (count($item['items']) > 0)
        <ul>
            @foreach($item['items'] as $treeItem)
                @include('admin.module.categories::partials.tree.form_category', [
                    'id' => 'parentCategoryId',
                    'item' => $treeItem,
                    'currentId' => $currentId,
                    'selected' => $selected,
                ])
            @endforeach
        </ul>
    @endif
</li>