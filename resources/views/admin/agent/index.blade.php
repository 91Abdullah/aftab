@extends('layouts.concept')

@section('page-title', 'Agent')
@section('page-desc', 'Dialer Interface')

@section('breadcrum-title', 'Panel')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/animate.css') }}">
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
                        <div class="col-6 offset-lg-3 mb-4">
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
                                <input id="inputNumber" type="text" value="" class="form-control-lg mb-2 text-center">
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
                            <button id="listDialBtn" type="button" class="btn btn-success btn-lg">
                                <i class="fas fa-phone"></i>
                                LIST DIAL
                            </button>
                            <button id="randomDialBtn" type="button" class="btn btn-success btn-lg">
                                <i class="fas fa-phone"></i>
                                RANDOM DIAL
                            </button>
                            <button id="manualDialBtn" type="button" class="btn btn-success btn-lg">
                                <i class="fas fa-phone"></i>
                                MANUAL DIAL
                            </button>
                            <button id="hangupBtn" type="reset" class="btn btn-danger btn-lg">
                                <i class="fas fa-phone-slash"></i>
                                HANGUP
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">STATS</div>

                <div class="card-body">

                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        let authUser = "{!! Auth::user()->auths()->first()->username !!}";
        let authPassword = "{!! Auth::user()->auths()->first()->password !!}";
        let wss_comm_port = "{{ \App\Setting::where('key', 'wss_comm_port')->first()->value }}";
        let wss_socket_port = "{{ \App\Setting::where('key', 'wss_socket_port')->first()->value }}";
        let server_address = "{{ \App\Setting::where('key', 'server_address')->first()->value }}";
        let auto_answer = "{{ \App\Setting::where('key', 'auto_answer')->first()->value }}";
        let random_url = "{{ route('agent.random') }}";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script src="{{ asset('js/sip.js') }}" defer></script>
    <script src="{{ asset('js/notify.js') }}" defer></script>
    <script src="{{ asset('js/agent.js') }}" defer></script>
@endpush

