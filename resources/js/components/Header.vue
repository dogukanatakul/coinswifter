<template>
  <header class="light-bb">
    <nav class="navbar navbar-expand-lg fixed-top bg-dark-custom my-border" role="navigation" >
      <router-link class="navbar-brand" to="/">
        <img v-bind:src="'/assets/img/logos/logo.png'" alt="CoinSwifter" />
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
                <i class="fas fa-home up"></i>
                {{ $t("Ana Sayfa") }}
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :to="{ name: 'exchange' }">
                <i class="fas fa-exchange-alt up"></i>
                {{ $t("Al-Sat") }}
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :to="{ name: 'markets' }">
                <i class="fas fa-coins up"></i>
                {{ $t("Piyasa") }}
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :to="{ name: 'CoinStartup' }">
                <i class="fas fa-rocket up"></i>
                {{ $t("Coin Startup") }}
              </router-link>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="https://forms.gle/nsrbdXNhGK9MKs4T9" target="_blank">
                <i class="fas fa-handshake up"></i>
                {{ $t("Coin Listeleme") }}
              </a>
            </li>
            <li v-if="user" class="nav-item">
              <router-link :to="{ name: 'wallets' }" class="nav-link">
                <i class="fas fa-wallet up"></i>
                {{ $t("Cüzdan") }}
              </router-link>
            </li>
            <li class="nav-item">
              <router-link :to="{ name: 'corporative-services' }" class="nav-link">
                <i class="fas fa-users up"></i>
                {{ $t("Kurumsal Hizmetler") }}
              </router-link>
            </li>
            <li v-if="user" class="nav-item">
              <router-link :to="{ name: 'profile' }" class="nav-link">
                <i class="fas fa-user up"></i>
                {{ $t("Hesabım") }}
              </router-link>
            </li>
            <li v-if="user" class="nav-item">
              <a class="nav-link" href="javascript:" @click="logout">
                <i class="fas fa-door-open up"></i>
                {{ $t("Çıkış Yap") }}
              </a>
            </li>
          </ul>
          <ul id="menu" v-else>
            <li class="nav-item">
              <router-link class="nav-link" aria-current="page" to="/">
                <i class="fas fa-home up"></i>
                {{ $t("Ana Sayfa") }}
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :to="{ name: 'exchange' }">
                <i class="fas fa-exchange-alt up"></i>
                {{ $t("Al-Sat") }}
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :to="{ name: 'markets' }">
                <i class="fas fa-coins up"></i>
                {{ $t("Piyasa") }}
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :to="{ name: 'CoinStartup' }">
                <i class="fas fa-rocket up"></i>
                {{ $t("Coin Startup") }}
              </router-link>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="https://forms.gle/nsrbdXNhGK9MKs4T9" target="_blank">
                <i class="fas fa-handshake up"></i>
                {{ $t("Coin Listeleme") }}
              </a>
            </li>
            <li class="nav-item">
              <router-link :to="{ name: 'corporative-services' }" class="nav-link">
                <i class="fas fa-users up"></i>
                {{ $t("Kurumsal Hizmetler") }}
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :to="{ name: 'signin' }">
                <i class="fas fa-sign-in-alt up"></i>
                {{ $t("Giriş Yap") }}
              </router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" :to="{ name: 'signup' }">
                <i class="fas fa-clipboard-check up"></i>
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
              <i class="fas fa-home up"></i>
              {{ $t("Ana Sayfa") }}
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" :to="{ name: 'exchange' }">
              <i class="fas fa-exchange-alt up"></i>
              {{ $t("Al-Sat") }}
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" :to="{ name: 'markets' }">
              <i class="fas fa-coins up"></i>
              {{ $t("Piyasa") }}
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" :to="{ name: 'CoinStartup' }">
              <i class="fas fa-rocket up"></i>
              {{ $t("Coin Startup") }}
            </router-link>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="https://forms.gle/nsrbdXNhGK9MKs4T9" target="_blank">
              <i class="fas fa-handshake up"></i>
              {{ $t("Coin Listeleme") }}
            </a>
          </li>
          <li class="nav-item">
              <router-link :to="{ name: 'corporative-services' }" class="nav-link">
                <i class="fas fa-users up"></i>
                {{ $t("Kurumsal Hizmetler") }}
              </router-link>
            </li>
          <!-- <li v-if="user" class="nav-item">
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
          </li> -->
        </ul>
        <!-- <ul class="navbar-nav ml-auto">
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
                </ul> -->
        
        <div class="dropdown mx-2" v-if="user">
          <button class="dropbtn">Cüzdan</button>
          <div class="dropdown-content">
            <ul class="nav-menu">
              <li>
                <router-link :to="{ name: 'wallets' }">
                  {{ $t("Cüzdanım") }}
                </router-link>
              </li>
              <li>
                <router-link :to="{ name: 'wallets' }">
                  {{ $t("Kayıtlar") }}
                </router-link>
              </li>
            </ul>
          </div>
        </div>
        <div class="dropdown mx-2" v-if="user">
          <button class="dropbtn">Emirler</button>
          <div class="dropdown-content">
            <ul class="nav-menu">
              <li>
                <router-link :to="{ name: 'exchange' }">
                  {{ $t("Spot Emri") }}
                </router-link>
              </li>
              <!-- <li>
                                <a href="#">Marjin Emri</a>
                            </li>
                            <li>
                                <a href="#">İndex Emri</a>
                            </li>
                            <li>
                                <a href="#">Vadeli İşlem Emri</a>
                            </li> -->
            </ul>
          </div>
        </div>
        <div class="dropdown mx-2" v-if="user">
          <button class="dropbtn">
            <i class="fas fa-user-circle up"></i>
          </button>
          <div class="dropdown-content2">
            <div class="user-top overflowed">Ca**r@gmail.com</div>
            <div class="user-uid overflowed">UID: 2136231131</div>
            <ul class="nav-menu popover-bottom">
              <li>
                <router-link :to="{ name: 'profile.contact' }">
                  {{ $t("Profil") }}
                </router-link>
              </li>
              <li>
                <router-link :to="{ name: 'profile.kyc' }">
                  {{ $t("Kimlik") }}
                </router-link>
              </li>
              <li>
                <router-link :to="{ name: 'profile.password' }">
                  {{ $t("Güvenlik") }}
                </router-link>
              </li>
              <li>
                <router-link :to="{ name: 'profile.banks' }">
                  {{ $t("Çekim Ayarları") }}
                </router-link>
              </li>
              <!-- <li>
                                <a href="#">Tercihler</a>
                            </li> -->
              <li>
                <router-link :to="{ name: 'profile.sessions' }">
                  {{ $t("Alt Hesap Yönetimi") }}
                </router-link>
              </li>
              <li>
                <router-link :to="{ name: 'profile.referer' }">
                  {{ $t("Arkadaşlarınızı Davet Edin") }}
                </router-link>
              </li>
              <li>
                <a href="#" @click="logout">{{ $t("Çıkış Yap") }}</a>
              </li>
            </ul>
          </div>
        </div>
        <div v-else>
          <b-button squared variant="outline-secondary" :to="{ name: 'signin' }" class="mx-2" >{{ $t("Giriş Yap") }}
          </b-button>
          <b-button squared variant="outline-secondary" :to="{ name: 'signup' }" >
            {{ $t("Kayıt Ol") }}
          </b-button>
        </div>
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
</style>
