export const getLiveCalls = () => {
    return window.axios.get('/foresight/public/live/show-channels')
}

export const getUserDetails = ({ queryKey }) => {
    const [_key, { authId }] = queryKey
    return window.axios.get('/foresight/public/live/get-user/' + authId)
}

export const getServer = () => {
    return window.axios.get('/foresight/public/live/get-server')
}

export const listenThisCall = async ({ queryKey }) => {
    const [_key, { channel, agent, mode }] = queryKey
    console.log(channel, agent, mode)
    const { data } = await window.axios.post(`/foresight/public/live/listen`, {
        channel,
        agent,
        mode
    })
    return data
}
