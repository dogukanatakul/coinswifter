<template>
  <div class="px-5 py-1 corporative-section">
    <b-tabs content-class="mt-3" fill>
      <b-tab title="Piyasa Yapıcı" active>
        <div class="banners">
          <div>
            <h1>Piyasa Yapıcı Başvurusu</h1>
            <b-button variant="success" class="btn-call">Başvur</b-button>
          </div>
        </div>
        <div class="corporative-content">
          <div class="equities">
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div class="corporative-col institution-equity">
                <h5 class="institution_equity-title">Yüksek API Hız Limiti</h5>
                <p class="institution-equity-paragraph">
                  Yüksek sıklıktaki alım satım ihtiyaçlarınız için tasarlandı
                </p>
              </div>
            </div>
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div class="corporative-col institution-equity">
                <h5 class="institution_equity-title">
                  Kişiye Özel Müşteri Hizmetleri Desteği
                </h5>
                <p class="institution-equity-paragraph">
                  Teknik sorunların çözümünde 7/24 destek
                </p>
              </div>
            </div>
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div class="corporative-col institution-equity">
                <h5 class="institution_equity-title">Çekim Limiti Artırıldı</h5>
                <p class="institution-equity-paragraph">
                  Platformlar arası arbitrajla gelirinizi ikiye katlayın
                </p>
              </div>
            </div>
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div class="corporative-col institution-equity">
                <h5 class="institution_equity-title">Faizsiz Borç</h5>
                <p class="institution-equity-paragraph">
                  Anapara kullanımını geliştirerek sorunsuz işlem yapmanızı sağlar
                </p>
              </div>
            </div>
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div class="corporative-col institution-equity">
                <h5 class="institution_equity-title">
                  Yüksek Hızlı API Arayüzü
                </h5>
                <p class="institution-equity-paragraph">
                  Daha hızlı işlem yapmanızı sağlamak için API v3 arayüzü
                </p>
              </div>
            </div>
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div class="corporative-col institution-equity">
                <h5 class="institution_equity-title">VIP Avantajları</h5>
                <p class="institution-equity-paragraph">
                  VIP müşterilerine özgü avantajlardan faydalanın
                </p>
              </div>
            </div>
          </div>
          <div class="marketMakerInfo">
            <h4 class="institution_marketMakerTitle">Koşullar</h4>
            <div class=" institution_row institution_space_between institution_row_middle " >
              <div class="institution_col institution_marketLevel1 mx-auto my-2" >
                <p class="institution_marketMaker1_tip">Seviye</p>
                <p class="institution_marketMaker1_title">P1</p>
                <p class="institution_marketMaker1_textTitle">
                  Başvuru Koşulları
                  <span>(Her iki koşulun aynı anda karşılanması gerekir)</span>
                </p>
                <p class="institution_marketMaker1_text">
                  1. Son 30 gündeki işlem hacmi ≥ 2.000 BTC olmalı veya negatif Yapıcı
                </p>
                <p class="institution_marketMaker1_text">
                  2. MEXC spot pozisyonu gerekliliği 200.000 USDT'den fazla olmalı
                </p>
              </div>
              <div class="institution_col institution_marketLevel2 mx-auto my-2" >
                <p class="institution_marketMaker1_tip">Seviye</p>
                <p class="institution_marketMaker1_title">P2</p>
                <p class="institution_marketMaker1_textTitle">
                  Başvuru Koşulları
                  <span> (koşullardan birini karşılamanız yeterli) </span>
                </p>
                <p class="institution_marketMaker1_text">
                  1. 1.000 BTC ≤ Son 30 gündeki işlem hacmi {{ $t("<") }} 2.000 BTC
                </p>
                <p class="institution_marketMaker1_text">
                  2. MEXC spot hesabı varlıkları 200.000 USDT'den fazla olmalı
                </p>
                <p class="institution_marketMaker1_text">
                  3. Diğer borsa yapıcı 0 piyasa yapıcı
                </p>
              </div>
            </div>
          </div>
          <div class="col-12 text-center mb-4">
            Daha fazla bilgi edinmek isterseniz, lütfen VIP asistanıyla iletişime geçin ya da
            <a href="mailto:info@coinswifter.com">info@coinswifter.com</a>
            adresine e-posta gönderebilirsiniz
          </div>
        </div>
      </b-tab>
      <b-tab title="Fonlama">
        <div class="banners">
          <div>
            <h1>Fonlama Hizmetleri</h1>
            <b-button variant="success" class="btn-call">Başvur</b-button>
          </div>
        </div>
        <div class="corporative-content">
          <div class="equities">
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div class="corporative-col institution-equity">
                <h5 class="institution_equity-title">
                  Anapara Kullanımını Üst Düzeye Çıkarın
                </h5>
                <p class="institution-equity-paragraph">
                  Kaldıraç oranını artırın ve getirilerinizi ikiye katlayın
                </p>
              </div>
            </div>
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div class="corporative-col institution-equity">
                <h5 class="institution_equity-title">Esnek Döngü</h5>
                <p class="institution-equity-paragraph">
                  Hemen borçlanın ve istediğiniz zaman geri ödeyin
                </p>
              </div>
            </div>
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div class="corporative-col institution-equity">
                <h5 class="institution_equity-title">
                  Daha Yüksek Çekim Limiti
                </h5>
                <p class="institution-equity-paragraph">
                  Platformlar arası arbitraj desteklenir
                </p>
              </div>
            </div>
          </div>
          <div class="fundingInfo table-responsive">
            <h4 class="institution_fundingTitle">
              MEXC, seçkin kurumsal müşteriler adına yüksek tutarlı dağıtım hizmetleri sağlamaktadır
            </h4>
            <div class="institution_fundingDesc">
              İhtiyaçlarınızla ilgili, lütfen müşteri yöneticinizle iletişime geçin ya da aşağıdaki adrese e-posta gönderebilirsiniz
            </div>
            <button class="btn btn-success btn-lg">info@coinswifter.com</button>
          </div>
        </div>
      </b-tab>
      <b-tab title="Kurumsal">
        <div class="banners">
          <div>
            <h1>Kurumsal Kullanıcı Olun</h1>
            <b-button variant="success" class="btn-call">Başvur</b-button>
          </div>
        </div>
        <div class="corporative-content">
          <div class="equities">
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon w-100">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div style="clear: none"></div>
              <div class="corporative-col institution-equity w-100">
                <h5 class="institution_equity-title">Daha Düşük Oranlar</h5>
              </div>
            </div>
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon w-100">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div style="clear: none"></div>
              <div class="corporative-col institution-equity w-100">
                <h5 class="institution_equity-title">
                  Kilit Müşteri Yöneticinize 7/24 Ulaşabilirsiniz
                </h5>
              </div>
            </div>
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon w-100">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div style="clear: none"></div>
              <div class="corporative-col institution-equity w-100">
                <h5 class="institution_equity-title">
                  Daha Yüksek Çekim Limiti
                </h5>
              </div>
            </div>
          </div>
          <div class="instutional_info">
            <h4 class="instutional_info_title">
              Kurumsal Yeterlilik Başvuru Belgeleri
            </h4>
            <p class="instutional_info_text">1. Yönetim Kurulu Kararı</p>
            <p class="instutional_info_text">2. Mutabakat ve Esas Sözleşme</p>
            <p class="instutional_info_text">3. Kuruluş Belgesi</p>
            <p class="instutional_info_text">4.Şirket Pay Defterinin Kopyası</p>
            <p class="instutional_info_text">
              5. Yönetim Kurulu Üye Defterinin Kopyası
            </p>
            <p class="instutional_info_text">
              6. Yöneticilerin, yetkili temsilcilerin, şirketi idare eden veya yöneten kişilerin ve tüm nihai hak sahiplerinin %25'ine ait kimlik belgeleri (yalnızca pasaportlar)
            </p>
            <p class="instutional_info_text">
              7. Şirket Yapısı (tüm nihai yararlanıcıları gösteren organizasyon şeması) Devlete ait web sitesinde
            </p>
            <p class="instutional_info_text">
              8. Devlet web sitesinde bulunan şirket kuruluş bilgilerinin ekran görüntüsü (sorgulama tarihini içermesi gerekir)
            </p>
            <p class="instutional_info_tip">
              Lütfen onaylanma sürecinden önce yukarıdaki belgeleri tamamlayın
            </p>
          </div>

          <div class="col-12 text-center mb-4">
            Daha ayrıntılı bilgi edinmek için lütfen müşteri yöneticinizle iletişime geçin ya da
            <a href="mailto:info@coinswifter.com">info@coinswifter.com</a>
            adresine e-posta gönderebilirsiniz
          </div>
        </div>
      </b-tab>
      <b-tab title="API">
        <div class="banners">
          <div>
            <h1>API Hizmetleri</h1>
            <b-button variant="success" class="btn-call">Başvur</b-button>
          </div>
        </div>
        <div class="corporative-content">
          <div class="equities">
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div class="corporative-col institution-equity">
                <h5 class="institution_equity-title">
                  Çoklu İşlevler Desteklenir
                </h5>
              </div>
            </div>
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div class="corporative-col institution-equity">
                <h5 class="institution_equity-title">
                  Ultra Yüksek Hacimli, Hızlı ve İstikrarlı TPS
                </h5>
              </div>
            </div>
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div class="corporative-col institution-equity">
                <h5 class="institution_equity-title">Güvenli ve Emniyetli</h5>
              </div>
            </div>
            <div class="corporative-row overflowed-corporotive">
              <div class="corporative-col corporative-icon">
                <img src="../../assets/img/coinicon/BRS.png" />
              </div>
              <div class="corporative-col institution-equity">
                <h5 class="institution_equity-title">
                  Kişiselleştirme İmkanı Sunan Onlarca Özellik
                </h5>
              </div>
            </div>
          </div>
          <div class="instutional_apiInfo table-responsive">
            <h4 class="institution_apiInfo">
              REST+WEBSOCKET hizmetini destekler<br /> API aracılığıyla otomatik spot, kaldıraç ve vadeli işlem alım satımı gerçekleştirin.
            </h4>
            <button class="btn btn-success btn-lg overflowed-table">
              API Belgesi
            </button>
          </div>
          <div class="col-12 text-center mb-4">
            Daha fazla bilgi edinmek isterseniz, lütfen VIP asistanıyla iletişime geçin ya da
            <a href="mailto:info@coinswifter.com">info@coinswifter.com</a>
            adresine e-posta gönderebilirsiniz
          </div>
        </div>
      </b-tab>
      <b-tab title="Şampiyon">
        <div class="instutional_championBanner">
          <div class="instutional_championBannerInner">
            <h3 class="instutional_championTitle">DeFi Power Miktara Dayalı Alım Satım Finali</h3>
            <p class="instutional_championDesc">
              DeFi pazarı, 2021'in üçüncü çeyreğinde 200 milyar doları aşan Kilitlenmiş Toplam Değer (TVL) ile tarihinde yeni bir sayfa açarken, bu miktara dayalı alım satım yarışması dünyanın elit nicel ekiplerini seçmek ve çok yönlü bir destek sağlamak amacını gütmektedir.
            </p>
          </div>
        </div>
        <div class="corporative-content">
          <div class="corporative-row justify-content-between instution_activityDate">
            <div class="institution_col instution_dateItem instution_registrationItem">
              <p class="instution_dateTitle">Kayıt Zamanı</p>
              <p class="instution_dateDesc">15 Şubat 2022 - 25 Şubat 2022 (11:00 TSİ)</p>
            </div>
            <div class="institution_col instution_dateItem instution_competitionItem">
              <p class="instution_dateTitle">Yarışma Zamanı</p>
              <p class="instution_dateDesc">15 Şubat 2022 - 15 Nisan 2022 (11:00 TSİ)</p>
            </div>
          </div>
        </div>
        <div class="instution_countdown">
          <p class="instution_countdownTitle">Geri Sayım</p>
          <div class="instution_countdownTime">00<span>:</span>00<span>:</span>00<span>:</span>00</div>
        </div>
        <div class="instution_championHandler">
          <button type="button" class="instution_championButton btn btn-success btn-lg">Telegrama Katılın</button>
        </div>
        <div class="instution_championDescItem">
          <h5 class="instution_championSubtitle">Yarışma Kuralları ve İlkeleri</h5>
          <p class="instution_championSubdesc">1. Bu yarışma bir kripto varlık vadeli işlem hesabı alım satım yarışmasıdır. İlk giriş sermayesi, en az 50.000 USD olan bir kripto varlık hesabıdır. Sonuç hesaplamasında teminat olarak USD kullanılır. Katılımcı takımların kendi fonlarını artırmaları gerekir. Katılımcılar tek başlarına alım satım yaparlar, kendi kar ve zararlarından sorumludurlar. Tüm işlemler, ticari rekabet kurallarına ve ilgili ulusal yasa ve yönetmeliklere uygun olmalıdır;</p>
          <p class="instution_championSubdesc">2. Yarışma Başlangıç Tarihi: 15 Şubat 2022 (11.00 TSİ) ile 15 Nisan 2022 (11.00 TSİ) arasındadır ve sonuçlar bu tarihten itibaren hesaplanacaktır. Son kayıt tarihi olan 25 Şubattan önce kaydolan  geçerli hesapların sonuçları, düzenleyici tarafından belirtilen tarihten itibaren hesaplanacaktır.</p>
          <p class="instution_championSubdesc">3. Yarışmanın genel sıralaması "günlük" olarak hesaplanır. Yarışma süresi boyunca, 11.00'dan ertesi gün 11.00'a (TSİ, 24 saat) kadar günde yalnız bir kez hesaplanır. Her gösterge, aynı gün güncellenecektir;</p>
          <p class="instution_championSubdesc">4. Her takım, yarışma sırasında aynı anda 4 hesaba erişim sağlayabilir. 4 hesabın toplam ilk giriş sermayesi en az 50.000 USDT'den az olmamalıdır, 4 hesabın net varlık değeri toplanıp 1 sıralamaya dönüştürülebilir. Tüm katılımcıların, organizasyon komitesi personelinin API testi için 10 gün önceden test hesaplarını göndermeleri gerekir. Böylece, yarışmanın ilk gününde verilerin zamanında yayınlanması sağlanacaktır. API testinde başarılı olamayan takımlar sonuçların duyurulmasında yer alamayacaktır.</p>
          <p class="instution_championSubdesc">5. İşlem hesabı, belirli MEXC hesabında alım satım yapmalıdır;</p>
          <p class="instution_championSubdesc">6. Yarışma boyunca alım satımı yapılan tokenler: SOL, UNI, AVAX, DOT, DOG, BNB, MATIC, FIL; (piyasa yapıcı alım satım kategorisi çoğunlukla popüler tokenlerle işlem yaparken, diğer kategoriler yukarıdaki tokenlerle işlem yapar)</p>
          <p class="instution_championSubdesc">7. Bu yarışma, Vadeli ve Spot alım satımları kabul eder;</p>
          <p class="instution_championSubdesc">8. Sıralama, katılan her takımın toplam puanlarına göre hesaplanır. Tüm sonuçlar, yarışmanın resmi Twitter duyurusuna tabidir. Ulusal resmi tatillerde verilerin yayınlanması askıya alınacaktır fakat sonuçlar aynı şekilde kaydedilmeye devam edecektir;</p>
          <p class="instution_championSubdesc down">Toplam Puan = Geometrik Ortalama Getiri Puanı Oranı x 0,4 + Kümülatif Getiri Puanı Oranı x 0,4 + Maksimum Kayıp Puanı x 0,2</p>
          <p class="instution_championSubdesc">9. Yarışma 2 ay sürecektir ve kaydolduktan sonra düzenleyincinin son hafta duyurusuna tabi olacaktır.</p>
        </div>
        <div class="instution_championDescItem">
          <h5 class="instution_championSubtitle">Yarışma Ödülleri</h5>
          <p class="instution_championSubdesc up">CTA Kategorisi: Şampiyon, İkinci</p>
          <p class="instution_championSubdesc up">Arbitraj Alım Satım Kategorisi: Şampiyon, İkinci</p>
          <p class="instution_championSubdesc up">HFT Kategorisi: Şampiyon</p>
          <p class="instution_championSubdesc up">Piyasa Yapıcı Miktara Dayalı Alım Satım Kategorisi: Şampiyon, İkinci, Üçüncü</p>
          <p class="instution_championSubdesc up">Yarışma boyunca En Büyük Kazanç Ödülü: 1 (maksimum kayıp oranı %40'ın altında olan ve en büyük kümülatif kazanca sahip oyuncular)</p>
          <p class="instution_championSubdesc up">Yarışma boyunca En Yüksek Yatırım Getirisi Ödülü: 1 (maksimum kayıp oranı %40'ın altında olan ve kümülatif yatırım getirisi en yüksek olan oyuncular)</p>
          <p class="instution_championSubdesc up">Yarışma boyunca En İstikrarlı Kazanç Ödülü: 1 (APY'si %50'nin üzerinde olan ve en düşük maksimum kayba sahip oyuncular)</p>
          <p class="instution_championSubdesc up">Mükemmellik Ödülü: Kümülatif net varlık değeri 1,02'nin üzerinde olan oyuncular</p>
          <p class="instution_championSubdesc up">En Büyük İşlem Hacmi Ödülü: 1</p>
          <p class="instution_championSubdesc up">Günlük ve Haftalık En Büyük Kazançlar: Her biri 1 kişi</p>
        </div>
        <div class="instution_championDescItem">
          <h5 class="instution_championSubtitle">Ödül tutarı ve yarışma sonrası yatırım fonu</h5>
          <p class="instution_championSubdesc">1. MEXC kazanan ekibe varlık yönetimi için 200 BTC sağlayacaktır;</p>
          <p class="instution_championSubdesc">2. APY'nin %30'undan fazla kazançla yarışmaya katılan mükemmel nicel ekipler, varlık yönetimi için minimum 10 BTC alabilir;</p>
          <p class="instution_championSubdesc">3. MEXC, performans denetim raporlarını yayınlayacak ve kaynak yaratma hizmetleri sağlayacaktır.</p>
          <p class="instution_championSubdesc">4. Piyasa Yapıcı Miktara Dayalı Alım Satım Kategorisi Özel Ödülü: 10.000 USDT Değerinde MX Platform Puanı</p>
          <p class="instution_championSubdesc">Kayıtlar resmi <a href="https://t.me/coinswifter" target="_blank" class="up">Telegram</a> ya da <a href="mailto:info@coinswifter.com" class="up">info@coinswifter.com</a> adresinden yapılabilir.</p>
        </div>
      </b-tab>
      <b-tab title="Daha Fazla">
        <div class="banners">
          <div>
            <h1>Daha Fazla Kurumsal Hizmet</h1>
          </div>
        </div>
      </b-tab>
    </b-tabs>
  </div>
  <!-- <Footer class="w-100"></Footer> -->
</template>

<script>
import Footer from "../../components/Footer.vue";

export default {
  components: { Footer },
  name: "corporative-services",
  data() {
    return {};
  },
  methods: {},
};
</script>

<style scoped>
</style>
