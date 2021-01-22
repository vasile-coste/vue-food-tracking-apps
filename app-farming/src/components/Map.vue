<template>
  <div class="row mt-1">
    <div
      class="col-md-2 col-sm-12 mapTitle"
      :class="{
        'col-md-12 col-sm-12': actionStarted == false,
        'col-md-2 col-sm-12': actionStarted == true,
      }"
    >
      <img src="@/assets/images/icons/map.png" alt="" />Map
    </div>
    <div
      class="col-md-8 col-sm-12 text-center"
      :class="{ 'd-none': actionStarted == false }"
    >
      <div class="mapStatus" v-for="(item, index) in mapData" :key="index">
        <span class="mapStatus-label">{{ item.label }}:</span>
        <span class="mapStatus-value">{{ item.value }}</span>
      </div>
    </div>
    <div class="col-md-2 col-sm-12" :class="{ 'd-none': actionStarted == false }">
      <button type="button" class="btn btn-danger" @click="stopAction">Stop</button>
    </div>
  </div>
</template>

<script>
export default {
  name: "Map",
  props: {
    actionStarted: Boolean,
    mapData: Array,
    fieldData: Object,
  },
  data() {
    return {};
  },
  methods: {
    stopAction() {
      console.log("mapData", this.mapData);
      /** send data to parrent component */
      this.$emit("stopAction", false);
    },
  },
  mounted() {},
};
</script>
