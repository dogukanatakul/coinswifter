<template>
    <line-chart :chartData="data" :options="options"></line-chart>
</template>

<script>
import {LineChart} from 'vue-chart-3';
import {Chart, registerables} from 'chart.js'

const skipped = (ctx, value) => ctx.p0.skip || ctx.p1.skip ? value : undefined;
const down = (ctx, value) => ctx.p0.parsed.y > ctx.p1.parsed.y ? value : undefined;

const delayBetweenPoints = 10;
const previousY = (ctx) => ctx.index === 0 ? ctx.chart.scales.y.getPixelForValue(100) : ctx.chart.getDatasetMeta(ctx.datasetIndex).data[ctx.index - 1].getProps(['y'], true).y;
const animation = {
    x: {
        type: 'number',
        easing: 'linear',
        duration: delayBetweenPoints,
        from: NaN, // the point is initially skipped
        delay(ctx) {
            if (ctx.type !== 'data' || ctx.xStarted) {
                return 0;
            }
            ctx.xStarted = true;
            return ctx.index * delayBetweenPoints;
        }
    },
    y: {
        type: 'number',
        easing: 'linear',
        duration: delayBetweenPoints,
        from: previousY,
        delay(ctx) {
            if (ctx.type !== 'data' || ctx.yStarted) {
                return 0;
            }
            ctx.yStarted = true;
            return ctx.index * delayBetweenPoints;
        }
    }
};

export default {
    setup() {
        Chart.register(...registerables)
    },
    components: {LineChart},
    props: [
        'chart',
        'selectedCoin',
    ],
    data() {
        return {
            data: {
                labels: [],
                datasets: [
                    {
                        borderWidth: 2,
                        radius: 0,
                        data: [],
                        borderColor: 'rgb(0,204,46)',
                        segment: {
                            borderColor: ctx => skipped(ctx, 'rgba(16,231,117,0.2)') || down(ctx, 'rgb(255,91,91)'),
                            borderDash: ctx => skipped(ctx, [6, 6]),
                        }
                    },
                ]
            },
            options: {
                animation,
                interaction: {
                    intersect: false
                },
                plugins: {
                    legend: false,
                    title: {
                        display: true,
                        text: this.selectedCoin.source.isim + " / " + this.selectedCoin.coin.isim,
                    },
                },
            }
        }
    },
    async created() {
        let i = 0
        for (const [key, value] of Object.entries(this.chart)) {
            this.data.datasets[0].data.push(value)
            this.data.labels.push(key)
        }
    },
}

</script>
