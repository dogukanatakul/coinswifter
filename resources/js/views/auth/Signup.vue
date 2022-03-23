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
                                        {{ $t("Hesap Oluştur") }}
                                    </h5>
                                    <hr/>
                                </div>
                                <b-form @submit="register">
                                    <b-row>
                                        <b-col cols="12" md="6">
                                            <b-form-group :label="$t('Adınız')">
                                                <b-form-input
                                                    v-model="form['name']"
                                                    type="text"
                                                    :placeholder="$t('Adınız')"
                                                    inputmode="text"
                                                ></b-form-input>
                                                <b-form-text
                                                    v-if="v$.form.name.$error"
                                                    class="text-danger"
                                                >
                                                    <p class="text-danger">
                                                        {{ $t("Lütfen adınızı giriniz.") }}
                                                    </p>
                                                </b-form-text>
                                            </b-form-group>
                                        </b-col>
                                        <b-col cols="12" md="6">
                                            <b-form-group :label="$t('Soyadınız')">
                                                <b-form-input
                                                    v-model="form['surname']"
                                                    type="text"
                                                    :placeholder="$t('Soyadınız')"
                                                    inputmode="text"
                                                ></b-form-input>
                                                <b-form-text
                                                    v-if="v$.form.surname.$error"
                                                    class="text-danger"
                                                >
                                                    <p class="text-danger">
                                                        {{ $t("Lütfen soyadınızı giriniz.") }}
                                                    </p>
                                                </b-form-text>
                                            </b-form-group>
                                        </b-col>
                                        <b-col cols="12" md="6">
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
                                        <b-col cols="12" md="6">
                                            <b-form-group :label="$t('Uyruğunuz')">
                                                <v-select
                                                    v-model.number="form['nationality']"
                                                    label="text"
                                                    :options="nationalities"
                                                    :reduce="(data) => data.value"
                                                ></v-select>
                                                <b-form-text
                                                    v-if="v$.form.nationality.$error"
                                                    class="text-danger"
                                                >
                                                    <p class="text-danger">
                                                        {{ $t("Lütfen uyruğunuzu giriniz.") }}
                                                    </p>
                                                </b-form-text>
                                            </b-form-group>
                                        </b-col>
                                        <b-col v-if="form['nationality'] === 218" cols="12">
                                            <b-form-group :label="$t('T.C. Kimlik Numaranız')">
                                                <b-form-input
                                                    v-model="form['tck_no']"
                                                    type="number"
                                                    :placeholder="$t('T.C. Kimlik Numaranız')"
                                                    inputmode="number"
                                                ></b-form-input>
                                                <b-form-text
                                                    v-if="v$.form.tck_no.$error"
                                                    class="text-danger"
                                                >
                                                    <p class="text-danger">
                                                        {{ $t("Lütfen T.C. Kimlik numaranızı doğru girdiğinizden emin olun!") }}
                                                    </p>
                                                </b-form-text>
                                            </b-form-group>
                                        </b-col>
                                        <b-col
                                            v-if="form['nationality'] !== 218 && form['nationality'] !== null"
                                            cols="12"
                                        >
                                            <b-form-group :label="$t('Pasaport Numaranız')">
                                                <b-form-input
                                                    v-model="form['pasaport_no']"
                                                    type="text"
                                                    :placeholder="$t('Pasaport Numaranız')"
                                                    inputmode="number"
                                                ></b-form-input>
                                                <b-form-text v-if="v$.form.pasaport_no.$error">
                                                    <p class="text-danger">
                                                        {{ $t("Lütfen pasaport numaranızı giriniz!") }}
                                                    </p>
                                                </b-form-text>
                                            </b-form-group>
                                        </b-col>
                                        <b-col cols="12">
                                            <b-form-group :label="$t('E-Posta Adresiniz')">
                                                <b-form-input
                                                    v-model="form['email']"
                                                    type="email"
                                                    :placeholder="$t('E-Posta Adresiniz')"
                                                    inputmode="email"
                                                ></b-form-input>
                                                <b-form-text v-if="v$.form.email.$error">
                                                    <p class="text-danger">
                                                        {{ $t("Lütfen geçerli mail adresi giriniz!") }}
                                                    </p>
                                                </b-form-text>
                                            </b-form-group>
                                        </b-col>
                                        <b-col cols="12" md="6">
                                            <b-form-group :label="$t('Telefon Kodunuz')">
                                                <v-select
                                                    v-model.number="form['country_code']"
                                                    label="text"
                                                    :options="phoneCodes"
                                                    :reduce="(data) => data.value"
                                                ></v-select>
                                            </b-form-group>
                                        </b-col>
                                        <b-col cols="12" md="6">
                                            <b-form-group :label="$t('Telefon Numaranız')">
                                                <b-form-input
                                                    v-model="form['telephone']"
                                                    :key="formInputKey"
                                                    type="text"
                                                    placeholder="53XXXXX"
                                                    inputmodde="tel"
                                                ></b-form-input>
                                                <b-form-text v-if="v$.form.telephone.$error">
                                                    <p class="text-danger">
                                                        {{ $t("Lütfen geçerli telefon numarası giriniz!") }}
                                                    </p>
                                                </b-form-text>
                                            </b-form-group>
                                        </b-col>
                                        <b-col cols="12">
                                            <b-form-group :label="$t('Kullanıcı Adınız')">
                                                <b-form-input
                                                    v-model="form['username']"
                                                    type="text"
                                                    :placeholder="$t('Kullanıcı Adınız')"
                                                    inputmode="text"
                                                ></b-form-input>
                                                <b-form-text
                                                    v-if="v$.form.username.$error"
                                                    class="text-danger"
                                                >
                                                    <p class="text-danger">
                                                        {{ $t("En az 8 karakterli bir kullanıcı adı oluşturunuz!") }}
                                                    </p>
                                                    <p class="text-danger">
                                                        {{ $t("Metinsel ifade ve sonuna sadece rakamsal ifadeler kabul edilir.") }}
                                                    </p>
                                                    <p>
                                                        {{ $t("Doğru Örnek:") }}
                                                        <b class="text-success">coinswifter01</b> |
                                                        {{ $t("Yanlış örnekler:") }}
                                                        <b class="text-danger">01coin</b> -
                                                        <b class="text-danger">@coin</b> -
                                                        <b class="text-danger">coin01swifter</b>
                                                    </p>
                                                </b-form-text>
                                            </b-form-group>
                                        </b-col>
                                        <b-col cols="12">
                                            <b-form-group :label="$t('Şifreniz')">
                                                <b-form-input
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
                                                        {{
                                                            $t(
                                                                "Güvenliğiniz için az 8 karakterli, büyük harf, küçük harf ve sembolik ifade (?,*,+) bulunduran bir şifre belirleyiniz."
                                                            )
                                                        }}
                                                    </p>
                                                </b-form-text>
                                            </b-form-group>
                                        </b-col>
                                        <b-col cols="12" md="12">
                                            <b-form-group :label="$t('Şifreniz Tekrar')">
                                                <b-form-input
                                                    v-model="form.password_repeat"
                                                    :placeholder="$t('Şifreniz Tekrar')"
                                                    type="password"
                                                    inputmode="text"
                                                ></b-form-input>
                                                <b-form-text
                                                    v-if="v$.form.password_repeat.$error"
                                                    class="text-danger"
                                                >
                                                    <p class="text-danger">
                                                        {{ $t("Şifreleriniz eşleşmiyor!") }}
                                                    </p>
                                                </b-form-text>
                                            </b-form-group>
                                        </b-col>
                                    </b-row>
                                    <v-row>
                                        <b-col cols="12" class="py-2">
                                            <b-form-checkbox
                                                v-model="form['user_agreement']"
                                                required
                                            >
                                                <a href="javascript:" @click="contract.visible = true; contract.key = 'lighting_text';">{{ $t("sozlesme_1") }}</a>
                                                {{ $t("sozlesme_2") }}
                                                <a href="javascript:" @click="contract.visible = true; contract.key = 'usage_contract';">{{ $t("sozlesme_3") }}</a>
                                                {{ $t("sozlesme_4") }}
                                                <a href="javascript:" @click="contract.visible = true;contract.key = 'business_conditions';">{{ $t("sozlesme_5") }}</a>{{ $t("sozlesme_6") }}
                                            </b-form-checkbox>
                                            <b-form-text
                                                v-if="v$.form.user_agreement.$error"
                                                class="text-danger"
                                            >
                                                <p class="text-danger">
                                                    {{ $t("Lütfen kullanıcı sözleşmesini okuyup onaylayınız.") }}
                                                </p>
                                            </b-form-text>
                                        </b-col>
                                        <b-col cols="12" class="py-2">
                                            <b-form-checkbox v-model="form['open_consent']" required>
                                                <a
                                                    href="javascript:"
                                                    @click="
                            contract.visible = true;
                            contract.key = 'open_consent';
                          "
                                                >{{ $t("sozlesme_7") }}</a
                                                >{{ $t("sozlesme_8") }}
                                            </b-form-checkbox>
                                            <b-form-text
                                                v-if="v$.form.open_consent.$error"
                                                class="text-danger"
                                            >
                                                <p class="text-danger">
                                                    {{ $t("Lütfen açık rıza metnini okuyup onaylayınız.") }}
                                                </p>
                                            </b-form-text>
                                        </b-col>
                                        <b-col cols="12" class="py-2">
                                            <b-form-checkbox v-model="form['lighting_text']" required>
                                                {{ $t("sozlesme_9") }}
                                                <a
                                                    href="javascript:"
                                                    @click="
                            contract.visible = true;
                            contract.key = 'lighting_text';
                          "
                                                >{{ $t("sozlesme_10") }}</a
                                                >
                                                {{ $t("sozlesme_11") }}
                                            </b-form-checkbox>
                                            <b-form-text
                                                v-if="v$.form.lighting_text.$error"
                                                class="text-danger"
                                            >
                                                <p class="text-danger">
                                                    {{ $t("Lütfen aydınlatma metnini okuyup onaylayınız.") }}
                                                </p>
                                            </b-form-text>
                                        </b-col>
                                    </v-row>
                                    <div class="d-grid gap-2">
                                        <b-button block type="submit" variant="primary">{{ $t("Hesap Oluşturma İşlemini Tamamla") }}</b-button>
                                    </div>
                                </b-form>
                            </div>
                        </div>
                        <div class="row justify-content-center py-0 py-md-4">
                            <div
                                class="col-12 col-md-4 text-md-end d-grid gap-2 py-2 py-md-0"
                            >
                                <b-button
                                    squared
                                    block
                                    variant="outline-secondary"
                                    :to="{ name: 'signin' }"
                                >{{ $t("Zaten Hesabım Var") }}
                                </b-button
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <Dialog
        v-model:visible="contract.visible"
        :breakpoints="{ '2000px': '75vw', '640px': '100vw' }"
    >
        <aydinlatma-metni
            v-if="contract.key === 'lighting_text'"
        ></aydinlatma-metni>
        <acik-riza v-else-if="contract.key === 'open_consent'"></acik-riza>
        <isleyis-kosullari
            v-else-if="contract.key === 'business_conditions'"
        ></isleyis-kosullari>
        <terms-of-use v-else-if="contract.key === 'usage_contract'"></terms-of-use>
        <template #footer>
            <div class="d-grid gap-2">
                <b-button block @click="contract.visible = false" variant="success">{{ $t("Okudum ve Anladım") }}</b-button>
            </div>
        </template>
    </Dialog>
