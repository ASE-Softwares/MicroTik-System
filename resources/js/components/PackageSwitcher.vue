<template>
  <div>
    <Button @click="AddPackageModal = true" type="primary"
      >Change Package</Button
    >
    <Modal
      v-model="AddPackageModal"
      :mask-closable="false"
      :styles="{ top: '30px' }"
      :closable="!loading"
    >
      <!-- Profiles -->
      <Form>
        <FormItem label="select new Profile">
          <Select transfer v-model="data.package_id">
            <Option
              v-for="(subscription, s) in subscriptions"
              :key="s"
              :value="subscription.id"
            >
              {{
                subscription.name +
                subscription.download +
                "/" +
                subscription.upload
              }}</Option
            >
          </Select>
        </FormItem>
      </Form>

      <div slot="footer">
        <Button
          @click="submitChanges"
          type="success"
          :loading="loading"
          :disabled="loading"
          >{{ loading ? "working..." : "Update" }}</Button
        >
      </div>
    </Modal>
  </div>
</template>
<script>
export default {
  props: ["client"],
  data() {
    return {
      loading: false,
      AddPackageModal: false,
      subscriptions: [],
      data: {
        package_id: "",
      },
    };
  },
  created() {
    this.getSubscriptions();
  },
  methods: {
    async sendResultsToBackEnd() {
      this.loading = true;
      let obj = {
        package_id: this.data.package_id,
        client_id: this.client.id,
      };
      const res = await this.callApi(
        "post",
        "/admin/swith_client_profile" + obj.client_id,
        obj
      );
      if (res.status == 201) {
        this.s("Profile Updated");
        this.AddPackageModal = false;
      } else {
        this.e("Error Occured");
      }
      this.loading = false;
    },
    submitChanges() {
      return new Promise((resolve) => {
        this.$Modal.confirm({
          title: "Confirm Action",
          content:
            "Changing A Clients Profile Changes Their Speed Limits On The Router",
          onOk: () => {
            this.sendResultsToBackEnd();
          },
        });
      });
    },
    async getSubscriptions() {
      const res = await this.callApi("get", "/admin/all_subs");
      if (res.status == 200) {
        this.subscriptions = res.data;
      }
    },
  },
};
</script>