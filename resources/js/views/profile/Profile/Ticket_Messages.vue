<template>
  <div>
    <b-form enctype="multipart/form-data" @submit="setTicketMessage">
      <b-card-group deck v-for="(message, message_table) in messages" :key="message_table" >
        <b-card header-tag="header" footer-tag="footer">
          <template #header>
            <div class="mb-0 d-flex justify-content-between">
              <h6 class="mb-0" v-if=" message['user']['username'] === this.$store.getters.user.username " >
                {{ message["user"]["username"] }}
              </h6>
              <h6 class="mb-0" v-else>Müşteri Temsilcisi</h6>
              <h6 class="mb-0">{{ formatDate(message.created_at) }}</h6>
            </div>
          </template>
          <div v-if="message.file_name !== null">
            <photo-provider>
              <photo-consumer :intro="ticket" :key="ticket" :src="ticket">
                <img :src=" ticket + '/' + formatDate2(message.created_at) + '/' + message.file_name " class="view-box cursor-pointer" style="max-height: 50px" />
              </photo-consumer>
            </photo-provider>
          </div>
          <b-card-text v-html="message.message"></b-card-text>
          <!--                <template #footer>-->
          <!--                    <em>Footer Slot</em>-->
          <!--                </template>-->
        </b-card>
      </b-card-group>
      <div v-if="status !== '3'">
        <ckeditor :editor="editor" v-model="form.editorData" :config="editorConfig" ></ckeditor>
        <b-form-group class="mt-3" label="Destek Bileti Ekleri">
          <input type="file" class="mt-3 form-control" placeholder="Destek Bileti detayını giriniz" @change="onChange($event)" inputmode="text" />
        </b-form-group>
        <div class="d-flex justify-content-between mt-3">
          <b-button type="submit" block variant="primary" @click="ticketId(messages[0]['ticket_id'])" >
            {{ $t("YENİ MESAJ GÖNDER") }}
          </b-button>
          <b-form enctype="multipart/form-data" @submit="closeTicketMessage">
            <b-button type="submit" block variant="danger" @click="ticketId2(messages[0]['ticket_id'])" >
              {{ $t("DESTEK BİLETİNİ KAPAT") }}
            </b-button>
          </b-form>
        </div>
      </div>
    </b-form>
  </div>
</template>

<script>
import restAPI from "../../../api/restAPI";
import moment from "moment";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

export default {
  name: "Ticket_Messages",
  data: function () {
    return {
      form: {
        file: null,
        editorData: null,
        ticketId: null,
      },
      form2: {
        ticketId: null,
      },
      formGet: {
        ticket: this.$store.getters.ticket,
      },
      ticket: null,
      editor: ClassicEditor,
      editorConfig: {
        toolbar: {
          items: [
            "heading",
            "|",
            "bold",
            "italic",
            "|",
            "bulletedList",
            "numberedList",
            "|",
            "|",
            "|",
            "undo",
            "redo",
          ],
        },
      },
      status: null,
      messages: [],
    };
  },
  async created() {
    await this.getTicketMessage();
  },
  methods: {
    async getTicketMessage() {
      await restAPI
        .getData({ Action: "ticket-messages" }, this.formGet)
        .then((response) => {
          if (response.status === "success") {
            this.status = response.ticket_status;
            this.ticket = response.ticket_url;
            this.messages = response.ticket_messages;
          } else if (response.status === "fail") {
            this.$notify({ text: response.message, type: "error" });
          }
        });
    },
    async setTicketMessage() {
      let formData = new FormData();
      formData.append("ticket_id", this.form.ticketId);
      formData.append("detail", this.form.editorData);
      if (this.form.file !== null) {
        formData.append("file", this.form.file);
      }
      await restAPI
        .getData({ Action: "ticket-set-messages" }, formData, true)
        .then((response) => {
          if (response.status === "success") {
            this.getTicketMessage();
            this.form = {
              editorData: "",
              file: null,
            };
            this.$notify({ text: response.message, type: "success" });
          } else if (response.status === "fail") {
            this.$notify({ text: response.message, type: "error" });
          }
        });
    },
    async closeTicketMessage() {
      await restAPI
        .getData({ Action: "ticket-close-messages" }, this.form2)
        .then((response) => {
          if (response.status === "success") {
            this.$router.push({ name: "profile.ticket" });
            this.$notify({ text: response.message, type: "success" });
          } else if (response.status === "fail") {
            this.$notify({ text: response.message, type: "error" });
          }
        });
    },
    ticketId(id) {
      this.form.ticketId = id;
    },
    ticketId2(id) {
      this.form2.ticketId = id;
    },
    formatDate(date) {
      return moment(date).format("MM/DD/YYYY H:m:s");
    },
    formatDate2(date) {
      return moment(date).format("Y-MM-DD");
    },
    onChange(event) {
      this.form.file = event.target.files[0];
    },
  },
  watch: {
    $route(to, from) {
      this.$store.commit("TICKET", null);
    },
  },
};
</script>

<style scoped>
</style>
