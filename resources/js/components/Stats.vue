<template>
  <div>
    <a @click="showStas">
      <i class="fas fa-plus text-success"></i>
    </a>
    <Modal
      @on-hidden="reloadPage()"
      v-model="statsModal"
      title="Client Details"
      :mask-closable="false"
      :closable="true"
      :width="75"
      :styles="{ top: '20px' }"
    >
      {{
        enabled ? "Switch Click to Disable Client" : "Switch to Enable Client"
      }}
      <i-switch
        :before-change="handleBeforeChange"
        true-color="#13ce66"
        false-color="#ff4949"
        v-model="enabled"
        :loading="isWorking"
      >
        <Icon type="md-checkmark" slot="open"></Icon>
        <Icon type="md-close" slot="close"></Icon>
      </i-switch>

      <hr class="mt-2 mb-1" />

      <div class="card">
        <div class="card-header">More Information</div>
        <div class="card-body">
          <div class="row mb-2">
            <div class="col-sm">
              <div class="card">
                <div class="card-body bg-info">
                  <h4 class="text-dark">ID :</h4>
                  <span class="text-white font-weight-bold h5">
                    {{ client[".id"] }}</span
                  >
                </div>
              </div>
            </div>
            <div class="col-sm">
              <div class="card">
                <div class="card-body bg-info">
                  <h4 class="text-dark">Address :</h4>
                  <span class="text-white font-weight-bold h5">{{
                    client.address
                  }}</span>
                </div>
              </div>
            </div>
            <div class="col-sm">
              <div class="card">
                <div class="card-body bg-info">
                  <h4 class="text-dark">Network :</h4>
                  <span class="text-white font-weight-bold h5">{{
                    client.network
                  }}</span>
                </div>
              </div>
            </div>
            <div class="col-sm">
              <div class="card">
                <div class="card-body bg-info">
                  <h4 class="text-dark">interface :</h4>
                  <span class="text-white font-weight-bold h5">
                    {{ client.interface }}</span
                  >
                </div>
              </div>
            </div>
            <div class="col-sm">
              <div class="card">
                <div class="card-body bg-info">
                  <h4 class="text-dark">Status :</h4>

                  <span class="text-white font-weight-bold h5">{{
                    enabled ? "Active" : "Disabled"
                  }}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-4" v-if="moreData.client != null">
              <div class="card">
                <div class="card-header">Client Information</div>
                <div class="card-body">
                  <div class="card">
                    <div class="card-body bg-success">
                      <h4 class="text-dark">Name</h4>
                      <span class="text-white font-weight-bold h5">
                        {{ moreData.client.user.name }}</span
                      >
                      <hr />
                      <h4 class="text-dark">Email</h4>
                      <span class="text-white font-weight-bold h5">
                        {{ moreData.client.user.email }}</span
                      >
                      <hr />
                      <h4 class="text-dark">Location</h4>
                      <span class="text-white font-weight-bold h5">
                        {{ moreData.client.location }}</span
                      >
                      <hr />
                      <h4 class="text-dark">Subscription</h4>
                      <span class="text-white font-weight-bold h5">
                        {{ moreData.client.package.name }}</span
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4" v-else>
              <div v-if="AppReady">
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
                <div class="form-group">
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
              </div>
            </div>
            <div class="col-md-8" v-if="moreData.queue != null">
              <div class="card">
                <div class="card-header">Network Statistics</div>
                <div class="card-body">
                  <div class="card">
                    <div class="card-body bg-success">
                      <h4 class="text-dark">Speed</h4>
                      <span class="text-white font-weight-bold h5">
                        {{ speedConv(moreData.queue["max-limit"]) }}</span
                      >
                      <h4 class="text-dark">Comment</h4>
                      <span class="text-white font-weight-bold h5">
                        {{ moreData.queue.comment }}</span
                      >
                      <h4 class="text-dark">Upload/Download</h4>
                      <span class="text-white font-weight-bold h5">
                        {{ speed(moreData.queue.rate) }}</span
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div slot="footer">
        <Button size="small" type="error" @click="statsModal = false"
          >Close <i class="fas fa-window-close" aria-hidden="true"></i
        ></Button>
      </div>
    </Modal>
  </div>
