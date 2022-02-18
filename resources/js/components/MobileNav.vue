<template>
    <nav class="navbar navbar-mobile navbar-expand-md navbar-dark fixed-bottom bg-dark d-block d-sm-none">
        <b-button-group style="display: block;" class="w-100 d-flex justify-content-between">
            <b-button @click="go('wallets')" variant="outline-secondary">
                <b-icon icon="wallet2" size="md"></b-icon>
                <div><small>Cüzdan</small></div>
            </b-button>
            <b-button @click="go('markets')" variant="outline-secondary">
                <b-icon icon="graph-up" size="md"></b-icon>
                <div><small>Market</small></div>
            </b-button>
            <b-button @click="go('exchange')" variant="outline-secondary">
                <b-icon icon="arrow-left-right" size="md"></b-icon>
                <div><small>Takas</small></div>
            </b-button>
        </b-button-group>
    </nav>
</template>

<script>
export default {
    name: "MobileNav",
    methods: {
        go(route) {
            this.$router.push({name: route})
        }
    }
}
</script>

<style lang="scss" scoped>
.navbar-mobile {
    padding: 2px;
    height: auto;

    .btn {
        padding: 0rem 0rem !important;
    }
}
</style>
