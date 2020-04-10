@extends('layouts.concept')

@section('page-title', 'Agent')
@section('page-desc', 'Dialer Interface')

@section('breadcrum-title', 'Panel')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flatpickr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/datatables.min.css') }}"/>
@endpush

@section('content')

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex">
                    <h3 class="card-header-title">{{ Auth::user()->name }} - {{ Auth::user()->auths()->first()->username }}</h3>
                    <div class="toolbar ml-auto">
                        <button type="button" id="registerBtn" class="btn btn-success btn-sm"><i class="fas fa-users-cog"></i> Register</button>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="col-8 offset-lg-2 mb-4">
                            <div class="row text-center">
                                <div class="col-6">
                                    <span id="phone-status" class="p-2 text-danger font-weight-bold">
                                        <i class="fas fa-power-off"></i> DISCONNECTED
                                    </span>
                                </div>
                                <div class="col-6">
                                    <span id="agent-status" class="p-2 text-danger font-weight-bold">
                                        <i class="fas fa-sign-in-alt"></i> OFFLINE
                                    </span>
                                </div>
                                <audio id="remoteAudio"></audio>
                                <audio id="localAudio" muted="muted"></audio>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-6 offset-lg-3 text-center font-weight-bolder p-2 border-white rounded bg-light rounded">
                                <span style="display: none;" id="callNotif" class="animated infinite fadeIn delay-1s text-danger font-weight-bold mb-2">
                                    <i class="fas fa-circle"></i> In Call
                                </span>
                                <h6>
                                    <span id="callStatus">DISCONNECTED</span>
                                </h6>
                                <input aria-label="Input Number" id="inputNumber" type="text" value="" class="form-control-lg mb-2 text-center">
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
                            <div id="inCallBtns" style="display: none;" class="col-6 offset-lg-3">
                                <button id="muteBtn" aria-pressed="false" autocomplete="off" type="button" data-toggle="button" class="btn btn-outline-primary">
                                    <i class="fas fa-volume-mute"></i> MUTE
                                </button>
                                <button id="holdBtn" aria-pressed="false" autocomplete="off" type="button" data-toggle="button" class="btn btn-outline-secondary">
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
                                <label class="col-form-label text-sm-right">Random Mode</label>
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
        let random_url = "{{ route('agent.random') }}";
        let list_url = "{{ route('agent.list') }}";
        let recent_calls = "{{ route('agent.recent') }}";
        let schedule_call = "{{ route('agent.schedule') }}";
        let get_calls = "{{ route('agent.get-calls') }}";
        let codes = '{!! $responseCodes !!}';
        let response_route = "{{ route('agent.saveResponse') }}";
        let token = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('js/sweetalert2@8.js') }}"></script>
    <script src="{{ asset('js/flatpickr.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vfs_fonts.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/sip.js') }}" defer></script>
    <script src="{{ asset('js/notify.js') }}" defer></script>
    <script src="{{ asset('js/nAgent.js') }}" defer></script>
@endpush

