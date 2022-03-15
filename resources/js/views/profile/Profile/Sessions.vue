<template>
  <div class="table-responsive">
    <table class="table table-striped">
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
  },
};
</script>

<style scoped>
</style>
