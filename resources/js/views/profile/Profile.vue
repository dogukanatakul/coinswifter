<template>
  <b-card border-variant="white">
    <b-row>
      <b-col cols="12" md="4" class="mb-2">
        <b-card-group deck>
          <b-card :header="$t('Profil & Ayarlar')">
            <b-list-group>
              <router-link
                :to="{ name: 'profile.banks' }"
                class="text-decoration-none"
              >
                <b-list-group-item>{{
                  $t("Ödeme Yöntemlerim")
                }}</b-list-group-item>
              </router-link>
              <router-link
                :to="{ name: 'profile.contact' }"
                class="text-decoration-none"
              >
                <b-list-group-item>{{
                  $t("İletişim Bilgilerini Düzenle")
                }}</b-list-group-item>
              </router-link>
              <router-link
                :to="{ name: 'profile.adress' }"
                class="text-decoration-none"
              >
                <b-list-group-item>{{
                  $t("Adres Bilgilerini Güncelle")
                }}</b-list-group-item>
              </router-link>
              <router-link
                :to="{ name: 'profile.kyc' }"
                class="text-decoration-none"
              >
                <b-list-group-item>{{ $t("KYC Onayı") }}</b-list-group-item>
              </router-link>
              <router-link
                :to="{ name: 'profile.referer' }"
                class="text-decoration-none"
              >
                <b-list-group-item>{{
                  $t("Referans Sistemi")
                }}</b-list-group-item>
              </router-link>
              <router-link
                :to="{ name: 'profile.password' }"
                class="text-decoration-none"
              >
                <b-list-group-item>{{
                  $t("Şifreni Değiştir")
                }}</b-list-group-item>
              </router-link>
              <router-link
                :to="{ name: 'profile.sessions' }"
                class="text-decoration-none"
              >
                <b-list-group-item>{{ $t("Oturumlar") }}</b-list-group-item>
              </router-link>
              <!--                                <router-link :to="{name: 'profile.authentication'}" class="text-decoration-none">-->
              <!--                                    <b-list-group-item href="#">Kimliğini Doğrula</b-list-group-item>-->
              <!--                                </router-link>-->
            </b-list-group>
            <p class="card-text mt-2">
              <small>{{
                $t(
                  "Hesap güvenliğini korumak adına iletişim bilgilerini her zaman güncel tutmalısınız."
                )
              }}</small>
            </p>
          </b-card>
        </b-card-group>
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
};
</script>

<style lang="scss" scoped>

</style>
