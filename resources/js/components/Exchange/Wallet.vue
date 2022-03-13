<template>
    <b-card class="my-2 my-wallet" no-body>
        <b-row>
            <b-col cols="5">
                <div>
                    {{ wallet["source"]["balance"] }}
                    {{ wallet["source"]["symbol"] }}
                </div>
                <small class="text-danger">
                    <b-icon icon="lock-fill" font-scale="0.5"></b-icon>
                    {{ wallet["source"]["locked"] }}
                    {{ wallet["source"]["symbol"] }}</small
                >
            </b-col>
            <b-col cols="2" class="wallet-title d-none d-md-block">{{ $t("CÜZDAN") }}</b-col>
            <b-col cols="5">
                <div>
                    {{ wallet["coin"]["balance"] }}
                    {{ wallet["coin"]["symbol"] }}
                </div>
                <small class="text-danger">
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
            width: 69px;
            height: 65px;
            font-size: 9px;
            margin-right: -24px;
        }
    }
    @media screen and (max-width: 991px) {
        .wallet-title {
            height: 65px;
            margin-right: -24px;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: 0.1rem;
            -ms-transform: rotate(0);
            -moz-transform: rotate(0);
            -webkit-transform: rotate(0);
            transform: rotate(0);
        }
    }
    @media screen and (max-width: 575px) {
        .wallet-title {
            //   width: 69px;
            height: 65px;
            margin-right: -24px;
            font-weight: 700;
            font-size: 9px;
            letter-spacing: 0.03rem;
            -ms-transform: rotate(0);
            -moz-transform: rotate(0);
            -webkit-transform: rotate(0);
            transform: rotate(0);
        }
    }
}
</style>
