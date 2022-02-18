<template>
    <b-container class="exchange" fluid>
        <b-row>
            <b-col cols="3" class="d-none d-sm-block">
                <MarketPairs v-if="Object.values(selectedCoin).length>0"
                             :key="selectedCoin"
                             v-model:selectedCoin="selectedCoin"
                             v-model:parities="parities"
                             v-model:coins="coins"
                             @getTokens="getTokens"
                />
            </b-col>
            <b-col cols="12" md="6">
                <b-row>
                    <b-col class="coin-prices" cols="12" v-if="Object.values(selectedCoin).length>0">
                        <div class="coin-title">
                            {{ [selectedCoin.coin.symbol, selectedCoin.source.symbol].join("/") }}
                        </div>
                        <div class="coin-detail">
                            <div class="coin-info">
                                <div :class="'price '+ selectedCoin.parity_price.price.status">{{ selectedCoin.parity_price.price.value }}</div>
                                <div class="info">{{ selectedCoin.parity_price.price.value }}</div>
                            </div>
                            <div class="coin-info">
                                <div class="title">{{ $t("Değişim (24S)") }}</div>
                                <div :class="'info '+ selectedCoin.parity_price.percent_last_24_hours.status">{{ selectedCoin.parity_price.percent_last_24_hours.value }}</div>
                            </div>
                            <div class="coin-info">
                                <div class="title">{{ $t("En Yüksek (24S)") }}</div>
                                <div :class="'info '+ selectedCoin.parity_price.highest.status">{{ selectedCoin.parity_price.highest.value }}</div>
                            </div>
                            <div class="coin-info">
                                <div class="title">{{ $t("En Düşük (24S)") }}</div>
                                <div :class="'info '+ selectedCoin.parity_price.lowest.status">{{ selectedCoin.parity_price.lowest.value }}</div>
                            </div>
                            <div class="coin-info">
                                <div class="title">{{ $t("Hacim (24S)") }}</div>
                                <div :class="'info '+ selectedCoin.parity_price.volume_last_24_hours_price.status">{{ selectedCoin.parity_price.volume_last_24_hours_price.value }}</div>
                            </div>
                        </div>
                    </b-col>
                    <b-col cols="12">
                        <div v-if="Object.values(selectedCoin).length>0 && Object.values(selectedCoin.promotion).length>0 && selectedCoin.status=='ico'">
                            <a :href="selectedCoin.promotion.url" target="_blank">
                                <img :src="selectedCoin.promotion.banner" style="width:100%;">
                            </a>
                        </div>
                        <b-overlay v-if="Object.values(selectedCoin).length>0 && selectedCoin.status!=='ico'" :show="marketTradeLoader" rounded="sm">
                            <div>
                                <new-chart
                                    v-if="chart"
                                    :chart.sync="chart"
                                    v-model:selectedCoin="selectedCoin"
                                    :key="selectedCoin"
                                ></new-chart>
                                <trading-chart
                                    class="trading-chart"
                                    v-else-if="selectedCoin.settings.trading_market"
                                    v-model:selectedCoin="selectedCoin"
                                    :key="selectedCoin"
                                ></trading-chart>

                                <b-card class="pariy-banner" v-else-if="!selectedCoin.settings.trading_market" no-body>
                                    <img v-bind:src="'../assets/img/icon_banner/comingsoon.png'">
                                </b-card>
                            </div>
                            <template #overlay>
                                <div class="text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M0 0h1v15h15v1H0V0Zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5Z"/>
                                    </svg>
                                    <p>{{ $t("Grafik verileri getiriliyor..") }}</p>
                                </div>
                            </template>
                        </b-overlay>
                        <b-overlay :show="marketTradeLoader" rounded="sm">
                            <MarketTrade v-if="Object.values(selectedCoin).length>0"
                                         v-model:selectedCoin="selectedCoin"
                                         v-model:wallet="wallet"
                                         v-model:marketStatus="marketStatus"
                                         v-model:selectedTrade="selectedTrade"
                                         v-model:commission="selectedCoin.commission"
                                         :key="selectedCoin"
                                         @getParity="getParity"
                            />
                            <template #overlay>
                                <div class="text-center">
                                    <b-icon icon="bar-chart" font-scale="3" size="lg" animation="fade"></b-icon>
                                    <p>{{ $t("Borsa verileri getiriliyor..") }}</p>
                                </div>
                            </template>
                        </b-overlay>
                        <div v-if="myOrders" class="b-overlay-wrap position-relative">
                            <my-orders
                                :key="myOrders"
                                :myOrders.sync="myOrders"
                                v-model:selectedCoin="selectedCoin"
                                @getParity="getParity"
                            ></my-orders>
                        </div>
                    </b-col>
                </b-row>
            </b-col>

            <b-col cols="12" md="3">
                <wallet
                    v-if="Object.values(selectedCoin).length>0 && Object.values(wallet).length>0"
                    :wallet.sync="wallet"
                    :key="wallet"
                ></wallet>
                <order-book
                    v-if="Object.values(selectedCoin).length>0"
                    v-model:orders="orders"
                    v-model:selectedCoin="selectedCoin"
                    v-model:marketStatus="marketStatus"
                    @sendTradeForm="sendTradeForm"
                />
                <last-operations
                    v-if="Object.values(selectedCoin).length>0"
                    v-model:lastOperations="lastOperations"
                    v-model:selectedCoin="selectedCoin"
                ></last-operations>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
