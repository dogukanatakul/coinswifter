<template>
    <b-card
        :header="$t('Emir Defteri')"
        header-tag="header">
        <b-card-text>
            <table class="table table-sm order-table">
                <thead>
                <tr>
                    <th>{{ $t('Fiyat') }} ({{ selectedCoin.source.symbol }})</th>
                    <th>{{ $t('Miktar') }} ({{ selectedCoin.coin.symbol }})</th>
                    <th>{{ $t('Toplam') }} ({{ selectedCoin.source.symbol }})</th>
                </tr>
                </thead>
                <tbody>
                <tr
                    v-for="(item,key) in orders['sell']"
                    :key="key"
                    @click="sendTradeForm('buy', toFixedCustom(item.price), toFixedCustom(item.amount))"
                    class="cursor-pointer sell"
                >
                    <td class="text-danger">{{ toFixedCustom(item.price) }}</td>
                    <td class="text-danger">{{ toFixedCustom(item.amount) }}</td>
                    <td class="text-danger">{{ toFixedCustom((item.price * item.amount)) }}</td>
                </tr>

                </tbody>
                <tbody class="ob-heading">
                <tr>
                    <th>
                        <span>{{ $t('Fiyat') }}</span>
                        {{ toFixedCustom(marketStatus.price) }}
                    </th>
                    <th>
                        <span>{{ $t('Sembol') }}</span>
                        {{ selectedCoin.coin.symbol + "/" + selectedCoin.source.symbol }}
                    </th>
                    <th :class="(marketStatus.percent>0?'green':'red')">
                        <span>{{ $t('Fark') }}</span>
                        {{ selectedCoin.parity_price.price.value }}%
                    </th>
                </tr>
                </tbody>
                <tbody>
                <tr
                    v-for="(item,key) in orders['buy']"
                    :key="key"
                    @click="sendTradeForm('sell', toFixedCustom(item.price), toFixedCustom(item.amount))"
                    class="cursor-pointer buy"
                >
                    <td class="text-success">{{ toFixedCustom(item.price) }}</td>
                    <td class="text-success">{{ toFixedCustom(item.amount) }}</td>
                    <td class="text-success">{{ toFixedCustom((item.price * item.amount)) }}</td>
                </tr>
                </tbody>
            </table>
        </b-card-text>
    </b-card>
</template>

<script>
import {toFixedCustom} from "../../helpers/helpers";

export default {
    name: 'OrderBook',
    props: [
        'orders',
        'selectedCoin',
        'marketStatus'
    ],
    methods: {
        sendTradeForm(action, price, amount) {
            this.$emit('sendTradeForm', {action, price, amount})
        }
    },
    watch: {

    },
    data: () => ({
        toFixedCustom: toFixedCustom
    }),

}
</script>
