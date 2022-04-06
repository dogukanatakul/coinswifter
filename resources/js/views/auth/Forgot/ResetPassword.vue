<template>

    <div class="row">
        <b-form @submit="changePass">
            <b-col cols="12">
                <div class="text-center">
                    <h5 class="h5 text-gray-900 mb-2">
                        {{ $t("Şifreni Değiştir") }}
                    </h5>
                    <hr/>
                </div>
            </b-col>
            <b-col cols="12">
                <b-form-group :label="$t('Şifreniz')">
                    <b-form-input v-model="form['password']" type="password" :placeholder="$t('Şifreniz')" inputmode="text" ></b-form-input>
                    <b-form-text v-if="v$.form.password.$error" class="text-danger-custom" >
                        <p class="text-danger-custom">
                            {{ $t("Güvenliğiniz için az 8 karakterli, büyük harf, küçük harf ve sembolik ifade (?,*,+) bulunduran bir şifre belirleyiniz.") }}
                        </p>
                    </b-form-text>
                </b-form-group>
            </b-col>
            <b-col cols="12">
                <b-form-group :label="$t('Şifreniz Tekrar')">
                    <b-form-input v-model="form.password_confirm" :placeholder="$t('Şifreniz Tekrar')" type="password" inputmode="text" ></b-form-input>
                    <b-form-text v-if="v$.form.password_confirm.$error" class="text-danger-custom" >
                        <p class="text-danger-custom">
                            {{ $t("Şifreleriniz eşleşmiyor!") }}
                        </p>
                    </b-form-text>
                </b-form-group>
            </b-col>
            <b-col cols="12">
                <div class="d-grid gap-2">
                    <b-button block type="submit" variant="primary" >{{ $t("Şifreni Değiştir") }}
                    </b-button>
                </div>
            </b-col>
        </b-form>
    </div>

</template>

<script>
import useVuelidate from "@vuelidate/core";
import {helpers, minLength, required, sameAs} from "@vuelidate/validators";
import {mapGetters} from "vuex";
import restAPI from "../../../api/restAPI";

const password = helpers.regex(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/);

export default {
    name: "ResetPassword",
    setup() {
        return {v$: useVuelidate()};
    },
    data: () => ({
        form: {
            password: null,
            password_confirm: null,
        },
    }),
    validations() {
        return {
            form: {
                password: {
                    minLength: minLength(8),
                    required,
                    password,
                },
                password_confirm: {
                    required,
                    sameAsPassword: sameAs(this.form.password),
                },
            }
        }
    },
    computed: {
        ...mapGetters([
            'forgot'
        ])
    },
    methods: {
        async changePass() {
            const isFormCorrect = await this.v$.$validate();
            if (!isFormCorrect) return;
            await restAPI.getData({Action: "forgot/change"}, this.form).then((response) => {
                if (response.status === "success") {
                    this.$notify({text: response.message, type: "success"});
                        this.$store.commit('FORGOT', {
                            info: {},
                            step: 0,
                        })
                } else if (response.status === "fail") {
                    this.$notify({text: response.message, type: "error"});
                }
            });
        }
    }
}
</script>

<style scoped>

</style>
