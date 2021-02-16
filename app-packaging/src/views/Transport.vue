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

        <div class="col-sm-12 col-md-5">
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

        <div class="col-sm-12 col-md-7">
          <table class="table table-hover">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Invoice</th>
                <th scope="col">Ship</th>
                <th scope="col">Company</th>
                <th scope="col">Market</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in transports" :key="index">
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ item.invoice }}</td>
                <td>{{ item.ship_from }} - {{ item.ship_to }}</td>
                <td>{{ item.ship_company }}</td>
                <td>{{ item.market }}</td>
                <td>
                  <img
                    class="qrCodeBtn m-1"
                    src="@/assets/images/icons/qr-code.png"
                    alt="QR Code"
                    @click="generateTransportQR(item.id)"
                  />
                  <button
                    type="button"
                    class="m-1 btn btn-sm btn-outline-info"
                    @click="openModalTransport(item, index)"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    class="m-1 btn btn-sm btn-outline-danger"
                    @click="deleteTransport(item.id, index)"
                  >
                    <span class="deleteText">x</span>
                  </button>
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
            <h5 class="modal-title">{{ transportForm.title }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="container-fluid">
              <label class="col-form-label">
                You have selected <b>{{ selectedPacks.length }}</b> package(s).
              </label>
              <div class="row">
                <div class="form-group col-md-12 col-sm-12">
                  <label for="invoice" class="col-form-label">Invoice:</label>
                  <input
                    type="text"
                    v-model="transport.invoice"
                    id="invoice"
                    class="form-control"
                  />
                </div>
                <div class="form-group col-md-6 col-sm-12">
                  <label for="ship_from" class="col-form-label">Ship From:</label>
                  <input
                    type="text"
                    v-model="transport.ship_from"
                    id="ship_from"
                    class="form-control"
                  />
                </div>
                <div class="form-group col-md-6 col-sm-12">
                  <label for="ship_to" class="col-form-label">Ship To:</label>
                  <input
                    type="text"
                    v-model="transport.ship_to"
                    id="ship_to"
                    class="form-control"
                  />
                </div>
                <div class="form-group col-md-6 col-sm-12">
                  <label for="ship_company" class="col-form-label">Ship Company:</label>
                  <input
                    type="text"
                    v-model="transport.ship_company"
                    id="ship_company"
                    class="form-control"
                  />
                </div>
                <div class="form-group col-md-6 col-sm-12">
                  <label for="market" class="col-form-label">Market:</label>
                  <input
                    type="text"
                    v-model="transport.market"
                    id="market"
                    class="form-control"
                  />
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Close
            </button>
            <button type="button" class="btn btn-primary" @click="saveTransport">
              Save
            </button>
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
      transportForm: {
        title: null,
        add: null,
      },
      packs: [],
      transports: [],
      selectedPacks: [],
      transport: {
        invoice: null,
        ship_from: null,
        ship_to: null,
        ship_company: null,
        market: null,
      },
    };
  },
  methods: {
    openModalTransport(data, index) {
      this.index = index;
      if (data === false) {
        this.transportForm.title = "New Transport";
        this.transportForm.add = true;
        /** reset transport data to create a new item */
        this.transport = {
          invoice: null,
          ship_from: null,
          ship_to: null,
          ship_company: null,
          market: null,
        };
      } else {
        /** update obj with clicked row */
        this.transportForm.title = "Manage Transport";
        this.transportForm.add = false;
        this.transport = data;
      }
      $("#transportForm").modal("show");
    },
    saveTransport() {
      let err = false;
      if (this.transport.invoice == null) {
        helper.showWarning("Please enter invoice.");
        err = true;
      }
      if (this.transport.ship_from == null) {
        helper.showWarning("Please enter Ship From.");
        err = true;
      }
      if (this.transport.ship_to == null) {
        helper.showWarning("Please enter Ship To.");
        err = true;
      }
      if (this.transport.ship_company == null) {
        helper.showWarning("Please enter Ship Company.");
        err = true;
      }
      if (this.transport.market == null) {
        helper.showWarning("Please enter Market.");
        err = true;
      }

      if (err) {
        return;
      }

      let obj = {
        invoice: this.transport.invoice,
        ship_from: this.transport.ship_from,
        ship_to: this.transport.ship_to,
        ship_company: this.transport.ship_company,
        market: this.transport.market,
        user_id: this.user.id,
      };

      let action = null;
      if (this.transportForm.add) {
        action = "add-transport-and-packs";
        obj.packs = this.selectedPacks;
      } else {
        obj.id = this.transport.id;
        action = "update";
      }

      helper.toggleLoadingScreen(true);
      this.$axios
        .post(`packaging/ship/${action}`, obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            if (this.transportForm.add) {
              this.transports.push(result.data);
              this.getPackages();
              $("#transportForm").modal("hide");
            } else {
              this.transports[this.index] = result.data;
            }
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    deleteTransport(id, index) {
      if (!confirm("Delete Transport?")) {
        return;
      }

      let obj = {
        id: id,
        user_id: this.user.id,
      };

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("packaging/ship/delete", obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            helper.showSuccess(result.message);
            this.transports.splice(index, 1);
            this.getPackages();
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    getTransports() {
      helper.toggleLoadingScreen(true);
      this.$axios
        .post("packaging/ship/all-new", { user_id: this.user.id })
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.transports = result.data;
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    getPackages() {
      this.selectedPacks = [];
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
    generateTransportQR(data) {
      let qrText = helper.prepareQR(`transport-${data}`);
      this.showQRCode(qrText);
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
    this.getTransports();
  },
};
</script>
