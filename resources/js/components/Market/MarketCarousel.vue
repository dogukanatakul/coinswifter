<template>
    <carousel v-if="parities['TRY']!==undefined" :autoplay="2000" :wrapAround="true" :breakpoints="breakpoints">
        <slide v-for="(value,slide) in parities['TRY']" :key="slide">
            <div class="market-carousel-item">
                <div class="market-carousel-item-name">
                    <img :src="'../assets/img/coinicon/'+ value.coin.symbol +'.png'" alt=""/>
                    <strong>{{ value.coin.name }}</strong></div>
                <h2>{{ value.parity_price.price.value }}₺</h2>
                <p>{{ value.parity_price.volume_last_24_hours_price.value }}₺</p>
            </div>
        </slide>
    </carousel>
</template>

<script>
import "vue3-carousel/dist/carousel.css";
import {Carousel, Slide} from "vue3-carousel";

export default {
    name: 'MarketCarousel',
    props: [
        'parities',
    ],
    data: () => ({
        data: [],
        breakpoints: {
            576: {
                itemsToShow: 2,
            },
            768: {
                itemsToShow: 3,
            },
            992: {
                itemsToShow: 4,
            },
            1200: {
                itemsToShow: 5,
            },
        },
    }),
    components: {
        Carousel,
        Slide,
    },
};
</script>
