<template>
  <div class="package">
    <NavBar location="package" />

    <!-- rest of the page here -->
    <div class="headerMenu">
      <div class="container">
        <h2 class="headerMenu-title">
          <img src="@/assets/images/icons/create-package-round.png" alt="" />Package
        </h2>
      </div>
    </div>

    <!-- Content -->
    <div class="container">
      <div class="row">
        <div class="col-sm-12 col-md-12 sticky">
          <button
            type="button"
            class="btn btn-light m-1"
            data-toggle="modal"
            @click="openModalProduct(false, null)"
          >
            New Product
          </button>

          <button
            v-if="selectedProducts.length > 0"
            class="btn btn-dark m-1"
            type="button"
            @click="createPackageModal"
          >
            Add To Package
          </button>
        </div>

        <div class="col-sm-12 col-md-6">
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
              <tr v-for="(item, index) in products.items" :key="index">
                <th scope="row">
                  <input
                    type="checkbox"
                    v-bind:value="item.id"
                    v-model="selectedProducts"
                  />
                </th>
                <td>{{ item.product_name }}</td>
                <td>{{ item.product_weight }}</td>
                <td>
                  <img
                    class="qrCodeBtn m-1"
                    src="@/assets/images/icons/qr-code.png"
                    alt="QR Code"
                    @click="generateProductQR(item.id)"
                  />
                  <button
                    type="button"
                    class="m-1 btn btn-sm btn-outline-info"
                    @click="openModalProduct(item, index)"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    class="m-1 btn btn-sm btn-outline-danger"
                    @click="deleteProduct(item.id, index)"
                  >
                    <span class="deleteText">x</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
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
              <tr v-for="(item, index) in packs.items" :key="index">
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
                  <button
                    type="button"
                    class="m-1 btn btn-sm btn-outline-info"
                    @click="openModalPackContent(item, index)"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    class="m-1 btn btn-sm btn-outline-danger"
                    @click="deletePack(item.id, index)"
                  >
                    <span class="deleteText">x</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Modals -->
      <div
        class="modal fade"
        id="productForm"
        tabindex="-1"
        role="dialog"
        aria-labelledby="productFormModal"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ productForm.title }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="container-fluid">
                <div class="form-group" v-if="productForm.add">
                  <label for="field_id" class="col-form-label">Field:</label>
                  <select
                    type="text"
                    class="form-control"
                    id="field_id"
                    v-model="product.field"
                    @change="changeGetField"
                  >
                    <option
                      v-for="(item, index) in fields"
                      :key="index"
                      v-bind:value="item"
                    >
                      {{ item.field_name }}
                    </option>
                  </select>
                  <small class="form-text text-muted">
                    Once a product is created, the field cannot be changed, but the
                    product can be deleted.
                  </small>
                </div>
                <div class="form-check" v-if="productForm.add">
                  <input
                    type="checkbox"
                    v-model="product.setFieldAsCompleted"
                    id="setFieldAsCompleted"
                    value="true"
                    class="form-check-input"
                  />
                  <label for="setFieldAsCompleted" class="form-check-label">
                    Set Field as completed
                  </label>
                </div>
                <small class="form-text text-muted" v-if="productForm.add">
                  By setting the field as completed, you won't be able to select it again
                  to create products.
                </small>
                <div class="form-group">
                  <label for="product_name" class="col-form-label">Product Name:</label>
                  <input
                    type="text"
                    v-model="product.product_name"
                    id="product_name"
                    class="form-control"
                  />
                </div>
                <div class="form-group">
                  <label for="product_weight" class="col-form-label"
                    >Product Weight:</label
                  >
                  <input
                    type="text"
                    v-model="product.product_weight"
                    id="product_weight"
                    class="form-control"
                  />
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">
                Close
              </button>
              <button type="button" class="btn btn-primary" @click="saveProduct">
                Save
              </button>
            </div>
          </div>
        </div>
      </div>

      <div
        class="modal fade"
        id="packageForm"
        tabindex="-1"
        role="dialog"
        aria-labelledby="packageFormModal"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Manage Package</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="container-fluid">
                <label class="col-form-label">
                  You have selected <b>{{ selectedProducts.length }}</b> product(s).
                  Please enter the name of the new package or select an existing one
                </label>
                <div class="form-group" v-if="choosePack">
                  <label for="pack_id">Choose Package:</label>
                  <select
                    class="form-control col-md-7 col-sm-7"
                    id="pack_id"
                    v-model="pack.pack_id"
                    @change="showCreatePack"
                  >
                    <option
                      v-for="(item, index) in selectPacks"
                      :key="index"
                      v-bind:value="item.id"
                    >
                      {{ item.pack_name }}
                    </option>
                  </select>
                </div>
                <div class="form-group" v-else>
                  <label for="pack_name">New Package:</label>
                  <div class="row m-0">
                    <div class="col-md-7 col-sm-7 p-0">
                      <input
                        type="text"
                        v-model="pack.pack_name"
                        id="pack_name"
                        class="form-control"
                      />
                    </div>
                    <div class="col-md-5 col-sm-5">
                      <button type="button" @click="cancelNewPack" class="btn btn-light">
                        Cancel
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">
                Close
              </button>
              <button type="button" class="btn btn-primary" @click="savePackage">
                Save
              </button>
            </div>
          </div>
        </div>
      </div>
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
              <h5 class="modal-title">Package Content</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" id="loadMoreOnScroll">
              <div class="container-fluid" id="loadMoreOnScrollResult">
                <div class="form-group">
                  <label for="pack_name">Package Name:</label>
                  <div class="row m-0">
                    <div class="col-md-7 col-sm-7 p-0">
                      <input
                        type="text"
                        v-model="currentPack.pack_name"
                        id="pack_name"
                        class="form-control"
                      />
                    </div>
                    <div class="col-md-5 col-sm-5">
                      <button
                        type="button"
                        class="btn btn-primary"
                        @click="updatePackName"
                      >
                        Update
                      </button>
                    </div>
                  </div>
                </div>
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
                        <button
                          type="button"
                          class="m-1 btn btn-sm btn-outline-danger"
                          @click="removeProductFromPack(item.id, index)"
                        >
                          <span class="deleteText">x</span>
                        </button>
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
      <!-- end modals -->
      <!-- qr window start -->
      <div class="qrArea">
        <div class="qrBox">
          <canvas id="qr-aread"></canvas>
          <div class="qrAction">
            <button type="button" class="btn btn-secondary" @click="printQr">
              Print
            </button>
          </div>
        </div>
      </div>
      <!-- qr window end -->
    </div>
  </div>
