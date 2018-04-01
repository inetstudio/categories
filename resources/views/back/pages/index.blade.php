@extends('admin::back.layouts.app')

@php
    $title = 'Категории';
@endphp

@section('title', $title)

@section('content')

    @push('breadcrumbs')
        @include('admin.module.categories::back.partials.breadcrumbs.index')
    @endpush

    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins categories-package">
                    <div class="ibox-title">
                        <a href="{{ route('back.categories.create') }}" class="btn btn-sm btn-primary btn-lg">Добавить</a>
                    </div>
                    <div class="ibox-content">
                        <div class="dd categories-list" data-order-url="{{ route('back.categories.move') }}">

                            @if (count($tree) > 0)
                                <ol class="dd-list">
                                    @foreach ($tree as $item)
                                        @include('admin.module.categories::back.partials.tree.index_category', $item)
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
