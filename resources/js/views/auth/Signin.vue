<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="card o-hidden border-0 shadow-lg my-5 p-4">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center">
                                    <h5 class="h5 text-gray-900 mb-2">{{ $t("Giriş Yap") }}</h5>
                                    <hr/>
                                </div>
                                <b-form @submit="signin">
                                    <b-form-group id="input-group-1" :label="$t('E-Posta')" label-for="input-1">
                                        <b-form-input id="input-1" v-model="form['email']" type="email"
                                                      :placeholder="$t('E-Posta Adresiniz')"
                                                      inputmode="email"></b-form-input>
                                        <b-form-text v-if="v$.form.email.$error">
                                            <p class="text-danger-custom">
                                                {{ $t("Lütfen geçerli mail adresi giriniz!") }}
                                            </p>
                                        </b-form-text>
                                    </b-form-group>
                                    <b-form-group id="input-group-2" label="Şifre" label-for="input-2">
                                        <b-form-input id="input-2" v-model="form['password']" type="password"
                                                      :placeholder="$t('Şifreniz')" inputmode="text"></b-form-input>
                                        <b-form-text v-if="v$.form.password.$error" class="text-danger-custom">
                                            <p class="text-danger-custom">
                                                {{ $t("Lütfen şifrenizi giriniz.") }}
                                            </p>
                                        </b-form-text>
                                    </b-form-group>
                                    <b-form-group>
                                        <VueRecaptcha
                                            :sitekey="siteKey"
                                            :load-recaptcha-script="true"
                                            @verify="handleSuccess"
                                            @error="handleError"
                                        ></VueRecaptcha>
                                        <b-form-text v-if="v$.recaptchaVerified.$error" class="text-danger-custom">
                                            <p class="text-danger-custom">
                                                {{ $t("Lütfen doğrulama yapınız.") }}
                                            </p>
                                        </b-form-text>
                                    </b-form-group>
                                    <div class="d-grid gap-2">
                                        <b-button block type="submit" variant="primary">{{ $t("Giriş Yap") }}</b-button>
                                    </div>
                                </b-form>
                            </div>
                        </div>
                        <div class="row justify-content-between py-0 py-md-4">
                            <div class="col-12 col-md-6 text-md-start d-grid gap-2 py-2 py-md-0">
                                <b-button squared block variant="outline-secondary" :to="{ name: 'forgot' }"
                                          class="overflowed-table">{{ $t("Şifremi unuttum?") }}
                                </b-button>
                            </div>
                            <div class="col-12 col-md-6 text-md-end d-grid gap-2 py-2 py-md-0">
                                <b-button squared block variant="outline-secondary"
                                          class="overflowed-table" :to="{ name: 'signup' }">{{ $t("Yeni hesap oluştur!")
                                    }}
                                </b-button>
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
import {required, email, minLength} from "@vuelidate/validators";
import {computed, defineComponent} from 'vue';
import {VueRecaptcha} from 'vue-recaptcha';

export default {
    name: "Signin",
    components: {
        VueRecaptcha
    },
    data: () => ({
        form: {
            email: null,
            password: null,
        },
        recaptchaVerified: null,
    }),
    setup() {

        const siteKey = computed(() => {
            return '6LeXCqsfAAAAACb108osRotNrg32PCpD80pXZR7J';
        });

        return {
            v$: useVuelidate(),
            siteKey,
        };
    },
    validations: () => ({
        form: {
            email: {
                required,
                email,
            },
            password: {
                required,
            }
        },
        recaptchaVerified: {
            required
        }
    }),
    methods: {
        handleError () {
            window.location.reload();
            this.$router.push({ name: "signin" });
        },
        handleSuccess (response) {
            if (!this.recaptchaVerified) {
                this.recaptchaVerified = true
            }
        },
        async signin() {
            const isFormCorrect = await this.v$.$validate();
            if (!isFormCorrect) return;
            await restAPI
              .getData({ Action: "signin" }, this.form)
              .then((response) => {
                if (response.status === "success") {
                  this.$notify({ text: response.message, type: "success" });
                  this.$store.commit("USER", response.user);
                  window.location.reload();
                  this.$router.push({ name: "signin" });
                } else if (response.status === "fail") {
                  this.$notify({ text: response.message, type: "error" });
                }
              });
        },
    },
};
</script>

<style>
</style>
