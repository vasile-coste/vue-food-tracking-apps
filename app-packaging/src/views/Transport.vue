<template>
  <div class="transport">
    <NavBar location="transport" />

    <!-- rest of the page here -->
    <div class="headerMenu">
      <div class="container">
        <h2 class="headerMenu-title">
          <img src="@/assets/images/icons/create-transport-round.png" alt="" />Transport
        </h2>
      </div>
    </div>

    <!-- Content -->
    <div class="container">
      <div class="row">
        <div class="col-sm-12 col-md-12 sticky">
          <button
            v-if="selectedPacks.length > 0"
            class="btn btn-dark m-1"
            type="button"
            @click="openModalTransport(false, null)"
          >
            New Transport
          </button>
        </div>

        <div class="col-sm-12 col-md-6">
          <table class="table table-hover">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Package</th>
                <th scope="col">Prods</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in packs" :key="index">
                <th scope="row">
                  <input type="checkbox" v-bind:value="item.id" v-model="selectedPacks" />
                </th>
                <td>{{ item.pack_name }}</td>
                <td>{{ item.prod_num }}</td>
                <td>
                  <img
                    class="qrCodeBtn m-1"
                    src="@/assets/images/icons/qr-code.png"
                    alt="QR Code"
                    @click="generatePackQR(item.id)"
                  />
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="col-sm-12 col-md-6"></div>
      </div>
    </div>
    <!-- modal -->
    <div
      class="modal fade"
      id="transportForm"
      tabindex="-1"
      role="dialog"
      aria-labelledby="transportFormModal"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Manage Transport</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="container-fluid">
              <label class="col-form-label">
                You have selected <b>{{ selectedPacks.length }}</b> package(s).
              </label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Close
            </button>
            <button type="button" class="btn btn-primary">Save</button>
          </div>
        </div>
      </div>
    </div>
    <!-- qr window start -->
    <div class="qrArea">
      <div class="qrBox">
        <canvas id="qr-aread"></canvas>
        <div class="qrAction">
          <button type="button" class="btn btn-secondary" @click="printQr">Print</button>
        </div>
      </div>
    </div>
    <!-- qr window end -->
  </div>
</template>

<script>
import NavBar from "@/components/NavBar.vue";
import helper from "@/js/helper";
import $ from "jquery";
import QRCode from "qrcode";
export default {
  name: "Transport",
  components: {
    NavBar,
  },
  data() {
    return {
      user: this.$session.get("user"),
      index: null,
      packs: [],
      selectedPacks: [],
    };
  },
  methods: {
    openModalTransport(data, index) {
      this.index = index;
      if (data === false) {
        console.log(data, index);
      }
      $("#transportForm").modal("show");
    },
    getPackages() {
      helper.toggleLoadingScreen(true);
      this.$axios
        .post("packaging/packs/all-new", { user_id: this.user.id })
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.packs = result.data;
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    generatePackQR(data) {
      let qrText = helper.prepareQR(`package-${data}`);
      this.showQRCode(qrText);
    },
    showQRCode(text) {
      $(".qrArea").fadeIn().css("display", "flex");
      let canvas = document.getElementById("qr-aread");
      QRCode.toCanvas(canvas, text, () => {});
    },
    printQr() {
      $(".qrArea").fadeOut();
    },
  },
  mounted() {
    this.getPackages();
  },
};
</script>