</template>

<script>
import NavBar from "@/components/NavBar.vue";
import helper from "@/js/helper";
import $ from "jquery";
import QRCode from "qrcode";
export default {
  name: "Package",
  components: {
    NavBar,
  },
  data() {
    return {
      user: this.$session.get("user"),
      index: null,
      productForm: {
        title: null,
        add: null,
      },
      product: {
        id: null,
        user_id: null,
        field: null,
        product_name: null,
        product_weight: null,
        setFieldAsCompleted: false,
      },
      selectedProducts: [],
      fields: [],
      products: {
        total: 0,
        lastPage: 0,
        currentPage: 0,
        items: [],
      },
      choosePack: true,
      selectPacks: [],
      packs: {
        total: 0,
        lastPage: 0,
        currentPage: 0,
        items: [],
      },
      pack: {
        pack_id: null,
        pack_name: null,
      },
      packProducts: {
        total: 0,
        lastPage: 0,
        currentPage: 0,
        items: [],
      },
      currentPack: {
        id: null,
        pack_name: null,
      },
      onScrollRunning: false,
    };
  },
  methods: {
    openModalPackContent(item, index) {
      this.currentPack.id = item.id;
      this.currentPack.pack_name = item.pack_name;
      this.index = index;
      this.getPackProducts(true);
      $("#packageFormContent").modal("show");
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
        pack_id: this.currentPack.id,
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
    updatePackName() {
      if (!this.currentPack.pack_name || this.currentPack.pack_name.length == 0) {
        helper.showWarning("Please enter Package name.");
        return;
      }

      let obj = {
        user_id: this.user.id,
        id: this.currentPack.id,
        pack_name: this.currentPack.pack_name,
      };

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("packaging/packs/update", obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.packs.items[this.index].pack_name = obj.pack_name;
            this.setPacks(this.packs, true);
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
    createPackageModal() {
      $("#packageForm").modal("show");
    },
    showCreatePack() {
      if (this.pack.pack_id == null || this.pack.pack_id != "new") {
        this.choosePack = true;
        this.pack.pack_name = null;
      } else {
        this.choosePack = false;
        let count = this.packs.total + 1;
        this.pack.pack_name = `Pack ${count}`;
      }
    },
    cancelNewPack() {
      this.pack.pack_id = null;
      this.pack.pack_name = null;
      this.choosePack = true;
    },
    savePackage() {
      if (this.pack.pack_id == null) {
        helper.showWarning("Please choose a Package.");
        return;
      }

      if (
        this.pack.pack_id == "new" &&
        (!this.pack.pack_name || this.pack.pack_name.length == 0)
      ) {
        helper.showWarning("Please enter Package name.");
        return;
      }
      let obj = {
        user_id: this.user.id,
        products: this.selectedProducts,
      };
      let urlPart = null;
      if (this.choosePack) {
        obj.id = this.pack.pack_id;
        urlPart = "add-products";
      } else {
        obj.pack_name = this.pack.pack_name;
        urlPart = "add-pack-and-prods";
      }

      helper.toggleLoadingScreen(true);
      this.$axios
        .post(`packaging/packs/${urlPart}`, obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            helper.showSuccess(result.message);
            if (this.choosePack) {
              /** update pack if is in view */
              this.packs.items.map((element) => {
                if (element.id == this.pack.pack_id) {
                  element.prod_num += this.selectedProducts.length;
                }
                return element;
              });
            } else {
              /** get new list of packs */
              this.getPackages(true);
            }
            /** get new list of products */
            this.getProducts(true);
            /** reset values */
            this.selectedProducts = [];
            this.cancelNewPack();

            $("#packageForm").modal("hide");
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
            this.setPacks(result.data, reload);
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    setPacks(newPacks, reload) {
      let defaultSelectPacks = [
        {
          id: null,
          pack_name: null,
        },
        {
          id: "new",
          pack_name: "New package",
        },
      ];
      if (reload) {
        this.selectPacks = defaultSelectPacks.concat(newPacks.items);
        this.packs = newPacks;
      } else {
        this.selectPacks = (this.selectPacks.length > 0
          ? this.selectPacks
          : defaultSelectPacks
        ).concat(newPacks.items);
        this.packs = {
          total: newPacks.total,
          lastPage: newPacks.lastPage,
          currentPage: newPacks.currentPage,
          items: this.packs.items.concat(newPacks.items),
        };
      }
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
    openModalProduct(data, index) {
      if (data === false) {
        /** prepare form for add */
        this.productForm.title = "New Product";
        this.productForm.add = true;
      } else {
        /** prepare form for edit */
        this.productForm.title = "Update Product";
        this.productForm.add = false;
        this.product.id = data.id;
        this.product.product_name = data.product_name;
        this.product.product_weight = data.product_weight;
        this.index = index;
      }
      $("#productForm").modal("show");
    },
    changeGetField() {
      this.product.product_name = this.product.field.seed_name;
    },
    saveProduct() {
      let obj = {
        user_id: this.user.id,
        product_name: this.product.product_name,
        product_weight: this.product.product_weight,
      };

      let err = false;

      if (!obj.product_name || obj.product_name.length == 0) {
        helper.showWarning("Please enter Product name.");
        err = true;
      }

      if (!obj.product_weight || obj.product_weight.length == 0) {
        helper.showWarning("Please enter Product weight.");
        err = true;
      }

      let action = null;
      if (this.productForm.add) {
        if (!this.product.field) {
          helper.showWarning("Please select a field.");
          err = true;
        } else {
          obj.field_id = this.product.field.id;
          obj.setFieldAsCompleted = this.product.setFieldAsCompleted;
        }
        action = "add";
      } else {
        obj.id = this.product.id;
        action = "update";
      }

      if (err) {
        return;
      }

      helper.toggleLoadingScreen(true);
      this.$axios
        .post(`packaging/product/${action}`, obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            helper.showSuccess(result.message);
            if (this.productForm.add) {
              this.getProducts(true);
            } else {
              this.products.items[this.index].product_name = result.data.product_name;
              this.products.items[this.index].product_weight = result.data.product_weight;
              $("#productForm").modal("hide");
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
    removeProductFromPack(id, index) {
      if (this.packProducts.length == 1) {
        alert("To remove the last product please delete the package.");
        return;
      }
      if (!confirm("Remove product from package?")) {
        return;
      }

      let obj = {
        id: id,
        user_id: this.user.id,
      };

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("packaging/packs/remove-product", obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            helper.showSuccess(result.message);
            this.getProducts(true);
            this.packProducts.items.splice(index, 1);
            this.packs.items[this.index].prod_num -= 1;
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    deletePack(id, index) {
      if (!confirm("Delete package?")) {
        return;
      }

      let obj = {
        id: id,
        user_id: this.user.id,
      };

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("packaging/packs/delete", obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            helper.showSuccess(result.message);
            this.packs.items.splice(index, 1);
            this.setPacks(this.packs, true);
            this.getProducts(true);
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    deleteProduct(id, index) {
      if (!confirm("Delete product?")) {
        return;
      }

      let obj = {
        id: id,
        user_id: this.user.id,
      };
      helper.toggleLoadingScreen(true);
      this.$axios
        .post("packaging/product/delete", obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            helper.showSuccess(result.message);
            this.products.items.splice(index, 1);
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    getProducts(reload = false) {
      if (reload == false) {
        if (
          this.products.currentPage > 0 &&
          this.products.lastPage == this.products.currentPage
        ) {
          return;
        }
      } else {
        /** reload selected products */
        this.selectedProducts = [];
      }

      let obj = {
        user_id: this.user.id,
        page: reload ? 1 : ++this.products.currentPage,
      };

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("packaging/product/all", obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            if (reload) {
              this.products = result.data;
            } else {
              this.products = {
                total: result.data.total,
                lastPage: result.data.lastPage,
                currentPage: result.data.currentPage,
                items: this.products.items.concat(result.data.items),
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
    getFields() {
      helper.toggleLoadingScreen(true);
      this.$axios
        .get("packaging/product/fields")
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.fields = result.data;
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
  },
  mounted() {
    this.getFields();
    this.getProducts();
    this.getPackages();

    window.onscroll = () => {
      let fullHeight = document.documentElement.offsetHeight;
      let bottomOfWindow = document.documentElement.scrollTop + window.innerHeight;

      if (bottomOfWindow >= fullHeight) {
        this.getProducts();
        this.getPackages();
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
