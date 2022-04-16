<template>
    <div class="settings-profile">
        <b-form enctype="multipart/form-data" @submit="uploadTicket">
            <b-row>
                <b-col cols="12">
                    <b-form-group label="Destek Bileti Kategorisi">
                        <v-select :placeholder="$t('Kategori Seçin')" v-model="form.category" label="name"
                                  :options="category" :reduce="name => name.id" ></v-select>
                    </b-form-group>
                    <b-form-group label="Destek Bileti konusu">
                        <v-select :placeholder="$t('Konu Seçin')" v-model="form.issue" label="name" :options="issue"
                                  :reduce="name => name.id "></v-select>
                    </b-form-group>
                    <b-form-group label="Destek Bileti Detayı">
                        <textarea v-model="form.detail" class="mt-3 form-control" placeholder="Destek Bileti detayını giriniz" rows="5"
                                  inputmode="text"></textarea>
                    </b-form-group>
                    <b-form-group label="Destek Bileti Ekleri">
                        <input type="file" class="mt-3 form-control" placeholder="Destek Bileti detayını giriniz" @change="onChange($event)"
                               inputmode="text" multiple/>
                    </b-form-group>
                    <b-form-group>
                        <div class="d-grid gap-2">
                            <b-button type="submit" block variant="primary">{{ $t("TALEP OLUŞTUR") }}</b-button>
                        </div>
                    </b-form-group>
                </b-col>
            </b-row>
        </b-form>
        <b-row>
            <b-col cols="12" class="table-responsive ticket-table">
                <table class="table table-bordered table-stripped overflowed-table">
                    <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Konu</th>
                        <th>Destek Bileti Detayı</th>
                        <th>Destek Bileti Ekleri</th>
                        <th>Durum</th>
                        <th>Destek Bileti Gönderilme Tarihi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(tickets, tickets_table) in ticket" :key="tickets_table">
                        <td>{{ tickets.category[0].name }}</td>
                        <td>{{ tickets.issue[0].name }}</td>
                        <td>{{ tickets.detail }}</td>
                        <td><a href="#" class="link-dark text-decoration-none">deneme.jpg</a>, <a href="#"
                                                                                                  class="link-dark text-decoration-none">test.png</a>,<a
                            href="#" class="link-dark text-decoration-none">info.gif</a></td>
                        <td v-if="tickets.status == 0">Bekliyor</td>
                        <td v-else-if="tickets.status == 1">İnceleniyor</td>
                        <td v-else-if="tickets.status == 3">Tamamlandı</td>
                        <td>{{formatDate(tickets.created_at)}}</td>
                    </tr>
                    </tbody>
                </table>
            </b-col>
        </b-row>
    </div>
</template>

<script>
import restAPI from "../../../api/restAPI";
import moment from "moment";
export default {
    name: 'Ticket',
    data: () => ({
        form: {
            file: null,
            category: null,
            issue: null,
            detail:null,

        },
        category: [],
        issue: [],
        ticket: []
    }),
    async created() {
        await this.getTicket()
    },
    methods: {
        async getTicket(){
            await restAPI.getData({
                Action: "ticket",
            }).then((response) => {
                if (response.status === 'success') {
                    this.category = response.ticket_categories
                    console.log(response.ticket_categories)
                    this.issue = response.ticket_issues
                    this.ticket = response.ticket
                } else if (response.status === "fail") {
                    this.$notify({text: response.message, type: "error"});
                }
            });
        },
        async uploadTicket(){
            let formData = new FormData();
            formData.append("category", this.form.category);
            formData.append("issue", this.form.issue);
            formData.append("detail", this.form.detail);
            formData.append("file", this.form.file);
            await restAPI
                .getData({ Action: "ticket-set" }, formData, true)
                .then((response) => {
                    if (response.status === "success") {
                        this.$notify({ text: response.message, type: "success" });
                        this.getTicket();
                        this.form = {
                            file: null,
                            type: null,
                        };
                    } else if (response.status === "fail") {
                        this.$notify({ text: response.message, type: "error" });
                    }
                });
        },
        onChange(event) {
            this.form.file = event.target.files;
        },
        formatDate(date) {
            return moment(date).format('MM/DD/YYYY H:m');
        },
    }
}
</script>

<style lang="scss" scoped>

</style>
