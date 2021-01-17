<template>
  <div class="row mt-1">
    <div
      class="col-md-2 col-sm-3 mapTitle"
      :class="{ 'col-md-12 col-sm-12': mapData.startAction == false, 'col-md-2 col-sm-12': mapData.startAction == false, }"
    >
      <img src="@/assets/images/icons/map.png" alt="" />Map
    </div>
    <div
      class="col-md-8 col-sm-6 text-center"
      :class="{ 'd-none': mapData.startAction == false }"
    >
      <div class="mapStatus" v-for="(item, index) in mapData.status" :key="index">
        <span class="mapStatus-label">{{ item.label }}:</span>
        <span class="mapStatus-value">{{ item.value }}</span>
      </div>
    </div>
    <div class="col-md-2 col-sm-6" :class="{ 'd-none': mapData.startAction == false }">
      <button type="button" class="btn btn-danger" @click="stopAction">Stop</button>
    </div>
  </div>
</template>

<script>
export default {
  name: "Map",
  props: {
    mapData: Object,
    defaul: function () {
      return {
        startAction: false,
        status: [],
        map: [],
      };
    },
  },
  data() {
    return {};
  },
  methods: {
    stopAction() {
      this.mapData.startAction = false;
      console.log("mapData", this.map);
      /** send data to parrent component */
      this.$emit("stopAction", false);
    },
  },
  mounted() {},
};
</script>
