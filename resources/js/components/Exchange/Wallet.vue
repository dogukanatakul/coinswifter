<template>
    <b-card class="my-2 my-wallet ox-auto" no-body>
        <b-row>
            <b-col cols="5">
                <div class="text-smaller" @click="sendTradeFormWallet('buy', 1, wallet['source']['balance'])">
                    {{ wallet["source"]["balance"] }}
                    {{ wallet["source"]["symbol"] }}
                </div>
                <div class="text-danger text-smaller">
                    <b-icon icon="lock-fill" font-scale="0.5"></b-icon>
                    {{ wallet["source"]["locked"] }}
                    {{ wallet["source"]["symbol"] }}</div
                >
            </b-col>
            <b-col cols="1" class="wallet-title d-none d-lg-block">{{ $t("CÜZDAN") }}</b-col>
            <b-col cols="6" lg="5" class="text-lg-center">
                <div class="text-smaller" @click="sendTradeFormWallet('sell', wallet['coin']['balance'],1)">
                    {{ wallet['coin']['balance'] }}
                    {{ wallet['coin']['symbol'] }}
                </div>
                <div class="text-danger text-smaller">
                    <b-icon icon="lock-fill" font-scale="0.5"></b-icon>
                    {{ wallet["coin"]["locked"] }}
                    {{ wallet["coin"]["symbol"] }}</div
                >
            </b-col>
        </b-row>
    </b-card>
</template>

<script>
export default {
  name: "Wallet",
  props: ["wallet"],
  methods: {
    sendTradeFormWallet(action, price, amount) {
      this.$emit("sendTradeFormWallet", { action, price, amount });
    },
  },
};
</script>

<style lang="scss" scoped>

</style>