</template>
<script>
export default {
  data() {
    return {
      statsModal: false,
      enabled: this.client.disabled == "true" ? false : true,
      isWorking: false,
      moreData: {
        client: null,
        queue: null,
      },
      dataChanged: false,
      interalId: "",
      packages: [],
      clientData: {
        ip: this.client.network,
      },
      AppReady: false,
      loading: false,
    };
  },
  props: {
    client: Object,
  },
  watch: {
    statsModal() {
      if (this.statsModal == true) {
        // Reset the statistics before a new call
        this.moreData = {
          client: null,
          queue: null,
        };
        this.AppReady = false;
        this.getQueueData();
      }
    },
  },
  created() {
    this.getPackages();
  },
  methods: {
    async submitChanges() {
      this.loading = true;
      const res = await this.callApi(
        "post",
        "/admin/createUserFromIp",
        this.clientData
      );
      if (res.status == 201) {
        this.s("Client Updated");
        this.statsModal = false;
      }
      if (res.status == 422) {
        this.$validation(res);
      }
      this.loading = false;
    },
    async getPackages() {
      const res = await this.callApi("get", "/admin/subscriptions_json");
      if (res.status == 200) {
        this.packages = res.data;
      }
    },
    async getFastQueueData() {
      const res = await this.callApi(
        "get",
        "/admin/fast_queue_information/" + this.client.network
      );
      if (res.status == 201) {
        this.moreData.queue = res.data;
      }
    },
    speed(rate) {
      var right = Number(rate.split("/").pop());
      var left = Number(rate.substring(0, rate.indexOf("/")));

      return left + "Bps/" + right + "Bps";
    },
    reloadPage() {
      if (this.dataChanged == true) {
        window.location = "/admin/wired_clients";
      }
      clearInterval(this.interalId);
    },
    speedConv(rate) {
      // get the pos of the slash
      var right = Number(rate.split("/").pop());
      var left = Number(rate.substring(0, rate.indexOf("/")));

      return Number(left / 1000000) + "M/" + Number(right / 1000000) + "M";
      // split the string into two
      //join it
      //return it
    },
    async getQueueData() {
      const res = await this.callApi(
        "get",
        "/admin/queue_information/" + this.client.network
      );
      if (res.status == 201) {
        this.moreData = res.data;
        this.AppReady = true;
        this.startRealTime();
        // this.getFastQueueData();
      }
    },
    startRealTime() {
      this.interalId = setInterval(() => {
        this.getFastQueueData();
      }, 1500);
    },
    async CallApiToDisable() {
      this.isWorking = true;
      let obj = {
        client: this.client,
        action: this.enabled ? "Disable" : "Enable",
      };
      const res = await this.callApi(
        "post",
        "/admin/disableClientInRouter",
        obj
      );
      if (res.status == 201) {
        this.dataChanged = true;
        this.s("Client " + res.data.action + "d Successfuly");
        if (res.data.current_state == "Active") {
          this.enabled = true;
        } else {
          this.enabled = false;
        }
        // window.location = "/admin/wired_clients";
      } else {
        this.e("Error Occured");
      }
      this.isWorking = false;
    },
    handleBeforeChange() {
      return new Promise((resolve) => {
        this.$Modal.confirm({
          title: "Confirm Action",
          content:
            "Are You Sure you want to " +
            (this.enabled ? "Disable Client" : "Enable Client"),
          onOk: () => {
            this.CallApiToDisable();
          },
        });
      });
    },
    showStas() {
      this.statsModal = true;
    },
  },
};
</script>