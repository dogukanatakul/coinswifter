<template>
    <notifications/>
    <Header/>
    <MobileNav/>
    <router-view/>
    <vue-progress-bar></vue-progress-bar>
    <confirm-dialog></confirm-dialog>
</template>

<script>
import Header from "./components/Header.vue";
import ConfirmDialog from 'primevue/confirmdialog';
import MobileNav from "./components/MobileNav";
export default {
    components: {
        MobileNav,
        Header,
        ConfirmDialog
    },
    data() {
        return {
            socket:{}
        }
    },
    mounted() {
        window.Echo.channel('coinswifter_database_test')
            .listen('MessagePushed', (e) => {
                console.log(e)
            })
        this.socket = io("http://localhost:6001", {transports: ['websocket']})
        this.socket.emit("welcome_message", "x")

        // window.io.on('welcome', (data) => {
        //     alert(data)
        // })
        this.$Progress.finish();
    },
    created() {
        this.$Progress.start();
    },
};
</script>
