<template>
  <b-card :header="$t('Emir Defteri')" header-tag="header">
    <b-card-text class="table-responsive">
      <table class="table table-sm order-table">
        <thead>
          <tr>
            <th>{{ $t("Fiyat") }} ({{ selectedCoin.source.symbol }})</th>
            <th>{{ $t("Miktar") }} ({{ selectedCoin.coin.symbol }})</th>
            <th>{{ $t("Toplam") }} ({{ selectedCoin.source.symbol }})</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, key) in orders['sell']" :key="key" :style="bgColorOrder(item.percent, item.process)" @click="sendTradeForm('buy', item.price, item.amount)" class="cursor-pointer sell" >
              <td class="text-danger-custom">{{ item.price }}</td>
            <td class="text-danger-custom">{{ item.amount }}</td>
            <td class="text-danger-custom">{{ item.total }}</td>
          </tr>
        </tbody>
        <tbody class="ob-heading">
          <tr>
            <th>
              <span>{{ $t("Fiyat") }}</span>
              {{ marketStatus.price }}
            </th>
            <th>
              <span>{{ $t("Sembol") }}</span>
              {{ selectedCoin.coin.symbol + "/" + selectedCoin.source.symbol }}
            </th>
            <th :class="marketStatus.percent > 0 ? 'green' : 'red'">
              <span>{{ $t("Fark") }}</span>
              {{ selectedCoin.parity_price.price.value }}%
            </th>
          </tr>
        </tbody>
        <tbody>
          <tr v-for="(item, key) in orders['buy']" :key="key" :style="bgColorOrder(item.percent, item.process)" @click="sendTradeForm('sell', item.price, item.amount)" class="cursor-pointer buy" >
<!--              {{item.percent}}-->
              <td class="text-success">{{ item.price }}</td>
            <td class="text-success">{{ item.amount }}</td>
            <td class="text-success">{{ item.total }}</td>
          </tr>
        </tbody>
      </table>
    </b-card-text>
  </b-card>
</template>

<script>
import { toFixedCustom } from "../../helpers/helpers";

export default {
  name: "OrderBook",
  props: ["orders", "selectedCoin", "marketStatus"],
  methods: {
    sendTradeForm(action, price, amount) {
      this.$emit("sendTradeForm", { action, price, amount });
    },
      bgColorOrder(value, islem) {
          let css;
          if (islem === "buy") {
              css =
                  "background: -moz-linear-gradient(left, #35ac136b 0, #80e8626b " +
                  value +
                  "%, transparent 100%);background: -webkit-linear-gradient(left, #35ac136b 0, #80e8626b " +
                  value +
                  "%, transparent 100%);background: -linear-gradient(left, #35ac136b 0, #80e8626b " +
                  value +
                  "%, transparent 100%);";
          } else {
              css =
                  "background: -moz-linear-gradient(left, #ea4d4da3 0, #ff8080a3 " +
                  value +
                  "%, transparent 100%);background: -webkit-linear-gradient(left, #ea4d4da3 0, #ff8080a3 " +
                  value +
                  "%, transparent 100%);background: -linear-gradient(left, #ea4d4da3 0, #ff8080a3 " +
                  value +
                  "%, transparent 100%);";
          }
          return css;
      },
  },
  watch: {},
  data: () => ({
    toFixedCustom: toFixedCustom,
  }),
};
</script>
