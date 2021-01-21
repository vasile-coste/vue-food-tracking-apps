<template>
  <div id="Settingsfertilizer">
    <div class="col-md-12 col-sm-12">
      <h2 class="actionHeader">Fertilizing Settings</h2>
      <button
        type="button"
        class="btn btn-light m-1"
        data-toggle="modal"
        @click="openModalFertilizer(false, null)"
      >
        New fertilizer
      </button>
      <button
        type="button"
        class="btn btn-light m-1"
        data-toggle="modal"
        @click="openModalFertilizerCompany(false, null)"
      >
        New Fertilizing Company
      </button>
      <div class="row">
        <div class="col-md-5 col-sm-12">
          <h3 class="settingsTitle">Fertilizers</h3>
          <table class="table table-hover">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in fertilizers" :key="index">
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ item.name }}</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-link"
                    @click="openModalFertilizer(item, index)"
                  >
                    Edit
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="col-md-7 col-sm-12">
          <h3 class="settingsTitle">Fertilizing Companies</h3>
          <table class="table table-hover">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Company</th>
                <th scope="col">Fertilizer</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in fertilizerCompanies" :key="index">
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ item.company_name }}</td>
                <td>{{ item.fertilizer_name }}</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-link"
                    @click="openModalFertilizerCompany(item, index)"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    class="btn btn-danger"
                    @click="deleteFertilizerCompany(item.id, index)"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <div
      class="modal fade"
      id="fertilizerForm"
      tabindex="-1"
      role="dialog"
      aria-labelledby="fertilizerFormModal"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ fertilizerForm.title }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="container-fluid">
              <div class="form-group">
                <label for="fertilizerName" class="col-form-label"
                  >Fertilizer Name:</label
                >
                <input
                  type="text"
                  class="form-control"
                  id="fertilizerName"
                  v-model="fertilizerForm.name"
                />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Close
            </button>
            <button type="button" class="btn btn-primary" @click="saveModalFertilizer">
              Save
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      class="modal fade"
      id="fertilizerCompanyForm"
      tabindex="-1"
      role="dialog"
      aria-labelledby="fertilizerCompanyFormModal"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ fertilizerCompanyForm.title }}
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="container-fluid">
              <div class="form-group">
                <label for="selectdFertilizer" class="col-form-label">Fertilizer:</label>
                <select
                  type="text"
                  class="form-control"
                  id="selectdFertilizer"
                  v-model="fertilizerCompanyForm.fertilizer_id"
                >
                  <option
                    v-for="(item, index) in fertilizers"
                    :key="index"
                    v-bind:value="item.id"
                  >
                    {{ item.name }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label for="fertilizerCompanyName" class="col-form-label"
                  >Company Name:</label
                >
                <input
                  type="text"
                  class="form-control"
                  id="fertilizerCompanyName"
                  list="FertilizerCompanies"
                  aria-describedby="companyHelpBlock"
                  autocomplete="off"
                  v-model="fertilizerCompanyForm.company_name"
                />
                <datalist id="FertilizerCompanies">
                  <option
                    v-for="(company_name, index) in dalaListCompanies()"
                    :key="index"
                    v-bind:value="company_name"
                  >
                    {{ company_name }}
                  </option>
                </datalist>
                <small id="companyHelpBlock" class="form-text text-muted">
                  Enter a name or choose one from the list(if available).
                </small>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Close
            </button>
            <button
              type="button"
              class="btn btn-primary"
              @click="saveModalFertilizerCompany"
            >
              Save
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import helper from "@/js/helper";
import $ from "jquery";
export default {
  name: "SeettingsFertilizing",
  props: {},
  data() {
    return {
      user: this.$session.get("user"),
      fertilizerForm: {
        title: null,
        name: null,
        obj: null,
      },
      fertilizerCompanyForm: {
        title: null,
        fertilizer_id: null,
        company_name: null,
        obj: null,
      },
      index: null,
      fertilizers: [],
      fertilizerCompanies: [],
    };
  },
  methods: {
    loadFertilizers() {
      helper.toggleLoadingScreen(true);
      this.$axios
        .get("farming/fertilizing/fertilizer/" + this.user.id)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.fertilizers = result.data;
            this.resetData();
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    openModalFertilizer(data, index) {
      if (data === false) {
        /** prepare form for add */
        this.fertilizerForm.title = "New fertilizer";
        this.fertilizerForm.name = null;
        this.fertilizerForm.obj = null;
      } else {
        /** prepare form for edit */
        this.fertilizerForm.title = "Update fertilizer";
        this.fertilizerForm.name = data.name;
        this.fertilizerForm.obj = data;
        this.index = index;
      }

      /** show modal */
      $("#fertilizerForm").modal("show");
    },
    saveModalFertilizer() {
      /** prepare form for inserting */
      let urlPart = "add";
      let fertilizerObj = {
        name: this.fertilizerForm.name,
        user_id: this.user.id,
      };

      if (!fertilizerObj.name || fertilizerObj.name.length == 0) {
        helper.showWarning("Please complete Fertilizer Name.");
        return;
      }

      if (this.fertilizerForm.obj != null) {
        /** prepare form for update */
        urlPart = "update";
        fertilizerObj.id = this.fertilizerForm.obj.id;
        if (fertilizerObj.name == this.fertilizerForm.obj.name) {
          helper.showSuccess("Nothing to update!");
          return;
        }
      }

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("farming/fertilizing/fertilizer/" + urlPart, fertilizerObj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            helper.showSuccess(result.message);
            if (this.fertilizerForm.obj == null) {
              /** add the new fertilizer to our obj */
              this.fertilizers.push(result.data);
            } else {
              /** update the fertilizer name from obj */
              this.fertilizers[this.index].name = fertilizerObj.name;
            }
            $("#fertilizerForm").modal("hide");
            this.resetData();
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    loadFertilizerCompanies() {
      helper.toggleLoadingScreen(true);
      this.$axios
        .get("farming/fertilizing/companies/" + this.user.id)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.fertilizerCompanies = result.data;
            this.resetData();
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    openModalFertilizerCompany(data, index) {
      if (data === false) {
        /** prepare form for add */
        this.fertilizerCompanyForm = {
          title: "New Company",
          company_name: null,
          fertilizer_id: null,
          obj: null,
        };
      } else {
        /** prepare form for edit */
        this.fertilizerCompanyForm = {
          title: "Update Company",
          company_name: data.company_name,
          fertilizer_id: data.fertilizer_id,
          obj: data,
        };
        this.index = index;
      }

      /** show modal */
      $("#fertilizerCompanyForm").modal("show");
    },
    saveModalFertilizerCompany() {
      /** prepare form for inserting */
      let urlPart = "add";
      let fertilizerCompanyObj = {
        company_name: this.fertilizerCompanyForm.company_name,
        user_id: this.user.id,
        fertilizer_id: this.fertilizerCompanyForm.fertilizer_id,
      };

      let err = false;

      if (
        !fertilizerCompanyObj.fertilizer_id ||
        fertilizerCompanyObj.fertilizer_id.length == 0
      ) {
        helper.showWarning("Please select a fertilizer.");
        err = true;
      }

      if (
        !fertilizerCompanyObj.company_name ||
        fertilizerCompanyObj.company_name.length == 0
      ) {
        helper.showWarning("Please complete Company Name.");
        err = true;
      }

      if (err) {
        return;
      }

      if (this.fertilizerCompanyForm.obj != null) {
        /** prepare form for update */
        urlPart = "update";
        fertilizerCompanyObj.id = this.fertilizerCompanyForm.obj.id;
        if (
          fertilizerCompanyObj.company_name ==
            this.fertilizerCompanyForm.obj.company_name &&
          fertilizerCompanyObj.fertilizer_id ==
            this.fertilizerCompanyForm.obj.fertilizer_id
        ) {
          helper.showSuccess("Nothing to update!");
          return;
        }
      }

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("farming/fertilizing/companies/" + urlPart, fertilizerCompanyObj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            helper.showSuccess(result.message);
            if (this.fertilizerCompanyForm.obj == null) {
              /** add the new fertilizer to our obj */
              this.fertilizerCompanies.push(result.data);
            } else {
              /** update the fertilizer name from obj */
              this.fertilizerCompanies[this.index] = result.data;
            }
            $("#fertilizerCompanyForm").modal("hide");
            this.resetData();
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    deleteFertilizerCompany(id, index) {
      if (!confirm("Are you sure?")) {
        return;
      }

      let obj = {
        id: id,
        user_id: this.user.id,
      };
      helper.toggleLoadingScreen(true);
      this.$axios
        .post("farming/fertilizing/companies/delete", obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            helper.showSuccess(result.message);
            this.fertilizerCompanies.splice(index, 1);
            this.resetData();
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    dalaListCompanies() {
      let companies = [];
      this.fertilizerCompanies.forEach((e) => {
        if (!companies.includes(e.company_name)) {
          companies.push(e.company_name);
        }
      });

      return companies;
    },
    resetData() {
      this.fertilizerForm = {
        title: null,
        name: null,
        obj: null,
      };
      this.fertilizerCompanyForm = {
        title: null,
        fertilizer_id: null,
        company_name: null,
        obj: null,
      };
    },
  },
  mounted() {
    this.loadFertilizers();
    this.loadFertilizerCompanies();
  },
};
</script>
