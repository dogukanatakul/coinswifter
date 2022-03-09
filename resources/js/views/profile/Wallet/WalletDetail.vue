<template>
    <safe-wallet v-if="walletSelect.network.short_name==='SOURCE'" v-model:walletSelect="walletSelect" @getWallets="getWallets"></safe-wallet>
    <b-card v-else>
        <template #header>
            <div class="float-start">
                <h5>{{ walletSelect.symbol }} {{ walletAction === 'deposit' ? 'Yatırma' : 'Çekme' }}</h5>
                <span>
                        <label>{{ $t("Toplam Varlık") }}</label>
                        <label class="mx-2">{{ parseFloat(walletSelect.total_balance) }} {{ walletSelect.symbol }}</label>
                    </span>
            </div>
            <div class="float-end">
                <b-button-group>
                    <b-button @click="walletAction='deposit'" :variant="(walletAction!=='deposit'?'outline-':'') + 'success'">{{ $t("Yatırma") }}</b-button>
                    <b-button v-if="walletSelect.durum!=='ico'" @click="walletAction='withdrawal'" :variant="(walletAction!=='withdrawal'?'outline-':'') +'danger'">{{ $t("Çekme") }}</b-button>
                </b-button-group>
            </div>
        </template>
        <b-card-text>
            <b-row class="justify-content-center align-items-center" v-if="walletAction === 'deposit'">
                <b-col cols="12" md="8">
                    <b-row align-content="center" align-h="center">
                        <b-col cols="4" class="text-center">
                            <img
                                :src="'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data='+walletSelect.wallet_code"
                                alt="qr-code"
                            />
                        </b-col>
                    </b-row>
                    <b-row align-content="center" align-h="center">
                        <b-col cols="12" md="8">
                            <b-input-group @click="copyWalletCode(walletSelect.wallet_code)" class="mt-3">
                                <b-form-input ref="walletCode" style="text-align: center;" autofocus :model-value="walletSelect.wallet_code" readonly></b-form-input>
                            </b-input-group>
                        </b-col>
                    </b-row>
                </b-col>
            </b-row>
            <b-row v-if="walletAction === 'withdrawal'">
                <b-col cols="12" md="4">
                    <table class="table table-sm table-responsive">
                        <tr>
                            <th>{{ $t("Minimum:") }}</th>
                            <td class="text--right">{{ parseFloat(walletSelect.transfer_min) }} {{ walletSelect.symbol }}</td>
                        </tr>
                        <tr>
                            <th>{{ $t("Maksimum:") }}</th>
                            <td class="text--right">{{ parseFloat(walletSelect.transfer_max) }} {{ walletSelect.symbol }}</td>
                        </tr>
                        <tr>
                            <th>{{ $t("İşlem Bedeli Oranı:") }}</th>
                            <td class="text--right">{{ parseFloat(walletSelect.commission_out) }}%</td>
                        </tr>
                        <tr>
                            <th>{{ $t("Transfer Bedeli:") }}</th>
                            <td class="text--right">{{ (parseFloat(form['amount']) * parseFloat(walletSelect.commission_out)) / 100 }} {{ walletSelect.symbol }}</td>
                        </tr>
                        <tr>
                            <th>{{ $t("Gidecek Tutar:") }}</th>
                            <td class="text--right">{{ form['amount'] - ((parseFloat(form['amount']) * parseFloat(walletSelect.commission_out)) / 100) }} {{ walletSelect.symbol }}</td>
                        </tr>
                    </table>
                </b-col>
                <b-col cols="12" md="8">
                    <b-form @submit="withdrawalSend">
                        <b-row align-v="center" align-h="center">
                            <b-col cols="12">
                                <b-input-group :append="walletSelect.symbol" class="mt-3">
                                    <b-form-input type="number" :min="parseFloat(walletSelect.transfer_min)" step="0.000001" :max="parseFloat(walletSelect.balance)" v-model="form['amount']" autofocus></b-form-input>
                                    <b-form-invalid-feedback :state="!v$.form.amount.$error">
                                        <p class="text-danger">
                                            {{ $t("Lütfen geçerli bir tutar giriniz.") }}
                                        </p>
                                    </b-form-invalid-feedback>
                                </b-input-group>
                            </b-col>
                            <b-col cols="1" class="mx-3 px-3 mt-3">
                                <b-icon icon="box-arrow-down" animation="cylon-vertical" font-scale="2"></b-icon>
                            </b-col>
                            <b-col cols="12">
                                <b-overlay :show="toWalletControl" rounded="sm">
                                    <b-input-group class="my-3">
                                        <b-form-input @paste="clipboardPaste" type="text" v-model="form['wallet']" :placeholder="$t('Gidecek cüzdan adresini giriniz')"></b-form-input>
                                        <b-form-invalid-feedback :state="!v$.form.wallet.$error">
                                            <p class="text-danger">
                                                {{ $t('Bu alan zorunludur.') }}
                                            </p>
                                        </b-form-invalid-feedback>
                                    </b-input-group>
                                    <template #overlay>
                                        <div class="text-center">
                                            <b-icon icon="search" font-scale="2" animation="throb"></b-icon>
                                            <p>{{ $t('Cüzdan kontrol ediliyor.') }}</p>
                                        </div>
                                    </template>
                                </b-overlay>
                            </b-col>
                            <b-col cols="12">
                                <div class="d-grid gap-2">
                                    <b-button block variant="primary" type="submit">{{ $t('Transfer İşlemini Başlat') }}</b-button>
                                </div>
                            </b-col>
                        </b-row>
                    </b-form>
                </b-col>

            </b-row>
        </b-card-text>
    </b-card>

    <b-card :header="$t('Aktif Emirler')" class="my-3">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ $t('Parite') }}</th>
                    <th>{{ $t('Miktar') }}</th>
                    <th>{{ $t('Tamamlanan') }}</th>
                    <th>{{ $t('Fiyat') }}</th>
                    <th>{{ $t('Tetikleme') }}</th>
                    <th>{{ $t('Tipi') }}</th>
                    <th>{{ $t('İşlem') }}</th>
                    <th>{{ $t('Tarih') }}</th>
                    <th>{{ $t('Aksiyon') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(order,order_key) in walletSelect.orders" :key="order_key"
                    :style="bgColorOrder(order.percent, order.operation)">
                    <td>{{ order.parity }}</td>
                    <td>{{ order.amount }}</td>
                    <td class="text-center">%{{ order.percent }}</td>
                    <td>{{ (order.price == '0' ? '-' : order.price) }}</td>
                    <td>{{ (order.trigger == '0' ? '-' : order.trigger) }}</td>
                    <td>{{ order.type }}</td>
                    <td>{{ order.operation }}</td>
                    <td>{{ order.created_at }}</td>
                    <td>
                        <b-button v-if="!order.is_deleted" squared variant="outline-danger" @click="deleteOrder(order.microtime)" size="sm">
                            <b-icon icon="x-circle" font-scale="0.5"></b-icon>
                        </b-button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </b-card>

    <b-card :header="$t('Çekim Talepleri')" header-tag="header" class="my-2 table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">{{ $t('Miktar') }}</th>
                <th scope="col">{{ $t('Gönderilen Miktar') }}</th>
                <th scope="col">{{ $t('Kesilen Komisyon') }}</th>
                <th scope="col">{{ $t('Alıcı Cüzdan') }}</th>
                <th scope="col">{{ $t('Tarih') }}</th>
                <th scope="col">{{ $t('Durum') }}</th>
                <th scope="col">{{ $t('İşlem') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(item, key) in walletSelect.user_withdrawal_wallet" :key="key">
                <th scope="row">{{ parseFloat(item.amount) }}</th>
                <th scope="row">{{ parseFloat(item.send_amount) }}</th>
                <th scope="row">{{ parseFloat(item.commission) }}</th>
                <td>{{ item.to }}</td>
                <td>{{ item.created_at }}</td>
                <td>{{ item.status }}</td>
                <td>
                    <b-button @click="deleteWithdrawalWallet(item.uuid)" block variant="primary">{{ $t("İptal") }}</b-button>
                </td>
            </tr>
            <tr v-for="(item, key) in walletSelect.user_withdrawal" :key="key">
                <th scope="row">{{ parseFloat(item.amount) }}</th>
                <td>{{ item.iban }}</td>
                <td>{{ item.created_at }}</td>
                <td>{{ item.status }}</td>
                <td>
                    <b-button @click="deleteWithdrawal(item.uuid)" block variant="primary">{{ $t("İptal") }}</b-button>
                </td>
            </tr>
            </tbody>
        </table>
    </b-card>
</template>

<script>

import SafeWallet from "./SafeWallet";
import {copyText} from "vue3-clipboard";
import useVuelidate from "@vuelidate/core";
import {required} from "@vuelidate/validators";
import restAPI from "../../../api/restAPI";

export default {
    name: "WalletDetail",
    setup() {
        return {v$: useVuelidate()}
    },
    components: {SafeWallet},
    props: [
        'walletSelect',
    ],
    data: () => ({
        walletCreateLoader: false,
        toWalletControl: false,
        walletAction: 'deposit', // deposit - withdrawal
        form: {
            amount: 0,
            wallet: null,
        },
    }),
    validations: () => ({
        form: {
            amount: {
                required,
            },
            wallet: {
                required
            },
        }
    }),
    watch: {
        'form.withdrawal'(val) {
            this.commission = (parseFloat(val) / 100) * parseFloat(3)
        }
    },
    methods: {
        async deleteWithdrawalWallet(uuid) {
            await this.$confirm.require({
                message: 'İptal etmek istediğinize emin misiniz?',
                header: 'Transfer İptal İşlemi',
                icon: 'pi pi-exclamation-triangle',
                acceptLabel: 'Evet',
                rejectLabel: 'Vazgeç',
                accept: () => {
                    restAPI.getData({
                        Action: "delete-withdrawal-wallet",
                    }, {uuid: uuid}).then((response) => {
                        if (response.status === 'success') {
                            this.$notify({text: response.message, type: 'success'})
                            this.getWallets()
                        } else if (response.status === 'fail') {
                            this.$notify({text: response.message, type: 'error'})
                        }
                    });
                }
            });
        },
        async deleteWithdrawal(uuid) {

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
                            this.getWallets()
                        } else if (response.status === 'fail') {
                            this.$notify({text: response.message, type: 'error'})
                        }
                    });
                }
            });
        },
        getWallets() {
            this.$emit('getWallets')
        },
        async createWallet() {
            this.$emit('createWallet')
        },
        copyWalletCode(code) {
            copyText(code, undefined, (error, event) => {
                if (error) {
                    this.$notify({text: $t('Başarısız kopyalama!'), type: 'error'})
                } else {
                    this.$notify({text: $t('Cüzdan kodu kopyalandı!'), type: 'success'})
                }
            })
            this.$refs.walletCode.input.select()
        },
        clipboardPaste(evt) {
            this.toWalletControl = false
            console.log('on paste', evt)
        },
        async withdrawalSend() {
            const isFormCorrect = await this.v$.$validate()
            if (!isFormCorrect) return
            this.$emit('withdrawalSend', this.form)
        },
        bgColorOrder(value, islem) {
            let css;
            if (islem === "buy") {
                css = "background: -moz-linear-gradient(left, #35ac136b 0, #80e8626b " + value + "%, transparent 100%);background: -webkit-linear-gradient(left, #35ac136b 0, #80e8626b " + value + "%, transparent 100%);background: -linear-gradient(left, #35ac136b 0, #80e8626b " + value + "%, transparent 100%);"
            } else {
                css = "background: -moz-linear-gradient(left, #ea4d4da3 0, #ff8080a3 " + value + "%, transparent 100%);background: -webkit-linear-gradient(left, #ea4d4da3 0, #ff8080a3 " + value + "%, transparent 100%);background: -linear-gradient(left, #ea4d4da3 0, #ff8080a3 " + value + "%, transparent 100%);"
            }
            return css
        }
    }
}
</script>

<style scoped>

</style>
