<template>
    <div class="main-chart mb15">
        <VueTradingView :options="options"/>
    </div>
</template>

<script>
import VueTradingView from "vue-trading-view/src/vue-trading-view.vue";

export default {
    components: {
        VueTradingView,
    },
    props: [
        'selectedCoin'
    ],
    data() {
        return {
            options: {
                symbol: 'BINANCE:' + this.selectedCoin.coin.symbol + 'USDT',
                theme: 'light',
                width: '100%',
                height: '100%',
                allow_symbol_change: true,
                locale: 'tr',
                hide_top_toolbar: true,
                save_image: false,
            },
        };
    },
};
</script>
