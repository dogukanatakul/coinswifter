<template>
    <div class="wallet">
        <b-row class="" v-on:scroll.passive="handleScroll" ref="handleScroll">
            <b-col cols="12" md="6" sm="6" class="px-auto d-lg-none">
                <div class="border myRounded text-sm-center xs-center mx-auto my-5 myDiv" @click="walletSelected('account_status')" :active="walletSelect === 'account_status'" v-b-tooltip v-b-tooltip.hover :title="totalMount.total + ' TRY'">
                    <h4 class="my-5 pt-sm-5">{{ $t("Toplam Varlık") }}</h4>
                    <h6 class="text-small my-5 mx-auto overflowed" style="max-width: 180px">
                        {{ totalMount.total }} TRY
                    </h6>
                </div>
            </b-col>
            <b-col cols="12" md="6" sm="6" lg="4" class="px-auto float-left mb-3">
                <b-list-group class="wallet-coins myOverflow my-sm-3 my-md-3">
                    <b-list-group-item class=" d-flex justify-content-between align-items-start d-none d-lg-block " style="cursor: pointer" @click="walletSelected('account_status')" :active="walletSelect === 'account_status'">
                        <b-row class="w-100">
                            <b-col cols="12" class="rounded bg-dark text-light mx-3" v-b-tooltip v-b-tooltip.hover :title="totalMount.total + ' TRY'">
                                <b-col cols="12" class="float-left px-3 overflowed-table">
                                    <span class="fw-bold small">{{ $t("Varlıklarım") }}</span>
                                </b-col>
                                <b-col cols="12" class="float-left px-3 overflowed-table">
                                    <span class="fw-bold small">{{ totalMount.total }} TRY</span>
                                </b-col>
                            </b-col>
                        </b-row>
                    </b-list-group-item>
                    <b-list-group-item v-for="(wallet, wallet_key) in wallets" :key="wallet_key" class="justify-content-between align-items-start" style="cursor: pointer" @click="walletSelected(wallet)"
                                       :active=" walletSelect !== null && walletSelect !== 'account_status' && wallet.symbol === walletSelect.symbol ? true : false ">
                        <b-row class="w-100 px-auto mx-2" v-if="parseInt(wallet.locked) !== 0">
                            <!-- <b-col cols="12" md="5" class="text-center text-md-start"> <span class="fw-bold">{{ wallet.symbol }}</span> {{ wallet.name }} </b-col> <b-col cols="6" md="3" v-if="parseInt(wallet.locked) !== 0"> <b-badge variant="warning" class="float-left mx-1 w-100"> <b-icon icon="lock-fill" font-scale="1"></b-icon> {{ wallet.locked }} {{ wallet.symbol }} </b-badge> </b-col> <b-col cols="6" md="4"> <b-badge variant="secondary" class="float-left mx-1 w-100" >{{ parseFloat(wallet.balance) }}
                              {{ wallet.symbol }}</b-badge
                              >
                            </b-col> -->
                            <b-col cols="12" class="rounded myBackground text-dark mx-auto" :class="{ smallest: parseInt(wallet.locked) === 0 }">
                                <b-col cols="12" class="float-left px-3 overflowed-wallet" v-b-tooltip v-b-tooltip.hover :title="wallet.symbol">
                                    <img :src="'../assets/img/coinicon/' + wallet.symbol + '.png'" alt="" width="16" height="16" class="rounded" @error="onImgError"/>
                                    <span class="fw-bold mx-2" id="symbols">{{ wallet.symbol }}</span>
                                </b-col>
                                <b-col cols="12" class="float-left px-3 overflowed-wallet" v-if="wallet.locked !== 0" v-b-tooltip v-b-tooltip.hover :title="wallet.locked">
                                    <b-icon icon="lock-fill" font-scale="1" class="p-0 myMargins" :class="{ smallest: parseInt(wallet.locked) === 0 }"></b-icon>
                                    <span class="">{{ wallet.locked }}</span>
                                </b-col>
                                <b-col cols="12" class="float-left px-3 overflowed-wallet" v-b-tooltip v-b-tooltip.hover :title="wallet.balance">
                                    <span class="">{{ wallet.balance }}</span>
                                </b-col>
                            </b-col>
                        </b-row>
                        <b-row class="w-100 pl-2 mx-2" v-else>
                            <div class="rounded myBackground text-dark mx-auto">
                                <b-col cols="12" class="float-left px-3 overflowed-wallet" v-b-tooltip v-b-tooltip.hover :title="wallet.symbol">
                                    <img :src="'../assets/img/coinicon/' + wallet.symbol + '.png'" alt="" width="16" height="16" class="rounded" @error="onImgError"/>

                                    <span class="fw-bold mx-2">{{ wallet.symbol }}</span>
                                </b-col>
                                <b-col cols="12" class="float-left px-3 overflowed-wallet">
                                  &nbsp;
                                </b-col>
                                <b-col cols="12" class="float-left px-3 overflowed-wallet" v-b-tooltip v-b-tooltip.hover :title="wallet.balance">
                                    <span class="">{{ wallet.balance }}</span>
                                </b-col>
                            </div>
                        </b-row>
                    </b-list-group-item>
                </b-list-group>
            </b-col>
            <b-col cols="12" sm="12" lg="8" class="mb-5 pb-2 float-left" v-if="walletSelect">
                <wallet-status v-if="walletSelect === 'account_status'" :totalMount="totalMount"></wallet-status>
                <wallet-detail v-else v-model:dynamicContent="dynamicContent" v-model:walletSelect="walletSelect" @getWallets="getWallets" @withdrawalSend="withdrawalSend"></wallet-detail>
            </b-col>
        </b-row>
        <b-row></b-row>
    </div>
