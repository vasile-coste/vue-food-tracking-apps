<template>
  <div id="homeSeeding">
    <h2 class="actionHeader">Seeding</h2>
    <div class="form-group" v-if="chooseField">
      <label for="chooseField">Choose Field Name</label>
      <select
        class="form-control col-md-7 col-sm-12"
        id="chooseField"
        v-model="selectField"
        @change="showCreateField"
      >
        <option
          v-for="(field, index) in fields"
          :key="index"
          v-bind:value="{ id: field.id, field_name: field.field_name, obj: field }"
        >
          {{ field.field_name }}
        </option>
      </select>
    </div>
    <div class="form-group" v-else>
      <label for="newFieldName">New Field Name</label>
      <div class="row m-0">
        <div class="col-md-7 col-sm-8 p-0">
          <input
            type="text"
            v-model="newField"
            id="newFieldName"
            class="form-control col-md-12 col-sm-12"
          />
        </div>
        <div class="col-md-5 col-sm-4">
          <button type="button" @click="cancelAddNewField" class="btn btn-light">
            Cancel
          </button>
        </div>
      </div>
    </div>
    <div class="form-group">
      <label for="chooseSeed">Choose Seed</label>
      <select
        class="form-control col-md-7 col-sm-12"
        id="chooseSeed"
        v-model="selectSeed"
        @change="getComanyBySeed"
        :disabled="chooseField"
      >
        <option
          v-for="(seed, index) in seeds"
          :key="index"
          v-bind:value="{ id: seed.id, name: seed.name }"
        >
          {{ seed.name }}
        </option>
      </select>
    </div>
    <div class="form-group">
      <label for="chooseSeedCompany">Choose Seeding Company</label>
      <select
        class="form-control col-md-7 col-sm-12"
        id="chooseSeedCompany"
        v-model="selectCompany"
        :disabled="chooseField"
      >
        <option
          v-for="(company, index) in companies"
          :key="index"
          v-bind:value="{ id: company.id, company_name: company.company_name }"
        >
          {{ company.company_name }}
        </option>
      </select>
    </div>
    <div class="form-group">
      <button type="button" class="btn btn-success" @click="startActionBefore">
        Start Seeding
      </button>
      <small class="form-text text-muted"
        >Once a Field is created you can no longer change the seed and company</small
      >
    </div>
  </div>
</template>
<script>
import helper from "@/js/helper";
export default {
  name: "HomeSeeding",
  props: {
    actionName: String,
    fields: Array,
  },
  data() {
    return {
      user: this.$session.get("user"),
      chooseField: true,
      newField: null,
      selectField: {},
      selectSeed: {},
      selectCompany: {},
      seeds: [],
      companies: [],
    };
  },
  methods: {
    cancelAddNewField() {
      this.chooseField = true;
      this.selectField = {};
    },
    showCreateField() {
      /** show input new field */
      if (this.selectField.id == "new") {
        this.chooseField = false;
        return;
      }

      // preselect seed and company
      if (this.selectField.id !== "new" && this.selectField.id !== "") {
        this.selectSeed = {
          id: this.selectField.obj.seed_id,
          name: this.selectField.obj.seed_name,
        };

        let companyExistsInSelect = false;
        this.companies.forEach((company) => {
          if (company.id == this.selectField.obj.seed_company_id) {
            companyExistsInSelect = true;
          }
        });
        if (companyExistsInSelect == false) {
          this.companies.push({
            id: this.selectField.obj.seed_company_id,
            company_name: this.selectField.obj.seed_company_name,
          });
        }

        this.selectCompany = {
          id: this.selectField.obj.seed_company_id,
          company_name: this.selectField.obj.seed_company_name,
        };
      }
    },
    startActionBefore() {
      let err = false;
      if ( 
        (Object.keys(this.selectField).length === 0 || this.selectField.id == "new" || this.selectField.id == "") &&
        (!this.newField || this.newField.length == 0)
      ) {
        helper.showWarning("Please select or enter a new field.");
        err = true;
      }

      if (Object.keys(this.selectSeed).length === 0) {
        helper.showWarning("Please select a seed.");
        err = true;
      }

      if (Object.keys(this.selectCompany).length === 0) {
        helper.showWarning("Please select a company.");
        err = true;
      }

      if (err) {
        return;
      }

      let fieldName = null;
      if (this.selectField.id == "new" || this.selectField.id == "") {
        fieldName = this.newField;
        let fieldObj = {
          user_id: this.user.id,
          field_name: fieldName,
          seed_id: this.selectSeed.id,
          seed_name: this.selectSeed.name,
          seed_company_id: this.selectCompany.id,
          seed_company_name: this.selectCompany.company_name,
          seeding_status: "in_progress",
        };
        // add new field and return it as obj
        helper.toggleLoadingScreen(true);
        this.$axios
          .post("farming/field/add", fieldObj)
          .then((res) => {
            let result = JSON.parse(res.request.response);
            if (result.success) {
              this.fields.push(result.data);
              this.selectField.id = result.data.id;
              this.selectField.field_name = result.data.field_name;
              this.selectField.obj = result.data;
              this.chooseField = true;

              this.startAction();
            } else {
              helper.showWarning(result.message);
            }
            helper.toggleLoadingScreen(false);
          })
          .catch(() => {
            helper.toggleLoadingScreen(false);
          });
      } else {
        this.startAction();
      }
    },
    startAction() {
      let actionData = {
        actionStarted: true,
        mapData: [
          {
            label: "Action",
            value: this.actionName,
          },
          {
            label: "Field",
            value: this.selectField.field_name,
          },
          {
            label: "Seed",
            value: this.selectSeed.name,
          },
          {
            label: "Company",
            value: this.selectCompany.company_name,
          },
        ],
        fieldData: this.selectField.obj,
      };

      this.$emit("startAction", actionData);
    },
    getSeeds() {
      helper.toggleLoadingScreen(true);
      this.$axios
        .get(`farming/seeding/seed/${this.user.id}`)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.seeds = result.data;
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    getComanyBySeed() {
      this.selectCompany = {};
      helper.toggleLoadingScreen(true);
      this.$axios
        .get(`farming/seeding/companies/${this.user.id}/${this.selectSeed.id}`)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.companies = result.data;
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
    this.getSeeds();
  },
};
</script>
