<template>
    <router-view></router-view>
    <Footer></Footer>
</template>

<script>
import Footer from "../../components/Footer";
export default {
    name: "Page",
    components: {Footer}
}
</script>

<style scoped>

</style>
