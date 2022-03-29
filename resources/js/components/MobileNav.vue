<template>
  <nav class=" navbar-mobile navbar-expand-md navbar-dark fixed-bottom foot-navigation d-block d-sm-none " id="bluescale" >
    <b-button-group style="display: block" class="w-100 d-flex justify-content-between" >
      <b-button @click="go('wallets')" variant="outline-secondary">
        <b-icon icon="wallet2" class="beyazimsi" size="md"></b-icon>
        <div class="bottom-navigator">
          <small>{{ $t("Cüzdan") }}</small>
        </div>
      </b-button>
      <b-button @click="go('markets')" variant="outline-secondary">
        <b-icon icon="graph-up" class="beyazimsi" size="md"></b-icon>
        <div class="bottom-navigator">
          <small>{{ $t("Market") }}</small>
        </div>
      </b-button>
      <b-button @click="go('exchange')" variant="outline-secondary">
        <b-icon icon="arrow-left-right" class="beyazimsi" size="md"></b-icon>
        <div class="bottom-navigator">
          <small>{{ $t("Takas") }}</small>
        </div>
      </b-button>
    </b-button-group>
  </nav>
</template>

<script>
export default {
  name: "MobileNav",
  methods: {
    go(route) {
      this.$router.push({ name: route });
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
