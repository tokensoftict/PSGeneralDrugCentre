@extends('layouts.app')

@section('pageHeaderTitle1', $title)
@section('pageHeaderDescription', $subtitle)

@section('css')
    <link rel="stylesheet" href="{{ asset('libs/flatpickr/flatpickr.min.css') }}"/>
    <link href="{{ asset('libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('DataTables/datatables.min.css') }}"/>
@endsection

@section('js')
    <script type="text/javascript" src="{{asset('DataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            flatpickr(".datepicker-basic", {  });
            var e = document.querySelectorAll("[data-trigger]");
            for (i = 0; i < e.length; ++i) {
                var a = e[i];
                new Choices(a, { placeholderValue: "This is a placeholder set in the config", searchPlaceholderValue: "This is a search placeholder" });
            }
        });

        $(function () {
            var table =  $('#invoice-list').DataTable({
                buttons: [
                    'copy', 'excel', 'pdf'
                ],

            });


        });

    </script>
@endsection

@section('pageHeaderAction')

    <x-report-filter-component :filters="$filters"/>

@endsection

@section('content')

    <h5><b>Opening Balance</b> : {{ number_format($opening,2) }}</h5>
    <table id="invoice-list" class="table table-bordered table-responsive table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Invoice</th>
            <th>Payment</th>
            <th>Transaction Date</th>
            <th>Balance</th>
        </tr>
        </thead>
        <tbody>
        @foreach($histories as $history)
            @php
                $opening =  ($opening+$history->amount)
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $history->amount < 0  ? number_format($history->amount,2) : "" }}</td>
                <td>{{ $history->amount > 0  ? number_format($history->amount,2) : "" }}</td>
                <td>{{ eng_str_date($history->transaction_date) }}</td>
                <td>{{ money($opening) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <th>Total Balance</th>
            <th>{{ money($opening) }}</th>
        </tr>
        </tfoot>
        <tfoot>
        </tfoot>
    </table>
@endsection
