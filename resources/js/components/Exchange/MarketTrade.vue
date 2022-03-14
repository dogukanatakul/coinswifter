<template>
    <b-card no-body class="p-0">
        <TabView v-model:activeIndex="tabindex" ref="tabview1">
            <TabPanel header="Limit" v-if="selectedCoin.durum!=='ico'" active>
                <b-row>
                    <b-col cols="12" md="6">
                        <b-card>
                            <b-card-body>
                                <b-row>
                                    <b-col cols="12">
                                        <b-input-group size="sm" :prepend="$t('Fiyat')" :append="selectedCoin.source.symbol.toString()">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['limit']['buy']['price']" :placeholder="marketStatus.sell_price"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Miktar')" :append="selectedCoin.coin.symbol">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['limit']['buy']['amount']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Toplam')" :append="selectedCoin.source.symbol.toString()">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['limit']['buy']['total']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group class="order-percents my-2" size="sm">
                                            <b-form-radio-group
                                                :disabled="disabledTrade"
                                                v-model.number="form['limit']['buy']['percent']"
                                                :options="percentOptions"
                                                button-variant="outline-success"
                                                size="sm"
                                                name="radios-btn-outline"
                                                buttons
                                            ></b-form-radio-group>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12">
                                        <order-preview :key="form['limit']['buy']" v-model:form="form['limit']['buy']" v-model:selectedCoin="selectedCoin" v-model:commission="commission" v-model:wallet="wallet" type="buy"></order-preview>
                                    </b-col>
                                    <b-col cols="12" class="py-2">
                                        <div class="d-grid gap-2">
                                            <b-button :disabled="disabledTrade" @click="sendOrder('limit','buy', form['limit']['buy'])" block variant="success">{{ selectedCoin.coin.symbol }} {{ $t('AL') }}</b-button>
                                        </div>
                                    </b-col>
                                </b-row>
                            </b-card-body>
                        </b-card>
                    </b-col>
                    <b-col cols="12" md="6">
                        <b-card>
                            <b-card-body>
                                <b-row>
                                    <b-col cols="12">
                                        <b-input-group size="sm" :prepend="$t('Fiyat')" :append="selectedCoin.source.symbol.toString()">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['limit']['sell']['price']" :placeholder="marketStatus.buy_price"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Miktar')" :append="selectedCoin.coin.symbol">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['limit']['sell']['amount']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Toplam')" :append="selectedCoin.source.symbol.toString()">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['limit']['sell']['total']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group class="order-percents my-2" size="sm">
                                            <b-form-radio-group
                                                :disabled="disabledTrade"
                                                v-model.number="form['limit']['sell']['percent']"
                                                :options="percentOptions"
                                                button-variant="outline-danger"
                                                size="sm"
                                                name="radios-btn-outline"
                                                buttons
                                            ></b-form-radio-group>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12">
                                        <order-preview :key="form['limit']['sell']" v-model:form="form['limit']['sell']" v-model:selectedCoin="selectedCoin" v-model:commission="commission" v-model:wallet="wallet" type="sell"></order-preview>
                                    </b-col>
                                    <b-col cols="12" class="py-2">
                                        <div class="d-grid gap-2">
                                            <b-button :disabled="disabledTrade" @click="sendOrder('limit','sell', form['limit']['sell'])" block variant="danger">{{ selectedCoin.coin.symbol }} {{ $t('SAT') }}</b-button>
                                        </div>
                                    </b-col>
                                </b-row>
                            </b-card-body>
                        </b-card>
                    </b-col>
                </b-row>
            </TabPanel>
            <TabPanel :header="(selectedCoin.durum!=='ico'?$t('Piyasa'):$t('Satın Al'))" :disabled="marketStatus.price===0 && selectedCoin.durum!=='ico'">
                <b-row align-content="center" align-v="center" align-h="center">
                    <b-col cols="12" md="6">
                        <b-card>
                            <b-card-body>
                                <b-row>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Miktar')" :append="selectedCoin.coin.symbol">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['market']['buy']['amount']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Toplam')" :append="selectedCoin.source.symbol.toString()">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['market']['buy']['total']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group class="order-percents my-2" size="sm">
                                            <b-form-radio-group
                                                :disabled="disabledTrade"
                                                v-model.number="form['market']['buy']['percent']"
                                                :options="percentOptions"
                                                button-variant="outline-success"
                                                size="sm"
                                                name="radios-btn-outline"
                                                buttons
                                            ></b-form-radio-group>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12">
                                        <order-preview :key="form['market']['buy']" v-model:form="form['market']['buy']" v-model:selectedCoin="selectedCoin" v-model:commission="commission" v-model:wallet="wallet" type="buy"></order-preview>
                                    </b-col>
                                    <b-col cols="12" class="py-2">
                                        <div class="d-grid gap-2">
                                            <b-button :disabled="disabledTrade" @click="sendOrder('market','buy', form['market']['buy'])" block variant="success">{{ selectedCoin.coin.symbol }} {{ $t('AL') }}</b-button>
                                        </div>
                                    </b-col>
                                </b-row>
                            </b-card-body>
                        </b-card>
                    </b-col>
                    <b-col cols="12" md="6" v-if="selectedCoin.durum!=='ico'">
                        <b-card>
                            <b-card-body>
                                <b-row>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Miktar')" :append="selectedCoin.coin.symbol">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['market']['sell']['amount']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Toplam')" :append="selectedCoin.source.symbol.toString()">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['market']['sell']['total']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group class="order-percents my-2" size="sm">
                                            <b-form-radio-group
                                                :disabled="disabledTrade"
                                                v-model.number="form['market']['sell']['percent']"
                                                :options="percentOptions"
                                                button-variant="outline-danger"
                                                size="sm"
                                                name="radios-btn-outline"
                                                buttons
                                            ></b-form-radio-group>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12">
                                        <order-preview :key="form['market']['sell']" v-model:form="form['market']['sell']" v-model:selectedCoin="selectedCoin" v-model:commission="commission" v-model:wallet="wallet" type="sell"></order-preview>
                                    </b-col>
                                    <b-col cols="12" class="py-2">
                                        <div class="d-grid gap-2">
                                            <b-button :disabled="disabledTrade" @click="sendOrder('market','sell', form['market']['sell'])" block variant="danger">{{ selectedCoin.coin.symbol }} {{ $t('SAT') }}</b-button>
                                        </div>
                                    </b-col>
                                </b-row>
                            </b-card-body>
                        </b-card>
                    </b-col>
                </b-row>
            </TabPanel>
            <TabPanel :header="$t('Stop Piyasa')" v-if="selectedCoin.durum!=='ico'" :disabled="true">
                <b-row>
                    <b-col cols="12" md="6">
                        <b-card>
                            <b-card-body>
                                <b-row>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :append="selectedCoin.source.symbol.toString()">
                                            <b-input-group-prepend is-text>
                                                {{ $t('Tetikleme Fiyatı') }}
                                            </b-input-group-prepend>
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopMarket']['buy']['trigger_price']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :append="selectedCoin.source.symbol.toString()">
                                            <b-input-group-prepend is-text>
                                                Toplam
                                            </b-input-group-prepend>
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopMarket']['buy']['total']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopMarket']['buy']['percent']"
                                                type="range"
                                                min="0"
                                                max="100"
                                                step="25"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="text-center">
                                        %{{ form['stopMarket']['buy']['percent'] }}
                                    </b-col>
                                    <b-col cols="12" class="py-2">
                                        <div class="d-grid gap-2">
                                            <b-button :disabled="disabledTrade" @click="sendOrder('stopMarket','buy', form['stopMarket']['buy'])" block variant="success">{{ selectedCoin.coin.symbol }} {{ $t('AL') }}</b-button>
                                        </div>
                                    </b-col>
                                </b-row>
                            </b-card-body>
                        </b-card>
                    </b-col>
                    <b-col cols="12" md="6">
                        <b-card>
                            <b-card-body>
                                <b-row>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Tetikleme Fiyatı')" :append="selectedCoin.source.symbol.toString()">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopMarket']['sell']['trigger_price']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Toplam')" :append="selectedCoin.coin.symbol">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopMarket']['sell']['total']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopMarket']['sell']['percent']"
                                                type="range"
                                                min="0"
                                                max="100"
                                                step="25"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="text-center">
                                        %{{ form['stopMarket']['sell']['percent'] }}
                                    </b-col>
                                    <b-col cols="12" class="py-2">
                                        <div class="d-grid gap-2">
                                            <b-button :disabled="disabledTrade" @click="sendOrder('stopMarket','sell', form['stopMarket']['sell'])" block variant="danger">{{ selectedCoin.coin.symbol }} {{ $t('SAT') }}</b-button>
                                        </div>
                                    </b-col>
                                </b-row>
                            </b-card-body>
                        </b-card>
                    </b-col>
                </b-row>
            </TabPanel>
            <TabPanel header="Stop Limit" v-if="selectedCoin.durum!=='ico'" :disabled="true">
                <b-row>
                    <b-col cols="12" md="6">
                        <b-card>
                            <b-card-body>
                                <b-row>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Tetikleme Fiyatı')" :append="selectedCoin.source.symbol.toString()">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopLimit']['buy']['trigger_price']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Fiyat')" :append="selectedCoin.source.symbol.toString()">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopLimit']['buy']['price']" :placeholder="marketStatus.price"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Miktar')" :append="selectedCoin.coin.symbol">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopLimit']['buy']['amount']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Toplam')" :append="selectedCoin.source.symbol.toString()">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopLimit']['buy']['total']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopLimit']['buy']['percent']"
                                                type="range"
                                                min="0"
                                                max="100"
                                                step="25"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="text-center">
                                        %{{ form['stopLimit']['buy']['percent'] }}
                                    </b-col>
                                    <b-col cols="12" class="py-2">
                                        <div class="d-grid gap-2">
                                            <b-button :disabled="disabledTrade" @click="sendOrder('stopLimit','buy', form['stopLimit']['buy'])" block variant="success">{{ selectedCoin.coin.symbol }} {{ $t('AL') }}</b-button>
                                        </div>
                                    </b-col>
                                </b-row>
                            </b-card-body>
                        </b-card>
                    </b-col>
                    <b-col cols="12" md="6">
                        <b-card>
                            <b-card-body>
                                <b-row>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Tetikleme Fiyatı')" :append="selectedCoin.source.symbol.toString()">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopLimit']['sell']['trigger_price']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Fiyat')" :append="selectedCoin.source.symbol.toString()">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopLimit']['sell']['price']" :placeholder="marketStatus.price"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Miktar')" :append="selectedCoin.coin.symbol">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopLimit']['sell']['amount']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm" :prepend="$t('Toplam')" :append="selectedCoin.source.symbol.toString()">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopLimit']['sell']['total']"
                                                step="0.00000001"
                                                min="0.00000001"
                                                @focus="dynamicFocus('in')"
                                                @focusout="dynamicFocus('out')"
                                                :type="dynamicInput"
                                                style="appearance: textfield;"
                                                inputmode="numeric"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="mt-2">
                                        <b-input-group size="sm">
                                            <b-form-input
                                                :disabled="disabledTrade"
                                                v-model.number="form['stopLimit']['sell']['percent']"
                                                type="range"
                                                min="0"
                                                max="100"
                                                step="25"
                                            ></b-form-input>
                                        </b-input-group>
                                    </b-col>
                                    <b-col cols="12" class="text-center">
                                        %{{ form['stopLimit']['sell']['percent'] }}
                                    </b-col>
                                    <b-col cols="12" class="py-2">
                                        <div class="d-grid gap-2">
                                            <b-button :disabled="disabledTrade" @click="sendOrder('stopLimit','sell', form['stopLimit']['sell'])" block variant="danger">{{ selectedCoin.coin.symbol }} {{ $t('SAT') }}</b-button>
                                        </div>
                                    </b-col>
                                </b-row>
                            </b-card-body>
                        </b-card>
                    </b-col>
                </b-row>
            </TabPanel>
        </TabView>
    </b-card>
