<template>
  <b-container class="exchange pb-5 mb-3" fluid>
    <b-row class="d-none d-lg-flex">
      <b-col cols="12" xs="3" sm="12" lg="3" class="d-sm-block">
        <MarketPairs
          v-if="Object.values(selectedCoin).length > 0"
          :key="selectedCoin"
          v-model:selectedCoin="selectedCoin"
          v-model:parities="parities"
          v-model:coins="coins"
          @getTokens="getTokens"
        />
      </b-col>
      <b-col cols="12" lg="6">
        <b-row>
          <b-col
            class="coin-prices"
            cols="12"
            v-if="Object.values(selectedCoin).length > 0"
          >
            <b-col cols="12" lg="2" class="float-left">
              <div
                class="
                  fw-bold
                  text-md-center text-sm-center text-center text-small
                  my-2
                "
              >
                {{
                  [selectedCoin.coin.symbol, selectedCoin.source.symbol].join(
                    "/"
                  )
                }}
              </div>
            </b-col>
            <b-col cols="12" lg="10" class="float-left">
              <div class="coin-detail">
                <div class="coin-info">
                  <div
                    :class="'price ' + selectedCoin.parity_price.price.status"
                  >
                    {{ selectedCoin.parity_price.price.value }}
                  </div>
                  <div class="info">
                    {{ selectedCoin.parity_price.price.value }}
                  </div>
                </div>
                <div class="coin-info">
                  <div class="title">{{ $t("Değişim (24S)") }}</div>
                  <div
                    :class="
                      'info ' +
                      selectedCoin.parity_price.percent_last_24_hours.status
                    "
                  >
                    {{ selectedCoin.parity_price.percent_last_24_hours.value }}
                  </div>
                </div>
                <div class="coin-info">
                  <div class="title">{{ $t("En Yüksek (24S)") }}</div>
                  <div
                    :class="'info ' + selectedCoin.parity_price.highest.status"
                  >
                    {{ selectedCoin.parity_price.highest.value }}
                  </div>
                </div>
                <div class="coin-info">
                  <div class="title">{{ $t("En Düşük (24S)") }}</div>
                  <div
                    :class="'info ' + selectedCoin.parity_price.lowest.status"
                  >
                    {{ selectedCoin.parity_price.lowest.value }}
                  </div>
                </div>
                <div class="coin-info">
                  <div class="title">{{ $t("Hacim (24S)") }}</div>
                  <div
                    :class="
                      'info ' +
                      selectedCoin.parity_price.volume_last_24_hours_price
                        .status
                    "
                  >
                    {{
                      selectedCoin.parity_price.volume_last_24_hours_price.value
                    }}
                  </div>
                </div>
              </div>
            </b-col>
          </b-col>
          <b-col cols="12">
            <div
              v-if="
                Object.values(selectedCoin).length > 0 &&
                Object.values(selectedCoin.promotion).length > 0 &&
                selectedCoin.status == 'ico'
              "
            >
              <a :href="selectedCoin.promotion.url" target="_blank">
                <img :src="selectedCoin.promotion.banner" style="width: 100%" />
              </a>
            </div>
            <b-overlay
              v-if="
                Object.values(selectedCoin).length > 0 &&
                selectedCoin.status !== 'ico'
              "
              :show="marketTradeLoader"
              rounded="sm"
            >
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

                <b-card
                  class="pariy-banner"
                  v-else-if="!selectedCoin.settings.trading_market"
                  no-body
                >
                  <img
                    v-bind:src="'../assets/img/icon_banner/comingsoon.png'"
                  />
                </b-card>
              </div>
              <template #overlay>
                <div class="text-center">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    fill="currentColor"
                    class="bi bi-graph-up-arrow"
                    viewBox="0 0 16 16"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M0 0h1v15h15v1H0V0Zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5Z"
                    />
                  </svg>
                  <p>{{ $t("Grafik verileri getiriliyor..") }}</p>
                </div>
              </template>
            </b-overlay>
            <b-overlay :show="marketTradeLoader" rounded="sm">
              <MarketTrade
                v-if="Object.values(selectedCoin).length > 0"
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
                  <b-icon
                    icon="bar-chart"
                    font-scale="3"
                    size="lg"
                    animation="fade"
                  ></b-icon>
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
      <b-col cols="12" lg="3">
        <wallet
          v-if="
            Object.values(selectedCoin).length > 0 &&
            Object.values(wallet).length > 0
          "
          :wallet.sync="wallet"
          :key="wallet"
          class="d-none d-lg-block"
          @sendTradeFormWallet="sendTradeFormWallet"
        ></wallet>
        <order-book
          v-if="Object.values(selectedCoin).length > 0"
          v-model:orders="orders"
          v-model:selectedCoin="selectedCoin"
          v-model:marketStatus="marketStatus"
          @sendTradeForm="sendTradeForm"
          class="mt-2"
        />
        <last-operations
          v-if="Object.values(selectedCoin).length > 0"
          v-model:lastOperations="lastOperations"
          v-model:selectedCoin="selectedCoin"
        ></last-operations>
      </b-col>
    </b-row>

    <b-row class="d-none d-md-flex d-sm-flex d-xs-flex">
      <b-col cols="12">
        <b-col
          class="coin-prices"
          cols="12"
          v-if="Object.values(selectedCoin).length > 0"
        >
          <b-col cols="12" lg="2" class="float-left">
            <div
              class="
                fw-bold
                text-md-center text-sm-center text-center text-small
                my-2
              "
              @click="paritiesShow = !paritiesShow"
            >
              {{
                [selectedCoin.coin.symbol, selectedCoin.source.symbol].join("/")
              }}
              <i class="fas fa-caret-down"></i>
            </div>
          </b-col>
          <b-col cols="12" lg="10" class="float-left">
            <div class="coin-detail">
              <div class="coin-info">
                <div :class="'price ' + selectedCoin.parity_price.price.status">
                  {{ selectedCoin.parity_price.price.value }}
                </div>
                <div class="info">
                  {{ selectedCoin.parity_price.price.value }}
                </div>
              </div>
              <div class="coin-info">
                <div class="title">{{ $t("Değişim (24S)") }}</div>
                <div
                  :class="
                    'info ' +
                    selectedCoin.parity_price.percent_last_24_hours.status
                  "
                >
                  {{ selectedCoin.parity_price.percent_last_24_hours.value }}
                </div>
              </div>
              <div class="coin-info">
                <div class="title">{{ $t("En Yüksek (24S)") }}</div>
                <div
                  :class="'info ' + selectedCoin.parity_price.highest.status"
                >
                  {{ selectedCoin.parity_price.highest.value }}
                </div>
              </div>
              <div class="coin-info">
                <div class="title">{{ $t("En Düşük (24S)") }}</div>
                <div :class="'info ' + selectedCoin.parity_price.lowest.status">
                  {{ selectedCoin.parity_price.lowest.value }}
                </div>
              </div>
              <div class="coin-info">
                <div class="title">{{ $t("Hacim (24S)") }}</div>
                <div
                  :class="
                    'info ' +
                    selectedCoin.parity_price.volume_last_24_hours_price.status
                  "
                >
                  {{
                    selectedCoin.parity_price.volume_last_24_hours_price.value
                  }}
                </div>
              </div>
            </div>
          </b-col>
        </b-col>
      </b-col>
      <b-col cols="12">
        <wallet
          v-if="
            Object.values(selectedCoin).length > 0 &&
            Object.values(wallet).length > 0
          "
          :wallet.sync="wallet"
          :key="wallet"
          @sendTradeFormWallet="sendTradeFormWallet"
        ></wallet>
      </b-col>
      <b-col cols="12">
        <TabView content-class="mt-3" :activeIndex="1" v-model:changeParitiesView="changeParitiesView">
          <TabPanel header="Grafik">
            <b-row>
              <b-col cols="12">
                <div
                  v-if="
                    Object.values(selectedCoin).length > 0 &&
                    Object.values(selectedCoin.promotion).length > 0 &&
                    selectedCoin.status == 'ico'
                  "
                >
                  <a :href="selectedCoin.promotion.url" target="_blank">
                    <img
                      :src="selectedCoin.promotion.banner"
                      style="width: 100%"
                    />
                  </a>
                </div>
                <b-overlay
                  v-if="
                    Object.values(selectedCoin).length > 0 &&
                    selectedCoin.status !== 'ico'
                  "
                  :show="marketTradeLoader"
                  rounded="sm"
                >
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

                    <b-card
                      class="pariy-banner"
                      v-else-if="!selectedCoin.settings.trading_market"
                      no-body
                    >
                      <img
                        v-bind:src="'../assets/img/icon_banner/comingsoon.png'"
                      />
                    </b-card>
                  </div>
                  <template #overlay>
                    <div class="text-center">
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        fill="currentColor"
                        class="bi bi-graph-up-arrow"
                        viewBox="0 0 16 16"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M0 0h1v15h15v1H0V0Zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5Z"
                        />
                      </svg>
                      <p>{{ $t("Grafik verileri getiriliyor..") }}</p>
                    </div>
                  </template>
                </b-overlay>
              </b-col>
            </b-row>
          </TabPanel>
          <!-- <b-tab title="Pariteler">
            
          </b-tab> -->
          <TabPanel header="Alım - Satım" :class="{'active' : changeParitiesView === true}">
            <b-overlay :show="marketTradeLoader" rounded="sm">
              <MarketTrade
                v-if="Object.values(selectedCoin).length > 0"
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
                  <b-icon
                    icon="bar-chart"
                    font-scale="3"
                    size="lg"
                    animation="fade"
                  ></b-icon>
                  <p>{{ $t("Borsa verileri getiriliyor..") }}</p>
                </div>
              </template>
            </b-overlay>
          </TabPanel>
          <TabPanel header="Emir Defteri">
            <order-book
              v-if="Object.values(selectedCoin).length > 0"
              v-model:orders="orders"
              v-model:selectedCoin="selectedCoin"
              v-model:marketStatus="marketStatus"
              @sendTradeForm="sendTradeForm"
              class="mt-2"
            />
          </TabPanel>
          <TabPanel header="Son İşlemler">
            <last-operations
              v-if="Object.values(selectedCoin).length > 0"
              v-model:lastOperations="lastOperations"
              v-model:selectedCoin="selectedCoin"
            ></last-operations>
            <!-- <b-col
              cols="12"
              class="d-none d-md-block d-sm-block d-xs-block my-2"
            >
              <b-button
                @click="modalShow = !modalShow"
                variant="primary"
                class="w-100"
                >{{ $t("Kullanılabilir Bakiyeler") }}</b-button
              >
              <b-modal
                v-model="modalShow"
                id="modal-lg"
                size="lg"
                title="Kullanılabilir Bakiyeler"
                centered
                ok-only
                hide-backdrop
                hide-footer
                >
              </b-modal>
            </b-col> -->
          </TabPanel>
          <TabPanel header="Emirlerim" v-if="myOrders">
            <div v-if="myOrders" class="b-overlay-wrap position-relative">
              <my-orders
                :key="myOrders"
                :myOrders.sync="myOrders"
                v-model:selectedCoin="selectedCoin"
                @getParity="getParity"
              ></my-orders>
            </div>
          </TabPanel>
        </TabView>
        <b-modal
          v-model="paritiesShow"
          id="modal-lg"
          size="lg"
          title="Parite Çiftleri"
          centered
          ok-only
          hide-footer
          position-y="bottom"
          ><b-col cols="12" xs="3" sm="12" lg="3" class="d-sm-block">
            <MarketPairsNew
              v-if="Object.values(selectedCoin).length > 0"
              :key="selectedCoin"
              v-model:selectedCoin="selectedCoin"
              v-model:parities="parities"
              v-model:coins="coins"
              @changeParities="changeParities"
              @getTokens="getTokens"
            />
          </b-col>
          <div slot="modal-footer">
            <b-button
              class="mr-sm-2 su-btn-link float-right"
              variant="primary"
              @click="paritiesShow = false"
              >{{ $t("Kapat") }}</b-button
            >
          </div>
        </b-modal>
      </b-col>
    </b-row>
  </b-container>
