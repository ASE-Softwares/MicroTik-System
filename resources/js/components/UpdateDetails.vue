<template>
  <div>
    <Button @click="UpdateDetailsModal = true">Update Data</Button>
    <Modal
      v-model="UpdateDetailsModal"
      :mask-closable="false"
      :styles="{ top: '20px' }"
    >
      <h4>Update Client Data</h4>
      <div class="form-group">
        <label for="ip">IP Address</label>
        <input
          type="text"
          v-model="clientData.ip"
          value=""
          id="ip"
          class="form-control"
          placeholder="Enter Client IP Adress"
          required
          readonly
        />
      </div>
      <div class="form-group">
        <label for="client_name">Client Name</label>
        <input
          type="text"
          v-model="clientData.client_name"
          id="client_name"
          class="form-control"
          placeholder="Enter Name"
        />
      </div>
      <div class="form-group">
        <label for="email">Email Address</label>
        <input
          type="text"
          v-model="clientData.email"
          id="email"
          class="form-control"
          placeholder="Provide an Email Address"
        />
      </div>

      <div class="form-group">
        <label for="package_id">Select Client Subscription</label>
        <Select
          v-model="clientData.package_id"
          id="package_id"
          transfer
          filterable
          placeholder="Select Subscription"
        >
          <Option
            v-for="(subscription, s) in packages"
            :key="s"
            :value="subscription.id"
            >{{
              subscription.name +
              " " +
              subscription.amount +
              " " +
              subscription.rate
            }}</Option
          >
        </Select>
      </div>
      <div slot="footer">
        <button
          class="btn btn-success"
          :disabled="loading"
          @click="submitChanges"
          type="submit"
        >
          {{ loading ? "Saving...." : "Save" }}
          <i class="fa fa-paper-plane" aria-hidden="true"></i>
        </button>
      </div>
    </Modal>
  </div>
</template>
<script>
export default {
  props: ["client"],
  data() {
    return {
      packages: [],
      UpdateDetailsModal: false,
      clientData: {
        ip: this.client.network,
      },
      loading: false,
    };
  },
  created() {
    this.getPackages();
  },
  methods: {
    async getPackages() {
      const res = await this.callApi("get", "/admin/subscriptions_json");
      if (res.status == 200) {
        this.packages = res.data;
      }
    },
    async submitChanges() {
      this.loading = true;
      const res = await this.callApi(
        "post",
        "/admin/createUserFromIp",
        this.clientData
      );
      if (res.status == 201) {
        this.s("Client Updated");
        this.$emit("client_updated", res.data);
        this.UpdateDetailsModal = false;
      }
      if (res.status == 422) {
        this.$validation(res);
      }
      this.loading = false;
    },
  },
};
</script>