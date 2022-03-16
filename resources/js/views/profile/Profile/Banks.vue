<template>
    <b-form @submit="sendBank">
        <b-row>
            <b-col cols="12" md="5">
                <b-form-group
                    :label="$t('Banka')"
                >
                    <v-select
                        :placeholder="$t('Banka Seçin')"
                        v-model.number="form.bank"
                        label="text"
                        :options="banks"
                        :reduce="data => data.value"
                    ></v-select>
                    <b-form-text v-if="v$.form.bank.$error" class="text-danger">
                        <p class="text-danger">
                            {{ $t("Lütfen banka seçiniz!") }}
                        </p>
                    </b-form-text>
                </b-form-group>
            </b-col>
            <b-col cols="12" md="7">
                <b-form-group
                    label="Iban"
                >
                    <b-form-input
                        v-model="form.iban"
                        size="sm"
                        class="mt-3"
                        placeholder="TRXXXXXXXXXX"
                        inputmode="text"
                    ></b-form-input>
                    <b-form-text v-if="v$.form.iban.$error" class="text-danger">
                        <p class="text-danger">
                            Lütfen iban bilgisi giriniz!
                        </p>
                    </b-form-text>
                </b-form-group>
            </b-col>
            <b-col cols="5">
                <b-form-checkbox
                    v-model="form.primary"
                >
                    {{ $t("Birincil Olarak Ayarla") }}
                </b-form-checkbox>
            </b-col>
            <b-col cols="7">
                <div class="d-grid gap-2">
                    <b-button type="submit" block variant="primary">{{ $t("BANKAYI TANIMLA") }}</b-button>
                </div>
            </b-col>
        </b-row>
    </b-form>
    <b-row>
        <b-card
            :header="$t('Tanımlı Banka Hesaplarım')"
            header-tag="header"
            nobody
            class="mt-4 table-responsive"
            style="padding: 0px">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">{{ $t("Banka") }}</th>
                    <th scope="col">{{ $t("Iban") }}</th>
                    <th scope="col">{{ $t("Birincil") }}</th>
                    <th scope="col">#</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(bank,key) in user_banks" :key="key">
                    <th scope="row">{{ bank.bank.name }}</th>
                    <td>{{ bank.iban }}</td>
                    <td v-if="bank.primary==0" @click="setPrimary(bank)"><i class="far fa-star"></i></td>
                    <td v-else><i class="fas fa-star"></i></td>
                    <td @click="deleteBank(bank)"><i class="fas fa-trash"></i></td>
                </tr>
                </tbody>
            </table>
        </b-card>
    </b-row>
</template>

<script>
import restAPI from "../../../api/restAPI";
import useVuelidate from "@vuelidate/core";
import {required} from '@vuelidate/validators'

export default {
    name: "Banks",
    setup() {
        return {v$: useVuelidate()}
    },
    data: () => ({
        form: {
            bank: null,
            iban: null,
            primary: false,
        },
        banks: [],
        user_banks: [],
    }),
    validations: () => ({
        form: {
            bank: {
                required,
            },
            iban: {
                required,
            },
        }
    }),
    async created() {
        await this.getBank();
    },
    methods: {
        async sendBank() {
            const isFormCorrect = await this.v$.$validate()
            if (!isFormCorrect) return
            await restAPI.getData({Action: "bank-set"}, this.form).then((response) => {
                if (response.status === 'success') {
                    this.getBank()
                    this.$notify({text: response.message, type: 'success'})
                } else if (response.status === 'fail') {
                    this.$notify({text: response.message, type: 'error'})
                }
            })
        },
        async getBank() {
            await restAPI.getData({Action: "banks"}).then((response) => {
                if (response.status === 'success') {
                    this.banks = response.banks
                    this.user_banks = response.user_banks
                } else if (response.status === 'fail') {
                    this.$notify({text: response.message, type: 'error'})
                }
            })
        },
        async setPrimary(bank) {
            await restAPI.getData({Action: "bank-set-primary"}, bank).then((response) => {
                if (response.status === 'success') {
                    this.getBank()
                    this.$notify({text: response.message, type: 'success'})
                } else if (response.status === 'fail') {
                    this.$notify({text: response.message, type: 'error'})
                }
            })
        },
        async deleteBank(bank) {
            await this.$confirm.require({
                message: 'Silmek istediğinize emin misiniz?',
                header: 'Ödeme Yöntemini Sil',
                icon: 'pi pi-exclamation-triangle',
                acceptLabel: 'Evet',
                rejectLabel: 'Vazgeç',
                accept: () => {
                    restAPI.getData({Action: "bank-delete"}, bank).then((response) => {
                        if (response.status === 'success') {
                            this.getBank()
                            this.$notify({text: response.message, type: 'success'})
                        } else if (response.status === 'fail') {
                            this.$notify({text: response.message, type: 'error'})
                        }
                    })
                },
            })
        }
    }
}
</script>

<style scoped>

</style>
