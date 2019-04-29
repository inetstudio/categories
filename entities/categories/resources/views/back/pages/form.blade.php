@extends('admin::back.layouts.app')

@php
    $title = ($item->id) ? 'Редактирование категории' : 'Создание категории';
@endphp

@section('title', $title)

@section('content')

    @push('breadcrumbs')
        @include('admin.module.categories::back.partials.breadcrumbs.form')
    @endpush

    <div class="wrapper wrapper-content">
        <div class="ibox">
            <div class="ibox-title">
                <a class="btn btn-sm btn-white m-r-xs" href="{{ route('back.categories.index') }}">
                    <i class="fa fa-arrow-left"></i> Вернуться назад
                </a>
                @if ($item->id && $item->href)
                    <a class="btn btn-sm btn-white" href="{{ $item->href }}" target="_blank">
                        <i class="fa fa-eye"></i> Посмотреть на сайте
                    </a>
                @endif
            </div>
        </div>

        {!! Form::info() !!}

        {!! Form::open(['url' => (! $item->id) ? route('back.categories.store') : route('back.categories.update', [$item->id]), 'id' => 'mainForm', 'enctype' => 'multipart/form-data', 'class' => 'categories-package form-horizontal']) !!}

        @if ($item->id)
            {{ method_field('PUT') }}
        @endif

        {!! Form::hidden('category_id', (! $item->id) ? '' : $item->id, ['id' => 'object-id']) !!}

        {!! Form::hidden('category_type', get_class($item), ['id' => 'object-type']) !!}

        <div class="ibox">
            <div class="ibox-title">
                {!! Form::buttons('', '', ['back' => 'back.categories.index']) !!}
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel-group float-e-margins" id="mainAccordion">

                            {!! Form::meta('', $item) !!}

                            {!! Form::social_meta('', $item) !!}

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h5 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#mainAccordion" href="#collapseMain"
                                           aria-expanded="true">Основная информация</a>
                                    </h5>
                                </div>
                                <div id="collapseMain" class="collapse show" aria-expanded="true">
                                    <div class="panel-body">

                                        {!! Form::string('name', $item->name, [
                                            'label' => [
                                                'title' => 'Название',
                                            ],
                                            'field' => [
                                                'class' => 'form-control slugify',
                                                'data-slug-url' => route('back.categories.getSlug'),
                                                'data-slug-target' => 'slug',
                                            ],
                                        ]) !!}

                                        {!! Form::string('slug', $item->slug, [
                                            'label' => [
                                                'title' => 'URL',
                                            ],
                                            'field' => [
                                                'class' => 'form-control slugify',
                                                'data-slug-url' => route('back.categories.getSlug'),
                                                'data-slug-target' => 'slug',
                                            ],
                                        ]) !!}

                                        {!! Form::string('title', $item->title, [
                                            'label' => [
                                                'title' => 'Заголовок',
                                            ],
                                        ]) !!}

                                        {!! Form::wysiwyg('description', $item->description, [
                                            'label' => [
                                                'title' => 'Лид',
                                            ],
                                            'field' => [
                                                'class' => 'tinymce-simple',
                                                'type' => 'simple',
                                            ],
                                        ]) !!}

                                        {!! Form::wysiwyg('content', $item->content, [
                                           'label' => [
                                               'title' => 'Содержимое',
                                           ],
                                           'field' => [
                                               'class' => 'tinymce',
                                               'id' => 'content',
                                               'hasImages' => true,
                                           ],
                                           'images' => [
                                               'media' => $item->getMedia('content'),
                                               'fields' => [
                                                   [
                                                       'title' => 'Описание',
                                                       'name' => 'description',
                                                   ],
                                                   [
                                                       'title' => 'Copyright',
                                                       'name' => 'copyright',
                                                   ],
                                                   [
                                                       'title' => 'Alt',
                                                       'name' => 'alt',
                                                   ],
                                               ],
                                           ],
                                       ]) !!}

                                        <div class="form-group row">

                                            <label for="title" class="col-sm-2 col-form-label font-bold">Родительская
                                                категория</label>

                                            <div class="col-sm-10">
                                                @if (count($tree) > 0)
                                                    <div class="categories-tree" data-target="parent_id"
                                                         data-multiple="false" data-cascade=""
                                                         data-selected="{{ old('parent_id') ? old('parent_id') : '' }}">
                                                        <ul>
                                                            <li id="parentCategoryId_0"
                                                                data-jstree='{"opened":true @if (intval($item->parent_id) == 0),"selected":true @endif}'>
                                                                Категории
                                                                <ul>
                                                                    @foreach ($tree as $treeItem)
                                                                        @include('admin.module.categories::back.partials.tree.form_category', [
                                                                            'item' => $treeItem,
                                                                            'currentId' => $item->id,
                                                                            'selected' => [intval($item->parent_id)],
                                                                        ])
                                                                    @endforeach
                                                                </ul>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @else
                                                    <p>Список категорий пуст.</p>
                                                @endif

                                                {!! Form::hidden('parent_id', intval($item->parent_id)) !!}

                                            </div>
                                        </div>
                                        <div class="hr-line-dashed"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="ibox-footer">
                {!! Form::buttons('', '', ['back' => 'back.categories.index']) !!}
            </div>
        </div>

        {!! Form::close()!!}
    </div>
@endsection
