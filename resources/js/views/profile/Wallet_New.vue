<template>
  <div class="wallet">
    <b-row class="mx-lg-2 my-lg-2 mx-md-2 my-md-2" v-on:scroll.passive="handleScroll" ref="handleScroll" >
      <b-col cols="12" md="6" sm="6" class="px-auto d-lg-none">
        <div class="border myRounded text-sm-center xs-center mx-auto my-5 myDiv" @click="walletSelected('account_status')" :active="walletSelect === 'account_status'" >
          <h4 class="my-5 pt-sm-5">Toplam Varlık</h4>
          <h6 class="text-small my-5">{{ totalMount.total }} TRY</h6>
        </div>
      </b-col>
      <b-col cols="12" md="6" sm="6" lg="4" class="px-auto float-left mb-3">
        <b-list-group class="wallet-coins myOverflow my-sm-3 my-md-3">
          <b-list-group-item class=" d-flex justify-content-between align-items-start d-none d-lg-block " style="cursor: pointer" @click="walletSelected('account_status')" :active="walletSelect === 'account_status'" >
            <b-row class="w-100">
              <b-col cols="12" class="rounded bg-dark text-light mx-auto">
                <b-col cols="6" class="float-left text-center">
                  <span class="fw-bold small">{{ $t("Varlıklarım") }}</span>
                </b-col>
                <b-col cols="6" class="float-left">
                  <span class="fw-bold small">{{ totalMount.total }} TRY</span>
                </b-col>
              </b-col>
            </b-row>
          </b-list-group-item>
          <b-list-group-item v-for="(wallet, wallet_key) in wallets" :key="wallet_key" class="d-flex justify-content-between align-items-start" style="cursor: pointer" @click="walletSelected(wallet)" :active=" walletSelect !== null && walletSelect !== 'account_status' && wallet.symbol === walletSelect.symbol ? true : false " >
            <b-row class="w-100 px-auto" v-if="parseInt(wallet.locked) !== 0">
              <!-- <b-col cols="12" md="5" class="text-center text-md-start"> <span class="fw-bold">{{ wallet.symbol }}</span> {{ wallet.name }} </b-col> <b-col cols="6" md="3" v-if="parseInt(wallet.locked) !== 0"> <b-badge variant="warning" class="float-left mx-1 w-100"> <b-icon icon="lock-fill" font-scale="1"></b-icon> {{ wallet.locked }} {{ wallet.symbol }} </b-badge> </b-col> <b-col cols="6" md="4"> <b-badge variant="secondary" class="float-left mx-1 w-100" >{{ parseFloat(wallet.balance) }}
                {{ wallet.symbol }}</b-badge
                >
              </b-col> -->
              <b-col cols="12" class="rounded myBackground text-dark mx-auto" :class="{ smallest: parseInt(wallet.locked) === 0 }" >
                <b-col cols="5" class="float-left">
                  <img :src="'../assets/img/coinicon/' + wallet.symbol + '.png'" alt="" width="16" height="16" class="rounded" />
                  <span class="fw-bold small mx-2">{{ wallet.symbol }}</span>
                </b-col>
                <b-col cols="3" class="float-left" v-if="wallet.locked !== 0">
                  <b-icon icon="lock-fill" font-scale="1" class="small p-0 myMargins" :class="{ smallest: parseInt(wallet.locked) === 0 }" ></b-icon>
                  <span class="small">{{ wallet.locked }}</span>
                </b-col>
                <b-col cols="4" class="float-left">
                  <span class="small">{{ wallet.balance }}</span>
                </b-col>
              </b-col>
            </b-row>
            <b-row class="w-100" v-else>
              <!-- <b-col cols="12" md="5" class="text-center text-md-start"> <span class="fw-bold">{{ wallet.symbol }}</span> {{ wallet.name }} </b-col> <b-col cols="12" md="6"> <b-badge variant="secondary" class="float-left mx-1 w-100" >{{ parseFloat(wallet.balance) }} {{ wallet.symbol }}</b-badge
                >
              </b-col> -->
              <div class="rounded myBackground text-dark mx-auto">
                <b-col cols="6" class="float-left">
                  <img :src="'../assets/img/coinicon/' + wallet.symbol + '.png'" alt="" width="16" height="16" class="rounded" />

                  <span class="fw-bold small mx-2">{{ wallet.symbol }}</span>
                </b-col>
                <b-col cols="6" class="float-left">
                  <span class="small">{{ wallet.balance }}</span>
                </b-col>
              </div>
            </b-row>
          </b-list-group-item>
        </b-list-group>
      </b-col>

      <b-col cols="12" sm="12" lg="8" class="mb-5 pb-2 float-left" v-if="walletSelect" >
        <wallet-status v-if="walletSelect === 'account_status'" :totalMount="totalMount" ></wallet-status>
        <wallet-detail v-else :key="walletSelect" :walletSelect="walletSelect" @getWallets="getWallets" @withdrawalSend="withdrawalSend" ></wallet-detail>
      </b-col>
    </b-row>
    <b-row> </b-row>
  </div>
</template>
<script>
import restAPI from "../../api/restAPI";
import { mapGetters } from "vuex";
import { convertDate } from "../../helpers/helpers";
import WalletStatus from "./Wallet/WalletStatus";
import WalletDetail from "./Wallet/WalletDetail";
export default {
  name: "wallets_new",
  components: { WalletDetail, WalletStatus },

  async created() {
    // this.loader = this.$loading.show({ container: null, canCancel: false, onCancel: this.onCancel,
    // });
    await this.getWallets().then(() => {
      this.walletSelect = Object.values(this.wallets)[0];
    });
  },
  data: () => ({
    dynamicInput: 'number',
    wallets: [],
    totalMount: {},
    walletSelect: false,
    commission: 0,
    loader: null,
    convertDate: convertDate,
  }),
  computed: {
    ...mapGetters(["user"]),
  },
  async mounted() {
    await this.$nextTick(() => {
      window.scrollTo(100, 0);
    });
  },
  methods: {
    dynamicFocus(method) {
            if (method === 'in') {
                this.dynamicInput = 'input'
            } else {
                this.dynamicInput = 'number'
            }
        },
    handleScroll(event) {
      console.log(event.target.scrollLeft);
    },
    async deleteOrder(microtime) {
      await this.$confirm.require({
        message: "Silmek istediğinize emin misiniz?",
        header: "Emir Silme İşlemi",
        icon: "pi pi-exclamation-triangle",
        acceptLabel: "Evet",
        rejectLabel: "Vazgeç",
        accept: () => {
          restAPI
            .getData({
              Action: "delete-order/" + microtime,
            })
            .then((response) => {
              if (response.status === "success") {
                this.$notify({ text: response.message, type: "success" });
                this.getWallets().then(() => {
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
    async getWallets() {
      await restAPI
        .getData({
          Action: "my-wallets",
        })
        .then((response) => {
          if (response.status === "success") {
            this.wallets = response.data;
            this.totalMount = response.total;
          }
          // this.loader.hide();
        });
      return "";
    },
    walletSelected(item) {
      this.walletSelect = item;
    },

    async withdrawalSend(form) {
      let setForm = form;
      setForm["coin"] = this.walletSelect.symbol;
      await restAPI
        .getData({ Action: "withdrawal-wallet" }, setForm)
        .then((response) => {
          if (response.status === "success") {
            this.$notify({ text: response.message, type: "success" });
            this.getWallets();
          } else {
            this.$notify({ text: response.message, type: "error" });
          }
        });
    },
  },
};
</script>
<style scoped type="scss">

</style>
