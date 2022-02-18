<template>
    <b-form>
        <b-row>
            <b-col cols="4">
                <v-select
                    :placeholder="$t('Ülke')"
                    v-model.number="form.countries_id"
                    label="text"
                    :options="countries"
                    :reduce="data => data.value"
                ></v-select>
            </b-col>
            <b-col cols="4">
                <v-select
                    v-model.number="form.provinces_id"
                    label="text"
                    :options="provinces"
                    :reduce="data => data.value"
                ></v-select>
            </b-col>
            <b-col cols="4">
                <v-select
                    v-model.number="form.districts_id"
                    label="text"
                    :options="district"
                    :reduce="data => data.value"
                ></v-select>
            </b-col>
        </b-row>
        <b-row class="mt-3">
            <b-col cols="12">
                <b-form-textarea v-model="form['address']" size="sm" :placeholder="$t('Açık Adres')"></b-form-textarea>
            </b-col>
        </b-row>
        <b-row class="mt-3">
            <div class="d-grid gap-2">
                <b-button @click="setAdress" block variant="primary">{{ $t("Güncelle") }}</b-button>
            </div>
        </b-row>
    </b-form>
</template>

<script>
import restAPI from "../../../api/restAPI";

export default {
    name: "Adress",
    data: () => ({
        form: {
            countries_id: 218,
            provinces_id: null,
            districts_id: null,
            address: null,
        },
        countries: [],
        provinces: [],
        district: [],
    }),
    methods: {
        async getAdress(created = false) {
            await restAPI.getData({Action: "get-adress"}, this.form).then((response) => {
                this.countries = response.countries
                this.provinces = response.provinces
                this.district = response.district
                if (created && response.form) {
                    this.form = response.form
                }
            })
        },
        async setAdress() {
            await restAPI.getData({Action: "set-adress"}, this.form).then((response) => {
                if (response.status === 'success') {
                    this.$notify({text: response.message, type: 'success'})
                } else if (response.status === 'fail') {
                    this.$notify({text: response.message, type: 'error'})
                }
            })
        }
    },
    async created() {
        await this.getAdress(true)
    },
    watch: {
        form: {
            async handler() {
                await this.getAdress()
            },
            deep: true,
        },
        countries(val) {
            if (val.length === 0) {
                this.form.countries_id = null;
            }
        },
        provinces(val) {
            if (val.length === 0) {
                this.form.provinces_id = null;
            }
        },
        district(val) {
            if (val.length > 0 && val.find(n => n.value === this.form.districts_id) === undefined) {
                this.form.districts_id = null;
            }
        },
    }
}
</script>

<style scoped>

</style>
