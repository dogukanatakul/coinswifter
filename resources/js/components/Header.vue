<template>
    <header class="light-bb">
        <nav class="navbar navbar-expand-lg fixed-top bg-white my-border" role="navigation" >
            <router-link class="navbar-brand" to="/">
                <img v-bind:src="'../assets/img/logos/logo.png'" alt="CoinSwifter"/>
            </router-link>
            <div class="navbar-header">
                <div id="menuToggle" class="hamburger navbar-toggler collapsed d-lg-none" >
                    <input type="checkbox" id="hamburgers" @blur="closeHamburger($event)" />
                    <span></span>
                    <span></span>
                    <span></span>
                    <ul id="menu" v-if="user">
                        <li class="nav-item">
                            <router-link class="nav-link" aria-current="page" to="/">
                                <i class="fas fa-home"></i>
                                {{ $t("Ana Sayfa") }}
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'exchange' }">
                                <i class="fas fa-exchange-alt"></i>
                                {{ $t("Al-Sat") }}
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'markets' }">
                                <i class="fas fa-coins"></i>
                                {{ $t("Piyasa") }}
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'CoinStartup' }">
                                <i class="fas fa-rocket"></i>
                                {{ $t("Coin Startup") }}
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'coin_listing_request' }" >
                                <i class="fas fa-handshake"></i>
                                {{ $t("Coin Listeleme") }}
                            </router-link>
                        </li>
                        <li v-if="user" class="nav-item">
                            <router-link :to="{ name: 'wallets' }" class="nav-link">
                                <i class="fas fa-wallet"></i>
                                {{ $t("Cüzdan") }}
                            </router-link>
                        </li>
                        <li v-if="user" class="nav-item">
                            <router-link :to="{ name: 'profile' }" class="nav-link">
                                <i class="fas fa-user"></i>
                                {{ $t("Hesabım") }}
                            </router-link>
                        </li>
                        <li v-if="user" class="nav-item">
                            <a class="nav-link" href="javascript:" @click="logout">
                                <i class="fas fa-door-open"></i>
                                {{ $t("Çıkış Yap") }}
                            </a>
                        </li>
                    </ul>
                    <ul id="menu" v-else>
                        <li class="nav-item">
                            <router-link class="nav-link" aria-current="page" to="/">
                                <i class="fas fa-home"></i>
                                {{ $t("Ana Sayfa") }}
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'exchange' }">
                                <i class="fas fa-exchange-alt"></i>
                                {{ $t("Al-Sat") }}
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'markets' }">
                                <i class="fas fa-coins"></i>
                                {{ $t("Piyasa") }}
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'CoinStartup' }">
                                <i class="fas fa-rocket"></i>
                                {{ $t("Coin Startup") }}
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'coin_listing_request' }" >
                                <i class="fas fa-handshake"></i>
                                {{ $t("Coin Listeleme") }}
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'signin' }">
                                <i class="fas fa-sign-in-alt"></i>
                                {{ $t("Giriş Yap") }}
                            </router-link>
                        </li>
                        <li class="nav-item">
                            <router-link class="nav-link" :to="{ name: 'signup' }">
                                <i class="fas fa-clipboard-check"></i>
                                {{ $t("Kayıt Ol") }}
                            </router-link>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="collapse navbar-collapse" id="headerNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <router-link class="nav-link" aria-current="page" to="/">
                            <i class="fas fa-home"></i>
                            {{ $t("Ana Sayfa") }}
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" :to="{ name: 'exchange' }">
                            <i class="fas fa-exchange-alt"></i>
                            {{ $t("Al-Sat") }}
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" :to="{ name: 'markets' }">
                            <i class="fas fa-coins"></i>
                            {{ $t("Piyasa") }}
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" :to="{ name: 'CoinStartup' }">
                            <i class="fas fa-rocket"></i>
                            {{ $t("Coin Startup") }}
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" :to="{ name: 'coin_listing_request' }" >
                            <i class="fas fa-handshake"></i>
                            {{ $t("Coin Listeleme") }}
                        </router-link>
                    </li>
                    <li v-if="user" class="nav-item">
                        <router-link :to="{ name: 'wallets' }" class="nav-link">
                            <i class="fas fa-wallet"></i>
                            {{ $t("Cüzdan") }}
                        </router-link>
                    </li>
                    <li v-if="user" class="nav-item">
                        <router-link :to="{ name: 'profile' }" class="nav-link">
                            <i class="fas fa-user"></i>
                            {{ $t("Hesabım") }}
                        </router-link>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown header-img-icon">
                        <div>
                            <b-dropdown v-if="user" id="dropdown-header" :text="user.name">
                                <router-link :to="{ name: 'wallets' }" class="text-decoration-none" >
                                    <b-dropdown-item-button aria-describedby="dropdown-header-label" >
                                        {{ $t("Cüzdanım") }}
                                    </b-dropdown-item-button>
                                </router-link>
                                <router-link :to="{ name: 'profile' }" class="text-decoration-none" >
                                    <b-dropdown-item-button aria-describedby="dropdown-header-label" >
                                        {{ $t("Hesabım") }}
                                    </b-dropdown-item-button>
                                </router-link>
                                <b-dropdown-item-button @click="logout" aria-describedby="dropdown-header-label" >
                                    {{ $t("Çıkış Yap") }}
                                </b-dropdown-item-button>
                            </b-dropdown>
                            <div v-else>
                                <b-button squared variant="outline-secondary" :to="{ name: 'signin' }" class="mx-2" >{{ $t("Giriş Yap") }}
                                </b-button>
                                <b-button squared variant="outline-secondary" :to="{ name: 'signup' }" >
                                    {{ $t("Kayıt Ol") }}
                                </b-button>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
</template>

<script>
import {mapGetters} from "vuex";

export default {
    methods: {
        logout() {
            this.$store.dispatch("logout");
        },
        closeHamburger(event) {
            $("#hamburgers").prop("checked", false);
        },
    },
    computed: {
        ...mapGetters(["user"]),
    },
    watch: {
        $route(to, from) {
            $("#hamburgers").prop("checked", false);
        },
    },
};
</script>
<style scoped type="scss">

</style>
