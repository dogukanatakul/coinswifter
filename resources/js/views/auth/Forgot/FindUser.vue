<template>
    <div class="row">
        <div class="col-12">
            <div class="text-center">
                <h5 class="h5 text-gray-900 mb-2">
                    {{ $t("Şifremi Unuttum!") }}
                </h5>
                <hr/>
            </div>
            <b-form @submit="findUser">
                <b-form-group
                    id="input-group-1"
                    :label="$t('E-Posta')"
                    label-for="input-1"
                >
                    <b-form-input
                        id="input-1"
                        v-model="form['email']"
                        type="email"
                        placeholder="xxx@xxx.xxx"
                        inputmode="email"
                    ></b-form-input>
                    <b-form-text v-if="v$.form.email.$error">
                        <p class="text-danger">
                            {{ $t("Lütfen geçerli mail adresi giriniz!") }}
                        </p>
                    </b-form-text>
                </b-form-group>
                <b-row>
                    <b-col cols="12" md="6" class="float-left">
                        <b-form-group :label="$t('Telefon Kodunuz')">
                            <v-select
                                v-model.number="form['country_code']"
                                label="text"
                                :options="phoneCodes"
                                :reduce="(data) => data.value"
                            ></v-select>
                        </b-form-group>
                    </b-col>
                    <b-col cols="12" md="6" class="float-left">
                        <b-form-group
                            id="input-group-1"
                            label="Telefon Numaranız"
                            label-for="input-1"
                        >
                            <b-form-input
                                id="input-1"
                                v-model="form['telephone']"
                                type="text"
                                placeholder="53XXXXXXX"
                                class="mt-1"
                                inputmode="tel"
                            ></b-form-input>
                            <b-form-text v-if="v$.form.telephone.$error">
                                <p class="text-danger">
                                    {{ $t("Lütfen geçerli telefon numarası giriniz!") }}
                                </p>
                            </b-form-text>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>

                    <b-col cols="12">
                        <b-form-group :label="$t('Doğum Tarihiniz')">
                            <v-date-picker
                                v-model="form.birthday"
                                :masks="dateInputMask"
                                :model-config="dateModelMask"
                                is-required
                            >
                                <template v-slot="{ inputValue, inputEvents }">
                                    <input
                                        class="form-control w-100"
                                        :value="inputValue"
                                        :placeholder="$t('Gün-Ay-Yıl')"
                                        v-on="inputEvents"
                                        v-maska="'##-##-####'"
                                    />
                                </template>
                            </v-date-picker>

                            <b-form-text
                                v-if="v$.form.birthday.$error"
                                class="text-danger"
                            >
                                <p class="text-danger">
                                    {{ $t("Lütfen doğum tarihinizi giriniz.") }}
                                </p>
                            </b-form-text>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-form-group
                    v-if="code"
                    id="input-group-1"
                    :label="$t('Doğrulama Kodunuz')"
                    label-for="input-1"
                >
                    <b-form-input
                        id="input-1"
                        v-model="form['code']"
                        type="number"
                        placeholder="XXXXXX"
                        inputmode="number"
                    ></b-form-input>
                </b-form-group>
                <b-form-group
                    v-if="code"
                    id="input-group-2"
                    :label="$t('Yeni Şifreniz')"
                    label-for="input-2"
                >
                    <b-form-input
                        id="input-2"
                        v-model="form['password']"
                        type="password"
                        :placeholder="$t('Şifreniz')"
                        inputmode="text"
                    ></b-form-input>
                    <b-form-text
                        v-if="v$.form.password.$error"
                        class="text-danger"
                    >
                        <p class="text-danger">
                            {{ $t("Güvenliğiniz için az 8 karakterli, büyük harf, küçük harf ve sembolik ifade (?,*,+) bulunduran bir şifre belirleyiniz.") }}
                        </p>
                    </b-form-text>
                </b-form-group>
                <div class="d-grid gap-2">
                    <b-button block type="submit" variant="primary">{{ $t('Hesabımı Bul') }}</b-button>
                </div>
            </b-form>
        </div>
    </div>
</template>

<script>
import {email, required} from "@vuelidate/validators";
import restAPI from "../../../api/restAPI";
import useVuelidate from "@vuelidate/core";
import {mapGetters} from "vuex";

export default {
    name: "FindUser",
    setup() {
        return {v$: useVuelidate()};
    },
    async created() {
        await restAPI.getData({Action: "phone-codes"}).then((response) => {
            this.phoneCodes = response;
        });
    },
    data() {
        return {
            dateInputMask: {
                input: "DD-MM-YYYY",
            },
            dateModelMask: {
                type: "string",
                mask: "YYYY-MM-DD",
            },
            form: {
                email: null,
                telephone: null,
                birthday: null,
                country_code: 218
            },
            phoneCodes: []
        }
    },
    validations() {
        return {
            form: {
                email: {
                    required,
                    email,
                },
                telephone: {required},
                country_code: {required},
                birthday: {required},
            },
        }
    },
    methods: {
        async findUser() {
            const isFormCorrect = await this.v$.$validate();
            if (!isFormCorrect) return;
            await restAPI.getData({Action: "forgot/find"}, this.form)
                .then((response) => {
                    if (response.status === "success") {
                        this.$notify({text: response.message, type: "success"});
                        this.$store.commit('FORGOT', {
                            info: response.info,
                            step: 1,
                        })
                    } else if (response.status === "fail") {
                        this.$notify({text: response.message, type: "error"});
                    }
                });
        },
    },
    computed: {
        ...mapGetters([
            'forgot'
        ])
    },
}
</script>

<style scoped>

</style>
