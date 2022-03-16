<template>
  <div id="section0" class="invented-container overflow-hidden">
    <b-container>
      <b-form @submit="listingRequest">
        <b-row class="justify-content-center">
          <b-col cols="12" sm="12" md="8">
            <b-card class="o-hidden border-0 shadow-lg my-5 p-4">
              <b-card-body class="p-0">
                <b-row>
                  <b-col cols="12">
                    <div class="text-center">
                      <h5 class="h5 text-gray-900 mb-2">
                        {{
                          $t(
                            "Kripto Paranızı Borsamızda Sergilemek Bizimle İletişime Geçin"
                          )
                        }}
                      </h5>
                    </div>

                    <b-form-group :label="$t('Coin Adı')">
                      <b-form-input
                        v-model="form['coin_name']"
                        type="text"
                        :placeholder="$t('Coin adını giriniz')"
                        inputmode="text"
                      >
                      </b-form-input>
                      <b-form-text
                        v-if="v$.form.coin_name.$error"
                        class="text-danger"
                      >
                        <p class="text-danger">
                          {{ $t("Lütfen coin adınızı giriniz!") }}
                        </p>
                      </b-form-text>
                    </b-form-group>
                    <b-form-group :label="$t('Ağ Bilgisi')">
                      <b-form-input
                        v-model="form['network_info']"
                        type="text"
                        :placeholder="$t('Ağ bilgisini giriniz')"
                        inputmode="text"
                      ></b-form-input>
                      <b-form-text
                        v-if="v$.form.network_info.$error"
                        class="text-danger"
                      >
                        <p class="text-danger">
                          {{ $t("Lütfen ağ bilgisini giriniz!") }}
                        </p>
                      </b-form-text>
                    </b-form-group>
                    <b-form-group :label="$t('Kontrat Adresi')">
                      <b-form-input
                        v-model="form['contract_adress']"
                        type="text"
                        :placeholder="$t('Kontrat adresini giriniz')"
                        inputmode="text"
                      ></b-form-input>
                      <b-form-text
                        v-if="v$.form.contract_adress.$error"
                        class="text-danger"
                      >
                        <p class="text-danger">
                          {{ $t("Lütfen kontrat adresini giriniz!") }}
                        </p>
                      </b-form-text>
                    </b-form-group>
                    <b-form-group :label="$t('İnternet Adresi')">
                      <b-form-input
                        v-model="form['coin_site']"
                        type="text"
                        :placeholder="$t('İnternet sitesini giriniz')"
                        inputmode="url"
                      ></b-form-input>
                      <b-form-text
                        v-if="v$.form.coin_site.$error"
                        class="text-danger"
                      >
                        <p class="text-danger">
                          {{ $t("Lütfen internet sitesini giriniz!") }}
                        </p>
                      </b-form-text>
                    </b-form-group>
                    <b-form-group :label="$t('Beyaz Kağıt Linki')">
                      <b-form-input
                        v-model="form['whitepaper_url']"
                        type="text"
                        :placeholder="$t('Beyaz Kağıt adresini giriniz')"
                        inputmode="url"
                      ></b-form-input>
                      <b-form-text
                        v-if="v$.form.whitepaper_url.$error"
                        class="text-danger"
                      >
                        <p class="text-danger">
                          {{ $t("Lütfen beyaz kağıt adresini giriniz!") }}
                        </p>
                      </b-form-text>
                    </b-form-group>
                    <b-form-group :label="$t('Yol Haritası Linki')">
                      <b-form-input
                        v-model="form['roadmap_url']"
                        type="text"
                        :placeholder="$t('Yol Haritası adresini giriniz')"
                        inputmode="url"
                      ></b-form-input>
                      <b-form-text
                        v-if="v$.form.roadmap_url.$error"
                        class="text-danger"
                      >
                        <p class="text-danger">
                          {{ $t("Lütfen yol haritası adresini giriniz!") }}
                        </p>
                      </b-form-text>
                    </b-form-group>
                    <b-form-group :label="$t('Proje Özeti')">
                      <b-form-textarea
                        v-model="form['project_info']"
                        :placeholder="$t('Proje özetini giriniz')"
                        rows="5"
                        max-rows="10"
                      ></b-form-textarea>
                      <b-form-text
                        v-if="v$.form.project_info.$error"
                        class="text-danger"
                      >
                        <p class="text-danger">
                          {{ $t("Lütfen proje özetini giriniz!") }}
                        </p>
                      </b-form-text>
                    </b-form-group>
                    <b-form-group :label="$t('Maksimum Arz')">
                      <b-form-input
                        v-model="form['maximum_supply']"
                        @focus="dynamicFocus('in')"
                        @focusout="dynamicFocus('out')"
                        :type="dynamicInput"
                        inputmode="numeric"
                        :placeholder="$t('Maksimum arz tutarını giriniz')"
                      ></b-form-input>
                      <b-form-text
                        v-if="v$.form.maximum_supply.$error"
                        class="text-danger"
                      >
                        <p class="text-danger">
                          {{ $t("Lütfen maksimum arz tutarını giriniz!") }}
                        </p>
                      </b-form-text>
                    </b-form-group>
                    <b-form-group :label="$t('Listelenen Borsalar')">
                      <b-form-input
                        v-model="form['listing_exchanges']"
                        type="text"
                        :placeholder="$t('Listelenen borsaları yazınız')"
                        inputmode="text"
                      ></b-form-input>
                      <b-form-text
                        v-if="v$.form.listing_exchanges.$error"
                        class="text-danger"
                      >
                        <p class="text-danger">
                          {{ $t("Lütfen listelenen borsaları yazınız!") }}
                        </p>
                      </b-form-text>
                    </b-form-group>
                    <b-form-group :label="$t('Github Adresi')">
                      <b-form-input
                        v-model="form['github_url']"
                        type="text"
                        :placeholder="$t('Github adresini giriniz')"
                        inputmode="url"
                      ></b-form-input>
                      <b-form-text
                        v-if="v$.form.github_url.$error"
                        class="text-danger"
                      >
                        <p class="text-danger">
                          {{ $t("Lütfen github adresini giriniz!") }}
                        </p>
                      </b-form-text>
                    </b-form-group>
                    <b-form-group :label="$t('CoinMarketCap Adresi')">
                      <b-form-input
                        v-model="form['coinmarketcap_url']"
                        type="text"
                        :placeholder="$t('CoinMarketCap Adresini giriniz')"
                        inputmode="url"
                      ></b-form-input>
                      <b-form-text
                        v-if="v$.form.coinmarketcap_url.$error"
                        class="text-danger"
                      >
                        <p class="text-danger">
                          {{ $t("Lütfen CoinMarketCap adresini giriniz!") }}
                        </p>
                      </b-form-text>
                    </b-form-group>
                    <b-form-group :label="$t('CoinGecko Adresi')">
                      <b-form-input
                        v-model="form['coingecko_url']"
                        type="text"
                        :placeholder="$t('CoinGecko adresini giriniz')"
                        inputmode="url"
                      ></b-form-input>
                      <b-form-text
                        v-if="v$.form.coingecko_url.$error"
                        class="text-danger"
                      >
                        <p class="text-danger">
                          {{ $t("Lütfen CoinGecko adresini giriniz!") }}
                        </p>
                      </b-form-text>
                    </b-form-group>
                    <b-form-group :label="$t('Twitter Adresi')">
                      <b-form-input
                        v-model="form['twitter_url']"
                        type="text"
                        :placeholder="$t('Twitter adresini giriniz')"
                        inputmode="url"
                      ></b-form-input>
                      <b-form-text
                        v-if="v$.form.twitter_url.$error"
                        class="text-danger"
                      >
                        <p class="text-danger">
                          {{ $t("Lütfen twitter adresini giriniz!") }}
                        </p>
                      </b-form-text>
                    </b-form-group>
                    <b-form-group :label="$t('Telegram Adresi')">
                      <b-form-input
                        v-model="form['telegram_url']"
                        type="text"
                        :placeholder="$t('Telegram adresini giriniz')"
                        inputmode="url"
                      ></b-form-input>
                      <b-form-text
                        v-if="v$.form.telegram_url.$error"
                        class="text-danger"
                      >
                        <p class="text-danger">
                          {{ $t("Lütfen telegram adresini giriniz!") }}
                        </p>
                      </b-form-text>
                    </b-form-group>
                    <b-form-group :label="$t('Ekstra Bilgi')">
                      <b-form-textarea
                        v-model="form['info']"
                        :placeholder="$t('Ekstra bilgileri giriniz')"
                        rows="5"
                      ></b-form-textarea>
                    </b-form-group>
                    <div class="d-grid gap-2">
                      <b-button block type="submit" variant="primary">{{
                        $t("Kaydet")
                      }}</b-button>
                    </div>
                  </b-col>
                </b-row>
              </b-card-body>
            </b-card>
          </b-col>
        </b-row>
      </b-form>
    </b-container>
  </div>
