<template>
  <div id="section0" class="invented-container overflow-hidden">
    <b-container>
      <b-row class="invented-area pt-70 pb-40 mb-pt-45 justify-content-between">
        <b-col cols="12">
          <h1 class="text-center mb-5">Bitcoin Cüzdanı</h1>
          <p>
            Coin Swifter'da Bitcoin yatırma ve çekme işlemleri için Bitcoin
            Cüzdanı ile yapılmaktadır. Bitcoin Cüzdanı’nda Bitcoin yatırma
            adresine ve QR koduna ulaşılabilir, her yatırma işleminden önce yeni
            adres üretilebilir. Daha önce kullanılan Bitcoin yatırma adreslerine
            de ulaşılabilir. İşlem gizliliğini artırmak amacıyla, her yatırma
            işleminden önce yeni bir Bitcoin adresi üretilmesi tavsiye
            edilmektedir. Ancak daha önce transfer yapılan adresler de
            kullanılabilmektedir.
          </p>
          <p>Bitcoin yatırma adresine en az 0.0001 BTC gönderilmelidir.</p>
          <p>
            Bitcoin çekmek için Bitcoin Cüzdanı’na önceden tanımlanan Bitcoin
            adreslerine Bitcoin çekilebilir. Adres tanımlamak için 2 Aşamalı
            Doğrulama kodu kullanılmalıdır. Tanımlı adrese Bitcoin çekme isteği
            gönderebilmek için Bitcoin Cüzdan PIN kodu girilmeli ve sistemde
            kayıtlı adrese gönderilen e-postada yer alan bilgiler kontrol
            edilerek çekme işlemi onaylanmalıdır. Onaylanan çekme talebi, kısa
            bir süre içerisinde Bitcoin blokzincirine iletilmektedir.
          </p>
          <p>
            Bitcoin Cüzdanı’ndan 0.0005 BTC ve üzeri tutarlar çekilebilmektedir.
          </p>
          <p>Coin Swifter'da Bitcoin çekme işlemleri ücretsizdir.</p>
        </b-col>
      </b-row>
    </b-container>
  </div>
</template>

<script>
export default {
  name: "BitcoinWallet"
};
</script>

<style scoped>
</style>
