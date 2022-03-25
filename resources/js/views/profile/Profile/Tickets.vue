<template>
  <div class="settings-profile">
    <b-form enctype="multipart/form-data">
      <b-row>
        <b-col cols="12">
          <b-form-group label="Ticket Kategorisi">
              <select class="mt-3 form-control" inputmode="text">
                <option value="">Lütfen kategori seçiniz</option>
                <option value="1">KYC</option>
                <option value="2">Alım Satım</option>
                <option value="3">Profil Bilgileri</option>
                <option value="4">Coin Listeleme</option>
                <option value="5">Yazılımsal Hatalar</option>
              </select>
          </b-form-group>
          <b-form-group label="Ticket konusu">
              <b-form-input class="mt-3 form-control" placeholder="Ticket konusunu giriniz" inputmode="text"></b-form-input>
          </b-form-group>
          <b-form-group label="Ticket Detayı">
              <textarea class="mt-3 form-control" placeholder="Ticket detayını giriniz" rows="5" inputmode="text"></textarea>
          </b-form-group>
          <b-form-group label="Ticket Ekleri">
              <input type="file" class="mt-3 form-control" placeholder="Ticket detayını giriniz" inputmode="text" multiple />
          </b-form-group>
          <b-form-group>
              <div class="d-grid gap-2">
            <b-button type="sumit" block variant="primary">{{
              $t("TALEP OLUŞTUR")
            }}</b-button>
          </div>
          </b-form-group>
        </b-col>
      </b-row>
    </b-form>
  </div>
</template>

<script>
export default{
  name:'Ticket',
  data(){
    return{}
  },
  methods:{
  }
}
</script>

<style lang="scss" scoped>

</style>