</template>

<script>
import restAPI from "../../api/restAPI";
import useVuelidate from "@vuelidate/core";
import {
    required,
    email,
    minLength,
    helpers,
    sameAs,
} from "@vuelidate/validators";
import Dialog from "primevue/dialog";
import TermsOfUse from "../pages/TermsOfUse";
import AydinlatmaMetni from "../Contracts/AydinlatmaMetni";
import AcikRiza from "../Contracts/AcikRiza";
import IsleyisKosullari from "../Contracts/IsleyisKosullari";

const password = helpers.regex(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/);
const username = helpers.regex(/(^([a-zA-Z]+)(\d+)?$)/u);

export default {
    name: "Register",
    setup() {
        return {v$: useVuelidate()};
    },
    data: () => ({
        dateInputMask: {
            input: "DD-MM-YYYY",
        },
        dateModelMask: {
            type: "string",
            mask: "YYYY-MM-DD",
        },
        formInputKey: 0,
        form: {
            name: null,
            surname: null,
            birthday: null,
            nationality: 218,
            country_code: 218,
            pasaport_no: null,
            tck_no: null,
            email: null,
            telephone: null,
            username: null,
            password: null,
            password_repeat: null,
            user_agreement: false,
            open_consent: false,
            lighting_text: false,
        },
        phoneCodes: [],
        nationalities: [],
        isLoading: false,
        contract: {
            visible: false,
            key: null,
        },
    }),
    watch: {
        "form.telephone"(val) {
            if (
                (val === "0" || val === 0 || val === "+") &&
                this.form["country_code"] === 218
            ) {
                this.$notify({
                    text: $t("Telefon numaranızı 555XXXXXX formatında yazınız."),
                    type: "error",
                });
                this.form.telephone = "";
                this.formInputKey += 1;
            }
        },
    },
    validations() {
        let localRules = {
            form: {
                email: {
                    required,
                    email,
                },
                country_code: {required},
                telephone: {required},
                nationality: {required},
                birthday: {required},
                name: {required},
                surname: {required},
                username: {
                    minLength: minLength(8),
                    required,
                    username,
                },
                password: {
                    minLength: minLength(8),
                    required,
                    password,
                },
                password_repeat: {
                    required,
                    sameAsPassword: sameAs(this.form.password),
                },
                user_agreement: {
                    required,
                    sameAs: sameAs(true),
                },
                open_consent: {
                    required,
                    sameAs: sameAs(true),
                },
                lighting_text: {
                    required,
                    sameAs: sameAs(true),
                },
            },
        };
        if (this.form.nationality === 218) {
            localRules.form.tck_no = {
                minLength: minLength(11),
                required,
            };
        } else {
            localRules.form.pasaport_no = {
                required,
            };
        }
        return localRules;
    },
    methods: {
        async register() {
            const isFormCorrect = await this.v$.$validate();

            if (!isFormCorrect) return;
            // actually submit form
            await restAPI
                .getData({Action: "signup"}, this.form)
                .then((response) => {
                    if (response.status === "success") {
                        this.$notify({text: response.message, type: "success"});
                        this.$store.commit("USER", response.user);

                        window.location.reload();
                        this.$router.push({name: "signin"});
                    } else if (response.status === "fail") {
                        this.$notify({text: response.message, type: "error"});
                    }
                });
        },
    },
    async created() {
        await restAPI.getData({Action: "phone-codes"}).then((response) => {
            this.phoneCodes = response;
        });
        await restAPI.getData({Action: "nationalities"}).then((response) => {
            this.nationalities = response;
        });
    },
    components: {
        IsleyisKosullari,
        AcikRiza,
        AydinlatmaMetni,
        TermsOfUse,
        Dialog,
    },
};
</script>
<style>
</style>
