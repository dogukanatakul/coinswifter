<template>
    <b-form>
        <b-row class="justify-content-center">
            <b-col cols="12" md="6">
                <b-row>
                    <b-col cols="12">
                        <b-form-group
                            :label="$t('Referans Kodu')"
                        >
                            <b-form-input
                                v-model="form.referer_url"
                                size="sm"
                                class="mt-3"
                                placeholder="XXXXXXXXX"
                                disabled
                            ></b-form-input>
                            <b-form-text v-if="v$.form.referer.$error" class="text-danger">
                                <p class="text-danger">
                                    {{ $t('Lütfen iban bilgisi giriniz!') }}
                                </p>
                            </b-form-text>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col cols="12">
                        <div class="d-grid gap-2">
                            <b-button @click="fastCopy(form.referer_url, $t('Referans linki kopyalandı!'))" block variant="primary">{{ $t('REFERANS LİNKİNİ KOPYALA') }}</b-button>
                        </div>
                    </b-col>
                </b-row>
            </b-col>
        </b-row>
    </b-form>
    <b-row>
        <b-card
            :header="$t('Referanslarım')"
            header-tag="header"
            nobody
            class="mt-4"
            style="padding: 0px">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">{{ $t('Kullanıcı') }}</th>
                    <th scope="col">{{ $t('Kayıt Tarihi') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(ref,key) in referers" :key="key">
                    <th scope="row">{{ ref.name }}</th>
                    <td>{{ ref.date }}</td>
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
import {copyText} from "vue3-clipboard";

export default {
    name: "Referer",
    setup() {
        return {v$: useVuelidate()}
    },
    data: () => ({
        form: {
            referer: null,
        },
        referers: [],
    }),
    validations: () => ({
        form: {
            referer: {
                required,
            }
        }
    }),
    async created() {
        await this.getReferer();
    },
    methods: {
        fastCopy(text, message) {
            copyText(text, undefined, (error, event) => {
                if (error) {
                    this.$notify({text: $t('Başarısız kopyalama!'), type: 'error'})
                } else {
                    this.$notify({text: message, type: 'success'})
                }
            })
            this.$refs.walletCode.input.select()
        },
        async getReferer() {
            await restAPI.getData({Action: "referer"}).then((response) => {
                if (response.status === 'success') {
                    this.referers = response.referers
                    this.form.referer = response.referer
                    this.form.referer_url = response.referer_url
                } else if (response.status === 'fail') {
                    this.$notify({text: response.message, type: 'error'})
                }
            })
        },
    }
}
</script>

<style scoped>

</style>
