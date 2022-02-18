<template>
    <div class="home">
        <footer class="footer-container mt-2">
            <b-container>
                <b-row class="pb-50 mb-pb-20 footer-top-content overflow-hidden">
                    <b-col
                        cols="12"
                        md="6"
                        lg="4"
                        class="footer-col wow fadeInUp"
                        data-wow-duration="1s"
                        data-wow-delay=".2s"
                    >
                        <div class="footer-cryptu">
                            <div class="footer-logo">
                                <router-link :to="{name:'home'}">
                                    <img v-bind:src="'assets/img/logos/logo_white.png'" alt="CoinSwifter"/>
                                </router-link>
                            </div>
                            <p>Raysoft Yazılım A.Ş.</p>
                            <ul>
                                <li>
                                    <div class="foo-social">
                                        <span>
                                            <a href="javascript:">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </a>
                                        </span>
                                        <p>
                                            Odunluk mah. Akpınar Cad. No:5 Kat:10 Daire:23 Green White
                                            Plaza <br/>
                                        </p>
                                        <p class="text-end">Nilüfer / BURSA</p>
                                    </div>
                                </li>
                            </ul>
                            <ul class="d-flex flex-wrap social-link">
                                <li>
                                    <a href="https://www.facebook.com/coinswifter" target="_blank" rel="noreferrer">
                                        <i class="fab fa-facebook-f"></i></a>
                                </li>
                                <li>
                                    <a href="https://twitter.com/CoinSwifter" target="_blank" rel="noreferrer">
                                        <i class="fab fa-twitter"></i></a>
                                </li>
                                <li>
                                    <a href="https://www.instagram.com/coinswifter/" target="_blank" rel="noreferrer">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://t.me/CoinSwifter" target="_blank" rel="noreferrer">
                                        <i class="fab fa-telegram"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </b-col>
                    <b-col
                        cols="12"
                        md="6"
                        lg="2"
                        class="footer-col link-hover wow fadeInUp"
                        data-wow-duration="1s"
                        data-wow-delay=".4s"
                    >
                        <h4>COINSWIFTER</h4>
                        <ul>
                            <li>
                                <router-link :to="{name:'AboutUs'}">{{ $t('Hakkımızda') }}</router-link>
                            </li>
                        </ul>
                    </b-col>
                    <b-col
                        cols="12"
                        md="6"
                        lg="2"
                        class="footer-col link-hover wow fadeInUp"
                        data-wow-duration="1s"
                        data-wow-delay=".6s"
                    >
                        <h4>{{ $t('Yasal Bilgiler') }}</h4>
                        <ul>
                            <li>
                                <router-link :to="{ name: 'TermsOfUse' }">{{ $t('Kullanım Sözleşmesi') }}</router-link>
                            </li>
                            <li>
                                <router-link :to="{ name: 'PrivacyPolicy' }">{{ $t('Gizlilik Sözleşmesi') }}</router-link>
                            </li>
                            <li>
                                <router-link :to="{ name: 'DataPolicy' }">{{ $t('Kişisel Verilerin Korunması') }}</router-link>
                            </li>
                            <li>
                                <router-link :to="{ name: 'CookiePolicy' }">{{ $t('Çerez Politikası') }}</router-link>
                            </li>
                            <li>
                                <router-link :to="{ name: 'RiskPolicy' }">{{ $t('Risk Bildirimi') }}</router-link>
                            </li>
                            <li>
                                <router-link :to="{ name: 'UserNotification' }">{{ $t('Kullanıcı Bilgilendirme') }}</router-link>
                            </li>
                        </ul>
                    </b-col>
                    <b-col
                        cols="12"
                        md="6"
                        lg="2"
                        class="footer-col wow fadeInUp"
                        data-wow-duration="1s"
                        data-wow-delay=".8s"
                    >
                        <h4>{{ $t('Bilgi Platformu') }}</h4>
                        <ul>
                            <li>
                                <router-link :to="{name:'HowBtc'}">{{ $t('Bitcoin(BTC) Nedir?') }}</router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'HowCrypto'}">{{ $t('Kriptopara Nedir?') }}</router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'BTCHistory'}">{{ $t('Bitcoin Tarihi') }}</router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'HowBuy'}">{{ $t('Bitcoin Nasıl Alınır?') }}</router-link>
                            </li>
                        </ul>
                    </b-col>
                    <b-col
                        cols="12"
                        md="6"
                        lg="2"
                        class="footer-col wow fadeInUp"
                        data-wow-duration="1s"
                        data-wow-delay=".8s"
                    >
                        <h4>{{ $t('Yardım') }}</h4>
                        <ul>
                            <li>
                                <router-link :to="{name:'Security'}">{{ $t('Güvenlik') }}</router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'BountyHunting'}">{{ $t('Ödül Avcılığı') }}</router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'LimitsAndRules'}">{{ $t('Limitler ve Kurallar') }}</router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'DepositWithdraw'}">{{ $t('Yatırma / Çekme') }}</router-link>
                            </li>
                            <li>
                                <router-link :to="{name:'BitcoinWallet'}">{{ $t('Bitcoin Cüzdanı') }}</router-link>
                            </li>
                        </ul>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col cols="12">
                        <div class=" d-flex flex-wrap justify-content-between footer-bottom-content">
                            <p class="order-2 order-lg-1">
                                &copy; 2021 <a href="javascript:"> Raysoft Yazılım A.Ş. </a> {{ $t('Tüm Hakları Saklıdır.') }}
                            </p>
                        </div>
                    </b-col>
                </b-row>
            </b-container>
        </footer>
    </div>
    <a class="backtop" href="javascript:"><i class="far fa-long-arrow-up"></i></a>
</template>

<script>
export default {
    name: "Footer"
}
</script>

<style scoped>

</style>
