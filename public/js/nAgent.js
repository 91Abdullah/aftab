

$(document).ready(function () {

    // Transfer Feature
    let transferDial = document.getElementById('transferDial');
    let transferDialBtns = document.getElementById('transferDialBtns');
    let transferBtns = document.getElementById('transferBtns');
    let transferCall = document.getElementById('transferCall');
    let hangupTransferCall = document.getElementById('hangupTransferCall');
    let openAttendedTransfer = document.getElementById('openAttendedTransfer');

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
    let readyBtn = document.getElementById('readyBtn');
    let hangupBtn = document.getElementById('hangupBtn');

    var remoteAudio = document.getElementById('remoteAudio');
    var localAudio = document.getElementById('localAudio');

    let phoneStatus = document.getElementById('phone-status');
    let agentStatus = document.getElementById('agent-status');
    let userStatus = document.getElementById('agent-qstatus')

    let inCallBtns = document.getElementById('inCallBtns');

    let callStatus = document.getElementById('callStatus');
    let callNotif = document.getElementById('callNotif');

    let inputNumber = document.getElementById('inputNumber');

    let mvisible = false;
    let hVisible = false;
    let mins = 0;
    let seconds = 0;
    let tmins = 0;
    let tseconds = 0;
    let transferTimex = undefined;
    let timex = undefined;
    let random_mode = true;

    let currentSession = undefined;
    let transferSession = undefined;
    let currentNumber = "";
    let callID = undefined;

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

    let userStatusValue = true

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
        sessionDescriptionHandlerOptions: {
            peerConnectionOptions: {
                iceCheckingTimeout: 200,
            }
        }
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

    function transferThisCall() {
        if(currentSession !== undefined && transferSession !== undefined) {
            currentSession.refer(transferSession);
        }
    }

    function DialTransferCall() {
        let number = document.getElementById('transferNumber');
        if(number.length === 0 && isNaN(number.value) && currentSession === undefined) {
            $.notify("Please enter number to dial or you do not have any active call.", "error");
            return;
        }

        let session = userAgent.invite(number.value + '@' + server_address, {
            sessionDescriptionHandlerOptions: {
                constraints: {
                    audio: true,
                    video: false
                },
                extraHeaders: [
                    'X-User-Field: ' + create_UUID()
                ]
            },
        });

        console.log(`Call transfer initiated: ${session}`);

        transferSession = session;

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
            changeTransferBtns(true);
        });

        session.on('terminated', () => {
            changeTransferBtns(false);
            transferSession = undefined;
        });

        session.on('bye', (request) => {
            console.log('Bye Request received');
        });
    }

    function changeTransferBtns(mode) {
        if(mode) {
            startTransferTimer();
            transferDialBtns.style.display = 'none';
            transferBtns.style.display = 'block';
        } else {
            stopTransferTimer();
            resetTransferTimer();
            transferDialBtns.style.display = 'block';
            transferBtns.style.display = 'none';
            $("#attendedTransfer").modal('hide');
        }
    }

    function resetTransferTimer() {
        tmins =0;
        tseconds =0;
        $('#tmins').html('00:');
        $('#tseconds').html('00');
    }

    function startTransferTimer(){
        transferTimex = setTimeout(function(){
            tseconds++;
            if(tseconds >59){tseconds=0;tmins++;
                if(tmins<10){
                    $("#tmins").text('0'+tmins+':');}
                else $("#tmins").text(tmins+':');
            }
            if(tseconds <10) {
                $("#tseconds").text('0'+tseconds);} else {
                $("#tseconds").text(tseconds);
            }


            startTransferTimer();
        },1000);
    }

    function stopTransferTimer() {
        clearTimeout(transferTimex);
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
                    console.log(`Error in submitting workcode: ${error}`);
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
            console.log(`Incoming call received: ${result}`);
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
    }

    function readyUser() {

        axios.post(readyRoute, {
            agent: authUser,
            _token: token
        }).then(response => {
            $.notify(response.data.message)
            userStatusValue = true
            readyUI()
        }).catch(error => {
            $.notify(error.message, "error")
            $.notify(error.response.data.message, "error")
        })
    }

    function notReadyUser() {

        axios.post(notReadyRoute, {
            agent: authUser,
            _token: token
        }).then(response => {
            $.notify(response.data.message)
            userStatusValue = false
            notReadyUI()
        }).catch(error => {
            $.notify(error.message, "error")
            $.notify(error.response.data.message)
        })
    }

    function readyUI() {
        readyBtn.classList.remove('btn-success');
        readyBtn.classList.add('btn-danger');
        readyBtn.innerHTML = "<i class='fas fa-toggle-on'></i> Not-Ready"

        userStatus.innerHTML = "<i class='fas fa-power-off'></i> READY"
        userStatus.classList.remove('text-danger');
        userStatus.classList.add('text-success');
    }

    function notReadyUI() {
        readyBtn.classList.remove('btn-danger');
        readyBtn.classList.add('btn-success');
        readyBtn.innerHTML = "<i class='fas fa-power-off'></i> Ready"

        userStatus.innerHTML = "<i class='fas fa-power-off'></i> NOT-READY"
        userStatus.classList.remove('text-success');
        userStatus.classList.add('text-danger')
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
        if(random_mode) {
            listDialBtn.disabled = true;
            randomDialBtn.disabled = true;
        }
    }

    function enableAllDialBtns() {
        manualDialBtn.disabled = false;
        if(random_mode) {
            listDialBtn.disabled = false;
            randomDialBtn.disabled = false;
        }
    }

    function changeToUserStatusState() {
        axios.post(agentQStatus, {
            _token: token,
            agent: authUser,
        }).then(response => {
            userStatusValue = response.data.message.paused === "0"
            if(userStatusValue) {
                readyUI()
            } else {
                notReadyUI()
            }
        })
            .catch(error => {
                $.notify(error.message, "error")
                $.notify(error.response.data.message, "error")
            })
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

    function addCallerIdToTextBox(session = null) {
        if(session) {
            inputNumber.value = session.remoteIdentity.displayName
            inputNumber.disabled = true
        } else {
            inputNumber.disabled = false
        }
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

    async function showCodeForm() {
        if(Object.keys(JSON.parse(codes)).length === 0) {
            return;
        }
        const {value: code} = await Swal.fire({
            title: 'Select status field',
            input: 'select',
            inputOptions: JSON.parse(codes),
            inputPlaceholder: 'Select a Code',
            showCancelButton: false,
            backdrop: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showLoaderOnConfirm: true,
            preConfirm: (code) => {
                if(!code)
                    return false;
                axios
                    .post(response_route, {
                        _token: token,
                        call_id: callID,
                        code: code
                    })
                    .then((response) => {
                        $.notify(response.data.success, "success");
                    })
                    .catch((error) => {
                        $.notify(error, "error");
                        //console.log(error);
                    });
            }
        });
    }

    function create_UUID(){
        var dt = new Date().getTime();
        var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = (dt + Math.random()*16)%16 | 0;
            dt = Math.floor(dt/16);
            return (c==='x' ? r :(r&0x3|0x8)).toString(16);
        });
        return uuid;
    }

    function getListNumber()
    {
        return axios.get(list_url);
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
                },
                extraHeaders: [
                    'X-User-Field: ' + create_UUID()
                ]
            },
        });

        console.log(`External call dialed: ${session}`);

        currentSession = session;
        callID = session.request.callId;

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
            showCodeForm()
                .finally(() => {
                    if(random_mode && random_mode === true) {
                        dialRandomCall();
                    }
                });
            currentSession = undefined;
        });

        session.on('bye', (request) => {
            console.log(`Bye on external call: ${request}`);
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
                callID = session.request.callId;

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
                    showCodeForm()
                        .finally(() => {
                            if(random_mode && random_mode === true) {
                                dialRandomCall();
                            }
                        });
                    currentSession = undefined;
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
        changeToUserStatusState();
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

    userAgent.transport.on('transportError', (error) => {
        $.notify("Error connecting to websocket. Add certificate or no connection could be made with server")
    });

    userAgent.on('invite', (session) => {

        /*console.log(`Incoming call received: ${session}`);*/

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
                //localStream.addTrack(sender.track);
            });
            localAudio.srcObject = localStream;
            localAudio.play();
        });

        incomingNotif(session);

        session.on('accepted', () => {
            changeCallAcceptedState();
            addCallerIdToTextBox(session);
        });

        session.on('terminated', () => {
            changeCallTerminatedState();
            addCallerIdToTextBox();
            showCodeForm()
                .finally(() => {
                    if(random_mode && random_mode === true) {
                        dialRandomCall();
                    }
                });
            currentSession = undefined;
        });

    });

    //***** End UA Events *******//

    //***** Start Btn Events ****//

    openAttendedTransfer.onclick = (event) => {
        if(currentSession !== undefined) {
            $('#attendedTransfer').modal('show');
        } else {
            $.notify("You do not have any active call.", "error");
        }
    };

    transferCall.onclick = (event) => {
        transferThisCall();
    };

    transferDial.onclick = (event) => {
        DialTransferCall();
    };

    scheduleCall.onclick = function(event) {
        scheduleThisCall();
    };

    if(random_mode) {
        randomMode.onchange = function(event) {
            randomModeChange(event);
        };

        randomDialBtn.onclick = function(event) {
            changeCallDialingStatus();
            dialRandomCall();
        };

        listDialBtn.onclick = function (event) {
            getListNumber()
                .then((response) => {
                    console.log(response);
                    inputNumber.value = response.data;
                    dialExternalCall();
                })
                .catch((error) => {
                    $.notify(error.response.data, "error");
                });
        };
    }

    registerBtn.onclick = function(event) {
        if(userAgent.isRegistered()) {
            stopUserAgent();
        } else {
            startUserAgent();
        }
    };

    readyBtn.onclick = function(event) {
        if(userAgent.isRegistered()) {
            if(userStatusValue) {
                notReadyUser()
            } else {
                readyUser()
            }
        } else {
            $.notify("You need to first register with server.", "error")
        }
    }

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
        if(currentSession !== undefined) {
            if(hVisible) {
                holdNotif.classList.add('hide');
                currentSession.unhold();
            } else {
                holdNotif.classList.remove('hide');
                currentSession.hold();
            }
            hVisible = !hVisible;
        }
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

    // Transfer Events

    $("#attendedTransfer")
        .on("hide.bs.modal", function (e) {
            if(transferSession !== undefined) {
                transferSession.bye();
            }
        })
        .on("show.bs.modal", function (e) {
            if(currentSession !== undefined) {
                let direction = currentSession.sessionDescriptionHandler.getDirection();
                if(direction === 'sendrecv') {
                    holdBtn.click();
                } else {
                    $.notify("Call is already on hold.", "info");
                }
            }
        })
});
