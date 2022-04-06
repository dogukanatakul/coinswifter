<template>
    <b-form @submit="update">
        <b-row>
            <b-col cols="12" md="4">
                <b-form-group :label="$t('Telefon Kodunuz')" >
                    <v-select v-model.number="form.telephone.country_code" label="text" :options="phoneCodes" :reduce="data => data.value" :disabled="disabledInput==='telephone'" ></v-select>
                </b-form-group>
            </b-col>
            <b-col cols="12" md="8">
                <b-form-group :label="$t('Telefon Numaranız')" >
                    <b-form-input v-model="form.telephone.value" type="text" placeholder="53XXXXX" :disabled="disabledInput==='telephone'" inputmode="text" ></b-form-input>
                    <b-form-text v-if="v$.form.telephone.$error">
                        <p class="text-danger-custom">{{ $t("Lütfen geçerli telefon numarası giriniz!") }}</p>
                    </b-form-text>
                </b-form-group>
            </b-col>
            <b-col cols="12">
                <b-form-group :label="$t('E-Posta Adresiniz')" >
                    <template #append>
                        <b-input-group-text><strong class="text-danger-custom">!</strong></b-input-group-text>
                    </template>
                    <b-form-input v-model="form.email.value" type="text" placeholder="xxx@xxx.xx" :disabled="disabledInput==='email'" inputmode="email" ></b-form-input>
                    <b-form-text v-if="v$.form.email.$error">
                        <p class="text-danger-custom">{{ $t("Lütfen geçerli E-posta adresi giriniz!") }}</p>
                    </b-form-text>
                </b-form-group>
            </b-col>
            <b-col cols="12">
                <div class="d-grid gap-2">
                    <b-button block type="submit" variant="primary" :disabled="updateButton">{{ $t("İletişim Bilgilerimi Güncelle") }}</b-button>
                </div>
            </b-col>
        </b-row>
    </b-form>
</template>

<script>

import restAPI from "../../../api/restAPI";
import useVuelidate from "@vuelidate/core";
import {required, email} from "@vuelidate/validators";

export default {
    name: "Contact",
    setup() {
        return {v$: useVuelidate()}
    },
    data() {
        return {
            form: {
                email: {
                    value: null,
                    country_code: null,
                },
                telephone: {
                    value: null,
                    country_code: null,
                },
            },
            updateButton: true,
            phoneCodes: [],
            disabledInput: null,
        }
    },
    validations: () => ({
        form: {
            email: {
                value: {
                    required,
                    email,
                },
            },
            telephone: {
                value: {
                    required,
                },
                country_code: {
                    required,
                }
            },
        }
    }),
    async created() {
        await restAPI.getData({Action: "phone-codes"}).then((response) => {
            this.phoneCodes = response
        })
        await restAPI.getData({Action: "contact-get"}).then((response) => {
            if (response.status === 'success') {
                this.form = response.form
            } else if (response.status === 'fail') {
                this.$notify({text: response.message, type: 'error'})
            }
        })
        this.updateButton = true
        this.disabledInput = null;
    },
    methods: {
        async update() {
            await restAPI.getData({Action: "contact-update"}, this.form).then((response) => {
                if (response.status === 'success') {
                    this.$notify({text: response.message, type: 'success'})
                    if (response.redirect !== undefined && response.redirect) {
                        this.$router.push({name: response.redirect})
                    }
                } else if (response.status === 'fail') {
                    this.$notify({text: response.message, type: 'error'})
                }
            })
        }
    },
    watch: {
        form: {
            async handler() {
                this.updateButton = false;
            },
            deep: true,
        },
        'form.telephone': {
            async handler() {
                this.disabledInput = 'email';
            },
            deep: true,
        },
        'form.email': {
            async handler() {
                this.disabledInput = 'telephone';
            },
            deep: true,
        },
    }
};
</script>

<style scoped>
</style>
