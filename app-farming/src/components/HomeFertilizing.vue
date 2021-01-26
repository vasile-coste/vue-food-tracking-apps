<template>
  <div id="homeFertilizing">
    <h2 class="actionHeader">Fertilizing</h2>
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
      getNotfieldStatus: "completed",
      user: this.$session.get("user"),
      selectField: {},
      selectFertilizer: {},
      selectCompany: {},
    };
  },
  methods: {
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
        prevGPS: [],
      };

      this.$emit("startAction", actionData);
    },
  },
  mounted() {},
};
</script>
