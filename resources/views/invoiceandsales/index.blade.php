@extends('layouts.app')

@section('pageHeaderTitle1', $title)
@section('pageHeaderDescription', $subtitle)


@section('content')
    <livewire:invoice-and-sales.datatable.invoice-data-table :filters="$filters"/>
@endsection
