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
  </div>
</template>

<script>
// import helper from "@/js/helper";
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
      fieldIsInProgress: false,
    };
  },
  methods: {
    getComanyAndFertilizer() {
      console.log(this.selectField.obj);
      // if(this.selectField.obj.fertilizing_status)
    },
    getComanyByFertilizer(){
      console.log(this.selectFertilizer);
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
  mounted() {},
};
</script>
