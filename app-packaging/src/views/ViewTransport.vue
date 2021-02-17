<template>
  <div class="view-transport">
    <NavBar location="view-transport" />

    <!-- rest of the page here -->
    <div class="headerMenu">
      <div class="container">
        <h2 class="headerMenu-title">
          <img src="@/assets/images/icons/view-transport-round.png" alt="" />View
          Transport
        </h2>
      </div>
    </div>

    <!-- Content -->
    <div class="container">
      <div class="row">
        <div class="col-sm-3 col-md-3 text-center">
          <div class="bubble-title">Total</div>
          <div class="bubble-value">
            <span>{{ transports.total }}</span>
          </div>
        </div>
        <div class="col-sm-3 col-md-3 text-center">
          <div class="bubble-title">New</div>
          <div class="bubble-value">
            <span>{{ transports.ship_created }}</span>
          </div>
        </div>
        <div class="col-sm-3 col-md-3 text-center">
          <div class="bubble-title">In Transit</div>
          <div class="bubble-value">
            <span>{{ transports.ship_started }}</span>
          </div>
        </div>
        <div class="col-sm-3 col-md-3 text-center">
          <div class="bubble-title">Completed</div>
          <div class="bubble-value">
            <span>{{ transports.ship_completed }}</span>
          </div>
        </div>

        <div class="col-sm-12 col-md-12 overflow-auto">
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
              <td>
                <a href="#" @click="showContent(item, $event)">
                  {{ item.invoice }}
                </a>
              </td>
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
              </td>
            </tr>
          </tbody>
        </table>
        </div>
      </div>
    </div>

    <!-- modals -->

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
            <h5 class="modal-title">View Trasnport</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="loadMoreOnScroll">
            <div class="container-fluid" id="loadMoreOnScrollResult">
              <div class="row">
                <div class="form-group col-md-12 col-sm-12">
                  <label class="col-form-label">Invoice:</label>
                  <div class="form-control">{{ transport.invoice }}</div>
                </div>
                <div class="form-group col-md-6 col-sm-12">
                  <label class="col-form-label">Ship From:</label>
                  <div class="form-control">{{ transport.ship_from }}</div>
                </div>
                <div class="form-group col-md-6 col-sm-12">
                  <label class="col-form-label">Ship To:</label>
                  <div class="form-control">{{ transport.ship_to }}</div>
                </div>
                <div class="form-group col-md-6 col-sm-12">
                  <label class="col-form-label">Ship Company:</label>
                  <div class="form-control">{{ transport.ship_company }}</div>
                </div>
                <div class="form-group col-md-6 col-sm-12">
                  <label class="col-form-label">Market:</label>
                  <div class="form-control">{{ transport.market }}</div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12 col-md-12">
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
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Close
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
  name: "ViewTransport",
  components: {
    NavBar,
  },
  data() {
    return {
      user: this.$session.get("user"),
      transports: {
        ship_created: 0,
        ship_started: 0,
        ship_completed: 0,
        total: 0,
        lastPage: 0,
        currentPage: 0,
        items: [],
      },
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
    showContent(transport, event) {
      event.preventDefault();
      this.transportPacks.items = [];
      this.transport = transport;
      this.getPackagesInTransport(true);
      $("#transportForm").modal("show");
    },
    getTransports() {
      if (
        this.transports.currentPage > 0 &&
        this.transports.lastPage == this.transports.currentPage
      ) {
        return;
      }
      let obj = {
        user_id: this.user.id,
        page: ++this.transports.currentPage,
        status: "created",
      };

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("packaging/ship/all", obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.transports = {
              ship_created: result.data.ship_created,
              ship_started: result.data.ship_started,
              ship_completed: result.data.ship_completed,
              total: result.data.total,
              lastPage: result.data.lastPage,
              currentPage: result.data.currentPage,
              items: this.transports.items.concat(result.data.items),
            };
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
    this.getTransports();

    window.onscroll = () => {
      let fullHeight = document.documentElement.offsetHeight;
      let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight;

      if (bottomOfWindow >= fullHeight) {
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