</template>

<script>
import restAPI from "../../api/restAPI";
import useVuelidate from "@vuelidate/core";
import { required, minValue } from "@vuelidate/validators";

export default {
  name: "CoinListingRequest",
  setup() {
    return { v$: useVuelidate() };
  },
  data: () => ({
    form: {
      coin_name: null,
      network_info: null,
      contract_adress: null,
      coin_site: null,
      whitepaper_url: null,
      roadmap_url: null,
      project_info: null,
      maximum_supply: 0.0,
      listing_exchanges: null,
      github_url: null,
      coinmarketcap_url: null,
      coingecko_url: null,
      twitter_url: null,
      telegram_url: null,
      info: null,
    },
    dynamicInput: "number",
  }),
  validations: () => ({
    form: {
      coin_name: {
        required,
      },
      network_info: {
        required,
      },
      contract_adress: {
        required,
      },
      coin_site: {
        required,
      },
      whitepaper_url: {
        required,
      },
      roadmap_url: {
        required,
      },
      project_info: {
        required,
      },
      maximum_supply: {
        required,
        minValue: minValue(1),
      },
      listing_exchanges: {
        required,
      },
      github_url: {
        required,
      },
      coinmarketcap_url: {
        required,
      },
      coingecko_url: {
        required,
      },
      twitter_url: {
        required,
      },
      telegram_url: {
        required,
      },
    },
  }),
  methods: {
    dynamicFocus(method) {
      if (method === "in") {
        this.dynamicInput = "input";
      } else {
        this.dynamicInput = "number";
      }
    },
    async listingRequest() {
      const isFormCorrect = await this.v$.$validate();
      if (!isFormCorrect) return;

      await restAPI
        .getData({ Action: "form/coin-listing-request" }, this.form)
        .then((response) => {
          if (response.status === "success") {
            this.$notify({ text: response.message, type: "success" });
          } else if (response.status === "fail") {
            this.$notify({ text: response.message, type: "error" });
          }
        });
    },
  },
};
</script>

<style scoped>
</style>
