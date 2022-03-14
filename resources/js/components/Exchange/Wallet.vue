<template>
    <b-card class="my-2 my-wallet ox-auto" no-body>
        <b-row>
            <b-col cols="5">
                <div class="text-smaller" @click="sendTradeFormWallet('buy', 1, wallet['source']['balance'])">
                    {{ wallet["source"]["balance"] }}
                    {{ wallet["source"]["symbol"] }}
                </div>
                <small class="text-danger text-smaller">
                    <b-icon icon="lock-fill" font-scale="0.5"></b-icon>
                    {{ wallet["source"]["locked"] }}
                    {{ wallet["source"]["symbol"] }}</small
                >
            </b-col>
            <b-col cols="1" class="wallet-title d-none d-lg-block">{{ $t("CÜZDAN") }}</b-col>
            <b-col cols="6" lg="5" class="text-lg-center">
                <div class="text-smaller" @click="sendTradeFormWallet('sell', wallet['coin']['balance'],1)">
                    {{ wallet['coin']['balance'] }}
                    {{ wallet['coin']['symbol'] }}
                </div>
                <small class="text-danger text-smaller">
                    <b-icon icon="lock-fill" font-scale="0.5"></b-icon>
                    {{ wallet["coin"]["locked"] }}
                    {{ wallet["coin"]["symbol"] }}</small
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
.my-wallet {
  padding: 10px;
  font-size: 14px;

  p {
    font-size: 12px;
    margin: 0px;
  }

  .col {
    padding: 10px;
    text-align: center;
    align-items: center;
    align-content: center;
    justify-content: center;
  }

  @media screen and (min-width: 991px) {
    .wallet-title {
      -ms-transform: rotate(270deg);
      -moz-transform: rotate(270deg);
      -webkit-transform: rotate(270deg);
      transform: rotate(270deg);
      letter-spacing: 0.1rem;
      width: 62px;
      height: 65px;
      font-size: 8px;
      margin-right: -24px;
    }
    .text-smaller {
      font-size: calc(2px + 6 * ((100vw - 320px) / 720));
    }
  }
  @media screen and (max-width: 991px) {
    .wallet-title {
      height: 65px;
      margin-right: -24px;
      font-weight: 600;
      font-size: 9px;
      width: 68px;
      letter-spacing: 0.1rem;
      -ms-transform: rotate(0);
      -moz-transform: rotate(0);
      -webkit-transform: rotate(0);
      transform: rotate(0);
      margin-top: 2vh !important;
    }
    .text-smaller {
      font-size: calc(12px + 6 * ((100vw - 320px) / 720));
    }
  }
  @media screen and (max-width: 575px) {
    .wallet-title {
      //   width: 69px;
      height: 65px;
      margin-right: -24px;
      font-weight: 700;
      font-size: 9px;
      width: 68px;
      letter-spacing: 0.03rem;
      -ms-transform: rotate(0);
      -moz-transform: rotate(0);
      -webkit-transform: rotate(0);
      transform: rotate(0);
    }
  }
}
</style>