</template>

<script>
import restAPI from "../../api/restAPI";
import {required, minValue, maxValue} from "@vuelidate/validators";
import useVuelidate from "@vuelidate/core";
import OrderPreview from "./OrderPreview";
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';

const initialData = () => ({
    dynamicInput: 'number',
    tabindex: 0,
    percentOptions: [
        {text: '25%', value: 25},
        {text: '50%', value: 50},
        {text: '75%', value: 75},
        {text: '100%', value: 100},
    ],
    form: {
        limit: {
            buy: {
                percent: 0,
                price: null,
                amount: null,
                total: null,
            },
            sell: {
                percent: 0,
                price: null,
                amount: null,
                total: null,
            },
        },
        market: {
            buy: {
                amount: null,
                percent: 0,
            },
            sell: {
                amount: null,
                percent: 0,
            },
        },
        stopMarket: {
            buy: {
                percent: 0,
            },
            sell: {
                percent: 0,
            },
        },
        stopLimit: {
            buy: {
                percent: 0,
            },
            sell: {
                percent: 0,
            },
        },
    },
})
export default {
    name: 'MarketTrade',
    components: {
        OrderPreview,
        TabView,
        TabPanel
    },
    props: [
        'selectedCoin',
        'marketStatus',
        'wallet',
        'commission',
        'selectedTrade'
    ],
    setup() {
        return {v$: useVuelidate()}
    },
    validations() {
        return {
            form: {
                limit: {
                    buy: {
                        amount: {
                            required,
                            minValue: minValue(0),
                        },
                        total: {
                            required,
                            minValue: minValue(0),
                            maxValue: maxValue((this.wallet.source !== undefined ? this.wallet.source.balance : 0)),
                        }
                    },
                    sell: {
                        amount: {
                            required,
                            minValue: minValue(0),
                            maxValue: maxValue((this.wallet.coin !== undefined ? this.wallet.coin.balance : 0)),
                        },
                        total: {
                            required,
                            minValue: minValue(0),
                        }
                    },
                },
                market: {
                    buy: {
                        amount: {
                            required,
                            minValue: minValue(0),
                        },
                        total: {
                            required,
                            minValue: minValue(0),
                            maxValue: maxValue((this.wallet.source !== undefined ? this.wallet.source.balance : 0)),
                        }
                    },
                    sell: {
                        amount: {
                            required,
                            minValue: minValue(0),
                            maxValue: maxValue((this.wallet.coin !== undefined ? this.wallet.coin.balance : 0)),
                        },
                        total: {
                            required,
                            minValue: minValue(0),
                        }
                    },
                },
            }
        }
    },
    computed: {
        disabledTrade() {
            return this.selectedCoin.durum !== 'preview' ? false : true
        }
    },
    methods: {
        dynamicFocus(method) {
            if (method === 'in') {
                this.dynamicInput = 'input'
            } else {
                this.dynamicInput = 'number'
            }
        },
        async sendOrder(type, action, form) {
            this.v$.form[type][action].$touch();
            if (this.v$.form[type][action].$error) {
                this.$notify({text: this.$t('Lütfen alanları doğru şekilde doldurun ve bakiyenizin yeterli olduğundan emin olun.'), type: 'error'});
                return;
            }
            let userControl = await this.$store.dispatch('actionControl')
            if (!userControl) return

            if (form.price === null) {
                if (action === "sell") {
                    form.price = this.marketStatus.buy_price
                } else if (action === "buy") {
                    form.price = this.marketStatus.sell_price
                }
            }

            // Big Decimal Factor
            let postForm = {}
            for (const [key, value] of Object.entries(form)) {
                if (key == 'price' || key == 'amount' || key == 'total') {
                    postForm[key] = value.toString()
                } else {
                    postForm[key] = value
                }
            }
            //\ Big Decimal Factor
            await restAPI.getData({Action: "order"}, {
                type: type,
                action: action,
                form: postForm,
                selectedCoin: this.selectedCoin,
            }).then((response) => {
                if (response.status === "success") {
                    this.$notify({text: response.message, type: 'success'})
                    Object.assign(this.$data, initialData());
                    this.$emit('getParity')
                } else {
                    this.$notify({text: response.message, type: 'error'})
                }
            })
        }
    },
    watch: {
        selectedTrade(params) {
            let selectedTab;
            if (this.tabindex === 0) {
                selectedTab = "limit"
            } else if (this.tabindex === 1) {
                selectedTab = "market"
            }
            this.form[selectedTab][params.action]['price'] = params.price
            this.form[selectedTab][params.action]['amount'] = params.amount
        },
        'form.limit.buy.price'(value) {
            if (!(this.form.limit.buy.amount === null || this.form.limit.buy.total === null)) {
                this.form.limit.buy.total = isNaN(value * this.form.limit.buy.amount) ? 0 : value * this.form.limit.buy.amount
            }
        },
        'form.limit.buy.amount'(value) {
            if (this.form.limit.buy.price !== null) {
                this.form.limit.buy.total = isNaN(this.form.limit.buy.price * value) ? 0 : this.form.limit.buy.price * value
            } else {
                this.form.limit.buy.total = isNaN(this.marketStatus.sell_price * value) ? 0 : this.marketStatus.sell_price * value
            }
        },
        'form.limit.buy.total'(value) {
            if (this.form.limit.buy.price !== null) {
                this.form.limit.buy.amount = isNaN(value / this.form.limit.buy.price) ? 0 : value / this.form.limit.buy.price
            } else {
                if (this.marketStatus.sell_price != 0) {
                    this.form.limit.buy.amount = isNaN(value / (this.marketStatus.sell_price == 0 ? 1 : this.marketStatus.sell_price)) ? 0 : value / (this.marketStatus.sell_price == 0 ? 1 : this.marketStatus.sell_price)
                }
            }
        },
        'form.limit.buy.percent'(value) {
            this.form.limit.buy.total = isNaN((this.wallet.source !== undefined ? this.wallet.source.balance : 0) / value) ? 0 : (this.wallet.source !== undefined ? this.wallet.source.balance : 0) / value
        },
        'form.limit.sell.price'(value) {
            if (!(this.form.limit.sell.amount === null || this.form.limit.sell.total === null)) {
                this.form.limit.sell.total = isNaN(value * this.form.limit.sell.amount) ? 0 : value * this.form.limit.sell.amount
            }
        },
        'form.limit.sell.amount'(value) {
            if (this.form.limit.sell.price !== null) {
                this.form.limit.sell.total = isNaN(this.form.limit.sell.price * value) ? 0 : this.form.limit.sell.price * value
            } else {
                this.form.limit.sell.total = isNaN(this.marketStatus.buy_price * value) ? 0 : this.marketStatus.buy_price * value
            }
        },
        'form.limit.sell.total'(value) {
            if (this.form.limit.sell.price !== null) {
                this.form.limit.sell.amount = isNaN(value / this.form.limit.sell.price) ? 0 : value / this.form.limit.sell.price
            }
        },
        'form.limit.sell.percent'(value) {
            this.form.limit.sell.amount = isNaN(((this.wallet.coin !== undefined ? this.wallet.coin.balance : 0) / 100) * value) ? 0 : ((this.wallet.coin !== undefined ? this.wallet.coin.balance : 0) / 100) * value
        },
        'form.market.buy.amount'(value) {
            this.form.market.buy.total = isNaN(value * (this.marketStatus.sell_price === 0 ? 1 : this.marketStatus.sell_price)) ? 0 : value * (this.marketStatus.sell_price === 0 ? 1 : this.marketStatus.sell_price)
        },
        'form.market.buy.total'(value) {
            this.form.market.buy.amount = isNaN(value / (this.marketStatus.sell_price === 0 ? 1 : this.marketStatus.sell_price)) ? 0 : value / (this.marketStatus.sell_price === 0 ? 1 : this.marketStatus.sell_price)
        },
        'form.market.buy.percent'(value) {
            this.form.market.buy.total = isNaN(((this.wallet.source !== undefined ? this.wallet.source.balance : 0) / 100) * value) ? 0 : ((this.wallet.source !== undefined ? this.wallet.source.balance : 0) / 100) * value
        },
        'form.market.sell.amount'(value) {
            this.form.market.sell.total = isNaN(value * (this.marketStatus.buy_price === 0 ? 1 : this.marketStatus.buy_price)) ? 0 : value * (this.marketStatus.buy_price === 0 ? 1 : this.marketStatus.buy_price)
        },
        'form.market.sell.total'(value) {
            this.form.market.sell.amount = isNaN(value / (this.marketStatus.buy_price === 0 ? 1 : this.marketStatus.buy_price)) ? 0 : value / (this.marketStatus.buy_price === 0 ? 1 : this.marketStatus.buy_price)
        },
        'form.market.sell.percent'(value) {
            this.form.market.sell.amount = isNaN(((this.wallet.coin !== undefined ? this.wallet.coin.balance : 0) / 100) * value) ? 0 : ((this.wallet.coin !== undefined ? this.wallet.coin.balance : 0) / 100) * value
        },
    },
    data: () => (initialData()),
};
</script>
<style lang="scss" scoped>
::v-deep {
    .order-watch {
        .list-group-item {
            font-size: 12px;
            padding: 0.1rem 1rem;
        }
    }

    .input-group {
        .input-group-prepend {
            .input-group-text {
                border-top-right-radius: 0px !important;
                border-bottom-right-radius: 0px !important;
                min-width: 120px;
                width: 120px;
                text-align: end;
            }
        }
    }

    .green-range::-webkit-slider-thumb {
        background: #198754;
    }

    .green-range::-moz-range-thumb {
        background: #198754;
    }

    .green-range::-ms-thumb {
        background: #198754;
    }

    .red-range::-webkit-slider-thumb {
        background: #dc3545;
    }

    .red-range::-moz-range-thumb {
        background: #dc3545;
    }

    .red-range::-ms-thumb {
        background: #dc3545;
    }

    .order-percents {
        .btn-group {
            width: 100%;
            max-width: 100%;

            .form-check-inline {
                width: 25%;
                padding: 0px 3px;
                margin: 0px 0px;
            }

            .btn {
                width: 100%;
            }
        }
    }
    @media screen and (max-width:1080px) {
        .input-group-text {
            font-size:9px !important;
        }
    }
}
</style>
