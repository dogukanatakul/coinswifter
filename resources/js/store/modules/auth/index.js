import restAPI from "../../../api/restAPI";
import {notify} from "@kyvg/vue3-notification";
import router from '../../../router/index'

const state = {
    user: false,
    darkMode: false,
}

const getters = {
    user: state => {
        return state.user
    },
    darkMode: state => {
        return state.darkMode
    },
}


const mutations = {
    USER(state, payload) {
        state.user = payload
    },
    DARK_MODE(state, payload) {
        state.darkMode = payload
    },
}

const actions = {
    async checkAuth(context) {
        let resp;
        await restAPI.getData({
            Action: 'user'
        }).then((response) => {
            if (response.status === 'success') {
                context.commit('USER', response.user)
                resp = Promise.resolve(response)
            } else {
                context.dispatch('logout')
                resp = Promise.reject(response)
            }
        }).catch((error) => {
            resp = Promise.reject(error)
        })
        return resp
    },
    async logout(context) {
        if (context.getters.user) {
            await restAPI.getData({
                Action: 'logout'
            }).then((response) => {
                if (response.status === 'success') {
                    context.commit('USER', false)
                    notify({text: 'Başarıyla çıkış yapıldı.', type: 'success'})
                    location.reload()
                } else {
                    notify({text: 'Bir sorun oluştu.', type: 'error'})
                }
            }).catch(() => {
                notify({text: 'Bir sorun oluştu.', type: 'error'})
            })
        }
        return "success"
    },
    async actionControl(context) {
        var response = true
        if (!context.getters.user) {
            notify({text: 'Bu işlemi yapmak için oturum açmalısınız.'})
            router.push({name: "signin"});
            response = false
        }
        return response
    },
    changeDarkMode(context) {
        context.commit('DARK_MODE', !context.getters.darkMode)
    }
}


export default {
    state,
    getters,
    actions,
    mutations,
}
