@extends('admin::layouts.app')

@php
    $title = 'Категории';
@endphp

@section('title', $title)

@section('styles')
    <style>
        .dd3-content {
            display: block;
            margin: 10px 0;
            padding: 5px 10px 5px 40px;
            color: #333;
            text-decoration: none;
            background: #fafafa;
        }

        .dd3-content:hover {
            font-weight: bold;
            background: #f0f0f0;
            cursor: default;
        }

        .dd-dragel > .dd3-item > .dd3-content {
            margin: 0;
        }

        .dd3-item > button {
            margin-left: 30px;
        }

        .dd3-handle {
            position: absolute;
            margin: 0;
            left: 0;
            top: 0;
            width: 30px;
            text-indent: 100%;
            white-space: nowrap;
            overflow: hidden;
            background: #1ab394;
            border-width: 0px;
            cursor: move;
        }

        .dd3-handle:before {
            content: '≡';
            display: block;
            position: absolute;
            left: 0;
            top: 3px;
            width: 100%;
            text-align: center;
            text-indent: 0;
            color: #fff;
            font-size: 20px;
            font-weight: normal;
            cursor: move;
        }

        .dd3-handle:hover {
            background: #ddd;
        }
    </style>
@endsection

@section('content')

    @include('admin.module.categories::partials.breadcrumb_index', ['title' => $title])

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

@section('scripts')
    <!-- Nestable List -->
    <script src="{!! asset('admin/js/plugins/nestable/jquery.nestable.js') !!}"></script>
@endsection
