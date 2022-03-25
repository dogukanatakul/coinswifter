<template>
  <div class="settings-profile">
    <b-form @submit="uploadKYC" enctype="multipart/form-data">
      <b-row>
        <b-col cols="12">
          <div class="accordion" role="tablist" id="mytab">
            <b-card no-body class="mb-1">
              <b-card-header header-tag="header" class="p-1" role="tab">
                <b-button
                  block
                  v-b-toggle.accordion-7
                  class="bg-transparent text-dark border-0 w-100 text-left"
                  ><span
                    > Nasıl Kullanılır<i class="fas fa-question"></i></span
                  >
                  <span class="float-right when-opened"
                    ><i class="fas fa-chevron-down"></i></span
                  ><span class="float-right when-closed"
                    ><i class="fas fa-chevron-up"></i
                  ></span>
                </b-button>
              </b-card-header>
              <b-collapse
                id="accordion-7"
                accordion="my-accordions"
                role="tabpanel"
                visible
              >
                <b-card-body>
                  <b-card-text>
                    <b-row class="my-1">
                      <b-col cols="12">
                        <div class="fw-bolder text-justify text-dark">
                          Adım 1) Yükleyeceğiniz KYC'nin tipini "Tip" bölümünden seçiniz<br><br>
                          Adım 2) Tip seçildikten sonra "Dosya" kısmından KYC'nizi yükleyiniz<br><br>
                          Adım 3) KYC tipi seçildikten ve gerekli KYC yüklendikten sonra "Talep Oluştur" butonuna basınız.<br><br>
                          Aşağıda bulunan tabloda oluşturulan talepleriniz görüntülenecektir.<hr>
                          NOT: Tüm fotoğraf tiplerinden KYC'lerin yüklenmesi
                          gerekmektedir.
                        </div>
                      </b-col>
                    </b-row>
                  </b-card-text>
                </b-card-body>
              </b-collapse>
            </b-card>
          </div>
        </b-col>
      </b-row>
      <b-row class="my-1">
        <b-col cols="12" md="6">
          <b-form-group :label="$t('Tip')">
            <v-select
              v-model="form.type"
              label="text"
              :options="kyc_select"
              :reduce="(data) => data.value"
            ></v-select>
          </b-form-group>
        </b-col>
        <b-col cols="12" md="6" :key="update">
          <b-form-group :label="$t('Dosya')">
            <input
              class="form-control"
              type="file"
              :placeholder="$t('Dosya seçiniz')"
              @change="onChange($event)"
            />
          </b-form-group>
        </b-col>
      </b-row>
      <!-- <b-col cols="12" :key="update">
          <b-form-group label="Kimlik Ön Yüz">
            <input
              class="form-control"
              type="file"
              :placeholder="$t('Dosya seçiniz')"
              @change="degisim($event, 0)"
            />
          </b-form-group>
        </b-col>
        <b-col cols="12" :key="update">
          <b-form-group label="Kimlik Arka Yüz">
            <input
              class="form-control"
              type="file"
              :placeholder="$t('Dosya seçiniz')"
              @change="degisim($event, 1)"
              :disabled="enable[1] == false"
            />
          </b-form-group>
        </b-col>
        <b-col cols="12" :key="update">
          <b-form-group label="Fatura">
            <input
              class="form-control"
              type="file"
              :placeholder="$t('Dosya seçiniz')"
              @change="degisim($event, 2)"
              :disabled="enable[2] == false"
            />
          </b-form-group>
        </b-col>
        <b-col cols="12" :key="update">
          <b-form-group label="Selfie">
            <input
              class="form-control"
              type="file"
              :placeholder="$t('Dosya seçiniz')"
              @change="degisim($event, 3)"
              :disabled="enable[3] == false"
            />
          </b-form-group>
        </b-col>
        <b-col cols="12" :key="update">
          <b-form-group>
            <input
              type="checkbox"
              v-model="onay"
              @change="onaylama($event, 4)"
              :disabled="enable[4] == false"
            />
            Tüm bilgileri kendi açık rızam ile gönderiyorum.
          </b-form-group>
        </b-col>
      </b-row>
      -->
      <b-row class="my-1">
        <b-col cols="12">
          <div class="d-grid gap-2">
            <b-button type="sumit" block variant="primary">{{
              $t("TALEP OLUŞTUR")
            }}</b-button>
          </div>
        </b-col>
      </b-row>
    </b-form>

    <!-- <b-row class="my-1">
      <b-col cols="12">
        <div class="alert alert-danger fw-bolder text-justify text-light">
          KYC işlemlerini yapabilmeniz için aşağıda bulunan maddeleri eksiksiz bir şekilde tamamlamanız gerekmektedir.
          <ol>
            <li>Yükleyeceğiniz fotoğrafın tipini seçmek</li>
            <li>Fotoğraf yüklemek</li>
            <li>İlk iki madde tamamlandıktan sonra Talep Oluştur butonuna basmak</li>
          </ol>
          NOT: Tüm fotoğraf tiplerinden KYC'lerin yüklenmesi gerekmektedir.
        </div>
      </b-col>
    </b-row> -->
    <b-row class="my-2">
      <b-col cols="12" class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th scope="col">{{ $t("Dosya") }}</th>
              <th>{{ $t("Talep Tarihi") }}</th>
              <th>{{ $t("Talep Tipi") }}</th>
              <th>{{ $t("Durum") }}</th>
              <th>{{ $t("Cevap") }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(kyc, key) in kycs" :key="key">
              <td>
                <photo-provider>
                  <photo-consumer
                    :intro="kyc.file_url"
                    :key="kyc.file_url"
                    :src="kyc.file_url"
                  >
                    <img
                      :src="kyc.file_url"
                      class="view-box cursor-pointer"
                      style="max-height: 50px"
                    />
                  </photo-consumer>
                </photo-provider>
              </td>
              <td>{{ kyc.created_at }}</td>
              <td>{{ kyc.type }}</td>
              <td>{{ kyc.status }}</td>
              <td>{{ kyc.status_message }}</td>
            </tr>
          </tbody>
        </table>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import restAPI from "../../../api/restAPI";

export default {
  name: "KYC",
  data: () => ({
    form: {
      file: null,
      type: null,
    },
    kyc_select: [],
    kycs: [],
    update: 0,
    enable: [false, false, false, false, false, false],
    onay: false,
  }),
  methods: {
    async uploadKYC() {
      let formData = new FormData();
      formData.append("type", this.form.type);
      formData.append("file", this.form.file);
      await restAPI
        .getData({ Action: "kyc-set" }, formData, true)
        .then((response) => {
          if (response.status === "success") {
            this.$notify({ text: response.message, type: "success" });
            this.getKYC();
            this.form = {
              file: null,
              type: null,
            };
            this.update += 1;
          } else if (response.status === "fail") {
            this.$notify({ text: response.message, type: "error" });
          }
        });
    },
    async getKYC() {
      await restAPI
        .getData({
          Action: "kyc-get",
        })
        .then((response) => {
          if (response.status === "success") {
            this.kyc_select = response.kyc_select;
            this.kycs = response.kycs;
          } else if (response.status === "fail") {
            this.$notify({ text: response.message, type: "error" });
            this.$router.push({ name: "profile.adress" });
          }
        });
    },
    onChange(event) {
      this.form.file = event.target.files[0];
    },
    degisim(event, index) {
      console.log(event.target.files[0]);
      this.enable[index + 1] = true;
    },
    onaylama(event, index) {
      console.log(event.target.checked);
      if (event.target.checked == true) {
        this.enable[index + 1] = true;
      } else {
        this.enable[index + 1] = false;
      }
    },
  },
  async created() {
    await this.getKYC();
  },
};
</script>

<style scoped>
.collapsed > .when-opened,
:not(.collapsed) > .when-closed {
  display: none;
}
</style>
