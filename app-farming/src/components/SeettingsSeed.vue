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
                <th scope="col">Address</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in seedCompanies" :key="index">
                <th scope="row">{{ index + 1 }}</th>
                <td>{{ item.company }}</td>
                <td>{{ item.seed }}</td>
                <td>{{ item.address }}</td>
                <td>
                  <button
                    type="button"
                    class="btn btn-link"
                    @click="openModalSeedCompany(item, index)"
                  >
                    Edit
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
              <div class="form-group">data</div>
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
        name: null,
        seed: null,
        address: null,
        obj: null,
      },
      index: null,
      seeds: [],
      seedCompanies: [],
    };
  },
  methods: {
    loadSeeds() {
      this.$axios.get("farming/seeds/" + this.user.id).then((res) => {
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

      this.$axios.post("farming/seeds/" + urlPart, seedObj).then((res) => {
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
    openModalSeedCompany(data, index) {
      if (data === false) {
        /** prepare form for add */
        this.seedCompanyForm = {
          title: "New Company",
          name: null,
          seed: null,
          address: null,
          obj: null,
        };
      } else {
        /** prepare form for edit */
        this.seedForm.title = "Update Seed";
        this.seedCompanyForm = {
          title: "New Company",
          name: null,
          seed: null,
          address: null,
          obj: data,
        };
        this.index = index;
      }

      /** show modal */
      $("#seedCompanyForm").modal("show");
    },
    saveModalSeedCompany() {
      $("#seedCompanyForm").modal("hide");
    },
    resetData() {
      this.seedForm = {
        title: null,
        name: null,
        obj: null,
      };
      this.seedCompanyForm = {
        title: null,
        name: null,
        seed: null,
        address: null,
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
  },
};
</script>
