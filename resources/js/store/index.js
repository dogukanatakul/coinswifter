import {createStore, createLogger} from "vuex";
import createPersistedState from "vuex-persistedstate";
import SecureLS from "secure-ls";

const ls = new SecureLS({isCompression: false});

const modulesFiles = require.context('./modules', true, /index.js$/)

const modules = modulesFiles.keys().reduce((modules, modulePath) => {
    const moduleName = modulePath.replace(/^\.\/(.*)\.\w+$/, '$1')
    const value = modulesFiles(modulePath)
    modules[moduleName] = value.default
    return modules
}, {});

export default createStore({
    strict: true,
    modules: modules,
    plugins: [createPersistedState({
        paths: [
            'auth/index',
        ],
        key: 'coinswifter',
        storage: {
            getItem: (key) => ls.get(key),
            setItem: (key, value) => ls.set(key, value),
            removeItem: (key) => ls.remove(key)
        }
    }),
        createLogger()]
})

