<template>
    <b-card v-if="is_bank!==null">
        <template #header>
            <div class="float-start">
                <h5>{{ walletSelect.name }} {{ walletAction === 'deposit' ? $t("Yatırma") : $t("Çekme") }}</h5>
                <span>
                        <label>{{ $t("Toplam Varlık") }}</label>
                        <label class="mx-2">{{ walletSelect.total_balance }} {{ walletSelect.symbol }}</label>
                    </span>
            </div>
            <div class="float-end mt-2">
                <b-button-group>
                    <b-button @click="walletAction='deposit'" :variant="(walletAction!=='deposit'?'outline-':'') + 'success'">{{ $t("Yatırma") }}</b-button>
                    <b-button @click="walletAction='withdrawal'" :variant="(walletAction!=='withdrawal'?'outline-':'') +'danger'">{{ $t("Çekme") }}</b-button>
                </b-button-group>
            </div>
        </template>

        <b-row v-if="is_bank && walletAction==='deposit'">
            <b-col cols="12">
                <v-select
                    placeholder="Banka Seçin"
                    v-model.number="selectedBank"
                    label="text"
                    :options="banksSelect"
                    :reduce="data => data.value"
                ></v-select>
            </b-col>
            <b-col cols="12">
                <table class="table">
                    <tbody>
                    <tr>
                        <th scope="row">{{ $t("Banka") }}</th>
                        <td>{{ selectedBank.bank }}
                        </td>
                    </tr>
                    <tr class="cursor-pointer" @click="fastCopy(selectedBank.account_name, $t('Hesap adı kopyalandı.'))">
                        <th scope="row">{{ $t("Hesap Adı") }}</th>
                        <td>{{ selectedBank.account_name }}
                            <b-icon icon="files"></b-icon>
                        </td>
                    </tr>
                    <tr class="cursor-pointer" @click="fastCopy(selectedBank.iban, $t('Iban kopyalandı.'))">
                        <th scope="row">{{ $t("Iban") }}</th>
                        <td>{{ selectedBank.iban }}
                            <b-icon icon="files"></b-icon>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </b-col>
            <b-col cols="12">
                <p class="text-wrap text-center" v-html="$t('kasa_cuzdan_uyari')"></p>
                <p class="text-wrap text-center">
                    <b class="text-danger">{{ $t("Ziraat bankası ödeme yollarıyla 7/24 hesabınıza bakiye tanımlayabilirsiniz.") }}</b>
                </p>
            </b-col>
        </b-row>
        <b-row v-else-if="is_bank && walletAction==='withdrawal' && user.kyc===true">
            <b-col cols="12" md="4">
                <table class="table table-sm table-responsive">
                    <tr>
                        <th>Minimum:</th>
                        <td class="text--right">{{ walletSelect.transfer_min }} {{ walletSelect.symbol }}</td>
                    </tr>
                    <tr>
                        <th>Maksimum:</th>
                        <td class="text--right">{{ walletSelect.transfer_max }} {{ walletSelect.symbol }}</td>
                    </tr>
                    <tr>
                        <th>İşlem Bedeli Oranı:</th>
                        <td class="text--right">{{ walletSelect.commission_out }} {{ walletSelect.commission_type === 'percent' ? '%' : walletSelect.symbol }}</td>
                    </tr>
                    <tr>
                        <th>Çekim Bedeli:</th>
                        <td class="text--right" v-if="walletSelect.commission_type==='percent'">{{ (parseFloat(form['amount']) * parseFloat(walletSelect.commission_out)) / 100 }} {{ walletSelect.symbol }}</td>
                        <td class="text--right" v-else>{{ walletSelect.commission_out }} {{ walletSelect.symbol }}</td>
                    </tr>
                    <tr>
                        <th>Gidecek Tutar:</th>
                        <td class="text--right" v-if="walletSelect.commission_type==='percent'">{{ form['amount'] - ((parseFloat(form['amount']) * parseFloat(walletSelect.commission_out)) / 100) }} {{ walletSelect.symbol }}</td>
                        <td class="text--right" v-else>{{ form['amount'] - parseFloat(walletSelect.commission_out) }} {{ walletSelect.symbol }}</td>
                    </tr>
                </table>
            </b-col>
            <b-col cols="12" md="8">
                <b-form @submit="withdrawalSend">
                    <b-row align-v="center" align-h="center">
                        <b-col cols="12">
                            <b-input-group :append="walletSelect.symbol" class="mt-3">
                                <b-form-input type="number" :min="walletSelect.minimum" step="0.00000001"
                                              style="appearance: textfield;"
                                              :max="walletSelect.balance" v-model="form['amount']" inputmode="number" autofocus></b-form-input>
                            </b-input-group>
                            <b-form-invalid-feedback :state="!v$.form.amount.$error">
                                <p class="text-danger">
                                    {{ $t("Lütfen geçerli bir tutar giriniz.") }}
                                </p>
                            </b-form-invalid-feedback>
                        </b-col>
                        <b-col cols="1" class="mx-3 px-3 mt-3">
                            <b-icon icon="box-arrow-down" animation="cylon-vertical" font-scale="2"></b-icon>
                        </b-col>
                        <b-col cols="12">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">{{ $t("Banka") }}</th>
                                    <th scope="col">{{ $t("Iban") }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row">{{ userBank.bank.name }}</th>
                                    <td>{{ userBank.iban }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </b-col>
                        <b-col cols="12">
                            <div class="d-grid gap-2">
                                <b-button block variant="primary" type="submit">{{ $t("Çekim Talebi Oluştur") }}</b-button>
                            </div>
                        </b-col>
                    </b-row>
                </b-form>
            </b-col>
        </b-row>
        <b-row v-else-if="is_bank && walletAction==='withdrawal' && user.kyc===false" class="justify-content-center align-content-center">
            <b-col cols="12" md="8" class="text-center">
                <div>
                    {{ $t("Çekim talebi oluşturmadan önce KYC onayı yapmanız gerekmektedir.") }}
                </div>
                <router-link :to="{name:'profile.kyc'}">
                    <b-button variant="outline-primary">{{ $t("KYC Onayı Ekranına Git") }}</b-button>
                </router-link>
            </b-col>
        </b-row>
        <b-row v-else>
            <b-card class="justify-content-center text-center py-4" no-body>
                <p class="text-wrap lh-lg" v-html="$t('kasa_cuzdan_uyari_2')"></p>
                <p class="alert alert-danger fw-bolder bg-danger text-light lh-lg text-wrap">{{ $t("Gönderim yapacağınız banka hesabı sizin adınıza olmalıdır! Hesap ismi ve Hesap TC Kimlik numarası eşleşmeyen gönderimler gönderici hesaba iade edilir.") }}</p>
                <b-button class="mx-1" @click="goBank" variant="success">{{ $t("Banka Hesabı Tanımla") }}</b-button>
            </b-card>
        </b-row>
    </b-card>
</template>

<script>
import restAPI from "../../../api/restAPI";
import {copyText} from "vue3-clipboard";
import useVuelidate from "@vuelidate/core";
import {required, minValue} from "@vuelidate/validators";
import {mapGetters} from "vuex";


export default {
    name: "SafeWallet",
    data() {
        return {
            is_bank: null,
            system_banks: [],
            userBank: false,
            selectedBank: {},
            walletAction: 'deposit',
            form: {
                amount: parseFloat(this.walletSelect.transfer_min),
            },
        }
    },
    props: [
        'walletSelect'
    ],
    setup() {
        return {v$: useVuelidate()}
    },
    validations() {
        return {
            form: {
                amount: {
                    required,
                    minValue: minValue(this.walletSelect.transfer_min)
                },
            }
        }
    },
    async created() {
        await restAPI.getData({Action: "banks"}).then((response) => {
            if (response.status === 'success') {
                this.system_banks = response.system_banks
                this.selectedBank = response.system_banks[0]
                this.userBank = response.user_banks.filter(element => element.primary)
                if (this.userBank.length > 0) {
                    this.userBank = this.userBank[0]
                } else {
                    this.userBank = false;
                }
                this.is_bank = (response.user_banks.length > 0)
            } else if (response.status === 'fail') {
                this.$notify({text: response.message, type: 'error'})
            }
        })
    },
    methods: {
        async withdrawalSend() {
            const isFormCorrect = await this.v$.$validate()
            if (!isFormCorrect) return
            await restAPI.getData({Action: "withdrawal"}, this.form).then((response) => {
                if (response.status === 'success') {
                    this.$notify({text: response.message, type: 'success'})
                    this.$emit('getWallets')
                } else if (response.status === 'fail') {
                    this.$notify({text: response.message, type: 'error'})
                }
            })
        },
        goBank() {
            this.$router.push({name: 'profile.banks'})
        },
        fastCopy(text, message) {
            copyText(text, undefined, (error, event) => {
                if (error) {
                    this.$notify({text: $t('Başarısız kopyalama!'), type: 'error'})
                } else {
                    this.$notify({text: message, type: 'success'})
                }
            })
            this.$refs.walletCode.input.select()
        }
    },
    computed: {
        banksSelect() {
            return this.system_banks.map(function (item) {
                return {
                    text: item.bank,
                    value: item
                }
            })
        },
        ...mapGetters([
            'user',
        ])
    }
}
</script>

<style scoped>

</style>
