<template>
  <div id="SettingsSeed">
    <div class="col-md-12 col-sm-12">
      <h2 class="actionHeader">Seeding Settings</h2>
      <button
        type="button"
        class="btn btn-light m-1"
        data-toggle="modal"
        @click="openModalSeed(false, null)"
      >
        New Seed
      </button>
      <button
        type="button"
        class="btn btn-light m-1"
        data-toggle="modal"
        @click="openModalSeedCompany(false, null)"
      >
        New Seeding Company
      </button>
      <div class="row">
        <div class="col-md-5 col-sm-12">
          <h3 class="settingsTitle">Seeds</h3>
          <table class="table table-hover">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in seeds" :key="index">
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ item.name }}</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-link"
                    @click="openModalSeed(item, index)"
                  >
                    Edit
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="col-md-7 col-sm-12">
          <h3 class="settingsTitle">Seeding Companies</h3>
          <table class="table table-hover">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Company</th>
                <th scope="col">Seed</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in seedCompanies" :key="index">
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ item.company_name }}</td>
                <td>{{ item.seed_name }}</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-link"
                    @click="openModalSeedCompany(item, index)"
                  >
                    Edit
                  </button>
                  <button type="button" class="btn btn-danger" @click="deleteSeedCompany(item.id, index)">Delete</button>
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
      id="seedForm"
      tabindex="-1"
      role="dialog"
      aria-labelledby="seedFormModal"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">{{ seedForm.title }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="container-fluid">
              <div class="form-group">
                <label for="seedName" class="col-form-label">Seed Name:</label>
                <input
                  type="text"
                  class="form-control"
                  id="seedName"
                  v-model="seedForm.name"
                />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Close
            </button>
            <button type="button" class="btn btn-primary" @click="saveModalSeed">
              Save
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      class="modal fade"
      id="seedCompanyForm"
      tabindex="-1"
      role="dialog"
      aria-labelledby="seedCompanyFormModal"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">
              {{ seedCompanyForm.title }}
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="container-fluid">
              <div class="form-group">
                <label for="selectdSeed" class="col-form-label">Seed:</label>
                <select
                  type="text"
                  class="form-control"
                  id="selectdSeed"
                  v-model="seedCompanyForm.seed_id"
                >
                  <option
                    v-for="(item, index) in seeds"
                    :key="index"
                    v-bind:value="item.id"
                  >
                    {{ item.name }}
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label for="companyName" class="col-form-label">Company Name:</label>
                <input
                  type="text"
                  class="form-control"
                  id="companyName"
                  list="companies"
                  aria-describedby="companyHelpBlock"
                  v-model="seedCompanyForm.company_name"
                />
                <datalist id="companies">
                  <option
                    v-for="(item, index) in seedCompanies"
                    :key="index"
                    v-bind:value="item.company_name"
                  >
                    {{ item.company_name }}
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
            <button type="button" class="btn btn-primary" @click="saveModalSeedCompany">
              Save
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import $ from "jquery";
export default {
  name: "SeettingsSeed",
  props: {},
  data() {
    return {
      user: this.$session.get("user"),
      seedForm: {
        title: null,
        name: null,
        obj: null,
      },
      seedCompanyForm: {
        title: null,
        seed_id: null,
        company_name: null,
        obj: null,
      },
      index: null,
      seeds: [],
      seedCompanies: [],
    };
  },
  methods: {
    loadSeeds() {
      this.$axios.get("farming/seeding/seed/" + this.user.id).then((res) => {
        let result = JSON.parse(res.request.response);
        if (result.success) {
          this.seeds = result.data;
          this.resetData();
        } else {
          this.showWarning(result.message);
        }
      });
    },
    openModalSeed(data, index) {
      if (data === false) {
        /** prepare form for add */
        this.seedForm.title = "New Seed";
        this.seedForm.name = null;
        this.seedForm.obj = null;
      } else {
        /** prepare form for edit */
        this.seedForm.title = "Update Seed";
        this.seedForm.name = data.name;
        this.seedForm.obj = data;
        this.index = index;
      }

      /** show modal */
      $("#seedForm").modal("show");
    },
    saveModalSeed() {
      /** prepare form for inserting */
      let urlPart = "add";
      let seedObj = {
        name: this.seedForm.name,
        user_id: this.user.id,
      };

      if (!seedObj.name || seedObj.name.length == 0) {
        this.showWarning("Please complete Seed Name.");
        return;
      }

      if (this.seedForm.obj != null) {
        /** prepare form for update */
        urlPart = "update";
        seedObj.id = this.seedForm.obj.id;
        if (seedObj.name == this.seedForm.obj.name) {
          this.showSuccess("Nothing to update!");
          return;
        }
      }

      this.$axios.post("farming/seeding/seed/" + urlPart, seedObj).then((res) => {
        let result = JSON.parse(res.request.response);
        if (result.success) {
          this.showSuccess(result.message);
          if (this.seedForm.obj == null) {
            /** add the new seed to our obj */
            this.seeds.push(result.data);
          } else {
            /** update the seed name from obj */
            this.seeds[this.index].name = seedObj.name;
          }
          $("#seedForm").modal("hide");
          this.resetData();
        } else {
          this.showWarning(result.message);
        }
      });
    },
    loadSeedCompanies() {
      this.$axios.get("farming/seeding/companies/" + this.user.id).then((res) => {
        let result = JSON.parse(res.request.response);
        if (result.success) {
          this.seedCompanies = result.data;
          this.resetData();
        } else {
          this.showWarning(result.message);
        }
      });
    },
    openModalSeedCompany(data, index) {
      if (data === false) {
        /** prepare form for add */
        this.seedCompanyForm = {
          title: "New Company",
          company_name: null,
          seed_id: null,
          obj: null,
        };
      } else {
        /** prepare form for edit */
        this.seedCompanyForm = {
          title: "Update Company",
          company_name: data.company_name,
          seed_id: data.seed_id,
          obj: data,
        };
        this.index = index;
      }

      /** show modal */
      $("#seedCompanyForm").modal("show");
    },
    saveModalSeedCompany() {
      /** prepare form for inserting */
      let urlPart = "add";
      let seedCompanyObj = {
        company_name: this.seedCompanyForm.company_name,
        user_id: this.user.id,
        seed_id: this.seedCompanyForm.seed_id,
      };

      let err = false;

      if (!seedCompanyObj.seed_id || seedCompanyObj.seed_id.length == 0) {
        this.showWarning("Please select a Seed.");
        err = true;
      }

      if (!seedCompanyObj.company_name || seedCompanyObj.company_name.length == 0) {
        this.showWarning("Please complete Company Name.");
        err = true;
      }

      if (err) {
        return;
      }

      if (this.seedCompanyForm.obj != null) {
        /** prepare form for update */
        urlPart = "update";
        seedCompanyObj.id = this.seedCompanyForm.obj.id;
        if (
          seedCompanyObj.company_name == this.seedCompanyForm.obj.company_name &&
          seedCompanyObj.seed_id == this.seedCompanyForm.obj.seed_id
        ) {
          this.showSuccess("Nothing to update!");
          return;
        }
      }

      this.$axios
        .post("farming/seeding/companies/" + urlPart, seedCompanyObj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.showSuccess(result.message);
            if (this.seedCompanyForm.obj == null) {
              /** add the new seed to our obj */
              this.seedCompanies.push(result.data);
            } else {
              /** update the seed name from obj */
              this.seedCompanies[this.index] = result.data;
            }
            $("#seedCompanyForm").modal("hide");
            this.resetData();
          } else {
            this.showWarning(result.message);
          }
        });
    },
    deleteSeedCompany(id, index){
      if(!confirm("Are you sure?")){
        return;
      }

      let obj = {
        id:id,
        user_id:this.user.id
      }
      this.$axios
        .post("farming/seeding/companies/delete" , obj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.showSuccess(result.message);
            this.seedCompanies.splice(index, 1);
            this.resetData();
          } else {
            this.showWarning(result.message);
          }
        });
    },
    resetData() {
      this.seedForm = {
        title: null,
        name: null,
        obj: null,
      };
      this.seedCompanyForm = {
        title: null,
        seed_id: null,
        company_name: null,
        obj: null,
      };
    },
    showWarning(msg) {
      /** send data to parrent component */
      this.$emit("showWarning", msg);
    },
    showSuccess(msg) {
      /** send data to parrent component */
      this.$emit("showSuccess", msg);
    },
  },
  mounted() {
    this.loadSeeds();
    this.loadSeedCompanies();
  },
};
</script>
