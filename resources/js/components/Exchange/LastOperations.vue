<template>
    <b-card :header="$t('Son İşlemler')" header-tag="header" class="mt-2">
        <b-card-text>
            <div class="table-responsive" style="max-height: 30vh;overflow-y: auto;overflow-x: hidden;">
                <table class="table table-sm order-table">
                    <thead>
                    <tr>
                        <th class="text-start">{{ $t('Zaman') }}</th>
                        <th class="text-end">{{ $t('Fiyat') }} ({{ selectedCoin.source.symbol }})</th>
                        <th class="text-end">{{ $t('Miktar') }} ({{ selectedCoin.coin.symbol }})</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(operation,operation_key) in lastOperations" :key="operation_key">
                        <td class="text-start">{{ operation.created_at }}</td>
                        <td class="text-end">{{ operation.price }}</td>
                        <td class="text-end">{{ operation.amount }}</td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </b-card-text>
    </b-card>
</template>

<script>
import {convertDate} from "../../helpers/helpers";

export default {
    name: "LastOperations",
    props: [
        'lastOperations',
        'selectedCoin'
    ],
    data: () => ({
        convertDate: convertDate,
    }),
}
</script>

<style scoped>

</style>
