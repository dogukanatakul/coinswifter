<template>
  <nav
    class="
      navbar navbar-mobile navbar-expand-md navbar-dark
      fixed-bottom
      foot-navigation
      d-block d-sm-none
    "
    id="bluescale"
  >
    <b-button-group
      style="display: block"
      class="w-100 d-flex justify-content-between"
    >
      <b-button @click="go('wallets')" variant="outline-secondary">
        <b-icon icon="wallet2" class="beyazimsi" size="md"></b-icon>
        <div class="bottom-navigator"><small>Cüzdan</small></div>
      </b-button>
      <b-button @click="go('markets')" variant="outline-secondary">
        <b-icon icon="graph-up" class="beyazimsi" size="md"></b-icon>
        <div class="bottom-navigator"><small>Market</small></div>
      </b-button>
      <b-button @click="go('exchange')" variant="outline-secondary">
        <b-icon icon="arrow-left-right" class="beyazimsi" size="md"></b-icon>
        <div class="bottom-navigator"><small>Takas</small></div>
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
.navbar-mobile {
  padding: 2px;
  height: auto;

  .btn {
    padding: 0rem 0rem !important;
  }
  .bottom-navigator {
    letter-spacing: 3px;
    font-weight: bolder;
    color: aliceblue;
  }
  .beyazimsi {
    color: aliceblue;
  }
}
.foot-navigation {
  background-color: #080c3c !important;
}
</style>
