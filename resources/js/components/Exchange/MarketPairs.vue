<template>
  <b-card no-body class="market-pairs mb-2">
    <b-card-text>
      <b-row>
        <b-col>
          <b-form-input
            v-model="search"
            class="search"
            :placeholder="$t('Parite Çifti Arayın')"
            inputmode="text"
          ></b-form-input>
        </b-col>
      </b-row>
      <TabView v-model:activeIndex="tabindex">
        <TabPanel>
          <template #header>
            <i class="fas fa-star"></i> {{ $t("Favoriler") }}
          </template>
          <div class="table-responsive">
            <table class="table table-sm order-table">
              <thead>
                <tr>
                  <th><i class="far fa-star"></i></th>
                  <th>{{ $t("İşlem Çifti") }}</th>
                  <th>{{ $t("Son Fiyat") }}</th>
                  <th>{{ $t("Değişim (24S)") }}</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(coin, coin_key) in Object.values(coins).filter(
                    (element) => element.user_favorite === true
                  )"
                  :class="
                    selectedCoin.coin.symbol === coin.coin.symbol &&
                    selectedCoin.source.symbol === coin.source.symbol
                      ? 'selected'
                      : ''
                  "
                  style="cursor: pointer"
                  :key="coin_key"
                  @click="selectCoin(coin)"
                >
                  <td
                    @click="
                      addFavorite({
                        coin: coin.coin.symbol,
                        source: coin.source.symbol,
                      })
                    "
                  >
                    <i
                      v-if="coin.user_favorite"
                      class="fa fa-star"
                      style="color: yellow"
                    ></i>
                    <i v-else class="far fa-star"></i>
                  </td>
                  <td>{{ coin.coin.symbol + "/" + coin.source.symbol }}</td>
                  <td :class="coin.parity_price.price.status">
                    {{ coin.parity_price.price.value }}
                  </td>
                  <td
                    v-if="coin.parity_price.percent_last_24_hours !== undefined"
                    :class="coin.parity_price.price.status"
                  >
                    {{
                      parseFloat(
                        coin.parity_price.percent_last_24_hours.value
                      ).toFixed(2)
                    }}%
                  </td>
                  <td v-else>-%</td>
                </tr>
              </tbody>
            </table>
          </div>
        </TabPanel>
        <TabPanel
          v-for="(coin_tables, coin_tables_key) in parities"
          :header="coin_tables_key"
          :key="coin_tables_key"
          :value="coin_tables_key"
        >
          <div class="table-responsive">
            <table class="table table-sm order-table" :key="search">
              <thead>
                <tr>
                  <th><i class="far fa-star"></i></th>
                  <th>{{ $t("İşlem Çifti") }}</th>
                  <th>{{ $t("Son Fiyat") }}</th>
                  <th>{{ $t("Değişim (24S)") }}</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  :class="
                    selectedCoin.coin.symbol === coin.coin.symbol &&
                    selectedCoin.source.symbol === coin.source.symbol
                      ? 'selected'
                      : ''
                  "
                  style="cursor: pointer"
                  v-for="(coin, coin_key) in coin_tables.filter(
                    (element) =>
                      this.search === '' ||
                      element.coin.symbol.indexOf(this.search) >= 0
                  )"
                  :key="coin_key"
                  @click="selectCoin(coin)"
                >
                  <td
                    @click="
                      addFavorite({
                        coin: coin.coin.symbol,
                        source: coin.source.symbol,
                      })
                    "
                  >
                    <i v-if="coin.user_favorite" class="fa fa-star"></i>
                    <i v-else class="far fa-star"></i>
                  </td>
                  <td>{{ coin.coin.symbol + "/" + coin.source.symbol }}</td>
                  <td :class="coin.parity_price.price.status">
                    {{ coin.parity_price.price.value }}
                  </td>
                  <td
                    v-if="coin.parity_price.percent_last_24_hours !== undefined"
                    :class="coin.parity_price.price.status"
                  >
                    {{
                      parseFloat(
                        coin.parity_price.percent_last_24_hours.value
                      ).toFixed(2)
                    }}%
                  </td>
                  <td v-else>-%</td>
                </tr>
              </tbody>
            </table>
          </div>
        </TabPanel>
      </TabView>
    </b-card-text>
  </b-card>
</template>
<script>
import TabView from "primevue/tabview";
import TabPanel from "primevue/tabpanel";
import restAPI from "../../api/restAPI";

export default {
  name: "MarketPairs",
  components: { TabView, TabPanel },
  props: ["selectedCoin", "parities", "coins"],
  emits: ["update:selectedCoin"],
  methods: {
    selectCoin(value) {
      this.$emit("update:selectedCoin", value);
      this.$router.push({
        name: "exchange",
        params: { parity: value.source.symbol + "-" + value.coin.symbol },
      });
    },
    async addFavorite(parity) {
      await restAPI
        .getData(
          {
            Action: "exchange/favorite",
          },
          parity
        )
        .then((response) => {
          if (response.status === "success") {
            this.$notify({ text: response.message, type: "success" });
            this.$emit("getTokens");
          } else if (response.status === "fail") {
            this.$notify({ text: response.message, type: "error" });
          }
        });
    },
  },
  computed: {
    tabindex() {
      return this.selectedCoin.source.symbol === "TRY" ? 1 : 2;
    },
  },
  data: () => ({
    search: "",
  }),
};
</script>
<style scoped lang="scss">

</style>
