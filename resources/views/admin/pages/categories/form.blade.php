@extends('admin::layouts.app')

@php
    $title = ($item->id) ? 'Редактирование категории' : 'Добавление категории';
@endphp

@section('title', $title)

@pushonce('styles:jstree')
    <!-- JSTREE -->
    <link href="{!! asset('admin/css/plugins/jstree/style.min.css') !!}" rel="stylesheet">
@endpushonce

@section('content')

    @push('breadcrumbs')
        @include('admin.module.categories::partials.categories.breadcrumbs')
        <li>
            <a href="{{ route('back.categories.index') }}">Категории</a>
        </li>
    @endpush

    @if ($item->id)
        <div class="row m-sm">
            <a class="btn btn-white" href="{{ $item->href }}" target="_blank">
                <i class="fa fa-eye"></i> Посмотреть на сайте
            </a>
        </div>
    @endif

    <div class="wrapper wrapper-content">

        {!! Form::info() !!}

        {!! Form::open(['url' => (!$item->id) ? route('back.categories.store') : route('back.categories.update', [$item->id]), 'id' => 'mainForm', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}

        @if ($item->id)
            {{ method_field('PUT') }}
        @endif

        {!! Form::hidden('category_id', (!$item->id) ? '' : $item->id) !!}

        {!! Form::meta('', $item) !!}

        {!! Form::social_meta('', $item) !!}

        <div class="row">
            <div class="col-lg-12">
                <div class="panel-group float-e-margins" id="mainAccordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mainAccordion" href="#collapseMain" aria-expanded="true">Основная информация</a>
                            </h5>
                        </div>
                        <div id="collapseMain" class="panel-collapse collapse in" aria-expanded="true">
                            <div class="panel-body">

                                {!! Form::string('title', $item->title, [
                                    'label' => [
                                        'title' => 'Заголовок',
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

                                <div class="form-group ">

                                    <label for="title" class="col-sm-2 control-label">Родительская категория</label>

                                    <div class="col-sm-10">
                                        @if (count($tree) > 0)
                                            <div class="jstree-list" data-target="parent_id" data-multiple="false" data-cascade="">
                                                <ul>
                                                    <li id="parentCategoryId_0" data-jstree='{"opened":true @if (intval($item->parent_id) == 0),"selected":true @endif}'>Категории
                                                        <ul>
                                                            @foreach ($tree as $treeItem)
                                                                @include('admin.module.categories::pages.categories.partials.form_category', [
                                                                    'id' => 'parentCategoryId',
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

                                        {!! Form::hidden('parent_id', '') !!}

                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {!! Form::buttons('', '', ['back' => 'back.categories.index']) !!}

        {!! Form::close()!!}
    </div>
@endsection

@pushonce('scripts:jstree')
    <!-- JSTREE -->
    <script src="{!! asset('admin/js/plugins/jstree/jstree.min.js') !!}"></script>
@endpushonce
