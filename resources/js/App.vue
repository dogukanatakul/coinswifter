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
    mounted() {
        this.$Progress.finish();
    },
    created() {
        this.$Progress.start();
    },
};
</script>
