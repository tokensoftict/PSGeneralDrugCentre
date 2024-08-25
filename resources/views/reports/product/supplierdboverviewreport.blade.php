@extends('layouts.app')

@section('pageHeaderTitle1', $title)
@section('pageHeaderDescription', $subtitle)


@section('css')


@endsection

@section('js')

@endsection

@section('pageHeaderAction')

    <x-report-filter-component :filters="$filters"/>

@endsection

@section('content')

    <livewire:product-module.datatable.supplier-db-overview-datatable :payment_date="$filters['from']"/>
@endsection
