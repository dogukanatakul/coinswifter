import axios from "axios";
import {axiosConf} from "../helpers/helpers";
import store from "../store/index"

export default {
    // TODO: A1 API getdata
    async getData(params, data = {}, file = false) {
        let axiosSource = axios.CancelToken.source();
        let response;
        await axios(axiosConf(data, params.Action, axiosSource.token, file),).then((Response) => {
            if (Response.data.error_key !== undefined && Response.data.error_key === 'login') {
                store.dispatch('logout')
            }
            response = Promise.resolve(Response.data)
        }).catch(error => {
            if (axios.isCancel(error)) {
                response = Promise.reject({
                    durum: false,
                    data: ""
                })
            } else {
                response = Promise.reject({
                    durum: false,
                    data: (error.response === undefined ? '' : error.response.statusText)
                })
            }
        })
        return response
    },

};
