export default async function auth({next, store, to}) {
    await store.dispatch('checkAuth').then((resp) => {
        if ((to.name === 'signin' || to.name === 'signup') && resp.status === 'success') {
            return next({name: 'wallets'})
        } else if (resp.status === 'success' && resp.step === 'next' && (to.name === 'telephone_verification' || to.name === 'mail_verification' || to.name === 'contract')) {
            return next({name: 'wallets'})
        } else if (resp.status === 'success' && resp.step === 'next') {
            return next()
        } else if (resp.status === 'success' && resp.step === to.name && resp.step !== 'next') {
            return next()
        } else if (resp.status === 'success' && resp.step !== 'next' && to.name === 'profile.contact') {
            return next()
        } else if (resp.status === 'success' && resp.step !== 'next') {
            return next({name: resp.step})
        } else {
            store.dispatch('logout')
        }
    }).catch((err) => {
        if ((to.name === 'signin' || to.name === 'signup') && err.status === 'fail') {
            return next()
        } else {
            return next({name: 'signin'})
        }
    });
}
