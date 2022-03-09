<template>
  <header class="light-bb">
    <nav
      class="navbar navbar-expand-lg fixed-top bg-white my-border"
      role="navigation"
    >
      <router-link class="navbar-brand" to="/">
        <img v-bind:src="'../assets/img/logos/logo.png'" alt="CoinSwifter" />
      </router-link>
      <div class="navbar-header">
        <div
          id="menuToggle"
          class="hamburger navbar-toggler collapsed d-lg-none"
        >
          <input
            type="checkbox"
            id="hamburgers"
            @blur="closeHamburger($event)"
          />
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
              <router-link
                class="nav-link"
                :to="{ name: 'coin_listing_request' }"
              >
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
              <b-dropdown-item-button @click="logout">
                <i class="fas fa-door-open"></i>
                {{ $t("Çıkış Yap") }}
              </b-dropdown-item-button>
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
              <router-link
                class="nav-link"
                :to="{ name: 'coin_listing_request' }"
              >
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
            <router-link
              class="nav-link"
              :to="{ name: 'coin_listing_request' }"
            >
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
                <router-link
                  :to="{ name: 'wallets' }"
                  class="text-decoration-none"
                >
                  <b-dropdown-item-button
                    aria-describedby="dropdown-header-label"
                  >
                    {{ $t("Cüzdanım") }}
                  </b-dropdown-item-button>
                </router-link>
                <router-link
                  :to="{ name: 'profile' }"
                  class="text-decoration-none"
                >
                  <b-dropdown-item-button
                    aria-describedby="dropdown-header-label"
                  >
                    {{ $t("Hesabım") }}
                  </b-dropdown-item-button>
                </router-link>
                <b-dropdown-item-button
                  @click="logout"
                  aria-describedby="dropdown-header-label"
                >
                  {{ $t("Çıkış Yap") }}
                </b-dropdown-item-button>
              </b-dropdown>
              <div v-else>
                <b-button
                  squared
                  variant="outline-secondary"
                  :to="{ name: 'signin' }"
                  class="mx-2"
                  >{{ $t("Giriş Yap") }}
                </b-button>
                <b-button
                  squared
                  variant="outline-secondary"
                  :to="{ name: 'signup' }"
                >
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
import { mapGetters } from "vuex";

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
#menu a {
  text-decoration: none !important;
  color: black;
}
.hamburger ul li {
  opacity: 0.95;
  padding: 0.45rem !important;
}

#menuToggle {
  display: block;
  position: absolute;
  top: 12px;
  right: 30px;
  z-index: 1;
  -webkit-user-select: none;
  user-select: none;
}

#menuToggle input {
  display: block;
  width: 40px;
  height: 32px;
  position: absolute;
  top: -7px;
  left: -5px;
  cursor: pointer;
  opacity: 0;
  z-index: 2;
  -webkit-touch-callout: none;
}

#menuToggle span {
  display: block;
  width: 33px;
  height: 4px;
  margin-bottom: 5px;
  position: relative;
  background: #cdcdcd;
  border-radius: 3px;
  z-index: 1;
  transform-origin: 4px 0px;
  transition: transform 0.5s cubic-bezier(0.77, 0.2, 0.05, 1),
    background 0.5s cubic-bezier(0.77, 0.2, 0.05, 1), opacity 0.55s ease;
}

#menuToggle span:first-child {
  transform-origin: 0% 0%;
}

#menuToggle span:nth-last-child(2) {
  transform-origin: 0% 100%;
}

#menuToggle input:checked ~ span {
  opacity: 1;
  transform: rotate(45deg) translate(-2px, -1px);
  background: #232323;
}

#menuToggle input:checked ~ span:nth-last-child(3) {
  opacity: 0;
  transform: rotate(0deg) scale(0.2, 0.2);
}

#menuToggle input:checked ~ span:nth-last-child(2) {
  opacity: 1;
  transform: rotate(-45deg) translate(0, -1px);
}

#menu {
  position: absolute;
  width: 300px;
  height: 120vh !important;
  margin: -100px 0 0 0;
  padding: 10px;
  padding-top: 100px;
  padding-bottom: 75px;
  right: -70px;
  background: #ededed;
  list-style-type: none;
  -webkit-font-smoothing: antialiased;
  transform-origin: 0% 0%;
  transform: translate(100%, 0);
  overflow-x:auto;
  transition: transform 0.5s cubic-bezier(0.77, 0.2, 0.05, 1);
}

#menu li {
  padding: 10px 0;
}

#menuToggle input:checked ~ ul {
  transform: scale(1, 1);
  opacity: 1;
}


@media screen and (max-width: 991px) and (min-width: 768px) {
  .my-border {
    border-bottom: solid 1px lightgray;
  }
}

@media screen and (max-width: 1100px) and (min-width: 992px) {
  .nav-link {
    font-size: 1.3vw !important;
  }
} 

@media screen and (max-width: 768px) {
  header .navbar-brand img {
    margin-top: 2px !important;
  }
  .my-border {
    border-bottom: solid 1px lightgray;
  }
}
</style>