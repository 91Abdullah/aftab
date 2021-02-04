@extends('layouts.concept')

@section('page-title', 'Agent')
@section('page-desc', 'Dialer Interface')

@section('breadcrum-title', 'Panel')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/datatables.min.css') }}"/>
@endpush

@section('content')

    <div class="row">
        <div class="simple-card col-12">
            <ul class="nav nav-tabs" id="myTab5" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active border-left-0" id="home-tab-simple" data-toggle="tab" href="#agentui" role="tab" aria-controls="home" aria-selected="true">UI</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="profile-tab-simple" data-toggle="tab" href="#cdrs" role="tab" aria-controls="profile" aria-selected="false">CDRs</a>
                </li>
            </ul>
            <div class="tab-content p-0">
                <div class="tab-pane fade show active" id="agentui">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header d-flex">
                                    <h3 class="card-header-title">{{ Auth::user()->name }} - {{ Auth::user()->auths()->first()->username }}</h3>
                                    <div class="toolbar ml-auto">
                                        <button type="button" id="readyBtn" class="btn btn-danger btn-sm"><i class="fas fa-power-off"></i> Not-Ready</button>
                                        <button type="button" id="registerBtn" class="btn btn-success btn-sm"><i class="fas fa-users-cog"></i> Register</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form>
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <span id="phone-status" class="text-danger" style="font-size: 13px;">
                                                    <i class="fas fa-power-off"></i> DISCONNECTED
                                                </span>
                                            </div>
                                            <div class="col-6">
                                                <span id="agent-status" class="text-danger" style="font-size: 13px;">
                                                    <i class="fas fa-sign-in-alt"></i> OFFLINE
                                                </span> /
                                                <span id="agent-qstatus" class="text-success" style="font-size: 13px;">
                                                    <i class="fas fa-power-off"></i> READY
                                                </span>
                                            </div>
                                            <audio id="remoteAudio"></audio>
                                            <audio id="localAudio" muted="muted"></audio>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-12 text-center font-weight-bolder p-2 border-white rounded bg-light rounded">
                                <span style="display: none;" id="callNotif" class="animated infinite fadeIn delay-1s text-danger font-weight-bold mb-2">
                                    <i class="fas fa-circle"></i> In Call
                                </span>
                                                <h6>
                                                    <span id="callStatus">DISCONNECTED</span>
                                                </h6>
                                                <div class="col-sm-4 offset-4">
                                                    <span class="badge badge-pill badge-info" id="cName"></span>
                                                    <span class="badge badge-pill badge-dark" id="cCity"></span>
                                                </div>
                                                <input aria-label="Input Number" id="inputNumber" type="tel" value="" class="form-control-lg mb-2 text-center">
                                                <h6>
                                                    <span id="mins">00:</span><span id="seconds">00</span>
                                                </h6>
                                                <span id="holdNotif" class="animated infinite fadeIn delay-1s text-danger font-weight-bold hide">
                                    <i class="fas fa-circle"></i> HOLD
                                </span>
                                                <span id="muteNotif" class="animated infinite fadeIn delay-1s text-danger font-weight-bold hide">
                                    <i class="fas fa-circle"></i> MUTE
                                </span>
                                            </div>
                                        </div>
                                        <div class="row text-center mt-4 mb-4">
                                            <div id="inCallBtns" style="display: none;" class="m-auto">
                                                <button id="muteBtn" aria-pressed="false" type="button" data-toggle="button" class="btn btn-outline-primary">
                                                    <i class="fas fa-volume-mute"></i> MUTE
                                                </button>
                                                <button id="holdBtn" aria-pressed="false" type="button" data-toggle="button" class="btn btn-outline-secondary">
                                                    <i class="fas fa-music"></i> HOLD
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-12 text-center">
                                            @if($random_mode == "true")
                                                <button id="listDialBtn" type="button" class="btn btn-success">
                                                    <i class="fas fa-phone"></i>
                                                    LIST DIAL
                                                </button>
                                                <button id="randomDialBtn" type="button" class="btn btn-success">
                                                    <i class="fas fa-phone"></i>
                                                    RANDOM DIAL
                                                </button>
                                            @endif
                                            <button id="manualDialBtn" type="button" class="btn btn-primary">
                                                <i class="fas fa-phone"></i>
                                                DIAL
                                            </button>
                                            <button id="hangupBtn" type="reset" class="btn btn-danger">
                                                <i class="fas fa-phone-slash"></i>
                                                HANGUP
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            @if($random_mode == "true")
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <label class="col-form-label text-sm-right">Auto Mode</label>
                                                <div class="switch-button switch-button-success">
                                                    <input type="checkbox" name="randomMode" id="randomMode"><span>
                                <label for="randomMode"></label></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <button id="openAttendedTransfer" class="btn btn-success"><i class="fa fa-hand-paper"></i> Attended Transfer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header d-flex">
                                            <div class="card-header-title">
                                                Schedule A Call
                                            </div>
                                            <div class="toolbar ml-auto">
                                                <button title="Schedule this call" id="scheduleCall" class="btn btn-primary btn-xs"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <div class="card-body table-responsive">
                                            <table class="table table-hover table-sm" id="scheduleTable">
                                                <thead>
                                                <tr>
                                                    <th>number</th>
                                                    <th>status</th>
                                                    <th>time</th>
                                                    <th>last called</th>
                                                </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                            <span class="card-header-title">
                                Last 5 Calls
                            </span>
                                        </div>

                                        <div class="card-body">
                                            <div class="table-responsive" style="height: 200px;">
                                                <table class="table table-hover table-sm" id="cdrTable">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col">dst</th>
                                                        <th scope="col">agent</th>
                                                        <th scope="col">start</th>
                                                        <th scope="col">anwer</th>
                                                        <th scope="col">end</th>
                                                        <th scope="col">duration</th>
                                                        <th scope="col">disposition</th>
                                                    </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="cdrs">
                    <div class="card">
                        <div class="card-body">

                            @if (session('status'))
                                <div class="alert alert-info">
                                    {{ session('status') }}
                                </div>
                            @endif

                            {!! Form::open(['route' => 'cdr.report', 'method' => 'post', 'id' => 'getReport']) !!}

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
                                        <th>Dest</th>
                                        <th>CLID</th>
                                        <th>Agent</th>
                                        <th>Start</th>
                                        <th>Answer</th>
                                        <th>End</th>
                                        <th>Duration</th>
                                        <th>Disposition</th>
                                        <th>Code</th>
                                        <th>Recording</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="10">No records found.</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="attendedTransfer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Execute Attended Transfer</h5>
                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </a>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="transferNumber" class="col-form-label">Input Number</label>
                            <input id="transferNumber" type="text" class="form-control">
                        </div>
                        <h6 class="text-center">
                            <span id="tmins">00:</span><span id="tseconds">00</span>
                        </h6>
                    </form>
                </div>
                <div class="modal-footer">
                    <div id="transferDialBtns">
                        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="transferDial" class="btn btn-primary"><i class="fa fa-phone"></i> DIAL</button>
                    </div>
                    <div id="transferBtns" style="display: none;">
                        <button id="transferCall" class="btn btn-success"><i class="fa fa-phone"></i> Transfer</button>
                        <button id="hangupTransferCall" class="btn btn-danger"><i class="fa fa-phone-slash"></i> Hangup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let authUser = "{!! Auth::user()->auths()->first()->username !!}";
        let user_id = "{!! Auth::user()->id !!}";
        let authPassword = "{!! Auth::user()->auths()->first()->password !!}";
        let wss_comm_port = "{{ \App\Setting::where('key', 'wss_comm_port')->first()->value }}";
        let wss_socket_port = "{{ \App\Setting::where('key', 'wss_socket_port')->first()->value }}";
        let server_address = "{{ \App\Setting::where('key', 'server_address')->first()->value }}";
        let auto_answer = "{{ \App\Setting::where('key', 'auto_answer')->first()->value }}";
        let random_mode = "{{ \App\Setting::where('key', 'random_mode')->first()->value }}";
        let random_type = "{{ \App\Setting::where('key', 'random_type')->first()->value }}";
        let random_url = "{{ route('agent.random') }}";
        let list_url = "{{ route('agent.list') }}";
        let recent_calls = "{{ route('agent.recent') }}";
        let schedule_call = "{{ route('agent.schedule') }}";
        let get_calls = "{{ route('agent.get-calls') }}";
        let codes = '{!! $responseCodes !!}';
        let response_route = "{{ route('agent.saveResponse') }}";
        let token = "{{ csrf_token() }}";

        // Agent Routes
        let readyRoute = "{{route('agent.ready')}}";
        let notReadyRoute = "{{route('agent.notready')}}";
        let agentQStatus = "{{ route('agent.status') }}";

    </script>
    <script src="{{ asset('js/sweetalert2@8.js') }}"></script>
    <script src="{{ asset('js/flatpickr.js') }}"></script>
    <script src="{{ asset('js/bootstrap-datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vfs_fonts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/select.js') }}"></script>
    <script src="{{ asset('js/sip.js') }}" defer></script>
    <script src="{{ asset('js/notify.js') }}" defer></script>
    <script src="{{ asset('js/nAgent.js') }}" defer></script>
    <script>

        let url = "{!! route('cdr.report') !!}";

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
                        {data: 'dst', name: 'dst'},
                        {data: 'clid', name: 'clid'},
                        {data: 'agent', name: 'agent'},
                        {data: 'start', name: 'start'},
                        {data: 'answer', name: 'answer'},
                        {data: 'end', name: 'end'},
                        {data: 'duration', name: 'duration'},
                        {data: 'disposition', name: 'disposition'},
                        {data: 'code', name: 'code'},
                        {data: 'recordingfile', name: 'recordingfile'}
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

