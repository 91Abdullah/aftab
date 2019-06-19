

$(document).ready(function () {

    let randomMode = document.getElementById('randomMode');
    let scheduleCall = document.getElementById('scheduleCall');
    let scheduleTable = document.getElementById('scheduleTable');

    let muteBtn = document.getElementById('muteBtn');
    let holdBtn = document.getElementById('holdBtn');
    let holdNotif = document.getElementById('holdNotif');
    let muteNotif = document.getElementById('muteNotif');

    let manualDialBtn = document.getElementById('manualDialBtn');
    let listDialBtn = document.getElementById('listDialBtn');
    let randomDialBtn = document.getElementById('randomDialBtn');
    let registerBtn = document.getElementById('registerBtn');
    let hangupBtn = document.getElementById('hangupBtn');

    var remoteAudio = document.getElementById('remoteAudio');
    var localAudio = document.getElementById('localAudio');

    let phoneStatus = document.getElementById('phone-status');
    let agentStatus = document.getElementById('agent-status');

    let inCallBtns = document.getElementById('inCallBtns');

    let callStatus = document.getElementById('callStatus');
    let callNotif = document.getElementById('callNotif');

    let inputNumber = document.getElementById('inputNumber');

    let mvisible = false;
    let hVisible = false;
    let mins = 0;
    let seconds = 0;
    let timex = undefined;
    let random_mode = false;

    let currentSession = undefined;
    let currentNumber = "";

    // Set error mode

    $.fn.dataTable.ext.errMode = 'none';

    // SIP Status

    /*SessionStatus[SessionStatus["STATUS_NULL"] = 0] = "STATUS_NULL";
    SessionStatus[SessionStatus["STATUS_INVITE_SENT"] = 1] = "STATUS_INVITE_SENT";
    SessionStatus[SessionStatus["STATUS_1XX_RECEIVED"] = 2] = "STATUS_1XX_RECEIVED";
    SessionStatus[SessionStatus["STATUS_INVITE_RECEIVED"] = 3] = "STATUS_INVITE_RECEIVED";
    SessionStatus[SessionStatus["STATUS_WAITING_FOR_ANSWER"] = 4] = "STATUS_WAITING_FOR_ANSWER";
    SessionStatus[SessionStatus["STATUS_ANSWERED"] = 5] = "STATUS_ANSWERED";
    SessionStatus[SessionStatus["STATUS_WAITING_FOR_PRACK"] = 6] = "STATUS_WAITING_FOR_PRACK";
    SessionStatus[SessionStatus["STATUS_WAITING_FOR_ACK"] = 7] = "STATUS_WAITING_FOR_ACK";
    SessionStatus[SessionStatus["STATUS_CANCELED"] = 8] = "STATUS_CANCELED";
    SessionStatus[SessionStatus["STATUS_TERMINATED"] = 9] = "STATUS_TERMINATED";
    SessionStatus[SessionStatus["STATUS_ANSWERED_WAITING_FOR_PRACK"] = 10] = "STATUS_ANSWERED_WAITING_FOR_PRACK";
    SessionStatus[SessionStatus["STATUS_EARLY_MEDIA"] = 11] = "STATUS_EARLY_MEDIA";
    SessionStatus[SessionStatus["STATUS_CONFIRMED"] = 12] = "STATUS_CONFIRMED";*/

    //

    // User Agent

    let userAgent = new SIP.UA({
        uri: authUser + '@' + server_address + ':' + wss_comm_port,
        transportOptions: {
            wsServers: ['wss://' + server_address +':' + wss_socket_port + '/ws'],
            traceSip: false
        },
        authorizationUser: authUser,
        password: authUser,
        displayName: authUser,
        register: false,
        iceCheckingTimeout: 0.5
    });

    //startTimer();
    //startUserAgent();

    // Schedule Calls

    let table = $('#scheduleTable').DataTable({
        processsing: true,
        serverSide: true,
        ajax: get_calls,
        destroy: true,
        searching: false,
        paging: false,
        columns: [
            {data: 'number'},
            {data: 'status'},
            {data: 'schedule_time'},
            {data: 'updated_at'},
        ]
    })
        .on('error.dt', function (e, settings, helpPage, message) {
            $.notify(message, "error");
        });

    let cdrTable = $('#cdrTable').DataTable({
        processsing: true,
        serverSide: true,
        ajax: {
            url: recent_calls,
            data: {
                user_id: authUser
            }
        },
        destroy: true,
        searching: false,
        paging: false,
        columns: [
            {data: 'dst'},
            {data: 'clid'},
            {data: 'start'},
            {data: 'answer'},
            {data: 'end'},
            {data: 'duration'},
            {data: 'disposition'},
        ]
    });

    setInterval(tableReload, 10000);

    // Functions

    function tableReload() {
        table.ajax.reload();
        cdrTable.ajax.reload();
    }

    async function scheduleThisCall() {

        if(currentSession === undefined || inputNumber.value === "") {
            $.notify("You can't schedule an empty number.", "error");
            return;
        }

        const {value: formValues} = await Swal.fire({
            title: 'Schedule This Call',
            html: '<input id="datepicker" class="form-control" type="text">',
            showConfirmButton: true,
            customClass: 'swal2-overflow',
            preConfirm: () => {
                return document.getElementById('datepicker').value;
            },
            onOpen: () => {
                let config = {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                };
                $('#datepicker').flatpickr(config);
            }
        });

        if (formValues) {
            Swal.fire(JSON.stringify(formValues));
            axios.post(schedule_call, {
                schedule_time: formValues,
                user_id: user_id,
                number: inputNumber.value
            })
            .then((response) => {
                $.notify(response.data.success, "info");
                tableReload();
            })
            .catch((error) => {
                console.log(error);
            })
        }
    }

    function randomModeChange(event) {
        if(event.target.checked) {
            manualDialBtn.style.display = "none";
            listDialBtn.style.display = "none";
            randomDialBtn.style.display = "none";
            inputNumber.disabled = true;
            startRandomMode();
        } else {
            manualDialBtn.style.display = "inline-block";
            listDialBtn.style.display = "inline-block";
            randomDialBtn.style.display = "inline-block";
            inputNumber.disabled = false;
            stopRandomMode();
        }
    }

    function startRandomMode() {
        random_mode = true;
        $.notify("Starting Random dialing mode...", "success");
        dialRandomCall();
    }

    function stopRandomMode() {
        random_mode = false;
        $.notify("Stopped Random dialing mode...", "danger");
    }

    function incomingNotif(session) {
        //console.log(session);
        Swal.fire({
            title: 'You have an incoming call',
            text: session.remoteIdentity.displayName,
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Accept",
            cancelButtonText: "Reject"
        }).then((result) => {
            console.log(result);
            if(result.value) {
                session.accept({
                    sessionDescriptionHandlerOptions: {
                        constraints: {
                            audio: true,
                            video: false
                        }
                    }
                });

                Swal.fire("Accepted", "Call has been accepted", "success");

            } else if(result.dismiss === "cancel") {
                session.reject();
                Swal.fire("Rejected", "Call has been rejected", "error");
            } else {

            }
        });

        /*swal({
            title: "You have an incoming call",
            text:  session.remoteIdentity,
            icon: "warning",
            buttons: true,
            dangerMode: true
        })
            .then((status) => {
                if(status) {



                    swal("Call has been accepted.", {
                        icon: "success"
                    })
                } else {

                    session.reject();

                    swal("Call has been rejected.")
                }
            });*/
    }

    function startUserAgent() {
        userAgent.register();
    }

    function stopUserAgent() {
        userAgent.unregister();
    }

    function resetTimer() {
        mins =0;
        seconds =0;
        $('#mins').html('00:');
        $('#seconds').html('00');
    }

    function startTimer(){
        timex = setTimeout(function(){
            seconds++;
            if(seconds >59){seconds=0;mins++;
                if(mins<10){
                    $("#mins").text('0'+mins+':');}
                else $("#mins").text(mins+':');
            }
            if(seconds <10) {
                $("#seconds").text('0'+seconds);} else {
                $("#seconds").text(seconds);
            }


            startTimer();
        },1000);
    }

    function stopTimer() {
        clearTimeout(timex);
    }

    // User Interface Functions

    function disableAllDialBtns() {
        manualDialBtn.disabled = true;
        listDialBtn.disabled = true;
        randomDialBtn.disabled = true;
    }

    function enableAllDialBtns() {
        manualDialBtn.disabled = false;
        listDialBtn.disabled = false;
        randomDialBtn.disabled = false;
    }

    function changeToRegisteredState()
    {
        registerBtn.classList.remove('btn-success');
        registerBtn.classList.add('btn-danger');
        registerBtn.innerHTML = "<i class=\"fas fa-window-close\"></i> Unregister";

        agentStatus.classList.add('text-success');
        agentStatus.classList.remove('text-danger')
        agentStatus.innerHTML = "<i class=\"fas fa-user-plus\"></i> ONLINE";
    }

    function changeToUnRegisteredState()
    {
        registerBtn.classList.add('btn-danger');
        registerBtn.classList.remove('btn-success');
        registerBtn.innerHTML = "<i class=\"fas fa-users-cog\"></i> Register";

        agentStatus.classList.add('text-danger');
        agentStatus.classList.remove('text-success');
        agentStatus.innerHTML = "<i class=\"fas fa-sign-in-alt\"></i> OFFLINE";
    }

    function changeToConnectedState()
    {
        phoneStatus.innerHTML = "<i class='fas fa-plug'></i> CONNECTED";
        phoneStatus.classList.add('text-success');
        phoneStatus.classList.remove('text-danger');
    }

    function changeToDisconnectedState()
    {
        phoneStatus.innerHTML = "<i class='fas fa-power-off'></i> DISCONNECTED";
        phoneStatus.classList.add('text-danger');
        phoneStatus.classList.remove('text-success');
    }

    function showInCallBtns()
    {
        inCallBtns.style.display = "block";
    }

    function hideInCallBtns()
    {
        inCallBtns.style.display = "none";
    }

    function changeCallAcceptedState()
    {
        inCallBtns.style.display = "block";
        callStatus.innerText = "CONNECTED";
        callNotif.style.display = "block";
        startTimer();
        disableAllDialBtns();

    }

    function changeCallDialingStatus()
    {
        callStatus.innerText = "DIALING...";
    }

    function changeCallTerminatedState()
    {
        inCallBtns.style.display = "none";
        callStatus.innerText = "DISCONNECTED";
        callNotif.style.display = "none";
        muteNotif.classList.add('hide');
        holdNotif.classList.add('hide');
        stopTimer();
        resetTimer();
        enableAllDialBtns();
        Swal.close();
    }

    function dialExternalCall()
    {
        if(inputNumber.value.length === 0 || isNaN(inputNumber.value)) {
            $.notify("You can't dial an empty or invalid number.", "error");
            return;
        }

        let session = userAgent.invite(inputNumber.value + '@' + server_address, {
            sessionDescriptionHandlerOptions: {
                constraints: {
                    audio: true,
                    video: false
                }
            }
        });

        console.log(session);

        currentSession = session;

        session.on('trackAdded', function() {
            // We need to check the peer connection to determine which track was added

            var pc = session.sessionDescriptionHandler.peerConnection;

            // Gets remote tracks
            var remoteStream = new MediaStream();
            pc.getReceivers().forEach(function(receiver) {
                remoteStream.addTrack(receiver.track);
            });
            remoteAudio.srcObject = remoteStream;
            remoteAudio.play();

            // Gets local tracks
            var localStream = new MediaStream();
            pc.getSenders().forEach(function(sender) {
                localStream.addTrack(sender.track);
            });
            localAudio.srcObject = localStream;
            localAudio.play();
        });

        session.on('accepted', () => {
            changeCallAcceptedState();
        });

        session.on('terminated', () => {
            changeCallTerminatedState();
        });

        session.on('bye', (request) => {
            console.log(request);
        });
    }

    function dialRandomCall()
    {
        let number = "";
        axios.get(random_url)
            .then(function (response) {
                number = response.data;
                if(number.length === 0 || isNaN(number)) {
                    $.notify("You can't dial an empty or invalid number.", "error");
                    return;
                }

                $.notify("Number selected from database: " + number, "info");
                inputNumber.value = number;

                let session = userAgent.invite(number + '@' + server_address, {
                    sessionDescriptionHandlerOptions: {
                        constraints: {
                            audio: true,
                            video: false
                        }
                    }
                });

                console.log(session);

                currentSession = session;

                session.on('trackAdded', function() {
                    // We need to check the peer connection to determine which track was added

                    var pc = session.sessionDescriptionHandler.peerConnection;

                    // Gets remote tracks
                    var remoteStream = new MediaStream();
                    pc.getReceivers().forEach(function(receiver) {
                        remoteStream.addTrack(receiver.track);
                    });
                    remoteAudio.srcObject = remoteStream;
                    remoteAudio.play();

                    // Gets local tracks
                    var localStream = new MediaStream();
                    pc.getSenders().forEach(function(sender) {
                        localStream.addTrack(sender.track);
                    });
                    localAudio.srcObject = localStream;
                    localAudio.play();
                });

                session.on('accepted', () => {
                    changeCallAcceptedState();
                });

                session.on('terminated', () => {
                    changeCallTerminatedState();
                    if(random_mode && random_mode === true) {
                        dialRandomCall();
                    }
                });

                session.on('bye', (request) => {
                    console.log(request);
                });
            })
            .catch(function (error) {

            });
    }

    // End User Interface Functions

    // Events


    //***** UA Events *******//

    userAgent.on('registered', () => {
        changeToRegisteredState();
    });

    userAgent.on('unregistered', (response, cause) => {
        if(cause === undefined) {
            $.notify("Unregistered from server.");
        } else {
            $.notify("Registration failed with error: " + cause + " and response: " + response, "error");
        }
        changeToUnRegisteredState();
    });

    userAgent.transport.on('connected', () => {
        changeToConnectedState();
    });

    userAgent.transport.on('disconnected', () => {
        changeToDisconnectedState();
    });

    userAgent.transport.on('transportError', () => {

        // Swal.fire({
        //     title: "Add Exception",
        //     text: "Please click following link and add exception to certificate authority",
        //     type: "error",
        //     backdrop: false,
        //     allowOutsideClick: false,
        //     allowEscapeKey: false,
        //     allowEnterKey: false,
        //     confirmButtonColor: '#3085d6',
        //     cancelButtonColor: '#d33',
        //     confirmButtonText: 'Click Here'
        // }).then((result) => {
        //     if(result.value) {
        //         window.open("https://" + server_address + ":" + wss_socket_port + "/httpstatus", "_blank");
        //     }
        // });


    });

    userAgent.on('invite', (session) => {

        console.log(session);

        currentSession = session;

        session.on('trackAdded', function() {
            // We need to check the peer connection to determine which track was added

            var pc = session.sessionDescriptionHandler.peerConnection;

            // Gets remote tracks
            var remoteStream = new MediaStream();
            pc.getReceivers().forEach(function(receiver) {
                remoteStream.addTrack(receiver.track);
            });
            remoteAudio.srcObject = remoteStream;
            remoteAudio.play();

            // Gets local tracks
            var localStream = new MediaStream();
            pc.getSenders().forEach(function(sender) {
                localStream.addTrack(sender.track);
            });
            localAudio.srcObject = localStream;
            localAudio.play();
        });

        incomingNotif(session);

        session.on('accepted', () => {
            changeCallAcceptedState();
        });

        session.on('terminated', () => {
            changeCallTerminatedState();
        });

    });

    //***** End UA Events *******//

    scheduleCall.onclick = function(event) {
        scheduleThisCall();
    };

    randomMode.onchange = function(event) {
        randomModeChange(event);
    };

    randomDialBtn.onclick = function(event) {
        changeCallDialingStatus();
        dialRandomCall();
    };

    registerBtn.onclick = function(event) {
        if(userAgent.isRegistered()) {
            stopUserAgent();
        } else {
            startUserAgent();
        }
    };

    hangupBtn.onclick = function (event) {
        if(currentSession !== undefined) {
            if(currentSession.request instanceof SIP.OutgoingRequest && !currentSession.hasAnswer) {
                currentSession.cancel();
            } else {
                currentSession.bye();
            }
        }
    };

    muteBtn.onclick = function (event) {
        if(mvisible) {
            muteNotif.classList.add('hide');

            ///mute
            var mutePc = currentSession.sessionDescriptionHandler.peerConnection;
            mutePc.getLocalStreams().forEach(function (stream) {
                stream.getAudioTracks().forEach(function (track) {
                    track.enabled = true;
                });
            });

        } else {
            muteNotif.classList.remove('hide');

            ///mute
            var unmutePc = currentSession.sessionDescriptionHandler.peerConnection;
            unmutePc.getLocalStreams().forEach(function (stream) {
                stream.getAudioTracks().forEach(function (track) {
                    track.enabled = false;
                });
            });

        }
        mvisible = !mvisible;
    };

    holdBtn.onclick = function (event) {
        if(hVisible) {
            holdNotif.classList.add('hide');
            currentSession.unhold();
        } else {
            holdNotif.classList.remove('hide');
            currentSession.hold();
        }
        hVisible = !hVisible;
    };

    manualDialBtn.onclick = function (event) {
        changeCallDialingStatus();
        dialExternalCall();
    };

    window.addEventListener('keypress', function (e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            dialExternalCall();
        }
    }, false);

});
