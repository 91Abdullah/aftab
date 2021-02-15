export const getLiveCalls = () => {
    return window.axios.get('/live/show-channels')
}

export const getUserDetails = ({ queryKey }) => {
    const [_key, { authId }] = queryKey
    return window.axios.get('/live/get-user/' + authId)
}

export const getServer = () => {
    return window.axios.get('/live/get-server')
}

export const listenThisCall = async ({ queryKey }) => {
    const [_key, { channel, agent, mode, selfAgent }] = queryKey
    const { data } = await window.axios.post(`/live/listen`, {
        channel,
        agent,
        mode,
        selfAgent
    })
    return data
}
