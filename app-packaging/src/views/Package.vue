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
        <div class="col-sm-12 col-md-12">
          <button
            type="button"
            class="btn btn-light m-1"
            data-toggle="modal"
            @click="openModalProduct(false, null)"
          >
            New Product
          </button>
        </div>

        <div class="col-sm-12 col-md-7">
          <table class="table table-hover">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Product</th>
                <th scope="col">Weight</th>
                <th scope="col">QR</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in products" :key="index">
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ item.product_name }}</td>
                <td>{{ item.product_weight }}</td>
                <td>
                  <img
                    class="qrCodeBtn"
                    src="@/assets/images/icons/qr-code.png"
                    alt="QR Code"
                    @click="generateProductQR(item.id)"
                  />
                </td>
                <td>
                  <button
                    type="button"
                    class="btn btn-link"
                    @click="openModalProduct(item, index)"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    class="btn btn-danger"
                    @click="deleteProduct(item.id, index)"
                  >
                    Delete
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
                    class="form-control col-md-12 col-sm-12"
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
                    class="form-control col-md-12 col-sm-12"
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
      fields: [],
      products: [],
    };
  },
  methods: {
    generateProductQR(data) {
      let qrText = `product-${data}`;
      this.showQRCode(qrText);
    },
    showQRCode(text) {
      $(".qrArea").css("display", "flex");
      let canvas = document.getElementById("qr-aread");
      QRCode.toCanvas(canvas, text, () => {});
    },
    printQr() {
      $(".qrArea").css("display", "none");
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
              this.products.push(result.data);
            } else {
              this.products[this.index].product_name = result.data.product_name;
              this.products[this.index].product_weight = result.data.product_weight;
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
    deleteProduct(id, index) {
      if (!confirm("Are you sure?")) {
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
            this.products.splice(index, 1);
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    getProducts() {
      helper.toggleLoadingScreen(true);
      this.$axios
        .post("packaging/product/all", { user_id: this.user.id })
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.products = result.data;
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
    this.getProducts();
    this.getFields();
  },
};
</script>
