<template>
  <b-card border-variant="white" class="profile min-vh-100">
    <b-row>
      <b-col cols="12" md="4" class="mb-2">
        <b-card-group deck class="d-none d-md-block">
          <b-card :header="$t('Hesabım')">
            <template #header>
              <span><i class="fas fa-user-tie"></i> {{ $t("Hesabım") }}</span>
            </template>
            <div class="accordion" role="tablist">
              <b-card no-body class="mb-1">
                <b-card-header header-tag="header" class="p-1" role="tab">
                  <b-button block v-b-toggle.accordion-1 class="bg-transparent text-dark border-0 w-100 text-left" @click="activeIndex = 0" ><i class="fas fa-user"></i> {{ $t("Hesap Bilgilerim") }}
                    <span class="float-right when-opened" ><i class="fas fa-chevron-down"></i></span ><span class="float-right when-closed" ><i class="fas fa-chevron-up"></i></span ></b-button>
                </b-card-header>
                <b-collapse id="accordion-1" :aria-expanded="activeIndex === 0 ? true : false" :visible="activeIndex === 0" accordion="my-accordion" role="tabpanel" active >
                  <b-card-body>
                    <b-card-text>
                      <!-- İçerik kısmı buralara gelecek -->
                      <b-list-group>
                        <router-link :to="{ name: 'profile.contact' }" class="text-decoration-none" @click="changePage()" >
                          <b-list-group-item class="my-1 border-0" ><i class="fas fa-address-book"></i>
                            {{ $t("İletişim Bilgilerini Düzenle") }}</b-list-group-item >
                        </router-link>
                        <router-link :to="{ name: 'profile.adress' }" class="text-decoration-none" @click="changePage()" >
                          <b-list-group-item class="my-1 border-0" ><i class="fas fa-map"></i>
                            {{ $t("Adres Bilgilerini Güncelle") }}</b-list-group-item >
                        </router-link>
                        <router-link :to="{ name: 'profile.referer' }" class="text-decoration-none" @click="changePage()" >
                          <b-list-group-item class="my-1 border-0" ><i class="fas fa-user-plus"></i>
                            {{ $t("Referans Sistemi") }}</b-list-group-item >
                        </router-link>
                        <router-link :to="{ name: 'profile.banks' }" class="text-decoration-none" @click="changePage()" >
                          <b-list-group-item class="my-1 border-0" ><i class="fas fa-money-check"></i>
                            {{ $t("Banka Hesap Bilgilerim") }}</b-list-group-item >
                        </router-link>
                      </b-list-group>
                    </b-card-text>
                  </b-card-body>
                </b-collapse>
              </b-card>

              <b-card no-body class="mb-1">
                <b-card-header header-tag="header" class="p-1" role="tab">
                  <b-button block v-b-toggle.accordion-2 class="bg-transparent text-dark border-0 w-100 text-left" @click="activeIndex = 1" ><i class="fas fa-file"></i> {{ $t("KYC") }}
                    <span class="float-right when-opened" ><i class="fas fa-chevron-down"></i></span ><span class="float-right when-closed" ><i class="fas fa-chevron-up"></i></span ></b-button>
                </b-card-header>
                <b-collapse id="accordion-2" :visible="activeIndex === 1" :aria-expanded="activeIndex === 1 ? true : false" accordion="my-accordion" role="tabpanel" >
                  <b-card-body>
                    <b-card-text>
                      <!-- İçerik kısmı buralara gelecek -->
                      <b-list-group>
                        <router-link :to="{ name: 'profile.kyc' }" class="text-decoration-none" @click="changePage()" >
                          <b-list-group-item class="my-1 border-0" ><i class="fas fa-camera"></i>
                            {{ $t("KYC Onayı") }}</b-list-group-item >
                        </router-link>
                      </b-list-group>
                    </b-card-text>
                  </b-card-body>
                </b-collapse>
              </b-card>
              <b-card no-body class="mb-1">
                <b-card-header header-tag="header" class="p-1" role="tab">
                  <b-button block v-b-toggle.accordion-3 class="bg-transparent text-dark border-0 w-100 text-left" @click="activeIndex = 2" ><i class="fas fa-lock"></i> {{ $t("Güvenlik") }}
                    <span class="float-right when-opened" ><i class="fas fa-chevron-down"></i></span ><span class="float-right when-closed" ><i class="fas fa-chevron-up"></i></span ></b-button>
                </b-card-header>
                <b-collapse id="accordion-3" :visible="activeIndex === 2" :aria-expanded="activeIndex === 2 ? true : false" accordion="my-accordion" role="tabpanel" >
                  <b-card-body>
                    <b-card-text>
                      <!-- İçerik kısmı buralara gelecek -->
                      <b-list-group>
                        <router-link :to="{ name: 'profile.password' }" class="text-decoration-none" @click="changePage()" >
                          <b-list-group-item class="my-1 border-0" ><i class="fas fa-key"></i>
                            {{ $t("Şifremi Değiştir") }}</b-list-group-item >
                        </router-link>
                        <router-link :to="{ name: 'profile.sessions' }" class="text-decoration-none" @click="changePage()" >
                          <b-list-group-item class="my-1 border-0" ><i class="fas fa-lock-open"></i>
                            {{ $t("Oturumlar") }}</b-list-group-item >
                        </router-link>
                        <!--                                <router-link :to="{name: 'profile.authentication'}" class="text-decoration-none">-->
                        <!--                                    <b-list-group-item href="#">Kimliğini Doğrula</b-list-group-item>-->
                        <!--                                </router-link>-->
                      </b-list-group>
                    </b-card-text>
                  </b-card-body>
                </b-collapse>
              </b-card>
              <b-card no-body class="mb-1">
                <b-card-header header-tag="header" class="p-1" role="tab">
                  <b-button block v-b-toggle.accordion-4 class="bg-transparent text-dark border-0 w-100 text-left" @click="activeIndex = 3" ><i class="fas fa-headset"></i> {{ $t("Destek") }}
                    <span class="float-right when-opened" ><i class="fas fa-chevron-down"></i></span ><span class="float-right when-closed" ><i class="fas fa-chevron-up"></i></span ></b-button>
                </b-card-header>
                <b-collapse id="accordion-4" :visible="activeIndex === 3" :aria-expanded="activeIndex === 3 ? true : false" accordion="my-accordion" role="tabpanel" >
                  <b-card-body>
                    <b-card-text>
                      <!-- İçerik kısmı buralara gelecek -->
                      <!-- Ticket sayfası oluşturulup link verilecek -->
                      <b-list-group>
                        <router-link :to="{ name: 'profile.ticket' }" class="text-decoration-none" @click="changePage()" >
                          <b-list-group-item class="my-1 border-0" ><i class="fas fa-check-circle"></i>
                            {{ $t("Ticket") }}</b-list-group-item >
                        </router-link>
                      </b-list-group>
                    </b-card-text>
                  </b-card-body>
                </b-collapse>
              </b-card>

              <!-- <b-card no-body class="mb-1"> <b-card-header header-tag="header" class="p-1" role="tab"> <b-button block v-b-toggle.accordion-2 variant="info">Accordion 2</b-button> </b-card-header> <b-collapse id="accordion-2" accordion="my-accordion" role="tabpanel"> <b-card-body> <b-card-text>{{ text }}</b-card-text> </b-card-body> </b-collapse> </b-card>

    <b-card no-body class="mb-1"> <b-card-header header-tag="header" class="p-1" role="tab"> <b-button block v-b-toggle.accordion-3 variant="info">Accordion 3</b-button> </b-card-header> <b-collapse id="accordion-3" accordion="my-accordion" role="tabpanel"> <b-card-body> <b-card-text>{{ text }}</b-card-text> </b-card-body> </b-collapse> </b-card> -->
            </div>
            <p class="card-text mt-2">
              <small>{{ $t( "Hesap güvenliğini korumak adına iletişim bilgilerini her zaman güncel tutmalısınız." ) }}</small>
            </p>
          </b-card>
        </b-card-group>
        <div class="d-none d-sm-block d-xs-block">
          <div class="accordion" role="tablist" id="mytab">
            <b-card no-body class="mb-1">
              <b-card-header header-tag="header" class="p-1" role="tab">
                <b-button block v-b-toggle.accordion-6 class="bg-transparent text-dark border-0 w-100 text-left" @click="selectedMainIndex = true" ><span ><i class="fas fa-user-tie"></i> {{ $t("Hesabım") }}</span >
                  <span class="float-right when-opened" ><i class="fas fa-chevron-down"></i></span ><span class="float-right when-closed" ><i class="fas fa-chevron-up"></i ></span>
                </b-button>
              </b-card-header>
              <b-collapse id="accordion-6" accordion="my-accordions" role="tabpanel" :aria-expanded="selectedMainIndex ? true : false" :visible="selectedMainIndex === true" >
                <b-card-body>
                  <b-card-text>
                    <div class="accordion" role="tablist">
                      <b-card no-body class="mb-1">
                        <b-card-header header-tag="header" class="p-1" role="tab" >
                          <b-button block v-b-toggle.accordions-1 class=" bg-transparent text-dark border-0 w-100 text-left " @click="activeIndex = 0" ><i class="fas fa-user"></i>
                            {{ $t("Hesap Bilgilerim") }}
                            <span class="float-right when-opened" ><i class="fas fa-chevron-down"></i></span ><span class="float-right when-closed" ><i class="fas fa-chevron-up"></i></span ></b-button>
                        </b-card-header>
                        <b-collapse id="accordions-1" :aria-expanded="activeIndex === 0 ? true : false" :visible="activeIndex === 0" accordion="my-accordion" role="tabpanel" active >
                          <b-card-body>
                            <b-card-text>
                              <!-- İçerik kısmı buralara gelecek -->
                              <b-list-group>
                                <router-link :to="{ name: 'profile.contact' }" class="text-decoration-none" @click="changePage()" >
                                  <b-list-group-item class="my-1 border-0" ><i class="fas fa-address-book"></i>
                                    {{ $t("İletişim Bilgilerini Düzenle") }}</b-list-group-item >
                                </router-link>
                                <router-link :to="{ name: 'profile.adress' }" class="text-decoration-none" @click="changePage()" >
                                  <b-list-group-item class="my-1 border-0" ><i class="fas fa-map"></i>
                                    {{ $t("Adres Bilgilerini Güncelle") }}</b-list-group-item >
                                </router-link>
                                <router-link :to="{ name: 'profile.referer' }" class="text-decoration-none" @click="changePage()" >
                                  <b-list-group-item class="my-1 border-0" ><i class="fas fa-user-plus"></i>
                                    {{ $t("Referans Sistemi") }}</b-list-group-item >
                                </router-link>
                                <router-link :to="{ name: 'profile.banks' }" class="text-decoration-none" @click="changePage()" >
                                  <b-list-group-item class="my-1 border-0" ><i class="fas fa-money-check"></i>
                                    {{ $t("Banka Hesap Bilgilerim") }}</b-list-group-item >
                                </router-link>
                              </b-list-group>
                            </b-card-text>
                          </b-card-body>
                        </b-collapse>
                      </b-card>

                      <b-card no-body class="mb-1">
                        <b-card-header header-tag="header" class="p-1" role="tab" >
                          <b-button block v-b-toggle.accordions-2 class=" bg-transparent text-dark border-0 w-100 text-left " @click="activeIndex = 1" ><i class="fas fa-file"></i> {{ $t("KYC") }}
                            <span class="float-right when-opened" ><i class="fas fa-chevron-down"></i></span ><span class="float-right when-closed" ><i class="fas fa-chevron-up"></i></span ></b-button>
                        </b-card-header>
                        <b-collapse id="accordions-2" :visible="activeIndex === 1" :aria-expanded="activeIndex === 1 ? true : false" accordion="my-accordion" role="tabpanel" >
                          <b-card-body>
                            <b-card-text>
                              <!-- İçerik kısmı buralara gelecek -->
                              <b-list-group>
                                <router-link :to="{ name: 'profile.kyc' }" class="text-decoration-none" @click="changePage()" >
                                  <b-list-group-item class="my-1 border-0" ><i class="fas fa-camera"></i>
                                    {{ $t("KYC Onayı") }}</b-list-group-item >
                                </router-link>
                              </b-list-group>
                            </b-card-text>
                          </b-card-body>
                        </b-collapse>
                      </b-card>
                      <b-card no-body class="mb-1">
                        <b-card-header header-tag="header" class="p-1" role="tab" >
                          <b-button block v-b-toggle.accordions-3 class=" bg-transparent text-dark border-0 w-100 text-left " @click="activeIndex = 2" ><i class="fas fa-lock"></i> {{ $t("Güvenlik") }}
                            <span class="float-right when-opened" ><i class="fas fa-chevron-down"></i></span ><span class="float-right when-closed" ><i class="fas fa-chevron-up"></i></span ></b-button>
                        </b-card-header>
                        <b-collapse id="accordions-3" :visible="activeIndex === 2" :aria-expanded="activeIndex === 2 ? true : false" accordion="my-accordion" role="tabpanel" >
                          <b-card-body>
                            <b-card-text>
                              <!-- İçerik kısmı buralara gelecek -->
                              <b-list-group>
                                <router-link :to="{ name: 'profile.password' }" class="text-decoration-none" @click="changePage()" >
                                  <b-list-group-item class="my-1 border-0" ><i class="fas fa-key"></i>
                                    {{ $t("Şifremi Değiştir") }}</b-list-group-item >
                                </router-link>
                                <router-link :to="{ name: 'profile.sessions' }" class="text-decoration-none" @click="changePage()" >
                                  <b-list-group-item class="my-1 border-0" ><i class="fas fa-lock-open"></i>
                                    {{ $t("Oturumlar") }}</b-list-group-item >
                                </router-link>
                                <!--                                <router-link :to="{name: 'profile.authentication'}" class="text-decoration-none">-->
                                <!--                                    <b-list-group-item href="#">Kimliğini Doğrula</b-list-group-item>-->
                                <!--                                </router-link>-->
                              </b-list-group>
                            </b-card-text>
                          </b-card-body>
                        </b-collapse>
                      </b-card>
                      <b-card no-body class="mb-1">
                        <b-card-header header-tag="header" class="p-1" role="tab" >
                          <b-button block v-b-toggle.accordions-4 class=" bg-transparent text-dark border-0 w-100 text-left " @click="activeIndex = 3" ><i class="fas fa-headset"></i> {{ $t("Destek") }}
                            <span class="float-right when-opened" ><i class="fas fa-chevron-down"></i></span ><span class="float-right when-closed" ><i class="fas fa-chevron-up"></i></span ></b-button>
                        </b-card-header>
                        <b-collapse id="accordions-4" :visible="activeIndex === 3" :aria-expanded="activeIndex === 3 ? true : false" accordion="my-accordion" role="tabpanel" >
                          <b-card-body>
                            <b-card-text>
                              <!-- İçerik kısmı buralara gelecek -->
                              <!-- Ticket sayfası oluşturulup link verilecek -->
                              <b-list-group>
                                <router-link :to="{ name: 'profile.ticket' }" class="text-decoration-none" @click="changePage()" >
                                  <b-list-group-item class="my-1 border-0" ><i class="fas fa-check-circle"></i>
                                    {{ $t("Ticket") }}</b-list-group-item >
                                </router-link>
                              </b-list-group>
                            </b-card-text>
                          </b-card-body>
                        </b-collapse>
                      </b-card>

                      <!-- <b-card no-body class="mb-1"> <b-card-header header-tag="header" class="p-1" role="tab"> <b-button block v-b-toggle.accordion-2 variant="info">Accordion 2</b-button> </b-card-header> <b-collapse id="accordion-2" accordion="my-accordion" role="tabpanel"> <b-card-body> <b-card-text>{{ text }}</b-card-text> </b-card-body> </b-collapse> </b-card>

    <b-card no-body class="mb-1"> <b-card-header header-tag="header" class="p-1" role="tab"> <b-button block v-b-toggle.accordion-3 variant="info">Accordion 3</b-button> </b-card-header> <b-collapse id="accordion-3" accordion="my-accordion" role="tabpanel"> <b-card-body> <b-card-text>{{ text }}</b-card-text> </b-card-body> </b-collapse> </b-card> -->
                    </div>
                    <p class="card-text mt-2">
                      <small>{{ $t( "Hesap güvenliğini korumak adına iletişim bilgilerini her zaman güncel tutmalısınız." ) }}</small>
                    </p>
                  </b-card-text>
                </b-card-body>
              </b-collapse>
            </b-card>
          </div>
        </div>
      </b-col>
      <b-col cols="12" md="8" class="mb-5">
        <b-card>
          <router-view></router-view>
        </b-card>
      </b-col>
    </b-row>
  </b-card>
</template>

<script>
export default {
  name: "Profile",
  data: () => ({
    activeIndex: -1,
    selectedMainIndex: false,
  }),
  methods: {
    changePage() {
      this.activeIndex = -1;
      this.selectedMainIndex = false;
    },
  },
};
</script>

<style lang="scss" scoped>

</style>
