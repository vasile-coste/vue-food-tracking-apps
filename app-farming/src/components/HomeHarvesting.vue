<template>
  <div id="homeHarvesting">
    <h2 class="actionHeader">Harvesting</h2>
    <div class="form-group">
      <label for="chooseField">Choose Field Name</label>
      <select
        class="form-control col-md-7 col-sm-12"
        id="chooseField"
        v-model="selectField"
        @change="getComany"
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
    <div class="form-group">
      <label for="chooseHarvestingCompany">Choose Harvesting Company</label>
      <select
        class="form-control col-md-7 col-sm-12"
        id="chooseHarvestingCompany"
        v-model="selectCompany"
        :disabled="fieldIsInProgress"
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
        >Once a company is selected for a field you can no longer change it</small
      >
    </div>
  </div>
</template>

<script>
import helper from "@/js/helper";
export default {
  name: "HomeHarvesting",
  props: {
    actionName: String,
    fields: Array,
  },
  data() {
    return {
      user: this.$session.get("user"),
      selectField: {},
      selectCompany: {},
      companies: [],
      fieldIsInProgress: true,
    };
  },
  methods: {
    getComany() {
      this.fieldIsInProgress = false;
      this.selectCompany = {};
      if (
        this.selectField.obj.harvesting_status &&
        this.selectField.obj.harvesting_status == "in_progress"
      ) {
        this.selectCompany = {
          id: this.selectField.obj.harvesting_company_id,
          company_name: this.selectField.obj.harvesting_company_name,
        };
        this.fieldIsInProgress = true;
      } else if (this.selectField.id == "") {
        this.fieldIsInProgress = true;
      }
    },
    getCompanies() {
      helper.toggleLoadingScreen(true);
      this.$axios
        .get(`farming/harvesting/companies/${this.user.id}`)
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
    startActionBefore() {
      let err = false;
      if (Object.keys(this.selectField).length === 0 || this.selectField.id == "") {
        helper.showWarning("Please select or enter a new field.");
        err = true;
      }

      if (Object.keys(this.selectCompany).length === 0) {
        helper.showWarning("Please select a company.");
        err = true;
      }

      if (err) {
        return;
      }

      if (this.fieldIsInProgress == true) {
        this.startAction();
      } else {
        /** update field */
        let fieldObj = {
          id: this.selectField.id,
          harvesting_company_id: this.selectCompany.id,
          harvesting_company_name: this.selectCompany.company_name,
          harvesting_status: "in_progress",
        };

        helper.toggleLoadingScreen(true);
        this.$axios
          .post("farming/field/update", fieldObj)
          .then((res) => {
            let result = JSON.parse(res.request.response);
            if (result.success) {
              this.fieldIsInProgress = true;
              this.selectField.obj = result.data;
              this.startAction();
            } else {
              helper.showWarning(result.message);
            }
            helper.toggleLoadingScreen(false);
          })
          .catch(() => {
            helper.toggleLoadingScreen(false);
          });
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
            label: "Company",
            value: this.selectCompany.company_name,
          },
        ],
        fieldData: this.selectField.obj,
      };

      this.$emit("startAction", actionData);
    },
  },
  mounted() {
    this.getCompanies();
  },
};
</script>
