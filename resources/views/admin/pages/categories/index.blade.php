@extends('admin::layouts.app')

@php
    $title = 'Категории';
@endphp

@section('title', $title)

@pushonce('styles:categories_custom')
    <!-- CUSTOM STYLE -->
    <link href="{!! asset('admin/css/modules/categories/custom.css') !!}" rel="stylesheet">
@endpushonce

@section('content')

    @push('breadcrumbs')
        @include('admin.module.categories::partials.categories.breadcrumbs')
    @endpush

    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <a href="{{ route('back.categories.create') }}" class="btn btn-sm btn-primary btn-lg">Добавить</a>
                    </div>
                    <div class="ibox-content">
                        <div class="dd nested-list" data-order-url="{{ route('back.categories.move') }}">

                            @if (count($tree) > 0)
                                <ol class="dd-list">
                                    @foreach ($tree as $item)
                                        @include('admin.module.categories::pages.categories.partials.index_category', $item)
                                    @endforeach
                                </ol>
                            @else
                                <p>Список категорий пуст. Вы можете добавить категорию по кнопке выше.</p>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@pushonce('scripts:nestable')
    <!-- Nestable List -->
    <script src="{!! asset('admin/js/plugins/nestable/jquery.nestable.js') !!}"></script>
@endpushonce
