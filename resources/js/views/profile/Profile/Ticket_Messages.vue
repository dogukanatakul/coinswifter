<template>
    <div>
        <b-form enctype="multipart/form-data" @submit="setTicketMessage">
            <b-card-group deck v-for="(message, message_table) in messages" :key="message_table">
                <b-card header-tag="header" footer-tag="footer">
                    <template #header>
                        <div class="mb-0 d-flex justify-content-between">
                            <h6 class="mb-0" v-if="message['user']['username'] === this.$store.getters.user.username ">{{ message['user']['username']}}</h6>
                            <h6 class="mb-0" v-else>Müşteri Temsilcisi</h6>
                            <h6 class="mb-0">{{ formatDate(message.created_at) }}</h6>
                        </div>
                    </template>
                    <b-card-text v-html="message.message"></b-card-text>
                    <!--                <template #footer>-->
                    <!--                    <em>Footer Slot</em>-->
                    <!--                </template>-->
                </b-card>
            </b-card-group>
            <ckeditor :editor="editor" v-model="form.editorData" :config="editorConfig"></ckeditor>
            <div class="d-grid gap-2">
                <b-button type="submit" block variant="primary" @click="ticketId(messages[0]['ticket_id'])">{{ $t("YENİ MESAJ GÖNDER") }}</b-button>
            </div>
        </b-form>
    </div>
</template>

<script>
import restAPI from "../../../api/restAPI";
import moment from "moment";
import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

export default {
    name: "Ticket_Messages",
    data: function () {
        return {
            form: {
                editorData: null,
                ticketId: null,
            },
            formGet: {
                ticket: this.$store.getters.ticket,
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
            messages: [],
        }
    },
    async created() {
        await this.getTicketMessage()
    },
    methods: {
        async getTicketMessage() {
            await restAPI.getData({Action: "ticket-messages"}, this.formGet
            ).then((response) => {
                if (response.status === 'success') {
                    // this.form.ticketId = response.ticket_messages[0]['ticket_id']
                    this.messages = response.ticket_messages
                } else if (response.status === "fail") {
                    this.$notify({text: response.message, type: "error"});
                }
            });
        },
        async setTicketMessage() {
            await restAPI
                .getData({Action: "ticket-set-messages"}, this.form)
                .then((response) => {
                    if (response.status === "success") {
                        this.getTicketMessage();
                        this.form = {
                            editorData: '',
                        };
                        this.$notify({text: response.message, type: "success"});
                    } else if (response.status === "fail") {
                        this.$notify({text: response.message, type: "error"});
                    }
                });
        },
        ticketId(id){
          this.form.ticketId = id
        },
        formatDate(date) {
            return moment(date).format('MM/DD/YYYY H:m:s');
        },
    },
    watch:{
        $route (to, from){
            this.$store.commit('TICKET',null)
        }
    },
}
</script>

<style scoped>

</style>
