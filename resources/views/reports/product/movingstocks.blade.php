@extends('layouts.app')

@section('pageHeaderTitle1', $title)
@section('pageHeaderDescription', $subtitle)

@section('css')

@endsection

@section('js')


@endsection

@section('pageHeaderAction')

@endsection

@section('content')
    <div class="table-responsive">
        <livewire:product-module.report.moving-stock-report/>
    </div>
@endsection
