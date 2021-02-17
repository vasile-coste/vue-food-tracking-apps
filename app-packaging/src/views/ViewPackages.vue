<template>
  <div class="view-packages">
    <NavBar location="view-packages" />

    <!-- rest of the page here -->
    <div class="headerMenu">
      <div class="container">
        <h2 class="headerMenu-title">
          <img src="@/assets/images/icons/view-packages-round.png" alt="" />View Packages
        </h2>
      </div>
    </div>

    <!-- Content -->
    <div class="container">
      <div class="row">
        <div class="col-sm-4 col-md-4 text-center">
          <div class="bubble-title">Total</div>
          <div class="bubble-value">
            <span>{{ total }}</span>
          </div>
        </div>
        <div class="col-sm-4 col-md-4 text-center">
          <div class="bubble-title">Delivered</div>
          <div class="bubble-value">
            <span>{{ packs_done }}</span>
          </div>
        </div>
        <div class="col-sm-4 col-md-4 text-center">
          <div class="bubble-title">New</div>
          <div class="bubble-value">
            <span>{{ packs_new }}</span>
          </div>
        </div>

        <div class="col-sm-12 col-md-12 ml-1 mr-1" v-for="(item, index) in packs" :key="index">
          <div class="row rowItem">
            <div class="col-sm-4 col-md-4 rowItem-text">
              <a href="#" @click="showPackContent(item.id, item.pack_name, $event)">{{
                item.pack_name
              }}</a>
            </div>
            <div class="col-sm-4 col-md-4 rowItem-text">
              {{ item.prod_num }} product(s)
            </div>
            <div class="col-sm-4 col-md-4 text-right">
              <img
                class="qrCodeBtn m-1"
                src="@/assets/images/icons/qr-code.png"
                alt="QR Code"
                @click="generatePackQR(item.id)"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- modals -->
    <div
      class="modal fade"
      id="packageFormContent"
      tabindex="-1"
      role="dialog"
      aria-labelledby="packageFormContentModal"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Package: {{ pack_name }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="loadMoreOnScroll">
            <div class="container-fluid" id="loadMoreOnScrollResult">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Product</th>
                    <th scope="col">Weight</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in packProducts.items" :key="index">
                    <th scope="row">{{ index + 1 }}</th>
                    <td>{{ item.product_name }}</td>
                    <td>{{ item.product_weight }}</td>
                    <td>
                      <img
                        class="qrCodeBtn m-1"
                        src="@/assets/images/icons/qr-code.png"
                        alt="QR Code"
                        @click="generateProductQR(item.id)"
                      />
                    </td>
                  </tr>
                </tbody>
              </table>
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
  name: "ViewPackages",
  components: {
    NavBar,
  },
  data() {
    return {
      user: this.$session.get("user"),
      packs: [],
      packProducts: {
        total: 0,
        lastPage: 0,
        currentPage: 0,
        items: [],
      },
      pack_name: null,
      pack_id: null,
      total: 0,
      lastPage: 0,
      currentPage: 0,
      packs_new: 0,
      packs_done: 0,
      onScrollRunning: false,
    };
  },
  methods: {
    showPackContent(id, name, event) {
      event.preventDefault();

      this.pack_name = name;
      this.pack_id = id;
      this.packProducts.items = [];
      this.getPackProducts(true);
      $("#packageFormContent").modal("show");
    },
    generatePackQR(data) {
      let qrText = helper.prepareQR(`package-${data}`);
      this.showQRCode(qrText);
    },
    generateProductQR(data) {
      let qrText = helper.prepareQR(`product-${data}`);
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
    getPacks() {
      if (this.currentPage > 0 && this.lastPage == this.currentPage) {
        return;
      }
      let obj = {
        user_id: this.user.id,
        page: ++this.currentPage,
      };

      if (this.currentPage == 1) {
        helper.toggleLoadingScreen(true);
      }
      this.$axios
        .post("packaging/packs/all", obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (this.currentPage == 1) {
            helper.toggleLoadingScreen(false);
          }

          if (result.success) {
            result.data.items.forEach((item) => {
              this.packs.push(item);
            });
            this.total = result.data.total;
            this.lastPage = result.data.lastPage;
            this.currentPage = result.data.currentPage;
            this.packs_new = result.data.packs_new;
            this.packs_done = result.data.packs_done;
          } else {
            helper.showWarning(result.message);
          }
        })
        .catch(() => {
          if (this.currentPage == 1) {
            helper.toggleLoadingScreen(false);
          }
        });
    },
    getPackProducts(reload = false) {
      if (reload == false) {
        if (
          this.packProducts.currentPage > 0 &&
          this.packProducts.lastPage == this.packProducts.currentPage
        ) {
          return;
        }
      }

      let obj = {
        user_id: this.user.id,
        page: reload ? 1 : ++this.products.currentPage,
        pack_id: this.pack_id,
      };

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("packaging/product/all", obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            if (reload) {
              this.packProducts = result.data;
            } else {
              this.packProducts = {
                lastPage: result.data.lastPage,
                currentPage: result.data.currentPage,
                items: this.packProducts.items.concat(result.data.items),
              };
            }
          } else {
            helper.showWarning(result.message);
          }

          helper.toggleLoadingScreen(false);
          this.onScrollRunning = false;
        })
        .catch(() => {
          this.onScrollRunning = false;
          helper.toggleLoadingScreen(false);
        });
    },
  },
  mounted() {
    this.getPacks();

    window.onscroll = () => {
      let fullHeight = document.documentElement.offsetHeight;
      let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight;

      if (bottomOfWindow >= fullHeight) {
        this.getPacks();
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
        this.getPackProducts();
      }
    });
  },
};
</script>
