<template>
    <div class="row">
        <div class="col-12 text-center">
            <h3>{{ forgot.info.name_surname }}</h3>
        </div>
        <div class="col-6">
            <div class="d-grid gap-2">
                <b-button block @click="yesIm" variant="primary">{{ $t('Evet, Benim') }}</b-button>
            </div>
        </div>
        <div class="col-6">
            <div class="d-grid gap-2">
                <b-button block @click="notIm" variant="danger">{{ $t('Hayır, Ben Değilim') }}</b-button>
            </div>
        </div>
    </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
    name: "CheckUser",
    computed: {
        ...mapGetters([
            'forgot',
        ])
    },
    methods: {
        yesIm() {
            this.$store.commit('FORGOT', {
                info: this.forgot.info,
                step: 2,
            })
        },
        notIm() {
            this.$store.commit('FORGOT', {
                info: {},
                step: 0,
            })
        }
    }
}
</script>
