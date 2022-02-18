<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="card o-hidden border-0 shadow-lg my-5 p-4">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center">
                                    <h5 class="h5 text-gray-900 mb-2">{{ $t('Sözleşmeleri Onaylamanız Gerekli') }}</h5>
                                    <hr>
                                </div>

                                <b-form @submit="verify">
                                    <v-row>
                                        <b-col cols="12" class="py-2">
                                            <b-form-checkbox
                                                v-model="form['user_agreement']"
                                                required
                                            >
                                                <a href="javascript:" @click="contract.visible=true;contract.key='lighting_text'">{{ $t('sozlesme_1') }}</a>{{ $t('sozlesme_2') }} <a href="javascript:" @click="contract.visible=true;contract.key='usage_contract'">{{ $t('sozlesme_3') }}</a>
                                                {{ $t('sozlesme_4') }}
                                                <a href="javascript:" @click="contract.visible=true;contract.key='business_conditions'">{{ $t('sozlesme_5') }}</a>{{ $t('sozlesme_6') }}
                                            </b-form-checkbox>
                                            <b-form-text v-if="v$.form.user_agreement.$error" class="text-danger">
                                                <p class="text-danger">
                                                    {{ $t('Lütfen kullanıcı sözleşmesini okuyup onaylayınız.') }}
                                                </p>
                                            </b-form-text>
                                        </b-col>
                                        <b-col cols="12" class="py-2">
                                            <b-form-checkbox
                                                v-model="form['open_consent']"
                                                required
                                            >
                                                <a href="javascript:" @click="contract.visible=true;contract.key='open_consent'">{{ $t('sozlesme_7') }}</a>{{ $t('sozlesme_8') }}
                                            </b-form-checkbox>
                                            <b-form-text v-if="v$.form.open_consent.$error" class="text-danger">
                                                <p class="text-danger">
                                                    {{ $t('Lütfen açık rıza metnini okuyup onaylayınız.') }}
                                                </p>
                                            </b-form-text>
                                        </b-col>
                                        <b-col cols="12" class="py-2">
                                            <b-form-checkbox
                                                v-model="form['lighting_text']"
                                                required
                                            >
                                                {{ $t('sozlesme_9') }} <a href="javascript:" @click="contract.visible=true;contract.key='lighting_text'">{{ $t('sozlesme_10') }}</a> {{ $t('sozlesme_11') }}
                                            </b-form-checkbox>
                                            <b-form-text v-if="v$.form.lighting_text.$error" class="text-danger">
                                                <p class="text-danger">
                                                    {{ $t('Lütfen aydınlatma metnini okuyup onaylayınız.') }}
                                                </p>
                                            </b-form-text>
                                        </b-col>
                                    </v-row>
                                    <div class="d-grid gap-2">
                                        <b-button block type="submit" variant="primary">{{ $t('Okudum, Onaylıyorum') }}</b-button>
                                    </div>
                                </b-form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Dialog v-model:visible="contract.visible">
        <aydinlatma-metni v-if="contract.key==='lighting_text'"></aydinlatma-metni>
        <acik-riza v-else-if="contract.key==='open_consent'"></acik-riza>
        <isleyis-kosullari v-else-if="contract.key==='business_conditions'"></isleyis-kosullari>
        <terms-of-use v-else-if="contract.key==='usage_contract'"></terms-of-use>
        <template #footer>
            <b-button @click="contract.visible=false">{{ $t('Onaylıyorum') }}</b-button>
        </template>
    </Dialog>
</template>

<script>
import useVuelidate from '@vuelidate/core'
import {required, sameAs} from '@vuelidate/validators'
import Dialog from 'primevue/dialog';
import TermsOfUse from "../pages/TermsOfUse";
import AydinlatmaMetni from "../Contracts/AydinlatmaMetni";
import AcikRiza from "../Contracts/AcikRiza";
import IsleyisKosullari from "../Contracts/IsleyisKosullari";
import restAPI from "../../api/restAPI";

export default {
    name: "Contract",
    setup() {
        return {v$: useVuelidate()}
    },
    methods: {
        async verify() {
            const isFormCorrect = await this.v$.$validate()

            if (!isFormCorrect) return
            await restAPI.getData({Action: "contact-verify"}, this.form).then((response) => {
                if (response.status === 'success') {
                    this.$notify({text: response.message, type: 'success'})
                    this.$store.commit('USER', response.user)

                    window.location.reload()
                    this.$router.push({name: 'signin'})
                } else if (response.status === 'fail') {
                    this.$notify({text: response.message, type: 'error'})
                }
            })
        }
    },
    data: () => ({
        form: {
            user_agreement: false,
            open_consent: false,
            lighting_text: false,
        },
        contract: {
            visible: false,
            key: null,
        },
    }),
    validations() {
        return {
            form: {
                user_agreement: {
                    required,
                    sameAs: sameAs(true)
                },
                open_consent: {
                    required,
                    sameAs: sameAs(true)
                },
                lighting_text: {
                    required,
                    sameAs: sameAs(true)
                },
            }
        }
    },
    components: {
        IsleyisKosullari,
        AcikRiza,
        AydinlatmaMetni,
        TermsOfUse,
        Dialog,
    }
}
</script>

<style scoped>

</style>
