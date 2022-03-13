import tr from './tr'
import en from './en'
import {createI18n} from 'vue-i18n'

let userLang = navigator.language || navigator.userLanguage;
if (userLang.indexOf('tr') >= 0) {
    userLang = 'tr'
} else {
    userLang = 'en'
}
window.localStorage.setItem('language', userLang)
export default new createI18n({
    locale: userLang,
    messages: {
        tr,
        en
    }
})
