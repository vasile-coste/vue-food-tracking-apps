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
              <tr v-for="(item, index) in packs.items" :key="index">
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
                <th scope="col">Packs</th>
                <th scope="col">Ship</th>
                <th scope="col">Company</th>
                <th scope="col">Market</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in transports.items" :key="index">
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ item.invoice }}</td>
                <td>{{ item.pack_num }}</td>
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
          <div class="modal-body" id="loadMoreOnScroll">
            <div class="container-fluid" id="loadMoreOnScrollResult">
              <label class="col-form-label" v-if="transportForm.add">
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
                <div v-if="!transportForm.add" class="form-group col-md-6 col-sm-12">
                  <button type="button" class="btn btn-primary" @click="saveTransport">
                    Update
                  </button>
                </div>
              </div>
              <div class="row" v-if="!transportForm.add">
                <table class="col-sm-12 col-md-12 table table-hover">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Package</th>
                      <th scope="col">Prods</th>
                      <th scope="col"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in transportPacks.items" :key="index">
                      <th scope="row">{{ index + 1 }}</th>
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
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Close
            </button>
            <button
              v-if="transportForm.add"
              type="button"
              class="btn btn-primary"
              @click="saveTransport"
            >
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
      packs: {
        total: 0,
        lastPage: 0,
        currentPage: 0,
        items: [],
      },
      transports: {
        total: 0,
        lastPage: 0,
        currentPage: 0,
        items: [],
      },
      selectedPacks: [],
      transport: {
        invoice: null,
        ship_from: null,
        ship_to: null,
        ship_company: null,
        market: null,
      },
      transportPacks: {
        total: 0,
        lastPage: 0,
        currentPage: 0,
        items: [],
      },
      onScrollRunning: false,
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
        this.getPackagesInTransport(true);
      }
      $("#transportForm").modal("show");
    },
    saveTransport() {
      let err = false;
      let obj = {
        invoice: this.transport.invoice,
        ship_from: this.transport.ship_from,
        ship_to: this.transport.ship_to,
        ship_company: this.transport.ship_company,
        market: this.transport.market,
        user_id: this.user.id,
      };

      if (obj.invoice == null) {
        helper.showWarning("Please enter invoice.");
        err = true;
      }
      if (obj.ship_from == null) {
        helper.showWarning("Please enter Ship From.");
        err = true;
      }
      if (obj.ship_to == null) {
        helper.showWarning("Please enter Ship To.");
        err = true;
      }
      if (obj.ship_company == null) {
        helper.showWarning("Please enter Ship Company.");
        err = true;
      }
      if (obj.market == null) {
        helper.showWarning("Please enter Market.");
        err = true;
      }

      if (err) {
        return;
      }

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
              this.getTransports(true);
              this.getPackages(true);
              $("#transportForm").modal("hide");
            } else {
              this.transports.items[this.index] = result.data;
            }
            helper.showSuccess(result.message);
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
            this.transports.items.splice(index, 1);
            this.getPackages(true);
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    getTransports(reload = false) {
      if (reload == false) {
        if (
          this.transports.currentPage > 0 &&
          this.transports.lastPage == this.transports.currentPage
        ) {
          return;
        }
      }
      let obj = {
        user_id: this.user.id,
        page: reload ? 1 : ++this.transports.currentPage,
        status: "created",
      };

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("packaging/ship/all", obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            if (reload) {
              this.transports = result.data;
            } else {
              this.transports = {
                total: result.data.total,
                lastPage: result.data.lastPage,
                currentPage: result.data.currentPage,
                items: this.transports.items.concat(result.data.items),
              };
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
    getPackages(reload = false) {
      this.selectedPacks = [];
      if (reload == false) {
        if (this.packs.currentPage > 0 && this.packs.lastPage == this.packs.currentPage) {
          return;
        }
      }
      let obj = {
        user_id: this.user.id,
        page: reload ? 1 : ++this.packs.currentPage,
        transport_id: 0,
      };

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("packaging/packs/all", obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            if (reload) {
              this.packs = result.data;
            } else {
              this.packs = {
                total: result.data.total,
                lastPage: result.data.lastPage,
                currentPage: result.data.currentPage,
                items: this.packs.items.concat(result.data.items),
              };
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
    getPackagesInTransport(reload = false) {
      if (reload == false) {
        if (
          this.transportPacks.currentPage > 0 &&
          this.transportPacks.lastPage == this.transportPacks.currentPage
        ) {
          return;
        }
      }
      let obj = {
        user_id: this.user.id,
        page: reload ? 1 : ++this.transportPacks.currentPage,
        transport_id: this.transport.id,
      };

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("packaging/packs/all", obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            if (reload) {
              this.transportPacks = result.data;
            } else {
              this.transportPacks = {
                total: result.data.total,
                lastPage: result.data.lastPage,
                currentPage: result.data.currentPage,
                items: this.transportPacks.items.concat(result.data.items),
              };
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

    window.onscroll = () => {
      let fullHeight = document.documentElement.offsetHeight;
      let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight;

      if (bottomOfWindow >= fullHeight) {
        this.getPackages();
        this.getTransports();
      }
    };

    /** load more data on modal */
    document.getElementById("loadMoreOnScroll").addEventListener("scroll", () => {
      if (
        !this.onScrollRunning &&
        document.getElementById("loadMoreOnScroll").offsetHeight +
          document.getElementById("loadMoreOnScroll").scrollTop >=
          document.getElementById("loadMoreOnScrollResult").offsetHeight
      ) {
        this.onScrollRunning = true;
        this.getPackagesInTransport();
      }
    });
  },
};
</script>
