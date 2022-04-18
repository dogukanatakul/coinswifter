<template>
  <div class="markets pb70 mb-5 pb-3">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          <b-card no-body>
            <b-tabs v:model="$route.params.parityName" card>
              <b-tab v-for="(parityGroups, parities_key) in parities" :title="parities_key" @click="$emit('update:parityName', parities_key)" >
                <b-card-text id="table">
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th class="overflowed-table">{{ $t("İşlem Çifti") }}</th>
                          <th class="overflowed-table">{{ $t("Fiyat") }}</th>
                          <th class="td1 overflowed-table">{{ $t("Değişim 1 Saat") }}</th>
                          <th class="td2 overflowed-table">{{ $t("Değişim 24 Saat") }}</th>
                          <th class="overflowed-table">{{ $t("Piyasa Değeri") }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(parity, parity_key) in parityGroups" :key="parity_key" style="cursor: pointer" @click="selectCoin(parity)" >
                          <td>
                            {{ [parity.coin.symbol, parity.source.symbol].join( "/" ) }}
                          </td>
                          <td>
                            {{ parity.parity_price.price.value }}
                          </td>
                          <td class="td1">
                            {{ parity.parity_price.percent_last_1_hours.value }}
                          </td>
                          <td class="td2">
                            {{ parity.parity_price.percent_last_24_hours.value }}
                          </td>
                          <td>
                            {{ parity.parity_price.market_price.value }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </b-card-text>
              </b-tab>
            </b-tabs>
          </b-card>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "MarketList",
  props: ["parities", "parityName", "selectedCoin"],
  emits: ["update:parityName", "update:selectedCoin"],

  methods: {
    selectCoin(value) {
      this.$router.push({
        name: "exchange",
        params: { parity: value.source.symbol + "-" + value.coin.symbol },
      });
    },
  },
};
</script>
<style scoped lang="scss">

</style>
