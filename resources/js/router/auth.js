import auth from "../middleware/auth";

export default [
    {
        path: "/signin",
        name: "signin",
        component: () => import ("../views/auth/Signin.vue"),
        meta: {
            middleware: [
                auth
            ],
            title: 'Giriş Yap',
        },
    },
    {
        path: "/signup",
        name: "signup",
        component: () => import ("../views/auth/Signup.vue"),
        meta: {
            middleware: [
                auth
            ],
            title: 'Kayıt Ol',
        },
    },
    {
        path: '/profile',
        redirect: "/profile/banks",
        name: 'profile',
        component: () => import ("../views/profile/Profile.vue"),
        children: [
            {
                path: '/profile/adress',
                name: 'profile.adress',
                component: import ("../views/profile/Profile/Adress.vue"),
                meta: {
                    middleware: [
                        auth
                    ]
                },
            },
            {
                path: '/profile/referer',
                name: 'profile.referer',
                component: import ("../views/profile/Profile/Referer.vue"),
                meta: {
                    middleware: [
                        auth
                    ]
                },
            },
            {
                path: '/profile/contact',
                name: 'profile.contact',
                component: import ("../views/profile/Profile/Contact.vue"),
                meta: {
                    middleware: [
                        auth
                    ]
                },
            },
            {
                path: '/profile/kyc',
                name: 'profile.kyc',
                component: import ("../views/profile/Profile/KYC.vue"),
                meta: {
                    middleware: [
                        auth
                    ]
                },
            },
            {
                path: '/profile/password',
                name: 'profile.password',
                component: import ("../views/profile/Profile/Password.vue"),
                meta: {
                    middleware: [
                        auth
                    ]
                },
            },
            {
                path: '/profile/banks',
                name: 'profile.banks',
                component: import ("../views/profile/Profile/Banks.vue"),
                meta: {
                    middleware: [
                        auth
                    ]
                },
            },
            {
                path: '/profile/sessions',
                name: 'profile.sessions',
                component: import ("../views/profile/Profile/Sessions.vue"),
                meta: {
                    middleware: [
                        auth
                    ]
                },
            },
            {
                path: '/profile/ticket',
                name: 'profile.ticket',
                component: import ("../views/profile/Profile/Tickets.vue"),
                meta: {
                    middleware: [
                        auth
                    ]
                },
            },
        ]
    },
    {
        path: "/wallets",
        name: "wallets",
        component: () => import ("../views/profile/Wallet.vue"),
        meta: {
            middleware: [
                auth
            ],
            title: 'Cüzdanlar',
        },
    },
    {
        path: "/wallets_new",
        name: "wallets_new",
        component: () => import ("../views/profile/Wallet_New.vue"),
        meta: {
            middleware: [
                auth
            ],
            title: 'Cüzdanlar',
        },
    },
    {
        path: "/telephone-verification",
        name: "telephone_verification",
        component: () =>
            import ("../views/auth/Verification.vue"),
        meta: {
            middleware: [
                auth
            ],
            title: 'Telefon Doğrulama',
        },
    },
    {
        path: "/mail-verification",
        name: "mail_verification",
        component: () =>
            import ("../views/auth/Verification.vue"),
        meta: {
            middleware: [
                auth
            ],
            title: 'Mail Doğrulama',
        },
    },
    {
        path: "/contract",
        name: "contract",
        component: () =>
            import ("../views/auth/Contract.vue"),
        meta: {
            middleware: [
                auth
            ],
            title: 'Sözleşme Onaylama',
        },
    },
]


