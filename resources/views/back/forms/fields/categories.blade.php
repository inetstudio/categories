@inject('categoriesService', 'InetStudio\Categories\Contracts\Services\Back\CategoriesServiceContract')

@php
    $categories = $categoriesService->getTree();
    $item = $value;
@endphp

<div class="form-group row categories-package">
    <label for="title" class="col-sm-2 col-form-label font-bold">Категории</label>

    <div class="col-sm-10">
        @if (count($categories) > 0)
            <div class="categories-tree" data-target="categories" data-multiple="true" data-cascade="up" data-selected="{{ old('categories') ? old('categories') : '' }}">
                <ul>
                    @foreach ($categories as $category)
                        @include('admin.module.categories::back.partials.tree.form_category', [
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
