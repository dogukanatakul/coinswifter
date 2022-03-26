<template>
  <div class="settings-profile">
    <b-form enctype="multipart/form-data" @submit="talepGonder()">
      <b-row>
        <b-col cols="12">
          <b-form-group label="Destek Bileti Kategorisi">
              <select class="mt-3 form-control" inputmode="text">
                <option value="">Lütfen kategori seçiniz</option>
                <option value="1">KYC</option>
                <option value="2">Alım Satım</option>
                <option value="3">Profil Bilgileri</option>
                <option value="4">Coin Listeleme</option>
                <option value="5">Yazılımsal Hatalar</option>
              </select>
          </b-form-group>
          <b-form-group label="Destek Bileti konusu">
              <b-form-input class="mt-3 form-control" placeholder="Destek Bileti konusunu giriniz" inputmode="text"></b-form-input>
          </b-form-group>
          <b-form-group label="Destek Bileti Detayı">
              <textarea class="mt-3 form-control" placeholder="Destek Bileti detayını giriniz" rows="5" inputmode="text"></textarea>
          </b-form-group>
          <b-form-group label="Destek Bileti Ekleri">
              <input type="file" class="mt-3 form-control" placeholder="Destek Bileti detayını giriniz" inputmode="text" multiple />
          </b-form-group>
          <b-form-group>
              <div class="d-grid gap-2">
            <b-button type="submit" block variant="primary">{{
              $t("TALEP OLUŞTUR")
            }}</b-button>
          </div>
          </b-form-group>
        </b-col>
      </b-row>
    </b-form>
    <b-row>
      <b-col cols="12" class="table-responsive">
        <table class="table table-bordered table-stripped">
          <thead>
            <tr>
              <th>#</th>
              <th>Destek Bileti Başlığı</th>
              <th>Destek Bileti Detayı</th>
              <th>Destek Bileti Ekleri</th>
              <th>Destek Bileti Gönderilme Tarihi</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Deneme Deneme Deneme Deneme Deneme Deneme Deneme Deneme Deneme Deneme Deneme </td>
              <td>Test</td>
              <td><a href="#" class="link-dark text-decoration-none">deneme.jpg</a>, <a href="#" class="link-dark text-decoration-none">test.png</a>,<a href="#" class="link-dark text-decoration-none">info.gif</a></td>
              <td>26.03.2022 15:36:42</td>
            </tr>
          </tbody>
        </table>
      </b-col>
    </b-row>
  </div>
</template>

<script>
export default{
  name:'Ticket',
  data(){
    return{}
  },
  methods:{
    talepGonder()
    {
      this.$router.push({ name: "profile.ticketsss" });
    }
  }
}
</script>

<style lang="scss" scoped>
th, td{
  min-width: 10% !important;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
