<template>
    <b-list-group class="order-watch">
        <b-list-group-item class="d-flex justify-content-between align-items-center">
            {{ $t('Mevcut') }}
            <b-badge pill>{{ getCurrent.price }} {{ getCurrent.symbol }}</b-badge>
        </b-list-group-item>
        <b-list-group-item class="d-flex justify-content-between align-items-center">
            {{ $t('Miktar') }}
            <b-badge pill>{{ getAmount.price }} {{ getAmount.symbol }}</b-badge>
        </b-list-group-item>
        <b-list-group-item class="d-flex justify-content-between align-items-center">
            {{ $t('Komisyon') }} ({{ commission }}%)
            <b-badge pill>{{ getCommission.price }} {{ getCommission.symbol }}</b-badge>
        </b-list-group-item>
        <b-list-group-item class="d-flex justify-content-between align-items-center">
            {{ $t('Toplam') }}
            <b-badge pill>{{ getTotal.price }} {{ getTotal.symbol }}</b-badge>
        </b-list-group-item>
    </b-list-group>
</template>

<script>
import {toFixedCustom, priceFormat} from "../../helpers/helpers";

export default {
    name: "OrderPreview",
    props: [
        'form',
        'commission',
        'wallet',
        'type',
        'selectedCoin'
    ],
    created() {
        if (this.type === 'sell') {
            this.sourceType = 'coin'
            this.coinType = 'source'
        }
    },
    data: () => ({
        sourceType: 'source',
        coinType: 'coin',
        toFixedCustom: toFixedCustom,
        priceFormat: priceFormat,
    }),
    computed: {
        getAmount() {
            return {
                price: (this.type === "buy" ? priceFormat((this.form.amount / 100) * (100 - this.commission)) : this.form.amount),
                symbol: this.selectedCoin['coin'].symbol
            }
        },
        getTotal() {
            return {
                price: (this.type === "sell" ? priceFormat((this.form.total / 100) * (100 - this.commission)) : priceFormat(this.form.total)),
                symbol: this.selectedCoin['source'].symbol
            }
        },
        getCommission() {
            return {
                price: priceFormat((this.form.amount / 100) * this.commission),
                symbol: this.selectedCoin[this.coinType].symbol
            }
        },
        getCurrent() {
            let wallet = 0
            if (this.wallet[this.sourceType] !== undefined) {
                wallet = this.wallet[this.sourceType].balance;
            }
            return {
                price: priceFormat(wallet),
                symbol: this.selectedCoin[this.sourceType].symbol
            }
        }
    }
}
</script>

<style scoped>

</style>
