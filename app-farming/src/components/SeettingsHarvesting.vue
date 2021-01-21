<template>
  <div id="SettingsSeed">
    <div class="col-md-12 col-sm-12">
      <h2 class="actionHeader">Harvesting Settings</h2>
      <button
        type="button"
        class="btn btn-light m-1"
        data-toggle="modal"
        @click="openModalHarvestingCompany(false, null)"
      >
        New Harvesting Company
      </button>
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <h3 class="settingsTitle">Companies</h3>
          <table class="table table-hover">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Company Name</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in harvestingCompanies" :key="index">
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ item.company_name }}</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-link"
                    @click="openModalHarvestingCompany(item, index)"
                  >
                    Edit
                  </button>
                  <button
                    type="button"
                    class="btn btn-danger"
                    @click="deleteHarvestingCompany(item.id, index)"
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
      id="harvestingCompanyForm"
      tabindex="-1"
      role="dialog"
      aria-labelledby="harvestingFormModal"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ harvestingCompanyForm.title }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="container-fluid">
              <div class="form-group">
                <label for="company_name" class="col-form-label">Company Name:</label>
                <input
                  type="text"
                  class="form-control"
                  id="company_name"
                  v-model="harvestingCompanyForm.company_name"
                />
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
              @click="saveModalHarvestingCompany"
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
  name: "SeettingsHarvesting",
  props: {},
  data() {
    return {
      user: this.$session.get("user"),
      harvestingCompanyForm: {
        title: null,
        company_name: null,
        obj: null,
      },
      index: null,
      harvestingCompanies: [],
    };
  },
  methods: {
    loadHarvestingCompanies() {
      helper.toggleLoadingScreen(true);
      this.$axios
        .get("farming/harvesting/companies/" + this.user.id)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.harvestingCompanies = result.data;
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
    openModalHarvestingCompany(data, index) {
      if (data === false) {
        /** prepare form for add */
        this.harvestingCompanyForm.title = "New Harvesting Company";
        this.harvestingCompanyForm.company_name = null;
        this.harvestingCompanyForm.obj = null;
      } else {
        /** prepare form for edit */
        this.harvestingCompanyForm.title = "Update Harvesting Company";
        this.harvestingCompanyForm.company_name = data.company_name;
        this.harvestingCompanyForm.obj = data;
        this.index = index;
      }

      /** show modal */
      $("#harvestingCompanyForm").modal("show");
    },
    saveModalHarvestingCompany() {
      /** prepare form for inserting */
      let urlPart = "add";
      let harvestingObj = {
        company_name: this.harvestingCompanyForm.company_name,
        user_id: this.user.id,
      };

      if (!harvestingObj.company_name || harvestingObj.company_name.length == 0) {
        helper.showWarning("Please complete Company Name.");
        return;
      }

      if (this.harvestingCompanyForm.obj != null) {
        /** prepare form for update */
        urlPart = "update";
        harvestingObj.id = this.harvestingCompanyForm.obj.id;
        if (harvestingObj.company_name == this.harvestingCompanyForm.obj.company_name) {
          helper.showSuccess("Nothing to update!");
          return;
        }
      }

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("farming/harvesting/companies/" + urlPart, harvestingObj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            helper.showSuccess(result.message);
            if (this.harvestingCompanyForm.obj == null) {
              /** add the new seed to our obj */
              this.harvestingCompanies.push(result.data);
            } else {
              /** update the seed name from obj */
              this.harvestingCompanies[this.index].company_name =
                harvestingObj.company_name;
            }
            $("#harvestingCompanyForm").modal("hide");
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

    deleteHarvestingCompany(id, index) {
      if (!confirm("Are you sure?")) {
        return;
      }

      let obj = {
        id: id,
        user_id: this.user.id,
      };
      helper.toggleLoadingScreen(true);
      this.$axios
        .post("farming/harvesting/companies/delete", obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            helper.showSuccess(result.message);
            this.harvestingCompanies.splice(index, 1);
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
    resetData() {
      this.harvestingCompanyForm = {
        title: null,
        company_name: null,
        obj: null,
      };
    },
  },
  mounted() {
    this.loadHarvestingCompanies();
  },
};
</script>
