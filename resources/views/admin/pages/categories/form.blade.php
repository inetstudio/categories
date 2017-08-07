@php
    $title = ($item->id) ? 'Редактирование категории' : 'Добавление категории';
@endphp

@extends('admin::layouts.app')

@section('title', $title)

@section('styles')
    <!-- ICHECK -->
    <link href="{!! asset('admin/css/plugins/iCheck/custom.css') !!}" rel="stylesheet">

    <!-- CROPPER -->
    <link href="{!! asset('admin/css/plugins/cropper/cropper.min.css') !!}" rel="stylesheet">

    <!-- JSTREE -->
    <link href="{!! asset('admin/css/plugins/jstree/style.min.css') !!}" rel="stylesheet">

    <style>
        .jstree-open > .jstree-anchor > .fa-folder:before {
            content: "\f07c";
        }

        .jstree-default .jstree-icon.none {
            width: 0;
        }
    </style>

@endsection

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>
                {{ $title }}
            </h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/back/') }}">Главная</a>
                </li>
                <li>
                    <a href="{{ route('back.categories.index') }}">Категории</a>
                </li>
                <li class="active">
                    <strong>
                        {{ $title }}
                    </strong>
                </li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content">

        {!! Form::info() !!}

        {!! Form::open(['url' => (!$item->id) ? route('back.categories.store') : route('back.categories.update', [$item->id]), 'id' => 'mainForm', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}

            @if ($item->id)
                {{ method_field('PUT') }}
            @endif

            {!! Form::hidden('category_id', (!$item->id) ? '' : $item->id) !!}

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel-group float-e-margins" id="metaAccordion">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#metaAccordion" href="#collapseMeta" aria-expanded="false" class="collapsed">Мета теги</a>
                                </h5>
                            </div>
                            <div id="collapseMeta" class="panel-collapse collapse" aria-expanded="false">
                                <div class="panel-body">

                                    {!! Form::string('meta[title]', $item->getMeta('title'), [
                                        'label' => [
                                            'title' => 'Title',
                                        ],
                                    ]) !!}

                                    {!! Form::string('meta[description]', $item->getMeta('description'), [
                                        'label' => [
                                            'title' => 'Description',
                                        ],
                                    ]) !!}

                                    {!! Form::string('meta[keywords]', $item->getMeta('keywords'), [
                                        'label' => [
                                            'title' => 'Keywords',
                                        ],
                                    ]) !!}

                                    {!! Form::radios('meta[robots]', $item->getMeta('robots'), [
                                        'label' => [
                                            'title' => 'Индексировать',
                                        ],
                                        'radios' => [
                                            [
                                                'label' => 'Да',
                                                'value' => 'index, follow',
                                                'options' => [
                                                    'class' => 'i-checks',
                                                ],
                                            ],
                                            [
                                                'label' => 'Нет',
                                                'value' => 'noindex, nofollow',
                                                'options' => [
                                                    'class' => 'i-checks',
                                                ],
                                            ],
                                        ],
                                    ]) !!}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel-group float-e-margins" id="socialMetaAccordion">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#socialMetaAccordion" href="#collapseSocialMeta" aria-expanded="false" class="collapsed">Социальные мета теги</a>
                                </h5>
                            </div>
                            <div id="collapseSocialMeta" class="panel-collapse collapse" aria-expanded="false">
                                <div class="panel-body">

                                    {!! Form::string('meta[og:title]', $item->getMeta('og:title'), [
                                        'label' => [
                                            'title' => 'og:title',
                                        ],
                                    ]) !!}

                                    {!! Form::string('meta[og:description]', $item->getMeta('og:description'), [
                                        'label' => [
                                            'title' => 'og:description',
                                        ],
                                    ]) !!}

                                    @php
                                        $ogImageMedia = $item->getFirstMedia('og_image');
                                    @endphp

                                    {!! Form::crop('og_image', $ogImageMedia, [
                                        'label' => [
                                            'title' => 'og:image',
                                        ],
                                        'image' => [
                                            'src' => isset($ogImageMedia) ? url($ogImageMedia->getUrl()) : '',
                                            'type' => 'single',
                                        ],
                                        'crops' => [
                                            [
                                                'title' => 'Размер 968х475',
                                                'name' => 'default',
                                                'ratio' => '968/475',
                                                'value' => isset($ogImageMedia) ? $ogImageMedia->getCustomProperty('crop.default') : '',
                                            ],
                                        ],
                                    ]) !!}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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

            {!! Form::buttons('', '', ['back' => 'back.tags.index']) !!}

        {!! Form::close()!!}
    </div>

    {!! Form::modals_crop() !!}

@endsection

@section('scripts')
    <!-- ICHECK -->
    <script src="{!! asset('admin/js/plugins/iCheck/icheck.min.js') !!}"></script>

    <!-- CROPPER -->
    <script src="{!! asset('admin/js/plugins/cropper/cropper.min.js') !!}"></script>

    <!-- TINYMCE -->
    <script src="{!! asset('admin/js/plugins/tinymce/tinymce.min.js') !!}"></script>

    <!-- JSTREE -->
    <script src="{!! asset('admin/js/plugins/jstree/jstree.min.js') !!}"></script>
@endsection
