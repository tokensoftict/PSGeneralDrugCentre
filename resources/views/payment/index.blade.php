@extends('layouts.app')

@section('pageHeaderTitle1', $title)
@section('pageHeaderDescription', $subtitle)


@section('content')
    <livewire:payment-manager.datatable.payment-list-datatable :filters="$filters"/>
@endsection
