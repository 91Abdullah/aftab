@extends('layouts.concept')

@section('page-title', 'Report')
@section('page-desc', 'Agent Login')

@section('breadcrum-title', 'Index')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-colvis-1.5.6/b-html5-1.5.6/b-print-1.5.6/datatables.min.css"/>

@endpush

@section('content')

    <div class="card">
        <div class="card-body">

            @if (session('status'))
                <div class="alert alert-info">
                    {{ session('status') }}
                </div>
            @endif

            {!! Form::open(['route' => 'login.report', 'method' => 'post', 'id' => 'getReport']) !!}

            <div class="form-group">
                <label for="start">Select Date Range</label>
                <div class="input-daterange input-group" id="datepicker">
                    <input id="start" value="{{ old('start') }}" type="text" class="input-sm form-control" name="start" />
                    <span class="input-group-text">TO</span>
                    <input id="end" value="{{ old('end') }}" type="text" class="input-sm form-control" name="end" />
                </div>
            </div>

            {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}

            {!! Form::close() !!}

        </div>
    </div>

    <div class="card">
        <div class="card-body">

            @if (session('status'))
                <div class="alert alert-info">
                    {{ session('status') }}
                </div>
            @endif

            <div class="table-responsive">
                <table id="myTable" class="table table-striped first">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Agent</th>
                        <th>Session</th>
                        <th>Login Time</th>
                        <th>Logout Time</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="8">No records found.</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.18/b-1.5.6/b-colvis-1.5.6/b-html5-1.5.6/b-print-1.5.6/datatables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.19/pagination/select.js"></script>
    <script>

        let url = "{!! route('login.report') !!}";

        $(document).ready(function () {
            $('#datepicker').datepicker({
                format: "yyyy-mm-dd",
                todayBtn: "linked",
                clearBtn: true,
                keyboardNavigation: false,
                forceParse: false,
                todayHighlight: true,
                toggleActive: true,
                autoClose: true
            });

            document.getElementById('getReport').onsubmit = function (e) {
                e.preventDefault();
                $('#myTable').DataTable({
                    processing: true,
                    serverSide: true,
                    destroy: true,
                    ajax: {
                        url: url,
                        data: {
                            _token: '{!! csrf_token() !!}',
                            start_date: document.getElementById('start').value,
                            end_date: document.getElementById('end').value,
                        }
                    },
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'user_id', name: 'Agent'},
                        {data: 'session_id', name: 'Session'},
                        {data: 'login_time', name: 'Login Time'},
                        {data: 'logout_time', name: 'Logout Time'},
                    ],
                    dom: 'Bfrtip',
                    lengthMenu: [
                        [ 10, 25, 50, -1 ],
                        [ '10', '25', '50', 'All' ]
                    ],
                    buttons: {
                        dom: {
                            button: {
                                className: 'btn'
                            },
                            container: {
                                className: ''
                            }
                        },
                        buttons: [
                            {
                                extend:    'pageLength',
                                titleAttr: 'Page Length',
                                className: 'btn btn-primary'
                            },
                            {
                                extend:    'copyHtml5',
                                text:      '<i class="fas fa-file"></i> Copy',
                                titleAttr: 'Copy',
                                className: 'btn btn-primary'
                            },
                            {
                                extend:    'excelHtml5',
                                text:      '<i class="fas fa-file-excel"></i> Excel',
                                titleAttr: 'Excel',
                                className: 'btn btn-primary'
                            },
                            {
                                extend:    'csvHtml5',
                                text:      '<i class="fas fa-sticky-note"></i> CSV',
                                titleAttr: 'CSV',
                                className: 'btn btn-primary'
                            },
                            {
                                extend:    'pdfHtml5',
                                text:      '<i class="fas fa-file-pdf"></i> PDF',
                                titleAttr: 'PDF',
                                className: 'btn btn-primary'
                            },
                            {
                                extend: 'print',
                                text: '<i class="fas fa-print"></i> Print',
                                titleAttr: 'PRINT',
                                className: 'btn btn-primary'
                            },
                            {
                                extend: 'colvis',
                                text: '<i class="fas fa-columns"></i> Column Visibility',
                                titleAttr: 'COLVIS',
                                className: 'btn btn-primary'
                            }
                        ]
                    }
                });
            }
        })
    </script>
@endpush

