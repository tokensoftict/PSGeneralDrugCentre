@extends('layouts.app')
@section('pageHeaderTitle1', $title)
@section('pageHeaderDescription', $subtitle)

@section('pageHeaderAction')
    @if(userCanView('purchase.create'))
    <div class="row">
        <div class="col-sm">
            <div class="mb-4">
                <a href="{{ route('purchase.create') }}"  type="button" class="btn btn-primary waves-effect waves-light">
                    <i  class="bx bx-plus me-1"></i>
                    New Purchase
                </a>
            </div>
        </div>
        <div class="col-sm-auto">

        </div>
    </div>
    @endif
@endsection

@section('content')
    <livewire:purchase-order.datatable.purchaseorder-datatable :filters="$filters"/>
@endsection