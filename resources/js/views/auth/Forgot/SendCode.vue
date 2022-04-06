<template>
    <div class="row">
        <div class="col-12">
            <div class="text-center">
                <h5 class="h5 text-gray-900 mb-2">
                    {{ type }} {{ $t("Doğrulama") }}
                </h5>
                <hr/>
            </div>
            <div class="text-center fw-bolder my-2">
                {{ infoMsg }}
            </div>
            <b-form @submit="verify">
                <b-form-group id="input-group-2" :label="type + ' ' + $t('Doğrulama Kodunuz')" label-for="input-2" >
                    <b-form-input id="input-2" v-model="form['code']" type="text" :placeholder="type + ' ' + $t('Doğrulama Kodunuz')" inputmode="text" ></b-form-input>
                    <b-form-text v-if="v$.form.code.$error" class="text-danger-custom">
                        <p class="text-danger-custom">
                            {{ $t("6 haneli kodu giriniz") }}
                        </p>
                    </b-form-text>
                </b-form-group>
                <div class="d-grid gap-2">
                    <b-button block type="submit" variant="primary" :disabled="verificationBtn" v-html="verificationBtnMsg" ></b-button>
                </div>
            </b-form>
        </div>
    </div>
</template>

<script>
import {mapGetters} from "vuex";
import useVuelidate from "@vuelidate/core";
import {minLength, required} from "@vuelidate/validators";
import restAPI from "../../../api/restAPI";

export default {
    name: "SendCode",
    computed: {
        ...mapGetters([
            'forgot'
        ])
    },
    setup() {
        return {v$: useVuelidate()};
    },
    validations: () => ({
        form: {
            code: {
                required,
                minLength: minLength(6),
            },
        },
    }),
    data() {
        return {
            form: {
                code: null,
            },
            type: "",
            sendCodeButton: false,
            verificationBtn: true,
            verificationBtnMsg: null,
            sendCodeMsg: this.$t("Şimdi Kod Gönder"),
            bgClass: null,
            infoMsg: null,
            change: false,
        };
    },
    async created() {
        await restAPI.getData({Action: "forgot/verification"}).then((response) => {
            if (response.status === "success") {
                this.$notify({text: response.message, type: "success"});
                if (response.type === "telephone_verification") {
                    this.type = this.$t("Telefon");
                    this.verificationBtnMsg = '<i class="fas fa-sms"></i> ' + this.$t("Telefon Numaranı Doğrula");
                } else if (response.type === "email_verification") {
                    this.type = this.$t("E-Posta");
                    this.verificationBtnMsg = '<i class="far fa-envelope"></i> ' + this.$t("E-Posta Adresini Doğrula");
                }
                this.infoMsg = response.info
                this.verificationBtn = false;
            } else if (response.status === "fail") {
                this.$notify({text: response.message, type: "error"});
                this.$store.commit('FORGOT', {
                    info: {},
                    step: 0,
                })
            }
        });

    },
    methods: {
        async verify() {
            await restAPI.getData({Action: "forgot/verification"}, this.form).then((response) => {
                if (response.status === "success") {
                    if (this.forgot.step === 2) {
                        this.$store.commit('FORGOT', {
                            info: response.info,
                            step: 3,
                        })
                    } else if (this.forgot.step === 3) {
                        this.$store.commit('FORGOT', {
                            info: response.info,
                            step: 4,
                        })
                    }
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
