<template>
  <div class="container-fluid">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">All Packages</h6>
      <button class="float-right btn btn-info btn-sm" @click="AddModal=true">Add Package</button>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table
          class="display nowrap table table-hover table-striped table-bordered dataTable"
          cellspacing="0"
          width="100%"
          role="grid"
          aria-describedby="example23_info"
          style="width: 100%;"
        >
          <thead>
            <tr>
              <th>Name</th>
              <th>Amount</th>
              <th>Download</th>
              <th>Upload</th>
              <th>RX/TX</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(subscription, s) in subscriptions" :key="s">
              <td>{{subscription.name}}</td>
              <td>{{subscription.amount}}</td>
              <td>{{subscription.download}}</td>
              <td>{{subscription.upload}}</td>
              <td>{{subscription.download+ '/' +subscription.upload}}</td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Modal
      v-model="AddModal"
      :mask-closable="false"
      :closable="true"
      :styles="{top:'20px'}"
      :width="65"
      title="Add a new wired client subscription"
    >
      <div class="ml-1">
        <div class="form-group">
          <label for="name">Name</label>
          <input
            type="text"
            v-model="data.name"
            id="name"
            class="form-control"
            placeholder
            required
          />
        </div>
        <div class="form-group">
          <label for="amount">Amount</label>
          <input
            type="text"
            v-model="data.amount"
            id="amount"
            class="form-control"
            placeholder
            required
          />
        </div>
        <div class="row">
          <div class="col-md-4 form-group" v-if="!unlimitedUpload">
            <label for="uploadUnit">Upload Unit</label>
            <Select v-model="upload.unit" id="uploadUnit" transfer required>
              <Option value="k">kbps</Option>
              <Option value="M">Mbps</Option>
              <Option value="G">Gps</Option>
            </Select>
            {{upload.speed}} {{upload.unit}}
          </div>
          <div class="col-md-5 form-group" v-if="!unlimitedUpload">
            <label for="downloadSpeed">Upload Speed</label>
            <input
              type="number"
              v-model="upload.speed"
              id="uploadSpeed"
              class="form-control"
              placeholder
              required
            />
          </div>
          <div class="col-md-3 form-group" :class="unlimitedUpload ? 'col-md-12' : '' ">
            <div class="form-check form-check-inline">
              <label class="form-check-label">
                <input
                  class="form-check-input"
                  type="checkbox"
                  v-model="unlimitedUpload"
                  :value="true"
                />Unlimited Upload
              </label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 form-group" v-if="!unlimitedDownload">
            <label for="downloadUnit">Download Unit</label>
            <Select v-model="download.unit" id="downloadUnit" transfer required>
              <Option value="k">kbps</Option>
              <Option value="M">Mbps</Option>
              <Option value="G">Gps</Option>
            </Select>
            {{download.speed}} {{download.unit}}
          </div>
          <div class="col-md-5 form-group" v-if="!unlimitedDownload">
            <label for="downloadSpeed">Download Speed</label>
            <input
              type="number"
              v-model="download.speed"
              id="downloadSpeed"
              class="form-control"
              placeholder
              required
            />
          </div>
          <div class="col-md-3 form-group" :class="unlimitedDownload ? 'col-md-12' : '' ">
            <div class="form-check form-check-inline">
              <label class="form-check-label">
                <input
                  class="form-check-input"
                  type="checkbox"
                  v-model="unlimitedDownload"
                  :value="true"
                />Unlimited Download
              </label>
            </div>
          </div>
        </div>
      </div>
      <div slot="footer">
        <Button @click="submitForm">Submit</Button>
      </div>
    </Modal>
  </div>
</template>
<script>
export default {
  data() {
    return {
      AddModal: false,
      data: {
        name: "",
        amount: "",
        download: "",
        upload: ""
      },
      upload: {
        unit: "",
        speed: ""
      },
      download: {
        unit: "",
        speed: ""
      },
      unlimitedUpload: false,
      unlimitedDownload: false,
      subscriptions: []
    };
  },
  methods: {
    async submitForm() {
      if (this.unlimitedUpload) {
        this.data.upload = "unlimited";
      } else {
        this.data.upload = this.upload.speed + this.upload.unit;
      }
      if (this.unlimitedDownload) {
        this.data.download = "unlimited";
      } else {
        this.data.download = this.download.speed + this.download.unit;
      }
      const res = await this.callApi("post", "/admin/subscriptions", this.data);
      if (res.status == 201) {
        let obj = {
          name: "",
          amount: "",
          download: "",
          upload: ""
        };
        this.data = obj;
        this.s("Package Addded");
        this.subscriptions.unshift(res.data);
      } else {
        if (res.status == 422) {
          this.$validation(res);
        } else {
          this.swr();
        }
      }
    },
    async getSubscriptions() {
      const res = await this.callApi("get", "/admin/all_subs");
      if (res.status == 200) {
        this.subscriptions = res.data;
      }
    }
  },
  created() {
    this.getSubscriptions();
  }
};
</script>