@extends('layouts.app')

@section('pageHeaderTitle1', $title)
@section('pageHeaderDescription', $subtitle)


@section('content')
    <div class="table-responsive">
    <livewire:invoice-and-sales.datatable.invoice-data-table :filters="$filters"/>
    </div>
@endsection
