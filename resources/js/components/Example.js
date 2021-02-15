import React, { Component, useEffect, useState, useRef } from 'react';
import ReactDOM from 'react-dom';
import {
    useQuery,
    useMutation,
    useQueryClient,
    QueryClient,
    QueryClientProvider,
} from 'react-query'
import {bargeThisCall, getLiveCalls, getServer, getUserDetails, listenThisCall, whisperThisCall} from "../query"
import Monitor from "./Monitor"
import {NotificationContainer, NotificationManager} from 'react-notifications'

function Example(props) {

    const [listenChannel, setListenChannel] = useState(false)
    const [agent, setAgent] = useState(false)
    const [mode, setMode] = useState(false)
    const [isConnected, setIsConnected] = useState(false);
    const [selfAgent, setSelfAgent] = useState(false);
    const [server, setServer] = useState(false);
    const [data, setData] = useState(false);

    const userQuery = useQuery(['getUser', { authId: props.authId }], getUserDetails, {
        retry: false
    })
    const query = useQuery('liveCalls', getLiveCalls)
    const listenQuery = useQuery(['listenCalls', { channel: listenChannel, agent, mode}], listenThisCall, {
        refetchOnWindowFocus: false,
        enabled: false
    })

    const monitor = useRef(null)

    useEffect(() => {
        if(listenQuery.isSuccess) {
            console.log(listenQuery)
        }
    }, [listenQuery])

    useEffect(() => {
        if(userQuery.isSuccess) {
            setSelfAgent(userQuery.data.data.user)
            setServer(userQuery.data.data.server)
            setData(userQuery.data.data)
        }
    }, [userQuery])

    useEffect(() => {
        if(listenChannel && agent && mode && !isConnected) {
            listenQuery.refetch().then(() => console.log('query fetched'))
        } else {
            NotificationManager.error('Already connected with agent.')
        }
    }, [listenChannel, agent, mode])

    function onStartListen(channel, agent) {
        setListenChannel(channel)
        setAgent(agent)
        setMode("listen")
    }

    function onStartWhisper(channel, agent) {
        setListenChannel(channel)
        setAgent(agent)
        setMode("whisper")
    }

    function onStartBarge(channel, agent) {
        setListenChannel(channel)
        setAgent(agent)
        setMode("barge")
    }

    function onDisconnect() {
        NotificationManager.info('Disconnecting with agent...')
        monitor.current.onCallHangup()
    }

    if(query.isLoading) {
        return (
            <p>Loading...</p>
        )
    } else if(query.isError) {
        return (
            <p>Error: {query.error.message}</p>
        )
    } else if(query.isSuccess) {
        return (
            <div className="card">
                <div className="card-header">
                    {isConnected ? <span className="badge badge-success">Connected</span> : <span className="badge badge-danger">Disconnected</span>}
                </div>
                <div className="card-body">
                    <NotificationContainer />
                    <Monitor data={userQuery.data.data} server={server} agent={selfAgent} ref={monitor} isConnected={isConnected} setIsConnected={setIsConnected} />
                    <div className="table-responsive">
                        <table className="table">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Application</th>
                                <th scope="col">Data</th>
                                <th scope="col">CallerID</th>
                                <th scope="col">Channel</th>
                                <th scope="col">State</th>
                                <th scope="col">Desc</th>
                                <th scope="col">AgentID</th>
                                <th scope="col">Outgoing</th>
                                <th scope="col">Duration</th>
                                <th scope="col">UniqueID</th>
                                <th scope="col">
                                    Actions
                                </th>
                                <th scope="col">
                                    Close
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            {query.data.data.map((res, index) => {
                                return (
                                    <tr key={index}>
                                        <td>{index}</td>
                                        <td>{res.application}</td>
                                        <td>{res.applicationdata}</td>
                                        <td>{res.calleridnum}</td>
                                        <td>{res.channel}</td>
                                        <td>{res.channelstate}</td>
                                        <td>{res.channelstatedesc}</td>
                                        <td>{res.connectedlinename}</td>
                                        <td>{res.connectedlinenum}</td>
                                        <td>{res.duration}</td>
                                        <td>{res.uniqueid}</td>
                                        <td>
                                            <button onClick={() => onStartListen(res.channel, res.connectedlinename)} style={{marginRight: 10}}
                                                    className="btn btn-primary btn-xs btn-block"><i
                                                className="fa fa-fw fa-headset"/> Listen
                                            </button>
                                            <button onClick={() => onStartWhisper(res.channel, res.connectedlinename)} className="btn btn-secondary btn-xs btn-block"><i
                                                className="fa fa-fw fa-assistive-listening-systems"/> Whisper
                                            </button>
                                            <button onClick={() => onStartBarge(res.channel, res.connectedlinename)} className="btn btn-danger btn-xs btn-block"><i
                                                className="fa fa-fw fa-exclamation-triangle"/> Barge
                                            </button>
                                        </td>
                                        <td>
                                            <button disabled={!isConnected} onClick={onDisconnect} className="btn btn-danger btn-block">
                                                <i className="fa fa-fw fa-ban" /> Disconnect
                                            </button>
                                        </td>
                                    </tr>
                                )
                            })}
                            {/*<tr>
                            <th scope="row">1</th>
                            <td>Abdullah</td>
                            <td>PJSIP/1001</td>
                            <td>03403331963</td>
                            <td>
                                <button onClick={onStartListen} style={{ marginRight: 10 }} className="btn btn-primary"><i className="fa fa-fw fa-headset" /> Listen</button>
                                <button style={{ marginRight: 10 }} className="btn btn-secondary"><i className="fa fa-fw fa-assistive-listening-systems" /> Whisper</button>
                                <button className="btn btn-danger"><i className="fa fa-fw fa-exclamation-triangle" /> Barge</button>
                            </td>
                        </tr>*/}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        )
    }
}

export function App(props) {
    const queryClient = new QueryClient()
    return(
        <QueryClientProvider client={queryClient}>
            <Example {...props} />
        </QueryClientProvider>
    )
}

if (document.getElementById('example')) {
    const propsContainer = document.getElementById('example')
    const props = Object.assign({}, propsContainer.dataset);
    ReactDOM.render(
        <App {...props} />,
        document.getElementById('example')
    );
}
