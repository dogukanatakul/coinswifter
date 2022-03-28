<template>
  <div class="settings-profile">
    <b-row>
      <b-col cols="12" class="text-center fw-bolder">
        Yaşadığınız sorun aşağıdakilerden birisi ile ilgili olabilir mi ?
      </b-col>
      <b-col cols="12" class="my-1">
        <div class="accordion" role="tablist">
          <b-card no-body class="mb-1">
            <b-card-header header-tag="header" class="p-1" role="tab">
              <b-button
                block
                v-b-toggle.inner-accordion-1
                class="w-100 border-0 bg-transparent text-dark text-left"
                @click="activeIndices = 0"
                >Dışarıdaki bir uygulamaya nasıl cüzdanımdaki parayı
                aktarabilirim?
                <span class="float-right when-opened"
                  ><i class="fas fa-chevron-down"></i></span
                ><span class="float-right when-closed"
                  ><i class="fas fa-chevron-up"></i></span
              ></b-button>
            </b-card-header>
            <b-collapse
              id="inner-accordion-1"
              :visible="activeIndices === 0"
              :aria-expanded="activeIndices === 0 ? true : false"
              accordion="my-accordion"
              role="tabpanel"
            >
              <b-card-body>
                <b-card-text
                  >Cüzdanım bölümünden göndereceğiniz Coin / Tokeni seçerek
                  "Çekme" butonuna tıklayarak gönderilecek miktarı ve cüzdan
                  adresini yazıp "Transfer İşlemini Başlat" butonuna
                  tıklayabilirsiniz.
                </b-card-text>
              </b-card-body>
            </b-collapse>
          </b-card>

          <b-card no-body class="mb-1">
            <b-card-header header-tag="header" class="p-1" role="tab">
              <b-button
                block
                v-b-toggle.inner-accordion-2
                class="w-100 border-0 bg-transparent text-dark text-left"
                @click="activeIndices = 1"
                >KYC'lerimi eksiksiz yükledim fakat işlemim onaylanmadı ve Onay
                Bekliyor'a düştü.
                <span class="float-right when-opened"
                  ><i class="fas fa-chevron-down"></i></span
                ><span class="float-right when-closed"
                  ><i class="fas fa-chevron-up"></i></span
              ></b-button>
            </b-card-header>
            <b-collapse
              id="inner-accordion-2"
              accordion="my-accordion"
              role="tabpanel"
              :visible="activeIndices === 1"
              :aria-expanded="activeIndices === 1 ? true : false"
            >
              <b-card-body>
                <b-card-text
                  >KYC'leriniz yüklendikten sonra gerekli ekiplerce incelemeler
                  yapılıp doğruluğu kabul edilene kadar KYC onayınız
                  gerçekleşmez. Ancak tüm bilgilerin eksiksiz - doğru
                  gönderildiği ispat edilirse ekipler tarafından onaylanır ve
                  sizlerde tüm işlemlerinizi
                  gerçekleştirebilirsiniz.</b-card-text
                >
              </b-card-body>
            </b-collapse>
          </b-card>

          <!-- <b-card no-body class="mb-1">
            <b-card-header header-tag="header" class="p-1" role="tab">
              <b-button
                block
                v-b-toggle.inner-accordion-3
                class="w-100 border-0 bg-transparent text-dark"
                >Accordion 3</b-button
              >
            </b-card-header>
            <b-collapse
              id="inner-accordion-3"
              accordion="my-accordion"
              role="tabpanel"
            >
              <b-card-body>
                <b-card-text></b-card-text>
              </b-card-body>
            </b-collapse>
          </b-card> -->
        </div>
      </b-col>
    </b-row>
    <b-row>
      <b-col cols="12">
        <b-button variant="primary" class="float-right" @click="geriDon()">Destek Biletini Kapat</b-button>
      </b-col>
    </b-row>
  </div>
</template>

<script>
export default {
  name: "TicketSSS",
  data() {
    return {
      activeIndices: -1,
    };
  },
  methods: {
    geriDon() {
      this.activeIndices = -1;
      this.$router.push({name: 'profile.ticket'});
    }
  },
};
</script>

<style lang="scss" scoped>

</style>