@inject('categoriesService', 'InetStudio\Categories\Contracts\Services\Back\CategoriesServiceContract')

@php
    $categories = $categoriesService->getTree();
    $item = $value;
@endphp

@pushonce('styles:jstree')
    <!-- JSTREE -->
    <link href="{!! asset('admin/css/plugins/jstree/style.min.css') !!}" rel="stylesheet">
@endpushonce

<div class="form-group ">
    <label for="title" class="col-sm-2 control-label">Категории</label>

    <div class="col-sm-10">
        @if (count($categories) > 0)
            <div class="jstree-list" data-target="categories" data-multiple="true" data-cascade="up">
                <ul>
                    @foreach ($categories as $category)
                        @include('admin.module.categories::back.partials.tree.form_category', [
                            'id' => 'parentCategoryId',
                            'item' => $category,
                            'currentId' => null,
                            'selected' => $item->categories()->pluck('id')->toArray(),
                        ])
                    @endforeach
                </ul>
            </div>
        @else
            <p>Список категорий пуст.</p>
        @endif

        {!! Form::hidden('categories', implode($item->categories()->pluck('id')->toArray(), ',')) !!}

    </div>
</div>
<div class="hr-line-dashed"></div>

@pushonce('scripts:jstree')
    <!-- JSTREE -->
    <script src="{!! asset('admin/js/plugins/jstree/jstree.min.js') !!}"></script>
@endpushonce
