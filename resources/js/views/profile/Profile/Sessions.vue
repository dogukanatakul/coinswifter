<template>
  <div class="table-responsive">
    <table class="table table-striped overflowed-table">
      <thead>
        <tr>
          <th scope="col">{{ $t("IP") }}</th>
          <th scope="col">{{ $t("Platform") }}</th>
          <th scope="col">{{ $t("Cihaz") }}</th>
          <th scope="col">{{ $t("Tarayıcı") }}</th>
          <th scope="col">{{ $t("Son Oturum Tarihi") }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(session, key) in sessions" :key="key">
          <td>{{ session.ip }}</td>
          <td>{{ session.platform }}</td>
          <td>{{ session.device }}</td>
          <td>{{ session.browser }}</td>
          <td>{{ session.last_login }}</td>
        </tr>
      </tbody>
    </table>
  </div>
    <b-row class="mt-3">
        <div class="d-grid gap-2">
            <b-button @click="signOut" block variant="primary">{{ $t("Oturumu Kapat") }}</b-button>
        </div>
    </b-row>
</template>

<script>
import restAPI from "../../../api/restAPI";

export default {
  name: "Sessions",
  data: () => ({
    sessions: [],
  }),
  async created() {
    await this.getSessions();
  },
  methods: {
    async getSessions() {
      await restAPI.getData({ Action: "sessions" }).then((response) => {
        if (response.status === "success") {
          this.sessions = response.sessions;
        } else if (response.status === "fail") {
          this.$notify({ text: response.message, type: "error" });
        }
      });
    },
    async signOut() {
      await restAPI.getData({ Action: "sign-out" }).then((response) => {
        // if (response.status === "success") {
        //   // this.sessions = response.sessions;
        // } else if (response.status === "fail") {
        //   this.$notify({ text: response.message, type: "error" });
        // }
      });
    },
  },
};
</script>

<style scoped>
</style>
