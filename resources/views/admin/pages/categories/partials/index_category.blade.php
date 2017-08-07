<li class="dd-item dd3-item" data-id="{{ $item['id'] }}">
    <div class="dd-handle dd3-handle">Drag</div>
    <div class="dd3-content">
        {{ $item['name'] }}
        <div class="btn-group pull-right">
            <a href="{{ route('back.categories.edit', [$item['id']]) }}" class="btn btn-xs btn-default m-r-xs"><i class="fa fa-pencil"></i></a>
            <a href="#" class="btn btn-xs btn-danger delete" data-url="{{ route('back.categories.destroy', [$item['id']]) }}"><i class="fa fa-times"></i></a>
        </div>
    </div>
    @if (count($item['items']) > 0)
        <ol class="dd-list">
            @foreach($item['items'] as $item)
                @include('admin.module.categories::pages.categories.partials.index_category', $item)
            @endforeach
        </ol>
    @endif
</li>