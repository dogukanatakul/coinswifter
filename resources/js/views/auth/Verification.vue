<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="card o-hidden border-0 shadow-lg my-5 p-4">
                    <div class="card-body p-0">
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
                                    <span v-if="change">(<router-link :to="{ name: 'profile.contact' }">{{ $t("Değiştir") }}</router-link>)</span>
                                </div>
                                <div class="text-center">
                                    <b-button
                                        :disabled="sendCodeButton"
                                        @click="sendCode"
                                        squared
                                        variant="success"
                                        size="sm"
                                    >{{ sendCodeMsg }}
                                    </b-button>
                                </div>
                                <b-form @submit="signin">
                                    <b-form-group
                                        id="input-group-2"
                                        :label="type + ' ' + $t('Doğrulama Kodunuz')"
                                        label-for="input-2"
                                    >
                                        <b-form-input
                                            id="input-2"
                                            v-model="form['code']"
                                            type="text"
                                            :placeholder="type + ' ' + $t('Doğrulama Kodunuz')"
                                            inputmode="text"
                                        ></b-form-input>
                                        <b-form-text v-if="v$.form.code.$error" class="text-danger">
                                            <p class="text-danger">
                                                {{ $t("6 haneli kodu giriniz") }}
                                            </p>
                                        </b-form-text>
                                    </b-form-group>
                                    <div class="d-grid gap-2">
                                        <b-button
                                            block
                                            type="submit"
                                            variant="primary"
                                            :disabled="verificationBtn"
                                            v-html="verificationBtnMsg"
                                        ></b-button>
                                    </div>
                                </b-form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import restAPI from "../../api/restAPI";
import useVuelidate from "@vuelidate/core";
import {minLength, required} from "@vuelidate/validators";

export default {
    name: "Verification",
    setup() {
        return {v$: useVuelidate()};
    },
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
    validations: () => ({
        form: {
            code: {
                required,
                minLength: minLength(6),
            },
        },
    }),
    methods: {
        async signin() {
            const isFormCorrect = await this.v$.$validate();

            if (!isFormCorrect) return;
            await restAPI
                .getData({Action: "verification-control"}, this.form)
                .then((response) => {
                    if (response.status === "success") {
                        window.location.reload();
                        this.$notify({text: response.message, type: "success"});
                    } else if (response.status === "fail") {
                        this.$notify({text: response.message, type: "error"});
                    }
                });
        },
        async sendCode() {
            this.sendCodeButton = true;
            await restAPI.getData({Action: "verification"}).then((response) => {
                if (response.status === "success") {
                    this.verificationBtn = false;
                    this.sendCodeMsg = $t("Tekrar Kod Gönder");
                    setTimeout(() => {
                        this.sendCodeButton = false;
                    }, 15000);
                    this.$notify({text: response.message, type: "success"});
                } else if (response.status === "fail") {
                    this.$notify({text: response.message, type: "error"});
                }
            });
        },
    },
    async created() {
        await restAPI.getData({Action: "verification-info"}).then((response) => {
            if (response.status === "success") {
                this.infoMsg = response.contact;
                this.change = response.change;
            } else if (response.status === "fail") {
                this.$notify({text: response.message, type: "error"});
            }
        });
        if (this.$route.name === "telephone_verification") {
            this.type = this.$t("Telefon");
            this.verificationBtnMsg =
                '<i class="fas fa-sms"></i> ' + this.$t("Telefon Numaranı Doğrula");
        } else if (this.$route.name === "mail_verification") {
            this.type = this.$t("E-Posta");
            this.verificationBtnMsg =
                '<i class="far fa-envelope"></i> ' +
                this.$t("E-Posta Adresini Doğrula");
        }
    },
};
</script>
