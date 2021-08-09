<template>
  <div>
    <button
      type="button"
      @click="InitiatePurchase(sel_package)"
      class="btn btn-white btn-block text-dark font-weight-bold animate-up-2"
      tabindex="0"
      data-toggle="modal"
    >
      <span class="fas fa-money-check-alt"></span> Purchase
    </button>
    <Modal v-model="purchaseModal" :closable="false" :mask-closable="false" :styles="{top: '20px'}">
      <div slot="header">
        <h3 class="text-center">Purchase {{selected_package.name}} Subscription</h3>
      </div>
      <div class="row justify-content-center">
        <div class="col-sm-9">
          <div class="input-group">
            <input
              class="input--style-2 form-control"
              type="number"
              placeholder="Enter Your Mpesa  Phone Number: "
              v-model="data.phone_number"
            />
          </div>
          <small class="font-weight-bold text-info">
            Enter Your Phone Number In the Format
            <strong class="text-danger">07XXXXXXXX</strong>
            <small>{{data.phone_number}}</small>
          </small>
        </div>
      </div>
      <div class="row justify-content-center" v-if="HelpDiv">
        <div class="col-sm-9">
          <h3 class="text-info">How To Get Connected</h3>
          <p class="font-weight-bold">After confirming your Payment proceed as follows;</p>
          <ol>
            <li>Get Your Receipt from Number from Mpesa Payment Message</li>
            <li>Connect to our Hotspot</li>
            <li>Click on sign in to network</li>
            <li>Enter your Phone number in 254XXXXXXXXX format</li>
            <li>Enter your MPESA TRANSACTION RECEIPT NUMBER as password</li>
            <li>Enjoy</li>
          </ol>
          <Button type="primary" size="small" @click="HelpDiv=false">Understood</Button>
          <Button type="info" size="small">Need Help</Button>
        </div>
      </div>
      <div slot="footer">
        <Button type="error" :disabled="isSaving" @click="purchaseModal=false">Cancel</Button>
        <Button
          type="success"
          :disabled="isSaving"
          :loading="isSaving"
          @click="confirmPurchase"
        >{{isSaving ? 'Purchasing...' : 'Proceed'}}</Button>
      </div>
    </Modal>
  </div>
</template>
<script>
export default {
  data() {
    return {
      data: {
        phone_number: "",
        id: ""
      },
      HelpDiv: false,
      purchaseModal: false,
      isSaving: false,
      selected_package: ""
    };
  },
  props: {
    sel_package: Object
  },
  methods: {
    InitiatePurchase(sub_package) {
      this.data.id = sub_package.id;
      this.selected_package = sub_package;
      this.purchaseModal = true;
    },
    async confirmPurchase() {
      this.isSaving = true;
      if (this.data.phone_number == "") {
        this.isSaving = false;
        return this.e("Phone Number is required");
      }
      this.start();
      const res = await this.callApi("post", "/customer/purchase", this.data);
      if (res.status == 200) {
        this.stop();
        if (res.data.ResponseCode == 0) {
          this.s("Purchase Initiated,check your phone");
          this.HelpDiv = true;
        } else {
          this.i("Problem Encountered During Purchase. Please Try Again");
        }
        this.data.id = "";
        this.isSaving = false;
      } else {
        this.showError();
        this.isSaving = false;
        if (res.status == 422) {
          for (let i in res.data.errors) {
            this.e(res.data.errors[i][0]);
          }
        } else {
          this.swr("Something Went Wrong");
        }
      }
    }
  }
};
</script>