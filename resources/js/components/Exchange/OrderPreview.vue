<template>
  <b-list-group class="order-watch">
        <b-list-group-item class="d-flex justify-content-between align-items-center">
            <span class="threedot">{{ $t('Mevcut') }}</span>
            <b-badge pill class="threedot">{{ getCurrent.price }} {{ getCurrent.symbol }}</b-badge>
        </b-list-group-item>
        <b-list-group-item class="d-flex justify-content-between align-items-center">
            <span class="threedot">{{ $t('Miktar') }}</span>
            <b-badge pill class="threedot">{{ getAmount.price }} {{ getAmount.symbol }}</b-badge>
        </b-list-group-item>
        <b-list-group-item class="d-flex justify-content-between align-items-center">
            <span class="threedot">{{ $t('Komisyon') }} ({{ commission }}%)</span>
            <b-badge pill class="threedot">{{ getCommission.price }} {{ getCommission.symbol }}</b-badge>
        </b-list-group-item>
        <b-list-group-item class="d-flex justify-content-between align-items-center">
            <span class="threedot">{{ $t('Toplam') }}</span>
            <b-badge pill class="threedot">{{ getTotal.price }} {{ getTotal.symbol }}</b-badge>
        </b-list-group-item>
    </b-list-group>
  <!-- <b-row class="order-watch bordered">
    <b-col cols="12" class="px-auto bordered-bottom my-1">
      <b-col cols="12" xl="3" class="float-left text-small text-aligned-left">{{
        $t("Mevcut")
      }}</b-col>
      <b-col
        cols="12"
        xl="9"
        class="float-left text-small text-aligned-right text-responsive mb-2"
        ><b-badge pill
          >{{ getCurrent.price }} {{ getCurrent.symbol }}</b-badge
        ></b-col
      >
    </b-col>
    <b-col cols="12" class="px-auto bordered-bottom my-1">
      <b-col cols="12" xl="3" class="float-left text-small text-aligned-left">{{
        $t("Miktar")
      }}</b-col>
      <b-col
        cols="12"
        xl="9"
        class="float-left text-small text-aligned-right text-responsive mb-2"
        ><b-badge pill
          >{{ getAmount.price }} {{ getAmount.symbol }}</b-badge
        ></b-col
      >
    </b-col>
    <b-col cols="12" class="px-auto bordered-bottom my-1">
      <b-col cols="12" xl="3" class="float-left text-small text-aligned-left"
        >{{ $t("Komisyon") }} ({{ commission }}%)</b-col
      >
      <b-col
        cols="12"
        xl="9"
        class="float-left text-small text-aligned-right text-responsive mb-2"
        ><b-badge pill
          >{{ getCommission.price }} {{ getCommission.symbol }}</b-badge
        ></b-col
      >
    </b-col>
    <b-col cols="12" class="px-auto bordered-bottom my-1">
      <b-col cols="12" xl="3" class="float-left text-small text-aligned-left">{{
        $t("Toplam")
      }}</b-col>
      <b-col
        cols="12"
        xl="9"
        class="float-left text-small text-aligned-right text-responsive mb-2"
        ><b-badge pill
          >{{ getTotal.price }} {{ getTotal.symbol }}</b-badge
        ></b-col
      >
    </b-col>
  </b-row> -->
</template>

<script>
import { toFixedCustom, priceFormat } from "../../helpers/helpers";

export default {
  name: "OrderPreview",
  props: ["form", "commission", "wallet", "type", "selectedCoin"],
  created() {
    if (this.type === "sell") {
      this.sourceType = "coin";
      this.coinType = "source";
    }
  },
  data: () => ({
    sourceType: "source",
    coinType: "coin",
    toFixedCustom: toFixedCustom,
    priceFormat: priceFormat,
  }),
  computed: {
    getAmount() {
      return {
        price:
          this.type === "buy"
            ? priceFormat((this.form.amount / 100) * (100 - this.commission))
            : this.form.amount,
        symbol: this.selectedCoin["coin"].symbol,
      };
    },
    getTotal() {
      return {
        price:
          this.type === "sell"
            ? priceFormat((this.form.total / 100) * (100 - this.commission))
            : priceFormat(this.form.total),
        symbol: this.selectedCoin["source"].symbol,
      };
    },
    getCommission() {
      return {
        price: priceFormat((this.form.amount / 100) * this.commission),
        symbol: this.selectedCoin[this.coinType].symbol,
      };
    },
    getCurrent() {
      let wallet = 0;
      if (this.wallet[this.sourceType] !== undefined) {
        wallet = this.wallet[this.sourceType].balance;
      }
      return {
        price: priceFormat(wallet),
        symbol: this.selectedCoin[this.sourceType].symbol,
      };
    },
  },
};
</script>

<style scoped type="scss">
.threedot{
  overflow:hidden; white-space:nowrap; text-overflow:ellipsis; max-width:65% !important;
}
</style>
