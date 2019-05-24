@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/animate.css') }}">
@endpush

@section('content')

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        DIALER
                        <span class="float-right">
                            <button id="registerBtn" class="btn btn-danger"><i class="fas fa-users-cog"></i> Register</button>
                        </span>
                    </div>

                    <div class="card-body">
                        <form>
                            <div class="col-6 offset-lg-3 mb-4">
                                <div class="row text-center">
                                    <div class="col-6">
                                        <span id="call-status" class="p-2 text-success font-weight-bold">
                                            <i class="fas fa-plug"></i> CONNECTED
                                        </span>
                                    </div>
                                    <div class="col-6">
                                        <span id="agent-status" class="p-2 text-success font-weight-bold">
                                            <i class="fas fa-signal"></i> ONLINE
                                        </span>
                                    </div>
                                    <audio id="remoteAudio"></audio>
                                    <audio id="localAudio" muted="muted"></audio>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 offset-lg-3 text-center font-weight-bolder p-5 border-white rounded bg-light rounded">
                                    <h6>
                                        <span id="callStatus">CONNECTED</span>

                                    </h6>
                                    <h4 class="">
                                        03432877933
                                    </h4>
                                    <h6>
                                        <span id="mins">00:</span><span id="seconds">00</span>

                                    </h6>
                                    <span id="holdNotif" class="float-left animated infinite fadeIn delay-1s text-danger font-weight-bold hide">
                                        <i class="fas fa-circle"></i> HOLD
                                    </span>
                                    <span id="muteNotif" class="float-right animated infinite fadeIn delay-1s text-danger font-weight-bold hide">
                                        <i class="fas fa-circle"></i> MUTE
                                    </span>
                                </div>
                            </div>
                            <div class="row text-center mt-4 mb-4">
                                <div class="col-6 offset-lg-3">
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
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script src="{{ asset('js/agent.js') }}" defer></script>
    <script src="{{ asset('js/sip.js') }}" defer></script>
@endpush
