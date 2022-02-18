<template>
    <div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <MarketCarousel :parities="parities"/>
                </div>
            </div>
        </div>
        <markets-list :parities="parities" v-model:parityName="parityName" v-model:selectedcoin="selectedCoin"></markets-list>
    </div>
</template>

<script>
import MarketCarousel from "../components/Market/MarketCarousel.vue";
import MarketsList from "../components/Market/MarketsList.vue";
import restAPI from "../api/restAPI";
import {groupBy} from "../helpers/helpers";

export default {
    name: "Markets",
    components: {
        MarketCarousel,
        MarketsList,
    },
    async created() {
        if (this.$route.params.parityName !== undefined || this.$route.params.parityName == null) {
            this.parityName = this.$route.params.parityName;
        }
        await restAPI.getData({
            Action: "exchange/tokens",
        }).then((response) => {
            if (response.status === 'success') {
                this.coins = response.data
            }
        });
    },
    data() {
        return {
            coins: {},
            selectedCoin: {},
            parityName: null,
        }
    },
    computed: {
        parities() {
            return groupBy(Object.values(this.coins), 'source', 'symbol')
        }
    },
    updated() {
        console.log("updated")

    },
};
</script>
