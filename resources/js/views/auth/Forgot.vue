<template>
  <div class="row justify-content-center">
    <div class="col-12 col-md-6">
      <div class="card o-hidden border-0 shadow-lg my-5 p-4">
        <div class="card-body p-0">
          <!-- Nested Row within Card Body -->
          <div class="row">
            <div class="col-12">
              <div class="text-center">
                <h5 class="h5 text-gray-900 mb-2">
                  {{ $t("Şifremi Unuttum!") }}
                </h5>
                <hr />
              </div>
              <b-form @submit="forgot">
                <b-form-group
                  v-if="!code"
                  id="input-group-1"
                  :label="$t('E-Posta')"
                  label-for="input-1"
                >
                  <b-form-input
                    id="input-1"
                    v-model="form['email']"
                    type="email"
                    placeholder="xxx@xxx.xxx"
                  ></b-form-input>
                  <b-form-text v-if="v$.form.email.$error">
                    <p class="text-danger">
                      {{ $t("Lütfen geçerli mail adresi giriniz!") }}
                    </p>
                  </b-form-text>
                </b-form-group>
                <b-row>
                  <b-col cols="12" md="4" class="float-left">
                    <b-form-group :label="$t('Telefon Kodunuz')">
                      <v-select label="text"></v-select>
                    </b-form-group>
                  </b-col>
                  <b-col cols="12" md="8" class="float-left">
                    <b-form-group
                      v-if="!code"
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
                      ></b-form-input>
                      <b-form-text v-if="v$.form.telephone.$error">
                        <p class="text-danger">
                          {{ $t("Lütfen geçerli telefon numarası giriniz!") }}
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
                <div class="d-grid gap-2">
                  <b-button block type="submit" variant="primary">{{
                    sendBtnMessage
                  }}</b-button>
                </div>
              </b-form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import useVuelidate from "@vuelidate/core";
import { email, helpers, minLength, required } from "@vuelidate/validators";
import restAPI from "../../api/restAPI";

const password = helpers.regex(/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,20}$/);
export default {
  name: "Forgot",
  setup() {
    return { v$: useVuelidate() };
  },
  validations() {
    let localRules = {
      form: {
        email: {
          required,
          email,
        },
        telephone: {
          required,
        },
      },
    };
    if (this.code) {
      localRules.form.password = {
        minLength: minLength(8),
        required,
        password,
      };
    }
    return localRules;
  },
  data() {
    return {
      form: {
        email: null,
        telephone: null,
        code: null,
        password: null,
      },
      code: false,
      sendBtnMessage: this.$t("Sıfırlama Kodu Gönder"),
    };
  },
  methods: {
    async forgot() {
      const isFormCorrect = await this.v$.$validate();
      if (!isFormCorrect) return;
      await restAPI
        .getData({ Action: "forgot-password" }, this.form)
        .then((response) => {
          if (response.status === "success") {
            this.$notify({ text: response.message, type: "success" });
            if (this.code) {
              this.$router.push({ name: "wallets" });
            }
            this.sendBtnMessage = this.$t("Tamamla");
            this.code = true;
          } else if (response.status === "fail") {
            this.$notify({ text: response.message, type: "error" });
          }
        });
    },
  },
};
</script>
