@extends('layouts.concept')

@section('page-title', 'Report')
@section('page-desc', 'Response Code Report')

@section('breadcrum-title', 'Index')

@push('styles')
    @include('admin.shared.styles')

@endpush

@section('content')

    <div class="card">
        <div class="card-body">

            @if (session('status'))
                <div class="alert alert-info">
                    {{ session('status') }}
                </div>
            @endif

            {!! Form::open(['route' => 'code.report', 'method' => 'post', 'id' => 'getReport']) !!}

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
                <table id="myTable" class="table table-striped first" style="width: 100%;">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Agent</th>
                        <th>Dialed Calls</th>
                        @foreach(\App\ResponseCode::pluck('name') as $responseCode)
                            <th>{{ $responseCode }}</th>
                        @endforeach
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @include('admin.shared.scripts')
    <script>

        let url = "{!! route('code.report') !!}";
        let columns = JSON.parse('{!! $columns !!}');

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
                    responsive: true,
                    ajax: {
                        url: url,
                        method: "POST",
                        data: {
                            _token: '{!! csrf_token() !!}',
                            start_date: document.getElementById('start').value,
                            end_date: document.getElementById('end').value,
                        }
                    },
                    columns: columns/*[
                        //columns
                        {data: "id", name: "id"},
                        {data: "agent", name: "agent"},
                        {data: "calls", name: "calls"},
                        {data: "Test", name: "Test"},
                        {data: "Test2", name: "Test2"},
                    ]*/,
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