import MarketPairs from "../components/Exchange/MarketPairs.vue";
import MarketTrade from "../components/Exchange/MarketTrade.vue";
import OrderBook from "../components/Exchange/OrderBook.vue";
import restAPI from "../api/restAPI";
import Chart from "../components/Exchange/Chart";
import Wallet from "../components/Exchange/Wallet";
import {mapGetters} from "vuex";
import MyOrders from "../components/Exchange/MyOrders";
import LastOperations from "../components/Exchange/LastOperations";
import TradingChart from "../components/Exchange/TradingChart";
import NewChart from "../components/Exchange/newChart";
import {groupBy} from "../helpers/helpers";


const initialData = () => ({
    coins: [],
    chart: false,
    wallet: {
        source: {
            balance: 0,
        },
        coin: {
            balance: 0,
        },
    },
    lastOperations: [],
    orders: {},
    myOrders: false,
    selectedCoin: {},
    marketTradeLoader: true,
    marketStatus: {
        price: 0,
    },
    loader: null,
    selectedTrade: {},
})


export default {
    components: {
        NewChart,
        TradingChart,
        LastOperations,
        MyOrders,
        Wallet,
        Chart,
        MarketPairs,
        MarketTrade,
        OrderBook,
    },
    async created() {
        this.loader = await this.$loading.show({
            container: null,
            canCancel: false,
            onCancel: this.onCancel,
        });
        await this.getTokens()
    },
    watch: {
        async selectedCoin(value) {
            if (value !== undefined && Object.values(value) > 0) {
                Object.assign(this.$data, initialData());
                this.marketTradeLoader = true
                this.loader = await this.$loading.show({
                    container: null,
                    canCancel: false,
                    onCancel: this.onCancel,
                });
            }
            await this.getParity(true)

        },
        '$route.params.parity': function (value) {
            this.selectedCoin = this.coins[value]
        },
    },
    data: () => (initialData()),
    computed: {
        ...mapGetters([
            'user',
        ]),
        parity() {
            let parity = undefined;
            if (this.$route.params.parity === undefined) {
                if (window.localStorage.getItem('parity') !== undefined) {
                    parity = window.localStorage.getItem('parity')
                } else {

                    parity = this.$route.params.parity
                }
            } else {
                parity = this.$route.params.parity
                window.localStorage.setItem('parity', parity)
            }
            return parity;
        },
        parities() {
            return groupBy(Object.values(this.coins), 'source', 'symbol')
        }
    },
    methods: {
        async getParity(loader = false) {
            let selectedCoin = this.selectedCoin
            await restAPI.getData({
                Action: "exchange/parity/" + selectedCoin.source.symbol + "-" + selectedCoin.coin.symbol,
            }).then((response) => {
                if (response.status === 'success') {
                    this.marketTradeLoader = false
                    this.marketStatus = response.marketStatus
                    this.chart = response.chart
                    this.wallet = response.wallet
                    this.myOrders = response.myOrders
                    this.lastOperations = response.lastOperations
                    this.orders = response.data
                    if (loader) {
                        this.loader.hide()
                    }
                }
            })
        },
        async getTokens() {
            await restAPI.getData({
                Action: "exchange/tokens",
            }).then((response) => {
                if (response.status === 'success') {
                    this.loader.hide()
                    this.coins = response.data
                    if (response.data[this.parity] !== undefined) {
                        this.selectedCoin = response.data[this.parity]
                    } else {
                        this.$router.push({name: 'markets'})
                    }
                }
            });
        },
        sendTradeForm(params) {
            this.selectedTrade = params
        }
    }

};
</script>
<style scoped lang="scss">
.pariy-banner {
    align-items: center;

    img {
        width: 100%;
        height: auto;
        max-width: 100%;
    }
}
</style>
