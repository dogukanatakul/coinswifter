<template>
    <div class="wallet">
        <b-row class="mx-lg-2 my-lg-2 mx-md-2 my-md-2" v-on:scroll.passive="handleScroll" ref="handleScroll">
            <b-col cols="12" sm="12" md="4">
                <b-list-group class="wallet-coins">
                    <b-list-group-item
                        class="d-flex justify-content-between align-items-start"
                        style="cursor: pointer;"
                        @click="walletSelected('account_status')"
                        :active="(walletSelect==='account_status')"
                    >
                        <b-row class="w-100">
                            <b-col cols="12" md="5">
                                <div class="fw-bold text-center text-md-start">{{ $t("Varlıklarım") }}</div>
                            </b-col>
                            <b-col cols="12" md="6">
                                <b-badge variant="dark" class="w-100">{{ totalMount.total }} TRY</b-badge>
                            </b-col>
                        </b-row>
                    </b-list-group-item>
                    <b-list-group-item
                        v-for="(wallet, wallet_key) in wallets"
                        :key="wallet_key"
                        class="d-flex justify-content-between align-items-start"
                        style="cursor: pointer;"
                        @click="walletSelected(wallet)"
                        :active="((walletSelect!==null && walletSelect!=='account_status' && wallet.symbol === walletSelect.symbol) ? true : false)"
                    >
                        <b-row class="w-100" v-if="parseInt(wallet.locked)!==0">
                            <b-col cols="12" md="5" class="text-center text-md-start">
                                <span class="fw-bold ">{{ wallet.symbol }}</span>
                                {{ wallet.name }}
                            </b-col>
                            <b-col cols="6" md="3" v-if="parseInt(wallet.locked)!==0">
                                <b-badge variant="warning" class="float-left mx-1 w-100">
                                    <b-icon icon="lock-fill" font-scale="1"></b-icon>
                                    {{ wallet.locked }} {{ wallet.symbol }}
                                </b-badge>
                            </b-col>
                            <b-col cols="6" md="4">
                                <b-badge variant="secondary" class="float-left mx-1 w-100">{{ parseFloat(wallet.balance) }} {{ wallet.symbol }}</b-badge>
                            </b-col>
                        </b-row>
                        <b-row class="w-100" v-else>
                            <b-col cols="12" md="5" class="text-center text-md-start">
                                <span class="fw-bold ">{{ wallet.symbol }}</span>
                                {{ wallet.name }}
                            </b-col>
                            <b-col cols="12" md="6">
                                <b-badge variant="secondary" class="float-left mx-1 w-100">{{ parseFloat(wallet.balance) }} {{ wallet.symbol }}</b-badge>
                            </b-col>
                        </b-row>
                    </b-list-group-item>
                </b-list-group>
            </b-col>
            <b-col cols="12" sm="12" md="8" class="mb-5 pb-2" v-if="walletSelect">
                <wallet-status v-if="walletSelect==='account_status'" :totalMount="totalMount"></wallet-status>
                <wallet-detail v-else :key="walletSelect" :walletSelect="walletSelect" @getWallets="getWallets" @withdrawalSend="withdrawalSend"></wallet-detail>
            </b-col>
        </b-row>
    </div>
</template>

<script>
import restAPI from "../../api/restAPI";
import {mapGetters} from "vuex";
import {convertDate} from "../../helpers/helpers";
import WalletStatus from "./Wallet/WalletStatus";
import WalletDetail from "./Wallet/WalletDetail";

export default {
    name: "Wallet",
    components: {WalletDetail, WalletStatus},

    async created() {
        this.loader = this.$loading.show({
            container: null,
            canCancel: false,
            onCancel: this.onCancel,
        });
        await this.getWallets().then(() => {
            this.walletSelect = Object.values(this.wallets)[0]
        })
    },
    data: () => ({
        wallets: [],
        totalMount: {},
        walletSelect: false,
        commission: 0,
        loader: null,
        convertDate: convertDate
    }),
    computed: {
        ...mapGetters(["user"]),
    },
    async mounted() {
        await this.$nextTick(() => {
            window.scrollTo(100, 0);
        })
    },
    methods: {
        handleScroll(event) {
            console.log(event.target.scrollLeft)
        },
        async deleteOrder(microtime) {
            await this.$confirm.require({
                message: 'Silmek istediğinize emin misiniz?',
                header: 'Emir Silme İşlemi',
                icon: 'pi pi-exclamation-triangle',
                acceptLabel: 'Evet',
                rejectLabel: 'Vazgeç',
                accept: () => {
                    restAPI.getData({
                        Action: "delete-order/" + microtime,
                    }).then((response) => {
                        if (response.status === 'success') {
                            this.$notify({text: response.message, type: 'success'})
                            this.getWallets().then(() => {
                                this.walletSelect = this.wallets.find(o => o.symbol === this.walletSelect.symbol)
                            })
                        } else if (response.status === 'fail') {
                            this.$notify({text: response.message, type: 'error'})
                        }
                    });
                }
            });
        },
        async getWallets() {
            await restAPI.getData({
                Action: "my-wallets",
            }).then((response) => {
                if (response.status === 'success') {
                    this.wallets = response.data
                    this.totalMount = response.total
                }
                this.loader.hide()
            });
            return ""
        },
        walletSelected(item) {
            this.walletSelect = item
        },

        async withdrawalSend(form) {
            let setForm = form
            setForm['coin'] = this.walletSelect.symbol
            for (const [key, value] of Object.entries(setForm)) {
                if (key == 'amount') {
                    setForm[key] = value.toString()
                } else {
                    setForm[key] = value
                }
            }
            await restAPI.getData({Action: "withdrawal-wallet"}, setForm).then((response) => {
                if (response.status === "success") {
                    this.$notify({text: response.message, type: 'success'})
                    this.getWallets()
                } else {
                    this.$notify({text: response.message, type: 'error'})
                }
            })
        },
    }
};
</script>
<style lang="scss" scoped>
.wallet-coins {
    max-height: 90vh;
    overflow-x: auto;
}
</style>
