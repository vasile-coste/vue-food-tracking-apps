<template>
  <div id="homeFertilizing">
    <h2 class="actionHeader">Fertilizing</h2>
    <div class="form-group">
      <label for="chooseField">Choose Field Name</label>
      <select
        class="form-control col-md-7 col-sm-12"
        id="chooseField"
        v-model="selectField"
        @change="getComanyAndFertilizer"
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
      <label for="chooseFertilizer">Choose Fertilizer</label>
      <select
        class="form-control col-md-7 col-sm-12"
        id="chooseFertilizer"
        v-model="selectFertilizer"
        @change="getComanyByFertilizer"
        :disabled="fieldIsInProgress"
      >
        <option
          v-for="(fertilizer, index) in fertilizers"
          :key="index"
          v-bind:value="{ id: fertilizer.id, name: fertilizer.name }"
        >
          {{ fertilizer.name }}
        </option>
      </select>
    </div>
    <div class="form-group">
      <label for="chooseFertilizerCompany">Choose Fertilizer Company</label>
      <select
        class="form-control col-md-7 col-sm-12"
        id="chooseFertilizerCompany"
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
        >Once a fertilizer and a company is selected for a field you can no longer change
        them</small
      >
    </div>
  </div>
</template>

<script>
import helper from "@/js/helper";
export default {
  name: "HomeFertilizing",
  props: {
    actionName: String,
    fields: Array,
  },
  data() {
    return {
      fieldStatus: "in_progress",
      user: this.$session.get("user"),
      selectField: {},
      selectFertilizer: {},
      selectCompany: {},
      fertilizers: [],
      companies: [],
      fieldIsInProgress: true,
    };
  },
  methods: {
    getComanyAndFertilizer() {
      this.fieldIsInProgress = true;
      this.selectFertilizer = {};
      this.selectCompany = {};

      if (this.selectField.obj) {
        if (this.selectField.obj.fertilizing_status == "in_progress") {
          this.fieldIsInProgress = true;

          this.selectFertilizer = {
            id: this.selectField.obj.fertilizer_id,
            name: this.selectField.obj.fertilizer_name,
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
        } else if (this.selectField.id == "") {
          this.fieldIsInProgress = true;
        } else {
          this.fieldIsInProgress = false;
        }
      }
    },
    getFertilizer() {
      helper.toggleLoadingScreen(true);
      this.$axios
        .get(`farming/fertilizing/fertilizer/${this.user.id}`)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.fertilizers = result.data;
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    getComanyByFertilizer() {
      helper.toggleLoadingScreen(true);
      this.$axios
        .get(`farming/fertilizing/companies/${this.user.id}/${this.selectFertilizer.id}`)
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
      console.log(this.selectField);
      if (Object.keys(this.selectField).length === 0 || this.selectField.id == "") {
        helper.showWarning("Please select or enter a new field.");
        err = true;
      }

      if (Object.keys(this.selectFertilizer).length === 0) {
        helper.showWarning("Please select a fertilizer.");
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
          fertilizer_id: this.selectFertilizer.id,
          fertilizer_name: this.selectFertilizer.name,
          fertilizer_company_id: this.selectCompany.id,
          fertilizer_company_name: this.selectCompany.company_name,
          fertilizing_status: "in_progress",
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
            label: "Fertilizer",
            value: this.selectFertilizer.name,
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
    this.getFertilizer();
  },
};
</script>
