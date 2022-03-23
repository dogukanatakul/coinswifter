<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6">
                <div class="card o-hidden border-0 shadow-lg my-5 p-4">
                    <div class="card-body p-0" :key="forgot.step">
                        <find-user v-if="forgot.step===0"></find-user>
                        <check-user v-else-if="forgot.step===1"></check-user>
                        <send-code v-else-if="forgot.step===2 || forgot.step===3"></send-code>
                        <reset-password v-else-if="forgot.step===4"></reset-password>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import FindUser from "./Forgot/FindUser";
import CheckUser from "./Forgot/CheckUser";
import {mapGetters} from "vuex";
import SendCode from "./Forgot/SendCode";
import ResetPassword from "./Forgot/ResetPassword";

export default {
    name: "Forgot",
    components: {
        ResetPassword,
        SendCode,
        CheckUser,
        FindUser
    },
    data() {
        return {};
    },
    computed: {
        ...mapGetters([
            'forgot'
        ])
    },
};
</script>