</template>
<script>
import restAPI from "../../api/restAPI";
import {mapGetters} from "vuex";
import {convertDate} from "../../helpers/helpers";
import WalletStatus from "./Wallet/WalletStatus";
import WalletDetail from "./Wallet/WalletDetail";

const initialData = () => ({
    dynamicInput: "number",
    wallets: [],
    totalMount: {},
    walletSelect: false,
    commission: 0,
    loader: null,
    convertDate: convertDate,
    setInterval: null,
    dynamicContent: 0,
    imgError: false,
});
export default {
    name: "wallets",
    components: {WalletDetail, WalletStatus},

    async created() {
        await this.getWallets().then(() => {
            this.walletSelect = Object.values(this.wallets)[0];
        });
    },
    data: function () {
        return initialData();
    },
    computed: {
        ...mapGetters(["user"]),
    },
    async mounted() {
        await this.$nextTick(() => {
            window.scrollTo(100, 0);
        });
        this.setInterval = setInterval(
            function () {
                this.getWallets().then(() => {
                    this.walletSelect = Object.values(this.wallets).find(
                        (o) => o.symbol === this.walletSelect.symbol
                    );
                });
            }.bind(this),
            5000
        );
    },
    methods: {
        dynamicFocus(method) {
            if (method === "in") {
                this.dynamicInput = "input";
            } else {
                this.dynamicInput = "number";
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
                                this.$notify({text: response.message, type: "success"});
                                this.getWallets().then(() => {
                                    this.walletSelect = Object.values(this.wallets).find(
                                        (o) => o.symbol === this.walletSelect.symbol
                                    );
                                });
                            } else if (response.status === "fail") {
                                this.$notify({text: response.message, type: "error"});
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
                        this.dynamicContent += 1
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
                .getData({Action: "withdrawal-wallet"}, setForm)
                .then((response) => {
                    if (response.status === "success") {
                        this.$notify({text: response.message, type: "success"});
                        this.getWallets().then(() => {
                            this.walletSelect = Object.values(this.wallets).find(
                                (o) => o.symbol === this.walletSelect.symbol
                            );
                        });
                        const index = Object.keys(this.wallets).indexOf(
                            this.walletSelect.symbol
                        );
                        this.walletSelect = Object.values(this.wallets)[index];
                    } else {
                        this.$notify({text: response.message, type: "error"});
                    }
                });
        },
        onImgError(event){
            this.imgError = true;
            event.target.src = '../assets/img/coinicon/empty-token.png';
        }
    },
    watch: {
        async walletSelect(value) {
            if (value !== undefined && Object.values(value) > 0) {
                Object.assign(this.$data, initialData())
            }
            await this.getWallets();
        },
    },
    beforeUnmount: function () {
        clearInterval(this.setInterval);
    },
};
</script>
<style scoped type="scss">
</style>
