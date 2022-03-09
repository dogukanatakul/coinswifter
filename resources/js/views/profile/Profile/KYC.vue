<template>
    <div class="settings-profile table-responsive">
        <b-form @submit="uploadKYC" enctype="multipart/form-data">
            <b-row>
                <b-col cols="12" md="6">
                    <b-form-group
                        :label="$t('Tip')"
                    >
                        <v-select
                            v-model="form.type"
                            label="text"
                            :options="kyc_select"
                            :reduce="data => data.value"
                        ></v-select>
                    </b-form-group>
                </b-col>
                <b-col cols="12" md="6" :key="update">
                    <b-form-group
                        :label="$t('Dosya')"
                    >
                        <input
                            class="form-control"
                            type="file"
                            :placeholder="$t('Dosya seçiniz')"
                            @change="onChange($event)"
                        />
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col cols="12">
                    <div class="d-grid gap-2">
                        <b-button type="sumit" block variant="primary">{{ $t('TALEP OLUŞTUR') }}</b-button>
                    </div>
                </b-col>
            </b-row>
        </b-form>

        <b-row>
            <b-col cols="12">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">{{ $t('Dosya') }}</th>
                        <th>{{ $t('Talep Tarihi') }}</th>
                        <th>{{ $t('Talep Tipi') }}</th>
                        <th>{{ $t('Durum') }}</th>
                        <th>{{ $t('Cevap') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(kyc,key) in kycs" :key="key">
                        <td>
                            <photo-provider>
                                <photo-consumer :intro="kyc.file_url" :key="kyc.file_url" :src="kyc.file_url">
                                    <img :src="kyc.file_url" class="view-box cursor-pointer" style="max-height: 50px;">
                                </photo-consumer>
                            </photo-provider>
                        </td>
                        <td>{{ kyc.created_at }}</td>
                        <td>{{ kyc.type }}</td>
                        <td>{{ kyc.status }}</td>
                        <td>{{ kyc.status_message }}</td>
                    </tr>
                    </tbody>
                </table>
            </b-col>
        </b-row>
    </div>
</template>

<script>

import restAPI from "../../../api/restAPI";


export default {
    name: "KYC",
    data: () => ({
        form: {
            file: null,
            type: null
        },
        kyc_select: [],
        kycs: [],
        update: 0,
    }),
    methods: {
        async uploadKYC() {
            let formData = new FormData();
            formData.append('type', this.form.type)
            formData.append('file', this.form.file)
            await restAPI.getData({Action: "kyc-set"}, formData, true).then((response) => {
                if (response.status === 'success') {
                    this.$notify({text: response.message, type: 'success'})
                    this.getKYC()
                    this.form = {
                        file: null,
                        type: null
                    }
                    this.update += 1
                } else if (response.status === 'fail') {
                    this.$notify({text: response.message, type: 'error'})
                }
            })
        },
        async getKYC() {
            await restAPI.getData({
                Action: "kyc-get",
            }).then((response) => {
                if (response.status === 'success') {
                    this.kyc_select = response.kyc_select
                    this.kycs = response.kycs
                } else if (response.status === 'fail') {
                    this.$notify({text: response.message, type: 'error'})
                    this.$router.push({name: 'profile.adress'})
                }
            })
        },
        onChange(event) {
            this.form.file = event.target.files[0];
        },
    },
    async created() {
        await this.getKYC()
    }
}
</script>

<style scoped>

</style>
