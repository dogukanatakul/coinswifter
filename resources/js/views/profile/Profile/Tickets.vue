<template>
    <div class="settings-profile">
        <b-form enctype="multipart/form-data" @submit="uploadTicket">
            <b-row>
                <b-col cols="12">
                    <b-form-group label="Destek Bileti Kategorisi">
                        <v-select :placeholder="$t('Kategori Seçin')" v-model="form.category" label="name"
                                  :options="category" :reduce="name => name.id"></v-select>
                    </b-form-group>
                    <b-form-group label="Destek Bileti konusu">
                        <v-select :placeholder="$t('Konu Seçin')" v-model="form.subject" label="name" :options="subject"
                                  :reduce="name => name.id "></v-select>
                    </b-form-group>
                    <b-form-group label="Destek Bileti Detayı">
                        <ckeditor :editor="editor" v-model="form.editorData" :config="editorConfig"></ckeditor>
                    </b-form-group>
                    <b-form-group label="Destek Bileti Ekleri">
                        <input type="file" class="mt-3 form-control" placeholder="Destek Bileti detayını giriniz"
                               @change="onChange($event)"
                               inputmode="text"/>
                    </b-form-group>
                    <b-form-group>
                        <div class="d-grid gap-2">
                            <b-button type="submit" block variant="primary">{{ $t("DESTEK TALEBİ OLUŞTUR") }}</b-button>
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
                        <th>Ticket Id</th>
                        <th>Kategori</th>
                        <th>Konu</th>
                        <th>Durum</th>
                        <th>Destek Bileti Gönderilme Tarihi</th>
                        <th>Detaya Git</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(tickets, tickets_table) in ticket" :key="tickets_table">
                        <td>#{{tickets.ticket_key}}</td>
                        <td>{{ tickets.category[0].name }}</td>
                        <td>{{ tickets.subject[0].name }}</td>
                        <td v-if="tickets.status == 0">Bekliyor</td>
                        <td v-else-if="tickets.status == 1">İşlemde</td>
                        <td v-else-if="tickets.status == 3">Tamamlandı</td>
                        <td>{{ formatDate(tickets.created_at) }}</td>
                        <td>
                            <router-link class="btn btn-primary"
                                         :to="{ name: 'profile.ticketmessages' }" @click="selectTicket(tickets.ticket_key)"
                            >
                                {{ $t("Detay") }}
                            </router-link>
                        </td>
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
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

export default {
    name: 'Ticket',
    data: () => ({
        form: {
            file: null,
            category: null,
            subject: null,
            editorData: null,
        },
        editor: ClassicEditor,
        editorConfig: {
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    '|',
                    'bulletedList',
                    'numberedList',
                    '|',
                    '|',
                    '|',
                    'undo',
                    'redo'
                ]
            },
        },
        category: [],
        subject: [],
        ticket: []
    }),
    async created() {
        await this.getTicket()
    },
    methods: {
        async getTicket() {
            await restAPI.getData({
                Action: "ticket",
            }).then((response) => {
                if (response.status === 'success') {
                    this.category = response.ticket_categories
                    this.subject = response.ticket_subjects
                    this.ticket = response.ticket
                } else if (response.status === "fail") {
                    this.$notify({text: response.message, type: "error"});
                }
            });
        },
        async uploadTicket() {
            let formData = new FormData();
            formData.append("category", this.form.category)
            formData.append("subject", this.form.subject)
            formData.append("detail", this.form.editorData)
            if (this.form.file !== null){
                formData.append("file", this.form.file)
            }
            await restAPI
                .getData({Action: "ticket-set"}, formData, true)
                .then((response) => {
                    if (response.status === "success") {
                        this.$notify({text: response.message, type: "success"});
                        this.getTicket();
                        this.form = {
                            file: null,
                            type: null,
                        };
                    } else if (response.status === "fail") {
                        this.$notify({text: response.message, type: "error"});
                    }
                });
        },
        onChange(event) {
            this.form.file = event.target.files[0];
        },
        formatDate(date) {
            return moment(date).format('MM/DD/YYYY H:m:s');
        },
        selectTicket(ticket){
            this.$store.commit('TICKET',ticket)
        },
    }
}
</script>

<style lang="scss" scoped>

</style>
