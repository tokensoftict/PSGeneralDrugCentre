@extends('layouts.app')

@section('pageHeaderTitle1', $title)
@section('pageHeaderDescription', $subtitle)

@section('css')

@endsection

@section('js')


@endsection

@section('pageHeaderAction')
    @if(app(\App\Classes\Settings::class)->get('m_run_nears') !== 'running')
   <div class="row">
       <div class="col-12">
           <a href="{{ route('run_retail_nearos') }}"  class="btn btn-primary float-end">Run Retail Near Os</a>
           <br/> <br/>  <br/>
       </div>
   </div>
    @endif
@endsection

@section('content')
    <div class="table-responsive">
        <livewire:product-module.near-o-s.retail-near-os-datatable/>
    </div>
@endsection
