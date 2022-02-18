<template>
    <apexchart type="candlestick" height="350" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
export default {
    name: "newChart",
    props: [
        'chart',
        'selectedCoin',
    ],
    data: function () {
        return {
            series: [{
                data: []
            }],
            chartOptions: {
                chart: {
                    type: 'candlestick',
                    height: 350
                },
                title: {
                    text: this.selectedCoin.source.name + " / " + this.selectedCoin.coin.name,
                    align: 'left'
                },
                xaxis: {
                    type: 'datetime'
                },
                yaxis: {
                    tooltip: {
                        enabled: true
                    }
                }
            }
        }
    },
    async created() {
        this.series[0].data = this.chart;
    }
}
</script>

<style scoped>

</style>
