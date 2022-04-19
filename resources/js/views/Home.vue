<template>
  <div class="home">
    <div class="service-container">
      <!-- <b-row class="banner-content-area justify-content-between text-responsive" > <b-col cols="12" md="6" class="banner-content"> <div class="banner-cont-info text-white wow fadeInUp" data-wow-duration="1s" data-wow-delay=".3s" > <img v-bind:src="'../assets/img/home/banner/home_slide_logo.png'" alt="CoinSwifter" class="banner-logo fade-in-out margin-5" /> <h5 class="text-white mt-2 text-responsive"> {{ $t("Güvenilir kripto para alım-satım deneyimi") }} </h5> <h1 class="text-white
          text-responsive"> {{ $t("CoinSwifter.com'da!") }} </h1> <a class="text-decoration-none" href="https://panel.broovs.io/whitepaper_tr.pdf" title="Broovs White Paper" rel="noreferrer" target="_blank" > <p class="text-white mx-auto text-responsive"> <b-icon icon="box-arrow-up-right" font-scale="0.5"></b-icon> {{ $t( "CoinSwifter Borsası Broovs (BRS) Ekosistemi Projesidir." ) }} </p> </a> <router-link v-if="!user" :to="{ name: 'signup' }" class="crp-btn mx-auto text-white" >{{ $t("Hemen
          Hesap Oluştur") }}</router-link > </div> </b-col> <b-col cols="12" md="6" lg="6" class="banner-content d-none d-sm-block" > <div class="banner-cont-img wow fadeInRight" data-wow-duration="1s" data-wow-delay=".3s" > <img src="assets/img/home/banner/hero-banner.png" alt="" /> </div> </b-col> </b-row> -->
      <b-row>
        <b-col cols="12" class="justify-content-between float-left" style="padding: inherit !important" >
          <div id="carouselExampleIndicators" class="carousel slide mh-700" data-bs-ride="carousel" >
            <!-- <div class="carousel-indicators">
              <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1" ></button>
              <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2" ></button>
              <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3" ></button>
            </div> -->
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img src="assets/img/bg/bg-5.png" class="d-block contain narrow" alt="assets/img/bg/bg-5.png" />
                <div class="carousel-caption">
                  <b-col cols="12">
                    <div>
                      <h1>
                        Türkiye'nin kendi Blokchain'i olan ilk ve tek
                      </h1>
                      <h1>
                        Borsası Coin<span class="up">Swifter'a</span> Hoşgeldiniz.
                      </h1>
                    </div>
                    <div class="slideDetail">
                      <img src="../assets/img/coinicon/xray.png" class="img-thumbnail bg-transparent border-0 wh-64"/>
                      <!-- Coin<span class="up">Swifter</span>, Kriptopara Alım - Satımın Tek Merkezi. -->
                    </div>
                  </b-col>
                </div>
              </div>
              <!-- <div class="carousel-item">
                <img src="assets/img/bg/bg-5.png" class="d-block contain narrow" alt="assets/img/bg/bg-5.png" />
                <div class="carousel-caption">
                  <b-col cols="12">
                    <div>
                      <h1>
                        Coin<span class="up">Swifter</span> Borsasına Hoşgeldiniz.
                      </h1>
                    </div>
                    <div class="slideDetail">
                      Coin<span class="up">Swifter</span>, Kriptopara Alım - Satımın Tek Merkezi.
                    </div>
                  </b-col>
                </div>
              </div>
              <div class="carousel-item">
                <img src="assets/img/bg/bg-5.png" class="d-block contain narrow" alt="assets/img/bg/bg-5.png" />
                <div class="carousel-caption">
                  <b-col cols="12">
                    <div>
                      <h1>
                        Coin<span class="up">Swifter</span> Borsasına Hoşgeldiniz.
                      </h1>
                    </div>
                    <div class="slideDetail">
                      Coin<span class="up">Swifter</span>, Kriptopara Alım - Satımın Tek Merkezi.
                    </div>
                  </b-col>
                </div>
              </div> -->
            </div>
          </div>
        </b-col>
      </b-row>
      <!-- <div>
                    <img class="home-broovs-banner" v-bind:src="'../assets/img/icon_banner/broovs-home.jpeg'">
                </div> -->
    </div>
    <div class="service-container d-600-big-block price-bg">
      <b-row>
        <b-col cols="2" class="justify-content-between float-left px-4 py-2 price-col" >
          <b-row class="my-2">
            <b-col cols="6" class="float-left overflowed fw-bolder price-text" ><h6>BTC/USDT</h6></b-col >
            <b-col cols="6" class="float-left overflowed fw-bolder price-percent up" >-0.30%</b-col >
          </b-row>

          <b-col cols="12" class="float-left overflowed price-text" ><h4 class="overflowed-table">40.555,26</h4></b-col >
          <b-col cols="12" class="float-left overflowed price-vol" >24H Vol 26.291,82</b-col >
          <!-- <b-col cols="12" class="float-left justify-content-between align-items-center" > <b-button v-b-modal.modal-hizli-al variant="success" class="mx-1 w-100 my-1" >Al</b-button > <b-button v-b-modal.modal-hizli-sat variant="danger" class="mx-1 w-100 my-1" >Sat</b-button > </b-col> -->
        </b-col>
        <b-col cols="2" class="justify-content-between float-left px-4 py-2 price-col" >
          <b-row class="my-2">
            <b-col cols="6" class="float-left overflowed fw-bolder price-text" ><h6>BTC/USDT</h6></b-col >
            <b-col cols="6" class="float-left overflowed fw-bolder price-percent down" >-0.30%</b-col >
          </b-row>
          <b-col cols="12" class="float-left overflowed price-text" ><h4 class="overflowed-table">40.555,26</h4></b-col >
          <b-col cols="12" class="float-left overflowed price-vol" >24H Vol 26.291,82</b-col >
          <!-- <b-col cols="12" class="float-left justify-content-between align-items-center" > <b-button v-b-modal.modal-hizli-al variant="success" class="mx-1 w-100 my-1" >Al</b-button > <b-button v-b-modal.modal-hizli-sat variant="danger" class="mx-1 w-100 my-1" >Sat</b-button > </b-col> -->
        </b-col>
        <b-col cols="2" class="justify-content-between float-left px-4 py-2 price-col" >
          <b-row class="my-2">
            <b-col cols="6" class="float-left overflowed fw-bolder price-text" ><h6>BTC/USDT</h6></b-col >
            <b-col cols="6" class="float-left overflowed fw-bolder price-percent down" >-0.30%</b-col >
          </b-row>
          <b-col cols="12" class="float-left overflowed price-text" ><h4 class="overflowed-table">40.555,26</h4></b-col >
          <b-col cols="12" class="float-left overflowed price-vol" >24H Vol 26.291,82</b-col >
          <!-- <b-col cols="12" class="float-left justify-content-between align-items-center" > <b-button v-b-modal.modal-hizli-al variant="success" class="mx-1 w-100 my-1" >Al</b-button > <b-button v-b-modal.modal-hizli-sat variant="danger" class="mx-1 w-100 my-1" >Sat</b-button > </b-col> -->
        </b-col>
        <b-col cols="2" class="justify-content-between float-left px-4 py-2 price-col" >
          <b-row class="my-2">
            <b-col cols="6" class="float-left overflowed fw-bolder price-text" ><h6>BTC/USDT</h6></b-col >
            <b-col cols="6" class="float-left overflowed fw-bolder price-percent down" >-0.30%</b-col >
          </b-row>
          <b-col cols="12" class="float-left overflowed price-text" ><h4 class="overflowed-table">40.555,26</h4></b-col >
          <b-col cols="12" class="float-left overflowed price-vol" >24H Vol 26.291,82</b-col >
          <!-- <b-col cols="12" class="float-left justify-content-between align-items-center" > <b-button v-b-modal.modal-hizli-al variant="success" class="mx-1 w-100 my-1" >Al</b-button > <b-button v-b-modal.modal-hizli-sat variant="danger" class="mx-1 w-100 my-1" >Sat</b-button > </b-col> -->
        </b-col>
        <b-col cols="2" class="justify-content-between float-left px-4 py-2 price-col" >
          <b-row class="my-2">
            <b-col cols="6" class="float-left overflowed fw-bolder price-text" ><h6>BTC/USDT</h6></b-col >
            <b-col cols="6" class="float-left overflowed fw-bolder price-percent down" >-0.30%</b-col >
          </b-row>
          <b-col cols="12" class="float-left overflowed price-text" ><h4 class="overflowed-table">40.555,26</h4></b-col >
          <b-col cols="12" class="float-left overflowed price-vol" >24H Vol 26.291,82</b-col >
          <!-- <b-col cols="12" class="float-left justify-content-between align-items-center" > <b-button v-b-modal.modal-hizli-al variant="success" class="mx-1 w-100 my-1" >Al</b-button > <b-button v-b-modal.modal-hizli-sat variant="danger" class="mx-1 w-100 my-1" >Sat</b-button > </b-col> -->
        </b-col>
        <b-col cols="2" class="justify-content-between float-left px-4 py-2 price-col" >
          <b-row class="my-2">
            <b-col cols="6" class="float-left overflowed fw-bolder price-text" ><h6>BTC/USDT</h6></b-col >
            <b-col cols="6" class="float-left overflowed fw-bolder price-percent down" >-0.30%</b-col >
          </b-row>
          <b-col cols="12" class="float-left overflowed price-text" ><h4 class="overflowed-table">40.555,26</h4></b-col >
          <b-col cols="12" class="float-left overflowed price-vol" >24H Vol 26.291,82</b-col >
          <!-- <b-col cols="12" class="float-left justify-content-between align-items-center" > <b-button v-b-modal.modal-hizli-al variant="success" class="mx-1 w-100 my-1" >Al</b-button > <b-button v-b-modal.modal-hizli-sat variant="danger" class="mx-1 w-100 my-1" >Sat</b-button > </b-col> -->
        </b-col>
      </b-row>
    </div>
    <div class="service-container d-600-small-block price-bg">
      <b-row>
        <b-col cols="12" class="justify-content-between float-left px-4 py-2 price-col" >
          <b-row class="my-2">
            <b-col cols="6" class="float-left overflowed fw-bolder price-text" ><h6>BTC/USDT</h6></b-col >
            <b-col cols="6" class="float-left overflowed fw-bolder price-percent up" >-0.30%</b-col >
          </b-row>

          <b-col cols="12" class="float-left overflowed price-text" ><h4 class="overflowed-table">40.555,26</h4></b-col >
          <b-col cols="12" class="float-left overflowed price-vol" >24H Vol 26.291,82</b-col >
          <!-- <b-col cols="12" class="float-left justify-content-between align-items-center" > <b-col cols="6" class="float-left mx-auto text-center"><b-button v-b-modal.modal-hizli-al variant="success" class="mx-auto w-75 my-1" >Al</b-button ></b-col> <b-col cols="6" class="float-left mx-auto text-center"><b-button v-b-modal.modal-hizli-sat variant="danger" class="mx-auto w-75 my-1" >Sat</b-button ></b-col> </b-col> -->
        </b-col>
        <b-col cols="12" class="justify-content-between float-left px-4 py-2 price-col" >
          <b-row class="my-2">
            <b-col cols="6" class="float-left overflowed fw-bolder price-text" ><h6>BTC/USDT</h6></b-col >
            <b-col cols="6" class="float-left overflowed fw-bolder price-percent down" >-0.30%</b-col >
          </b-row>
          <b-col cols="12" class="float-left overflowed price-text" ><h4 class="overflowed-table">40.555,26</h4></b-col >
          <b-col cols="12" class="float-left overflowed price-vol" >24H Vol 26.291,82</b-col >
          <!-- <b-col cols="12" class="float-left justify-content-between align-items-center" > <b-col cols="6" class="float-left mx-auto text-center"><b-button v-b-modal.modal-hizli-al variant="success" class="mx-auto w-75 my-1" >Al</b-button ></b-col> <b-col cols="6" class="float-left mx-auto text-center"><b-button v-b-modal.modal-hizli-sat variant="danger" class="mx-auto w-75 my-1" >Sat</b-button ></b-col> </b-col> -->
        </b-col>
        <b-col cols="12" class="justify-content-between float-left px-4 py-2 price-col" >
          <b-row class="my-2">
            <b-col cols="6" class="float-left overflowed fw-bolder price-text" ><h6>BTC/USDT</h6></b-col >
            <b-col cols="6" class="float-left overflowed fw-bolder price-percent down" >-0.30%</b-col >
          </b-row>
          <b-col cols="12" class="float-left overflowed price-text" ><h4 class="overflowed-table">40.555,26</h4></b-col >
          <b-col cols="12" class="float-left overflowed price-vol" >24H Vol 26.291,82</b-col >
          <!-- <b-col cols="12" class="float-left justify-content-between align-items-center" > <b-col cols="6" class="float-left mx-auto text-center"><b-button v-b-modal.modal-hizli-al variant="success" class="mx-auto w-75 my-1" >Al</b-button ></b-col> <b-col cols="6" class="float-left mx-auto text-center"><b-button v-b-modal.modal-hizli-sat variant="danger" class="mx-auto w-75 my-1" >Sat</b-button ></b-col> </b-col> -->
        </b-col>
        <b-col cols="12" class="justify-content-between float-left px-4 py-2 price-col" >
          <b-row class="my-2">
            <b-col cols="6" class="float-left overflowed fw-bolder price-text" ><h6>BTC/USDT</h6></b-col >
            <b-col cols="6" class="float-left overflowed fw-bolder price-percent down" >-0.30%</b-col >
          </b-row>
          <b-col cols="12" class="float-left overflowed price-text" ><h4 class="overflowed-table">40.555,26</h4></b-col >
          <b-col cols="12" class="float-left overflowed price-vol" >24H Vol 26.291,82</b-col >
          <!-- <b-col cols="12" class="float-left justify-content-between align-items-center" > <b-col cols="6" class="float-left mx-auto text-center"><b-button v-b-modal.modal-hizli-al variant="success" class="mx-auto w-75 my-1" >Al</b-button ></b-col> <b-col cols="6" class="float-left mx-auto text-center"><b-button v-b-modal.modal-hizli-sat variant="danger" class="mx-auto w-75 my-1" >Sat</b-button ></b-col> </b-col> -->
        </b-col>
        <b-col cols="12" class="justify-content-between float-left px-4 py-2 price-col" >
          <b-row class="my-2">
            <b-col cols="6" class="float-left overflowed fw-bolder price-text" ><h6>BTC/USDT</h6></b-col >
            <b-col cols="6" class="float-left overflowed fw-bolder price-percent down" >-0.30%</b-col >
          </b-row>
          <b-col cols="12" class="float-left overflowed price-text" ><h4 class="overflowed-table">40.555,26</h4></b-col >
          <b-col cols="12" class="float-left overflowed price-vol" >24H Vol 26.291,82</b-col >
          <!-- <b-col cols="12" class="float-left justify-content-between align-items-center" > <b-col cols="6" class="float-left mx-auto text-center"><b-button v-b-modal.modal-hizli-al variant="success" class="mx-auto w-75 my-1" >Al</b-button ></b-col> <b-col cols="6" class="float-left mx-auto text-center"><b-button v-b-modal.modal-hizli-sat variant="danger" class="mx-auto w-75 my-1" >Sat</b-button ></b-col> </b-col> -->
        </b-col>
        <b-col cols="12" class="justify-content-between float-left px-4 py-2 price-col" >
          <b-row class="my-2">
            <b-col cols="6" class="float-left overflowed fw-bolder price-text" ><h6>BTC/USDT</h6></b-col >
            <b-col cols="6" class="float-left overflowed fw-bolder price-percent down" >-0.30%</b-col >
          </b-row>
          <b-col cols="12" class="float-left overflowed price-text" ><h4 class="overflowed-table">40.555,26</h4></b-col >
          <b-col cols="12" class="float-left overflowed price-vol" >24H Vol 26.291,82</b-col >
          <!-- <b-col cols="12" class="float-left justify-content-between align-items-center" > <b-col cols="6" class="float-left mx-auto text-center"><b-button v-b-modal.modal-hizli-al variant="success" class="mx-auto w-75 my-1" >Al</b-button ></b-col> <b-col cols="6" class="float-left mx-auto text-center"><b-button v-b-modal.modal-hizli-sat variant="danger" class="mx-auto w-75 my-1" >Sat</b-button ></b-col> </b-col> -->
        </b-col>
      </b-row>
    </div>
    <div class="service-container">
      <b-container>
        <b-row class="service-area pt-70 mx-auto">
          <b-col cols="12">
            <b-card class="mb-2">
              <b-card-text>
                <b-row>
                  <b-col cols="12" class="text-center" ><h2 class="text-dark headings">KOLAY AL - SAT</h2></b-col >
                  <b-col cols="12" class="mb-4">
                    <div class="label d-flex justify-content-between">
                      <div class="span overflowed-table">Satılacak VARLIK</div>
                      <div class="d-flex span overflowed-table text-right">
                        Kullanılabilir: -- TRY
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="input-container">
                        <div class="input-group">
                          <input type="text" placeholder="Lütfen 20-2500000 arası giriniz." class="inputs form-control bg-transparent border-0" />
                          <div class="input-group-append">
                            <select name="deneme" id="deneme" class="bg-transparent form-control border-left-1" >
                              <option value="TRY">TRY</option>
                              <option value="USDT">USDT</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </b-col>
                  <b-col cols="12" class="mb-4 text-center">
                    <b-button variant="primary" style=" background: #f5f5f5 !important; color: black !important; border: none !important; " ><i class="fas fa-exchange-alt" style="transform: rotate(90deg)" ></i ></b-button>
                  </b-col>
                  <b-col cols="12" class="mb-4">
                    <div class="label d-flex justify-content-between">
                      <div class="span overflowed-table">Alınacak VARLIK</div>
                      <div class="d-flex span overflowed-table text-right">
                        Kullanılabilir: -- BRS
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="input-container">
                        <div class="input-group">
                          <input type="text" placeholder="Lütfen 10-2500000 arası giriniz." class="inputs form-control bg-transparent border-0" />
                          <div class="input-group-append">
                            <select name="test" id="test" class="bg-transparent form-control border-left-1" >
                              <option value="BRS">BRS</option>
                              <option value="TRX">TRX</option>
                              <option value="BTC">BTC</option>
                              <option value="ETH">ETH</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </b-col>
                  <b-col cols="12" class="mb-4">
                    <b-button variant="primary" class="btn btn-block w-100" >AL / SAT</b-button >
                  </b-col>
                </b-row>
              </b-card-text>
            </b-card>
          </b-col>
        </b-row>
      </b-container>
    </div>
    <div class="service-container">
      <b-container>
        <b-row class="service-area pt-70 mx-auto">
          <b-col cols="12" md="12" lg="3" class="mb-40 service wow zoomIn" data-wow-duration="1s" data-wow-delay=".3s" >
            <div class="service-info">
              <div class="service-icon">
                <img src="assets/img/home/icon/1.png" alt="" width="118" height="120" />
              </div>
              <div class="service-cont">
                <h3 class="overflowed-table">
                  {{ $t("Dünyanın Her Yerine") }}
                </h3>
                <h5 class="overflowed-table">{{ $t("Para Gönder-Al.") }}</h5>
                <!-- <a class="more-btn" href="#">Daha Fazla Oku</a> -->
              </div>
            </div>
          </b-col>
          <b-col cols="12" md="12" lg="3" class="mb-40 service wow zoomIn" data-wow-duration="1s" >
            <div class="service-info">
              <div class="service-icon">
                <img src="assets/img/home/icon/2.png" alt="" width="118" height="120" />
              </div>
              <div class="service-cont">
                <h3 class="overflowed-table">
                  {{ $t("Çeşitli Ödeme Yöntemlerimizle") }}
                </h3>
                <h5 class="overflowed-table">
                  {{ $t("Sanal Para Satın Al / Sat.") }}
                </h5>
                <!-- <a class="more-btn" href="#">Daha Fazla Oku</a> -->
              </div>
            </div>
          </b-col>
          <b-col cols="12" md="12" lg="3" class="mb-40 service wow zoomIn" data-wow-duration="1s" data-wow-delay=".7s" >
            <div class="service-info">
              <div class="service-icon">
                <img src="assets/img/home/icon/3.png" alt="" width="118" height="120" />
              </div>
              <div class="service-cont">
                <h3 class="overflowed-table">
                  {{ $t("Sistemimize İstediğiniz Cihazdan") }}
                </h3>
                <h5 class="overflowed-table">
                  {{ $t("Kolay ve Kesintisiz Bir Şekilde Erişin.") }}
                </h5>
              </div>
            </div>
          </b-col>
          <b-col cols="12" md="12" lg="3" class="mb-40 service wow zoomIn" data-wow-duration="1s" data-wow-delay=".5s" >
            <div class="service-info">
              <div class="service-icon">
                <img src="assets/img/home/icon/4.png" alt="" width="118" height="120" />
              </div>
              <div class="service-cont">
                <h3 class="overflowed-table">
                  {{ $t("Güvenlik Sistemimiz İle") }}
                </h3>
                <h5 class="overflowed-table">
                  {{ $t("Cüzdanınız Bizimle Daima Güvende.") }}
                </h5>
                <!-- <a class="more-btn" href="#">Daha Fazla Oku</a> -->
              </div>
            </div>
          </b-col>
        </b-row>
      </b-container>
    </div>

    <div id="section0" class="invented-container overflow-hidden">
      <b-container>
        <b-row class="invented-area pt-70 pb-40 mb-pt-45 justify-content-between" >
          <b-col cols="12" md="6" lg="6">
            <div class="wow fadeInLeft" data-wow-duration="1s" data-wow-delay="0.3s" >
              <img src="assets/img/home/icon/her_zaman_yaninda.png" alt="" class="w-100" />
            </div>
          </b-col>
          <b-col cols="12" md="6" lg="6" class="invented-cont wow fadeInUp align-middle" data-wow-duration="1s" data-wow-delay=".7s" >
            <h2>{{ $t("Her Anında Yanında") }}</h2>
            <h5>
              {{ $t( "Birden fazla cihazla uyumlu, güvenli ve kolay bir şekilde alım satım işlemlerini yapmaya başlayın." ) }}
            </h5>
          </b-col>
        </b-row>
      </b-container>
    </div>

    <section class="crp-inventment-container overflow-hidden">
      <b-container>
        <b-row class="pt-20 pb-90 mb-pb-20">
          <b-col cols="12" md="12" lg="8" class="mb-pb-0">
            <div class="crp-inventment-content pt-110 mb-pt-40 wow fadeInUp" data-wow-duration="1s" data-wow-delay=".7s" >
              <h3>{{ $t("Neden CoinSwifter'i Seçmeliyim?") }}</h3>
              <h2>
                {{ $t( "Dünyanın en güvenilir kripto para birimi çevrimiçi platformu" ) }}
              </h2>
              <p>
                {{ $t( "Dünyanın en güvenilir kripto para birimi çevrimiçi platformu" ) }} {{ $t( "CoinSwifter ile dünya çapında uygulanacak olan kriptopara aktarım işlemlerinizi kolay, hızlı ve güvenilir bir şekilde gerçekleştirebilirsiniz." ) }}
              </p>
              <div class="crp-inv-col-area">
                <div class="crp-inv-col">
                  <div class="crp-inv-col-row">
                    <div class=" crp-inv-col-icon display-2 text-white bg-transparent opacity-75 " >
                      <i class="fas fa-eye-slash"></i>
                    </div>
                    <div class="crp-inv-col-cont mx-4">
                      <h4>{{ $t("Veri Güvenliği") }}</h4>
                      <p>
                        {{ $t( "Verileriniz KVKK standartlarına uygun bir şekilde, yüksek güvenlikle saklanmaktadır." ) }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="crp-inv-col">
                  <div class="crp-inv-col-row">
                    <div class=" crp-inv-col-icon display-2 text-white bg-transparent opacity-75 " >
                      <i class="fas fa-envelope"></i>
                    </div>
                    <div class="crp-inv-col-cont mx-4">
                      <h4>{{ $t("7/24 Destek") }}</h4>
                      <p>
                        {{ $t( "Yapacağınız alım - satım işlemleri ile alakalı günün 24 saati destek talebinde bulunabilirsiniz." ) }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="crp-inv-col">
                  <div class="crp-inv-col-row">
                    <div class=" crp-inv-col-icon display-2 text-white bg-transparent opacity-75 " >
                      <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="crp-inv-col-cont mx-4">
                      <h4>{{ $t("Canlı Döviz Kuru") }}</h4>
                      <p>
                        {{ $t( "Alım satım işlemleriniz için canlı döviz kuru bazında işlemleriniz gerçekleştirilmektedir." ) }}
                      </p>
                    </div>
                  </div>
                </div>
                <div class="crp-inv-col">
                  <div class="crp-inv-col-row">
                    <div class=" crp-inv-col-icon display-2 text-white bg-transparent opacity-75 " >
                      <i class="fas fa-unlock"></i>
                    </div>
                    <div class="crp-inv-col-cont mx-4">
                      <h4>{{ $t("SSL Sertifikalı Koruma") }}</h4>
                      <p>
                        {{ $t( "Sunucularımızda saklanan dijital para birimimiz SSL sertifikamız kapsamındadır." ) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </b-col>
        </b-row>
      </b-container>
    </section>
  </div>
  <Footer></Footer>
</template>

<script>
import Footer from "../components/Footer";
import { mapGetters } from "vuex";

const initialData = () => ({
  showAl: false,
  showSat: false,
});

export default {
  components: { Footer },
  name: "Home",
  mounted() {
    let scriptTagTwo = document.createElement("script");
    scriptTagTwo.src = "assets/js/home/wow.min.js";
    document.getElementsByTagName("body")[0].appendChild(scriptTagTwo);
    let scriptTag = document.createElement("script");
    scriptTag.src = "assets/js/home/main.min.js";
    document.getElementsByTagName("body")[0].appendChild(scriptTag);
  },
  computed: {
    ...mapGetters(["user"]),
  },
  data: function () {
    return initialData();
  },
};
</script>
<style lang="scss" scoped>
</style>
