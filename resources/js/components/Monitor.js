import { Web } from "sip.js"
import React, { useEffect, useState, forwardRef, useImperativeHandle } from "react"
import 'react-notifications/lib/notifications.css'
import {NotificationContainer, NotificationManager} from 'react-notifications'

const Monitor = forwardRef((props, ref) => {

    const { isConnected, setIsConnected, agent, server, data } = props

    useImperativeHandle(ref, () => ({
        onCallHangup
    }))

    const [simpleAgent, setSimpleAgent] = useState(false);

    function getAudioElement(id) {
        const el = document.getElementById(id)
        if (!(el instanceof HTMLAudioElement)) {
            throw new Error(`Element "${id}" not found or not an audio element.`);
        }
        return el
    }

    async function onCallHangup() {
        await simpleAgent.hangup()
    }

    useEffect(() => {
        if(data) {
            //console.log(agent, server)
            const options = {
                aor: `sip:${data.user}@${data.server}`,
                media: {
                    constraints: { audio: true, video: false },
                    remote: { audio: getAudioElement("remoteAudio") }
                },
                userAgentOptions: {
                    authorizationUser: data.user,
                    authorizationPassword: data.user
                }
            }

            const server = `wss://${data.server}:8089/ws`
            const simpleUser = new Web.SimpleUser(server, options)

            simpleUser.delegate = {
                onCallReceived: async () => {
                    console.log('call received')
                    await simpleUser.answer()
                },
                onCallAnswered: async () => {
                    setIsConnected(true)
                },
                onCallHangup: async () => {
                    setIsConnected(false)
                }

            }

            simpleUser.connect().then(() => {
                console.log('simple user connected.')
                simpleUser.register().then(() => NotificationManager.info('Monitoring agent registered.'))
                    .catch(err => NotificationManager.error(err.message))
                setSimpleAgent(simpleUser)
                NotificationManager.success('Monitoring agent connected.')
            }).catch(err => NotificationManager.error(err.message))
        }
    }, [data])

    return(
        <>
            <NotificationContainer />
            <audio id="remoteAudio">
                <p>Your browser doesn't support HTML5 audio.</p>
            </audio>
        </>
    )
})

export default Monitor
