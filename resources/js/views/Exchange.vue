<template>
    <b-container class="exchange pb-5 mb-3" fluid>
        <b-row class="d-none d-lg-flex">
            <b-col cols="12" xs="3" sm="12" lg="3" class="d-sm-block">
                <MarketPairs
                    v-if="Object.values(selectedCoin).length > 0"
                    :key="selectedCoin"
                    v-model:selectedCoin="selectedCoin"
                    v-model:parities="parities"
                    v-model:coins="coins"
                    @getTokens="getTokens"
                />
            </b-col>
            <b-col cols="12" lg="6">
                <b-row>
                    <b-col
                        class="coin-prices"
                        cols="12"
                        v-if="Object.values(selectedCoin).length > 0"
                    >
                        <b-col cols="12" lg="2" class="float-left">
                            <div
                                class="
                  fw-bold
                  text-md-center text-sm-center text-center text-small
                  my-2
                "
                            >
                                {{
                                    [selectedCoin.coin.symbol, selectedCoin.source.symbol].join(
                                        "/"
                                    )
                                }}
                            </div>
                        </b-col>
                        <b-col cols="12" lg="10" class="float-left">
                            <div class="coin-detail">
                                <div class="coin-info">
                                    <div
                                        :class="'price ' + selectedCoin.parity_price.price.status"
                                    >
                                        {{ selectedCoin.parity_price.price.value }}
                                    </div>
                                    <div class="info">
                                        {{ selectedCoin.parity_price.price.value }}
                                    </div>
                                </div>
                                <div class="coin-info">
                                    <div class="title">{{ $t("Değişim (24S)") }}</div>
                                    <div
                                        :class="
                      'info ' +
                      selectedCoin.parity_price.percent_last_24_hours.status
                    "
                                    >
                                        {{ selectedCoin.parity_price.percent_last_24_hours.value }}
                                    </div>
                                </div>
                                <div class="coin-info">
                                    <div class="title">{{ $t("En Yüksek (24S)") }}</div>
                                    <div
                                        :class="'info ' + selectedCoin.parity_price.highest.status"
                                    >
                                        {{ selectedCoin.parity_price.highest.value }}
                                    </div>
                                </div>
                                <div class="coin-info">
                                    <div class="title">{{ $t("En Düşük (24S)") }}</div>
                                    <div
                                        :class="'info ' + selectedCoin.parity_price.lowest.status"
                                    >
                                        {{ selectedCoin.parity_price.lowest.value }}
                                    </div>
                                </div>
                                <div class="coin-info">
                                    <div class="title">{{ $t("Hacim (24S)") }}</div>
                                    <div
                                        :class="
                      'info ' +
                      selectedCoin.parity_price.volume_last_24_hours_price
                        .status
                    "
                                    >
                                        {{
                                            selectedCoin.parity_price.volume_last_24_hours_price.value
                                        }}
                                    </div>
                                </div>
                            </div>
                        </b-col>
                    </b-col>
                    <b-col cols="12">
                        <div
                            v-if="
                Object.values(selectedCoin).length > 0 &&
                Object.values(selectedCoin.promotion).length > 0 &&
                selectedCoin.status == 'ico'
              "
                        >
                            <a :href="selectedCoin.promotion.url" target="_blank">
                                <img :src="selectedCoin.promotion.banner" style="width: 100%"/>
                            </a>
                        </div>
                        <b-overlay
                            v-if="
                Object.values(selectedCoin).length > 0 &&
                selectedCoin.status !== 'ico'
              "
                            :show="marketTradeLoader"
                            rounded="sm"
                        >
                            <div class="justify-content-between no-wrap">
                                <div
                                    class="float-left text-center"
                                    style="
                    width: calc(4.6vw + 1 * ((1.4vw * 280) / 730)) !important;
                  "
                                    v-for="(array, index) in timeArray"
                                    :key="array.key"
                                >
                                    <b-link
                                        href="#"
                                        @click="chartTime = array.key"
                                        class="chartLink text-small"
                                    >{{ array.value }}
                                    </b-link
                                    >
                                </div>
                                <!-- <b-col cols="12" md="2" class="float-left"
                                  ><b-link
                                    href="#"
                                    @click="chartTime = '1h'"
                                    class="chartLink mx-2"
                                    >{{ $t("1 hour") }}</b-link
                                  ></b-col
                                >
                                <b-col cols="12" md="2" class="float-left"
                                  ><b-link
                                    href="#"
                                    @click="chartTime = '4h'"
                                    class="chartLink mx-2"
                                    >{{ $t("4 hour") }}</b-link
                                  ></b-col
                                >
                                <b-col cols="12" md="2" class="float-left"
                                  ><b-link
                                    href="#"
                                    @click="chartTime = '1d'"
                                    class="chartLink mx-2"
                                    >{{ $t("1 day") }}</b-link
                                  ></b-col
                                >
                                <b-col cols="12" md="2" class="float-left"
                                  ><b-link
                                    href="#"
                                    @click="chartTime = '1w'"
                                    class="chartLink mx-2"
                                    >{{ $t("1 week") }}</b-link
                                  ></b-col
                                >
                                <b-col cols="12" md="2" class="float-left"
                                  ><b-link
                                    href="#"
                                    @click="chartTime = '1m'"
                                    class="chartLink mx-2"
                                    >{{ $t("1 month") }}</b-link
                                  ></b-col
                                > -->
                            </div>
                            <div style="clear: both"></div>
                            <div>
                                <new-chart
                                    v-if="chart"
                                    :chart.sync="chart"
                                    v-model:selectedCoin="selectedCoin"
                                    :key="selectedCoin"
                                ></new-chart>
                                <trading-chart
                                    class="trading-chart"
                                    v-else-if="selectedCoin.settings.trading_market"
                                    v-model:selectedCoin="selectedCoin"
                                    :key="selectedCoin"
                                ></trading-chart>

                                <b-card
                                    class="pariy-banner"
                                    v-else-if="!selectedCoin.settings.trading_market"
                                    no-body
                                >
                                    <img
                                        v-bind:src="'../assets/img/icon_banner/comingsoon.png'"
                                    />
                                </b-card>
                            </div>
                            <template #overlay>
                                <div class="text-center">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="16"
                                        height="16"
                                        fill="currentColor"
                                        class="bi bi-graph-up-arrow"
                                        viewBox="0 0 16 16"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M0 0h1v15h15v1H0V0Zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5Z"
                                        />
                                    </svg>
                                    <p>{{ $t("Grafik verileri getiriliyor..") }}</p>
                                </div>
                            </template>
                        </b-overlay>
                        <b-overlay :show="marketTradeLoader" rounded="sm">
                            <MarketTrade
                                v-if="Object.values(selectedCoin).length > 0"
                                v-model:selectedCoin="selectedCoin"
                                v-model:wallet="wallet"
                                v-model:marketStatus="marketStatus"
                                v-model:selectedTrade="selectedTrade"
                                v-model:commission="selectedCoin.commission"
                                :key="selectedCoin"
                                @getParity="getParity"
                            />
                            <template #overlay>
                                <div class="text-center">
                                    <b-icon
                                        icon="bar-chart"
                                        font-scale="3"
                                        size="lg"
                                        animation="fade"
                                    ></b-icon>
                                    <p>{{ $t("Borsa verileri getiriliyor..") }}</p>
                                </div>
                            </template>
                        </b-overlay>
                        <div v-if="myOrders" class="b-overlay-wrap position-relative">
                            <my-orders
                                :key="myOrders"
                                :myOrders.sync="myOrders"
                                v-model:selectedCoin="selectedCoin"
                                @getParity="getParity"
                            ></my-orders>
                        </div>
                    </b-col>
                </b-row>
            </b-col>
            <b-col cols="12" lg="3">
                <wallet
                    v-if="
            Object.values(selectedCoin).length > 0 &&
            Object.values(wallet).length > 0
          "
                    :wallet.sync="wallet"
                    :key="wallet"
                    class="d-none d-lg-block"
                    @sendTradeFormWallet="sendTradeFormWallet"
                ></wallet>
                <order-book
                    v-if="Object.values(selectedCoin).length > 0"
                    v-model:orders="orders"
                    v-model:selectedCoin="selectedCoin"
                    v-model:marketStatus="marketStatus"
                    @sendTradeForm="sendTradeForm"
                    class="mt-2"
                />
                <last-operations
                    v-if="Object.values(selectedCoin).length > 0"
                    v-model:lastOperations="lastOperations"
                    v-model:selectedCoin="selectedCoin"
                ></last-operations>
            </b-col>
        </b-row>

        <b-row class="d-none d-md-flex d-sm-flex d-xs-flex">
            <b-col cols="12" class="d-none d-600-big-block">
                <b-col
                    class="coin-prices"
                    cols="12"
                    v-if="Object.values(selectedCoin).length > 0"
                >
                    <b-col cols="12" lg="2" class="float-left">
                        <div
                            class="
                fw-bold
                text-md-center text-sm-center text-center text-small
                my-2
              "
                            @click="paritiesShowView = !paritiesShowView"
                        >
                            {{
                                [selectedCoin.coin.symbol, selectedCoin.source.symbol].join("/")
                            }}
                            <i class="fas fa-caret-down"></i>
                        </div>
                    </b-col>
                    <b-col cols="12" lg="10" class="float-left">
                        <div class="coin-detail">
                            <div class="coin-info">
                                <div :class="'price ' + selectedCoin.parity_price.price.status">
                                    {{ selectedCoin.parity_price.price.value }}
                                </div>
                                <div class="info">
                                    {{ selectedCoin.parity_price.price.value }}
                                </div>
                            </div>
                            <div class="coin-info">
                                <div class="title">{{ $t("Değişim (24S)") }}</div>
                                <div
                                    :class="
                    'info ' +
                    selectedCoin.parity_price.percent_last_24_hours.status
                  "
                                >
                                    {{ selectedCoin.parity_price.percent_last_24_hours.value }}
                                </div>
                            </div>
                            <div class="coin-info">
                                <div class="title">{{ $t("En Yüksek (24S)") }}</div>
                                <div
                                    :class="'info ' + selectedCoin.parity_price.highest.status"
                                >
                                    {{ selectedCoin.parity_price.highest.value }}
                                </div>
                            </div>
                            <div class="coin-info">
                                <div class="title">{{ $t("En Düşük (24S)") }}</div>
                                <div :class="'info ' + selectedCoin.parity_price.lowest.status">
                                    {{ selectedCoin.parity_price.lowest.value }}
                                </div>
                            </div>
                            <div class="coin-info">
                                <div class="title">{{ $t("Hacim (24S)") }}</div>
                                <div
                                    :class="
                    'info ' +
                    selectedCoin.parity_price.volume_last_24_hours_price.status
                  "
                                >
                                    {{
                                        selectedCoin.parity_price.volume_last_24_hours_price.value
                                    }}
                                </div>
                            </div>
                        </div>
                    </b-col>
                </b-col>
            </b-col>

            <b-col cols="12" class="d-none d-600-small-block">
                <b-col
                    class="coin-prices"
                    cols="12"
                    v-if="Object.values(selectedCoin).length > 0"
                >
                    <b-row>
                        <div class="d-grid gap-2">
                            <b-row>
                                <div class="col-5 my-auto" style="grid-column:1/2; grid-row:1/2;">
                                    <div
                                        class="fw-bold mt-2 mx-2"
                                        @click="paritiesShowView = !paritiesShowView"
                                    >
                                        {{
                                            [
                                                selectedCoin.coin.symbol,
                                                selectedCoin.source.symbol,
                                            ].join("/")
                                        }}
                                        <i class="fas fa-caret-down"></i>
                                    </div>
                                    <div class="coin-detail mx-2">
                                        <div class="coin-info border-0">
                                            <div class="row">
                                                <div class="col">
                                                    <div
                                                        class="text-start"
                                                        :class="
                              'price ' + selectedCoin.parity_price.price.status
                            "
                                                    >
                                                        <h3>{{ selectedCoin.parity_price.price.value }}</h3>
                                                    </div>
                                                </div>
                                                <div class="w-100"></div>
                                                <div class="col">
                                                    <div class="info text-start">
                                                        {{ selectedCoin.parity_price.price.value }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-7 no-wrap text-start my-auto" style="grid-column:2/3; grid-row:2-3;">
                                    <div class="coin-detail">
                                        <div class="coin-info border-0 p-0">
                                            <div class="row">
                                                <div class="col-5">
                                                    <div class="title text-start">{{ $t("Değişim (24S)") }}</div>
                                                </div>
                                                <div class="col-7">
                                                    <div
                                                        class="text-start"
                                                        :class="
                              'info ' +
                              selectedCoin.parity_price.percent_last_24_hours
                                .status
                            "
                                                    >
                                                        {{
                                                            selectedCoin.parity_price.percent_last_24_hours
                                                                .value
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="coin-info border-0 p-0">
                                            <div class="row">
                                                <div class="col-5">
                                                    <div class="title text-start">{{ $t("En Yüksek (24S)") }}</div>
                                                </div>
                                                <div class="col-7">
                                                    <div
                                                        class="text-start"
                                                        :class="
                              'info ' + selectedCoin.parity_price.highest.status
                            "
                                                    >
                                                        {{ selectedCoin.parity_price.highest.value }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="coin-info border-0 p-0">
                                            <div class="row">
                                                <div class="col-5">
                                                    <div class="title text-start">{{ $t("En Düşük (24S)") }}</div>
                                                </div>
                                                <div class="col-7">
                                                    <div
                                                        class="text-start"
                                                        :class="
                              'info ' + selectedCoin.parity_price.lowest.status
                            "
                                                    >
                                                        {{ selectedCoin.parity_price.lowest.value }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="coin-info border-0 p-0">
                                            <div class="row">
                                                <div class="col-5">
                                                    <div class="title text-start">{{ $t("Hacim (24S)") }}</div>
                                                </div>
                                                <div class="col-7">
                                                    <div
                                                        class="text-start"
                                                        :class="
                              'info ' +
                              selectedCoin.parity_price
                                .volume_last_24_hours_price.status
                            "
                                                    >
                                                        {{
                                                            selectedCoin.parity_price
                                                                .volume_last_24_hours_price.value
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </b-row>
                        </div>
                        <!-- <div class="w-100"></div>
                        <div class="col-6">

                        </div> -->
                    </b-row>
                </b-col>
            </b-col>

            <b-col cols="12">
                <wallet
                    v-if="
            Object.values(selectedCoin).length > 0 &&
            Object.values(wallet).length > 0
          "
                    :wallet.sync="wallet"
                    :key="wallet"
                    @sendTradeFormWallet="sendTradeFormWallet"
                ></wallet>
            </b-col>
            <b-col cols="12">
                <TabView
                    content-class="mt-3"
                    :activeIndex="1"
                    v-model:changeParitiesView="changeParitiesView"
                >
                    <TabPanel header="Grafik">
                        <b-row>
                            <div class="justify-content-between no-wrap">
                                <div
                                    class="float-left d-none d-md-block d-sm-block text-center"
                                    style="
                    width: calc(10.3vw - 1 * ((1.4vw * 280) / 730)) !important;
                  "
                                    v-for="(array, index) in timeArray"
                                    :key="array.key"
                                >
                                    <b-link
                                        href="#"
                                        @click="chartTime = array.key"
                                        class="chartLink mx-2 text-small"
                                    >{{ array.value }}
                                    </b-link
                                    >
                                </div>

                                <!-- <b-col
                                  cols="4"
                                  md="2"
                                  class="float-left d-none d-small-block"
                                  v-for="(array, index) in timeArray"
                                  :key="array.key"
                                  ><b-link
                                    href="#"
                                    @click="chartTime = array.key"
                                    class="chartLink mx-2"
                                    >{{ array.value }}</b-link
                                  ></b-col
                                > -->

                                <v-select
                                    v-model="chartTime"
                                    :options="timeArray"
                                    label="value"
                                    :reduce="(array) => array.key"
                                    class="float-left d-none d-small-block col-12 my-2"
                                    :clearable="false"
                                ></v-select>
                                <!-- <b-col cols="4" md="2" class="float-left"
                                  ><b-link
                                    href="#"
                                    @click="chartTime = '1h'"
                                    class="chartLink mx-2"
                                    >{{ $t("1 hour") }}</b-link
                                  ></b-col
                                >
                                <b-col cols="4" md="2" class="float-left"
                                  ><b-link
                                    href="#"
                                    @click="chartTime = '4h'"
                                    class="chartLink mx-2"
                                    >{{ $t("4 hour") }}</b-link
                                  ></b-col
                                >
                                <b-col cols="4" md="2" class="float-left"
                                  ><b-link
                                    href="#"
                                    @click="chartTime = '1d'"
                                    class="chartLink mx-2"
                                    >{{ $t("1 day") }}</b-link
                                  ></b-col
                                >
                                <b-col cols="4" md="2" class="float-left"
                                  ><b-link
                                    href="#"
                                    @click="chartTime = '1w'"
                                    class="chartLink mx-2"
                                    >{{ $t("1 week") }}</b-link
                                  ></b-col
                                >
                                <b-col cols="4" md="2" class="float-left"
                                  ><b-link
                                    href="#"
                                    @click="chartTime = '1m'"
                                    class="chartLink mx-2"
                                    >{{ $t("1 month") }}</b-link
                                  ></b-col
                                > -->
                            </div>
                            <b-col cols="12">
                                <div
                                    v-if="
                    Object.values(selectedCoin).length > 0 &&
                    Object.values(selectedCoin.promotion).length > 0 &&
                    selectedCoin.status == 'ico'
                  "
                                >
                                    <a :href="selectedCoin.promotion.url" target="_blank">
                                        <img
                                            :src="selectedCoin.promotion.banner"
                                            style="width: 100%"
                                        />
                                    </a>
                                </div>
                                <b-overlay
                                    v-if="
                    Object.values(selectedCoin).length > 0 &&
                    selectedCoin.status !== 'ico'
                  "
                                    :show="marketTradeLoader"
                                    rounded="sm"
                                >
                                    <div>
                                        <new-chart
                                            v-if="chart"
                                            :chart.sync="chart"
                                            v-model:selectedCoin="selectedCoin"
                                            :key="selectedCoin"
                                        ></new-chart>
                                        <trading-chart
                                            class="trading-chart"
                                            v-else-if="selectedCoin.settings.trading_market"
                                            v-model:selectedCoin="selectedCoin"
                                            :key="selectedCoin"
                                        ></trading-chart>

                                        <b-card
                                            class="pariy-banner"
                                            v-else-if="!selectedCoin.settings.trading_market"
                                            no-body
                                        >
                                            <img
                                                v-bind:src="'../assets/img/icon_banner/comingsoon.png'"
                                            />
                                        </b-card>
                                    </div>
                                    <template #overlay>
                                        <div class="text-center">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="16"
                                                height="16"
                                                fill="currentColor"
                                                class="bi bi-graph-up-arrow"
                                                viewBox="0 0 16 16"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M0 0h1v15h15v1H0V0Zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5Z"
                                                />
                                            </svg>
                                            <p>{{ $t("Grafik verileri getiriliyor..") }}</p>
                                        </div>
                                    </template>
                                </b-overlay>
                            </b-col>
                        </b-row>
                    </TabPanel>
                    <!-- <b-tab title="Pariteler">

                    </b-tab> -->
                    <TabPanel
                        header="Alım - Satım"
                        :class="{ active: changeParitiesView === true }"
                    >
                        <b-overlay :show="marketTradeLoader" rounded="sm">
                            <MarketTrade
                                v-if="Object.values(selectedCoin).length > 0"
                                v-model:selectedCoin="selectedCoin"
                                v-model:wallet="wallet"
                                v-model:marketStatus="marketStatus"
                                v-model:selectedTrade="selectedTrade"
                                v-model:commission="selectedCoin.commission"
                                :key="selectedCoin"
                                @getParity="getParity"
                            />
                            <template #overlay>
                                <div class="text-center">
                                    <b-icon
                                        icon="bar-chart"
                                        font-scale="3"
                                        size="lg"
                                        animation="fade"
                                    ></b-icon>
                                    <p>{{ $t("Borsa verileri getiriliyor..") }}</p>
                                </div>
                            </template>
                        </b-overlay>
                    </TabPanel>
                    <TabPanel header="Emir Defteri">
                        <order-book
                            v-if="Object.values(selectedCoin).length > 0"
                            v-model:orders="orders"
                            v-model:selectedCoin="selectedCoin"
                            v-model:marketStatus="marketStatus"
                            @sendTradeForm="sendTradeForm"
                            class="mt-2"
                        />
                    </TabPanel>
                    <TabPanel header="Son İşlemler">
                        <last-operations
                            v-if="Object.values(selectedCoin).length > 0"
                            v-model:lastOperations="lastOperations"
                            v-model:selectedCoin="selectedCoin"
                        ></last-operations>
                        <!-- <b-col
                          cols="12"
                          class="d-none d-md-block d-sm-block d-xs-block my-2"
                        >
                          <b-button
                            @click="modalShow = !modalShow"
                            variant="primary"
                            class="w-100"
                            >{{ $t("Kullanılabilir Bakiyeler") }}</b-button
                          >
                          <b-modal
                            v-model="modalShow"
                            id="modal-lg"
                            size="lg"
                            title="Kullanılabilir Bakiyeler"
                            centered
                            ok-only
                            hide-backdrop
                            hide-footer
                            >
                          </b-modal>
                        </b-col> -->
                    </TabPanel>
                    <TabPanel header="Emirlerim" v-if="myOrders">
                        <div v-if="myOrders" class="b-overlay-wrap position-relative">
                            <my-orders
                                :key="myOrders"
                                :myOrders.sync="myOrders"
                                v-model:selectedCoin="selectedCoin"
                                @getParity="getParity"
                            ></my-orders>
                        </div>
                    </TabPanel>
                </TabView>
                <b-modal
                    v-model="paritiesShowView"
                    ref="modal"
                    id="modal-lg"
                    size="lg"
                    title="Parite Çiftleri"
                    centered
                    ok-only
                    hide-footer
                    position-y="bottom"
                >
                    <b-col cols="12" xs="3" sm="12" lg="3" class="d-sm-block">
                        <MarketPairsNew
                            v-if="Object.values(selectedCoin).length > 0"
                            :key="selectedCoin"
                            v-model:selectedCoin="selectedCoin"
                            v-model:parities="parities"
                            v-model:coins="coins"
                            @changeParities="changeParities"
                            @paritiesShow="paritiesShow"
                            @getTokens="getTokens"
                        />
                    </b-col>
                    <div slot="modal-footer">
                        <b-button
                            class="mr-sm-2 su-btn-link float-right"
                            variant="primary"
                            @click="paritiesShowView = false"
                        >{{ $t("Kapat") }}
                        </b-button
                        >
                    </div>
                </b-modal>
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
import MarketPairs from "../components/Exchange/MarketPairs.vue";
import MarketPairsNew from "../components/Exchange/MarketPairs_New.vue";
import MarketTrade from "../components/Exchange/MarketTrade.vue";
import OrderBook from "../components/Exchange/OrderBook.vue";
import restAPI from "../api/restAPI";
import Chart from "../components/Exchange/Chart";
import Wallet from "../components/Exchange/Wallet";
import {mapGetters} from "vuex";
import MyOrders from "../components/Exchange/MyOrders";
import LastOperations from "../components/Exchange/LastOperations";
import TradingChart from "../components/Exchange/TradingChart";
import NewChart from "../components/Exchange/newChart";
import {groupBy} from "../helpers/helpers";
import TabView from "primevue/tabview";
import TabPanel from "primevue/tabpanel";

const initialData = () => ({
    modalShow: false,
    paritiesShowView: false,
    coins: [],
    chart: false,
    wallet: {
        source: {
            balance: 0,
            symbol: "XXX",
            locked: 0,
        },
        coin: {
            balance: 0,
            symbol: "XXX",
            locked: 0,
        },
    },
    lastOperations: [],
    orders: {},
    myOrders: false,
    selectedCoin: {},
    marketTradeLoader: true,
    marketStatus: {
        price: 0,
    },
    loader: null,
    loadingParity: false,
    selectedTrade: {},
    setInterval: null,
    changeParitiesView: true,
    chartTime: "15min",
    timeArray: [],
});

export default {
    name: "exchange",
    components: {
        NewChart,
        TradingChart,
        LastOperations,
        MyOrders,
        Wallet,
        Chart,
        MarketPairs,
        MarketPairsNew,
        MarketTrade,
        OrderBook,
        TabView,
        TabPanel,
    },
    async created() {
        // this.loader = await this.$loading.show({
        //     container: null,
        //     canCancel: false,
        //     onCancel: this.onCancel,
        // });
        await this.getTokens();
    },
    async mounted() {
        this.setInterval = setInterval(
            function () {
                if (this.loadingParity) {
                    this.getParity();
                }
            }.bind(this),
            5000
        );
    },
    watch: {
        async selectedCoin(value) {
            if (value !== undefined && Object.values(value) > 0) {
                Object.assign(this.$data, initialData());
                this.marketTradeLoader = true;
                // this.loader = await this.$loading.show({
                //     container: null,
                //     canCancel: false,
                //     onCancel: this.onCancel,
                // });
            }
            await this.getParity(true);
        },
        "$route.params.parity": function (value) {
            this.selectedCoin = this.coins[value];
        },
    },
    data: function () {
        return initialData()
    },
    computed: {
        ...mapGetters(["user"]),
        parity() {
            let parity = undefined;
            if (this.$route.params.parity === undefined) {
                if (window.localStorage.getItem("parity") !== undefined) {
                    parity = window.localStorage.getItem("parity");
                } else {
                    parity = this.$route.params.parity;
                }
            } else {
                parity = this.$route.params.parity;
                window.localStorage.setItem("parity", parity);
            }
            return parity;
        },
        parities() {
            return groupBy(Object.values(this.coins), "source", "symbol");
        },
        timeArray() {
            return [
                {
                    key: "1min",
                    value: this.$t("1 min"),
                },
                {
                    key: "5min",
                    value: this.$t("5 min"),
                },
                {
                    key: "15min",
                    value: this.$t("15 min"),
                },
                {
                    key: "30min",
                    value: this.$t("30 min"),
                },
                {
                    key: "1hours",
                    value: this.$t("1 hours"),
                },
                {
                    key: "4hours",
                    value: this.$t("4 hours"),
                },
                {
                    key: "1day",
                    value: this.$t("1 day"),
                },
                {
                    key: "1week",
                    value: this.$t("1 week"),
                },
                {
                    key: "1month",
                    value: this.$t("1 month"),
                },
            ]
        }
    },
    methods: {
        async getParity(loader = false) {
            // console.log(selectedCoinValue);
            // console.log(parts);
            //Source = TRY
            //Coin = CATE
            this.loadingParity = false;
            let selectedCoin = this.selectedCoin;
            await restAPI
                .getData(
                    {
                        Action:
                            "exchange/parity/" +
                            selectedCoin.source.symbol +
                            "-" +
                            selectedCoin.coin.symbol,
                    },
                    {chartParts: this.chartTime}
                )
                .then((response) => {
                    if (response.status === "success") {
                        this.marketTradeLoader = false;
                        this.marketStatus = response.marketStatus;
                        this.chart = response.chart;
                        this.wallet = response.wallet;
                        this.myOrders = response.myOrders;
                        this.lastOperations = response.lastOperations;
                        this.orders = response.data;
                        this.loadingParity = true;
                        if (loader) {
                            // this.loader.hide();
                        }
                    }
                });
        },

        async getTokens() {
            await restAPI
                .getData({
                    Action: "exchange/tokens",
                })
                .then((response) => {
                    if (response.status === "success") {
                        // this.loader.hide();
                        this.coins = response.data;
                        if (response.data[this.parity] !== undefined) {
                            this.selectedCoin = response.data[this.parity];
                        } else {
                            this.$router.push({name: "markets"});
                        }
                    }
                });
        },
        sendTradeForm(params) {
            this.selectedTrade = params;
        },
        sendTradeFormWallet(params) {
            this.selectedTrade = params;
        },
        changeParities(params) {
            this.changeParitiesView = params;
            console.log(this.changeParitiesView);
        },
        paritiesShow(params) {
            this.paritiesShowView = params;
        },
    },
    beforeUnmount: function () {
        clearInterval(this.setInterval);
    },
};
</script>
<style scoped lang="scss">
.pariy-banner {
    align-items: center;

    img {
        width: 100%;
        height: auto;
        max-width: 100%;
    }
}

.float-left {
    float: left !important;
}

.text-small {
    font-size: 12px;
}

@media screen and (max-width: 1690px) and(min-width: 992px) {
    // .title,
    // .price,
    // .info {
    //   font-size: 8px !important;
    //   // text-align: center;
    // }
    .d-xs-flex {
        display: none !important;
    }
}

@media screen and (max-width: 575px) {
    .d-xs-block {
        display: block !important;
    }
    .d-xs-flex {
        display: flex !important;
    }

    .d-small-block {
        display: block !important;
    }
}

@media screen and (min-width: 992px) {
    .d-xs-block {
        display: none !important;
    }
    .d-xs-flex {
        display: none !important;
    }
}

.float-right {
    float: right !important;
}

.chartLink {
    text-decoration: none;
    color: darkgray;
    font-size: 14px !important;
}

.chartLink:hover {
    text-decoration: none;
    color: darkgray;
    font-size: 14px !important;
}

.chartLink:active,
.chartLink:focus {
    text-decoration: none;
    color: #16b979;
    font-size: 14px !important;
}

.no-wrap {
    white-space: nowrap;
}

@media screen and (max-width: 600px) {
    .d-600-big-block {
        display: none !important;
    }
    .d-600-small-block {
        display: block !important;
    }
}

@media screen and(min-width: 600px) {
    .d-600-big-block {
        display: block !important;
    }
    .d-600-small-block {
        display: none !important;
    }
}
</style>
