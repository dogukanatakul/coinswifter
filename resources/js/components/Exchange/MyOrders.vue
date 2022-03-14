<template>
  <b-card
    :header="$t('Emirlerim')"
    header-tag="header"
    class="my-2 p-0"
    no-body
  >
    <div class="table-responsive">
      <table class="table table-sm order-table">
        <thead>
          <tr>
            <th>{{ $t("Parite") }}</th>
            <th>{{ $t("Miktar") }}</th>
            <th>{{ $t("Kalan Miktar") }}</th>
            <th>{{ $t("Fiyat") }}</th>
            <th>{{ $t("Tamamlanan") }}</th>
            <!--                <th>{{ $t('Tetikleme') }}</th>-->
            <th>{{ $t("Tipi") }}</th>
            <th>{{ $t("İşlem") }}</th>
            <th>{{ $t("Tarih") }}</th>
            <th>{{ $t("İşlem") }}</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(order, order_key) in myOrders"
            :key="order_key"
            :style="bgColorOrder(order.percent, order.process)"
          >
            <td>
              {{ selectedCoin.coin.symbol + "/" + selectedCoin.source.symbol }}
            </td>
            <td>{{ order.amount_pure }} {{ selectedCoin.coin.symbol }}</td>
            <td>{{ order.amount }} {{ selectedCoin.coin.symbol }}</td>
            <td>
              {{ order.price == "0" ? "-" : order.price }}
              {{ order.price == "0" ? "" : selectedCoin.source.symbol }}
            </td>
            <td class="text-center">%{{ order.percent }}</td>
            <!--                <td>{{ (order.trigger == '0' ? '-' : order.trigger) }}</td>-->
            <td>{{ order.type }}</td>
            <td>{{ order.operation }}</td>
            <td>{{ order.created_at }}</td>
            <td>
              <b-button
                v-if="!order.is_deleted"
                squared
                variant="outline-danger"
                @click="deleteOrder(order.uuid)"
                size="sm"
              >
                <b-icon icon="x-circle" font-scale="0.5"></b-icon>
              </b-button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </b-card>
</template>

<script>
import { convertDate } from "../../helpers/helpers";
import restAPI from "../../api/restAPI";

export default {
  name: "MyOrders",
  props: ["myOrders", "selectedCoin"],
  data: () => ({
    convertDate: convertDate,
  }),
  methods: {
    async deleteOrder(uuid) {
      await this.$confirm.require({
        message: this.$t("Silmek istediğinize emin misiniz?"),
        header: this.$t("Emir Silme İşlemi"),
        icon: "pi pi-exclamation-triangle",
        acceptLabel: this.$t("Evet"),
        rejectLabel: this.$t("Vazgeç"),
        accept: () => {
          restAPI
            .getData({
              Action: "delete-order/" + uuid,
            })
            .then((response) => {
              if (response.status === "success") {
                this.$notify({ text: response.message, type: "success" });
                this.$emit("getParity").then(() => {
                  this.walletSelect = this.wallets.find(
                    (o) => o.symbol === this.walletSelect.symbol
                  );
                });
              } else if (response.status === "fail") {
                this.$notify({ text: response.message, type: "error" });
              }
            });
        },
      });
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
};
</script>

<style scoped>
</style>
