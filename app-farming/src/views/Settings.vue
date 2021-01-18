<template>
  <div id="settings">
    <NavBar location="settings" />

    <!-- rest of the page here -->
    <div class="headerMenu">
      <div class="container">
        <div
          class="submenuBtn"
          @click="changeAction('seeding')"
          :class="{ active: actionName == 'seeding' }"
        >
          <div class="submenuBtn-img seeding"></div>
          <div class="submenuBtn-text">Seeding</div>
        </div>
        <div
          class="submenuBtn"
          @click="changeAction('fertilizing')"
          :class="{ active: actionName == 'fertilizing' }"
        >
          <div class="submenuBtn-img fertilizing"></div>
          <div class="submenuBtn-text">Fertilizing</div>
        </div>
        <div
          class="submenuBtn"
          @click="changeAction('harvesting')"
          :class="{ active: actionName == 'harvesting' }"
        >
          <div class="submenuBtn-img harvesting"></div>
          <div class="submenuBtn-text">Harvesting</div>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row">
        <div class="col-md-12 col-sm-12" :class="{ 'd-none': actionName != 'seeding' }">
          <h2 class="actionHeader">Seeding</h2>
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
            data-target="#seedCompanyForm"
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
            </div>
          </div>
        </div>

        <div
          class="col-md-12 col-sm-12"
          :class="{ 'd-none': actionName != 'fertilizing' }"
        >
          <h2 class="actionHeader">Fertilizing</h2>
        </div>
        <div
          class="col-md-12 col-sm-12"
          :class="{ 'd-none': actionName != 'harvesting' }"
        >
          <h2 class="actionHeader">Harvesting</h2>
        </div>

        <!-- /row div -->
      </div>

      <!-- /container div -->
    </div>

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

    <!-- /page div -->
  </div>
</template>

<script>
import $ from "jquery";
import NavBar from "@/components/NavBar.vue";
export default {
  name: "Settings",
  components: {
    NavBar,
  },
  data() {
    return {
      actionName: "seeding",
      user: this.$session.get("user"),
      seedForm: {
        title: null,
        name: null,
        obj: null,
      },
      index: null,
      seeds: [],
    };
  },
  methods: {
    changeAction(action) {
      this.actionName = action;
    },
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
      if (this.seedForm.obj != null) {
        /** prepare form for update */
        urlPart = "update";
        seedObj.id = this.seedForm.obj.id;
      }

      if (!seedObj.name || seedObj.name.length == 0) {
        this.showWarning("Please complete Seed Name.");
        return;
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
    resetData() {
      this.seedForm = {
        title: null,
        name: null,
        obj: null,
      };
    },
    showWarning(msg) {
      this.$notify({
        group: "app",
        text: msg,
        type: "error",
      });
    },
    showSuccess(msg) {
      this.$notify({
        group: "app",
        text: msg,
        type: "success",
      });
    },
  },
  mounted() {
    this.loadSeeds();
  },
};
</script>
