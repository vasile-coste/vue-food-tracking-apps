<template>
  <div class="home">
    <NavBar location />

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

    <!-- Content -->
    <div class="container">
      <div class="row">
        <div
          class="col-md-8 col-sm-12"
          :class="{ 'd-none': actionName != 'seeding' || actionStarted }"
        >
          <HomeSeeding @startAction="startAction($event)" />
        </div>

        <div
          class="col-md-8 col-sm-12"
          :class="{ 'd-none': actionName != 'fertilizing' || actionStarted }"
        >
          <h2 class="actionHeader">Fertilizing</h2>
        </div>

        <div
          class="col-md-8 col-sm-12"
          :class="{ 'd-none': actionName != 'harvesting' || actionStarted }"
        >
          <h2 class="actionHeader">Harvesting</h2>
        </div>
        <div
          class="col-sm-12"
          :class="{ 'col-md-4': !actionStarted, 'col-md-12': actionStarted }"
        >
          <Map
            :fieldData="fieldData"
            :mapData="mapData"
            :actionStarted="actionStarted"
            @stopAction="stopAction($event)"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// @ is an alias to /src
import NavBar from "@/components/NavBar.vue";
import Map from "@/components/Map.vue";
import HomeSeeding from "@/components/HomeSeeding.vue";
export default {
  name: "Home",
  components: {
    NavBar,
    Map,
    HomeSeeding,
  },
  data() {
    return {
      actionStarted: false,
      defaultAction: "seeding",
      actionName: null,
      mapData: [],
      fieldData: {},
    };
  },
  methods: {
    /** change the type of action performed by the farmer */
    changeAction(action) {
      if (this.actionStarted) {
        alert("Please finish current action!");
        return false;
      }

      /** reset mapData */
      this.mapData = [];

      /** set current action */
      this.actionName = action;
    },
    /** Retrieve data from child component */
    stopAction(event) {
      console.log("mapEvent", event);
      this.actionStarted = event;
    },
    startAction(actionData) {
      this.actionStarted = actionData.actionStarted;
      this.mapData = actionData.mapData;
      this.fieldData = actionData.fieldData;
    },
  },
  mounted() {
    /** Default farmers action on entering the page */
    this.changeAction(this.defaultAction);
  },
};
</script>
