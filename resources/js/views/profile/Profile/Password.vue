<template>
    <b-form @submit="resetPassword">
        <b-row>
            <b-col cols="12" md="12">
                <b-form-group
                    :label="$t('Mevcut Şifreniz')"
                >
                    <b-form-input
                        v-model="form.current_password"
                        size="sm"
                        class="mt-3"
                        placeholder="*****"
                        type="password"
                    ></b-form-input>
                    <b-form-text v-if="v$.form.current_password.$error" class="text-danger">
                        <p class="text-danger">
                            {{ $t('Lütfen mevcut şifrenizi giriniz!') }}
                        </p>
                    </b-form-text>
                </b-form-group>
            </b-col>
            <b-col cols="12" md="12">
                <b-form-group
                    :label="$t('Yeni Şifreniz')"
                >
                    <b-form-input
                        v-model="form.new_password"
                        size="sm"
                        class="mt-3"
                        placeholder="*****"
                        type="password"
                    ></b-form-input>
                    <b-form-text v-if="v$.form.new_password.$error" class="text-danger">
                        <p class="text-danger">
                            {{ $t('Güvenliğiniz için az 8 karakterli, büyük harf, küçük harf ve sembolik ifade (?,*,+) bulunduran bir şifre belirleyiniz.') }}
                        </p>
                    </b-form-text>
                </b-form-group>
            </b-col>
            <b-col cols="12" md="12">
                <b-form-group
                    :label="$t('Yeni Şifreniz Tekrar')"
                >
                    <b-form-input
                        v-model="form.new_password_confirm"
                        size="sm"
                        class="mt-3"
                        placeholder="*****"
                        type="password"
                    ></b-form-input>
                    <b-form-text v-if="v$.form.new_password_confirm.$error" class="text-danger">
                        <p class="text-danger">
                            {{ $t('Şifreleriniz eşleşmiyor!') }}
                        </p>
                    </b-form-text>
                </b-form-group>
            </b-col>
            <b-col cols="12">
                <div class="d-grid gap-2">
                    <b-button type="submit" block variant="primary">{{ $t('ŞİFREYİ GÜNCELLE') }}</b-button>
                </div>
            </b-col>
        </b-row>
    </b-form>
</template>

<script>
import useVuelidate from "@vuelidate/core";
import {helpers, minLength, required, sameAs} from "@vuelidate/validators";
import restAPI from "../../../api/restAPI";

const password = helpers.regex(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/)
export default {
    name: "Password",
    setup() {
        return {v$: useVuelidate()}
    },
    data: () => ({
        form: {
            current_password: null,
            new_password: null,
            new_password_confirm: null
        },
    }),
    validations() {
        return {
            form: {
                current_password: {
                    minLength: minLength(8),
                    required,
                },
                new_password: {
                    minLength: minLength(8),
                    required,
                    password
                },
                new_password_confirm: {
                    required,
                    sameAsPassword: sameAs(this.form.new_password)
                },
            }
        }
    },
    methods: {
        async resetPassword() {
            const isFormCorrect = await this.v$.$validate()
            if (!isFormCorrect) return

            await restAPI.getData({Action: "password-reset"}, this.form).then((response) => {
                if (response.status === 'success') {
                    this.$notify({text: response.message, type: 'success'})
                } else if (response.status === 'fail') {
                    this.$notify({text: response.message, type: 'error'})
                }
            })
        }
    }
}
</script>

<style scoped>

</style>