</template>

<script>
import MarketPairs from "../components/Exchange/MarketPairs.vue";
import MarketPairsNew from "../components/Exchange/MarketPairs_New.vue";
import MarketTrade from "../components/Exchange/MarketTrade.vue";
import OrderBook from "../components/Exchange/OrderBook.vue";
import restAPI from "../api/restAPI";
import Chart from "../components/Exchange/Chart";
import Wallet from "../components/Exchange/Wallet";
import { mapGetters } from "vuex";
import MyOrders from "../components/Exchange/MyOrders";
import LastOperations from "../components/Exchange/LastOperations";
import TradingChart from "../components/Exchange/TradingChart";
import NewChart from "../components/Exchange/newChart";
import { groupBy } from "../helpers/helpers";
import TabView from "primevue/tabview";
import TabPanel from "primevue/tabpanel";
const initialData = () => ({
  modalShow: false,
  paritiesShow: false,
  coins: [],
  chart: false,
  wallet: {
    source: {
      balance: 0,
      symbol: "XXX",
      locked: 0,
    },
    coin: {
      balance: 0,
      symbol: "XXX",
      locked: 0,
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
  loadingParity: false,
  selectedTrade: {},
  setInterval: null,
  changeParitiesView : true,
});

export default {
  name: "exchange_new",
  components: {
    NewChart,
    TradingChart,
    LastOperations,
    MyOrders,
    Wallet,
    Chart,
    MarketPairs,
    MarketPairsNew,
    MarketTrade,
    OrderBook,
    TabView,
    TabPanel,
  },
  async created() {
    // this.loader = await this.$loading.show({
    //     container: null,
    //     canCancel: false,
    //     onCancel: this.onCancel,
    // });
    await this.getTokens();
  },
  async mounted() {
    this.setInterval = setInterval(
      function () {
        if (this.loadingParity) {
          this.getParity();
        }
      }.bind(this),
      5000
    );
  },
  watch: {
    async selectedCoin(value) {
      if (value !== undefined && Object.values(value) > 0) {
        Object.assign(this.$data, initialData());
        this.marketTradeLoader = true;
        // this.loader = await this.$loading.show({
        //     container: null,
        //     canCancel: false,
        //     onCancel: this.onCancel,
        // });
      }
      await this.getParity(true);
    },
    "$route.params.parity": function (value) {
      this.selectedCoin = this.coins[value];
    },
  },
  data: () => initialData(),
  computed: {
    ...mapGetters(["user"]),
    parity() {
      let parity = undefined;
      if (this.$route.params.parity === undefined) {
        if (window.localStorage.getItem("parity") !== undefined) {
          parity = window.localStorage.getItem("parity");
        } else {
          parity = this.$route.params.parity;
        }
      } else {
        parity = this.$route.params.parity;
        window.localStorage.setItem("parity", parity);
      }
      return parity;
    },
    parities() {
      return groupBy(Object.values(this.coins), "source", "symbol");
    },
  },
  methods: {
    async getParity(loader = false) {
      this.loadingParity = false;
      let selectedCoin = this.selectedCoin;
      await restAPI
        .getData({
          Action:
            "exchange/parity/" +
            selectedCoin.source.symbol +
            "-" +
            selectedCoin.coin.symbol,
        })
        .then((response) => {
          if (response.status === "success") {
            this.marketTradeLoader = false;
            this.marketStatus = response.marketStatus;
            this.chart = response.chart;
            this.wallet = response.wallet;
            this.myOrders = response.myOrders;
            this.lastOperations = response.lastOperations;
            this.orders = response.data;
            this.loadingParity = true;
            if (loader) {
              // this.loader.hide();
            }
          }
        });
    },
    async getTokens() {
      await restAPI
        .getData({
          Action: "exchange/tokens",
        })
        .then((response) => {
          if (response.status === "success") {
            // this.loader.hide();
            this.coins = response.data;
            if (response.data[this.parity] !== undefined) {
              this.selectedCoin = response.data[this.parity];
            } else {
              this.$router.push({ name: "markets" });
            }
          }
        });
    },
    sendTradeForm(params) {
      this.selectedTrade = params;
    },
    sendTradeFormWallet(params){
        this.selectedTrade = params;
    },
    changeParities(params){
        this.changeParitiesView = params;
        console.log(this.changeParitiesView);
    }
  },
  beforeUnmount: function () {
    clearInterval(this.setInterval);
  },
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
.float-left {
  float: left !important;
}
.text-small {
  font-size: 12px;
}
@media screen and (max-width: 1690px) and(min-width:992px) {
  .title,
  .price,
  .info {
    font-size: 8px !important;
    // text-align: center;
  }
  .d-xs-flex {
    display: none !important;
  }
}
@media screen and (max-width: 576px) {
  .d-xs-block {
    display: block !important;
  }
  .d-xs-flex {
    display: flex !important;
  }
}
@media screen and (min-width: 992px) {
  .d-xs-block {
    display: none !important;
  }
  .d-xs-flex {
    display: none !important;
  }
}
.float-right {
  float: right !important;
}
</style>