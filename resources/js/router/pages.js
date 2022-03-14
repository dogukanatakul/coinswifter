import auth from "../middleware/auth";

export default [
    {
        path: "/",
        name: "home",
        component: () => import ("../views/Home.vue"),
        meta: {
            title: 'Ana Sayfa',
        },
    },

    {
        path: "/exchange/:parity(.*)*",
        name: "exchange",
        // redirect: to => {
        //     return {path: '/exchange', query: {q: to.params.source}}
        // },
        component: () => import ("../views/Exchange.vue"),
    },
    {
        path: "/exchange_new/:parity(.*)*",
        name: "exchange_new",
        // redirect: to => {
        //     return {path: '/exchange', query: {q: to.params.source}}
        // },
        component: () => import ("../views/Exchange_New.vue"),
    },
    {
        path: "/markets",
        name: "markets",
        component: () =>
            import ("../views/Markets.vue"),
        meta: {
            title: 'Pazar',
        },
    },
    {
        path: "/forgot-password",
        name: "forgot",
        component: () =>
            import ("../views/auth/Forgot.vue"),
    },
    {
        path: '/page',
        redirect: "/coin-startup",
        component: () => import ("../views/pages/Page.vue"),
        children: [

            {
                path: "/coin-startup",
                name: "CoinStartup",
                component: () =>
                    import ("../views/pages/CoinStartup.vue"),
            },
            {
                path: "/cookie_pages",
                name: "CookiePolicy",
                component: () =>
                    import ("../views/pages/CookiePolicy.vue"),
            },
            {
                path: "/privacy_pages",
                name: "PrivacyPolicy",
                component: () =>
                    import ("../views/pages/PrivacyPolicy.vue"),
            },
            {
                path: "/data_pages",
                name: "DataPolicy",
                component: () =>
                    import ("../views/pages/DataPolicy.vue"),
            },
            {
                path: "/notification_pages",
                name: "UserNotification",
                component: () =>
                    import ("../views/pages/UserNotification.vue"),
            },
            {
                path: "/terms_pages",
                name: "TermsOfUse",
                component: () =>
                    import ("../views/pages/TermsOfUse.vue"),
            },
            {
                path: "/risk_pages",
                name: "RiskPolicy",
                component: () =>
                    import ("../views/pages/RiskPolicy.vue"),
            },
            {
                path: "/how_btc",
                name: "HowBtc",
                component: () =>
                    import ("../views/pages/HowBtc.vue"),
            },
            {
                path: "/how_buy",
                name: "HowBuy",
                component: () =>
                    import ("../views/pages/HowBuy.vue"),
            },
            {
                path: "/how_crypto",
                name: "HowCrypto",
                component: () =>
                    import ("../views/pages/HowCrypto.vue"),
            },
            {
                path: "/btc_history",
                name: "BTCHistory",
                component: () =>
                    import ("../views/pages/BTCHistory.vue"),
            },
            {
                path: "/security",
                name: "Security",
                component: () =>
                    import ("../views/pages/Security.vue"),
            },
            {
                path: "/bounty_hunting",
                name: "BountyHunting",
                component: () =>
                    import ("../views/pages/BountyHunting.vue"),
            },
            {
                path: "/limits_rules",
                name: "LimitsAndRules",
                component: () =>
                    import ("../views/pages/LimitsAndRules.vue"),
            },
            {
                path: "/deposit_withdraw",
                name: "DepositWithdraw",
                component: () =>
                    import ("../views/pages/DepositWithdraw.vue"),
            },
            {
                path: "/bitcoin_wallet",
                name: "BitcoinWallet",
                component: () =>
                    import ("../views/pages/BitcoinWallet.vue"),
            },
            {
                path: "/about",
                name: "AboutUs",
                component: () =>
                    import ("../views/pages/AboutUs.vue"),
            },
            {
                path: "/coin-listing-request",
                name: "coin_listing_request",
                component: () =>
                    import ("../views/pages/CoinListingRequest.vue"),
            },
        ]
    },
    {
        path: '/:other(.*)*',
        redirect: "/",
    }
]
