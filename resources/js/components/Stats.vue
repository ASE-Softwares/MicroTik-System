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
      :width="95"
      :styles="{ top: '20px' }"
      footer-hide
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

      <div class="card">
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
                  <br />
                  <small> {{ client.comment }}</small>
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
          <div class="row mt-2">
            <div class="col-md-3" v-if="moreData.client != null">
              <div class="card">
                <div class="card-header">Client Information</div>
                <div class="card-body">
                  <div>
                    <span class="text-info font-weight-bold h6">
                      <i class="fa fa-user" aria-hidden="true"></i>
                      {{ moreData.client.user.name }}</span
                    >
                    <br />
                    <span class="text-info font-weight-bold h6 brkln">
                      <i class="fa fa-envelope" aria-hidden="true"></i>
                      <a
                        target="_blank"
                        :href="'mailto:' + moreData.client.user.email"
                      >
                        {{ moreData.client.user.email }}</a
                      >
                    </span>
                    <br />
                    <span class="text-info font-weight-bold h6">
                      <i class="fa fa-map-marker" aria-hidden="true"></i>
                      {{ moreData.client.location }}</span
                    >
                    <br />
                    <p class="text-dark">Subscription</p>
                    <span class="text-info font-weight-bold h6">
                      {{ moreData.client.package.name }}</span
                    >
                  </div>
                  <package-switcher :client="moreData.client" />
                </div>
              </div>
            </div>
            <div class="col-md-3" v-else>
              <div v-if="AppReady">
                <update-profile
                  v-if="moreData.queue != null"
                  v-on:client_updated="UseNewData"
                  :client="client"
                />
              </div>
            </div>
            <div class="col-md-9" v-if="moreData.queue != null">
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
    </Modal>
  </div>
</template>
<script>
import PackageSwitcher from "./PackageSwitcher.vue";
import UpdateProfile from "./UpdateDetails.vue";
export default {
  components: {
    UpdateProfile,
    PackageSwitcher,
  },
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

  methods: {
    UseNewData(data) {
      this.moreData.client = data;
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
        if (!this.moreData.queue == null) {
          this.startRealTime();
        }
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
<style scoped>
.brkln {
  overflow: auto;
  white-space: nowrap;
}
</style>