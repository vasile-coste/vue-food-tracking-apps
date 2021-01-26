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
          <HomeSeeding :actionName="actionName" @startAction="startAction($event)" />
        </div>

        <div
          class="col-md-8 col-sm-12"
          :class="{ 'd-none': actionName != 'fertilizing' || actionStarted }"
        >
          <HomeFertilizing :actionName="actionName" @startAction="startAction($event)" />
        </div>

        <div
          class="col-md-8 col-sm-12"
          :class="{ 'd-none': actionName != 'harvesting' || actionStarted }"
        >
          <HomeHarvesting :actionName="actionName" @startAction="startAction($event)" />
        </div>
        <div
          class="col-sm-12"
          :class="{ 'col-md-4': !actionStarted, 'col-md-12': actionStarted }"
        >
          <Map
            :actionName="actionName"
            :fieldData="fieldData"
            :prevGPS="prevGPS"
            :mapData="mapData"
            :actionStarted="actionStarted"
            @stopAction="stopAction($event)"
          />
        </div>
      </div>

      <div
        class="modal fade"
        id="errorChangeAction"
        tabindex="-1"
        role="dialog"
        aria-labelledby="errorChangeActionLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="errorChangeActionLabel">Ups</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">Please complete the current action before changing it.</div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">
                Ok
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</template>

<script>
// @ is an alias to /src
import NavBar from "@/components/NavBar.vue";
import $ from "jquery";
import Map from "@/components/Map.vue";
import HomeSeeding from "@/components/HomeSeeding.vue";
import HomeFertilizing from "@/components/HomeFertilizing.vue";
import HomeHarvesting from "@/components/HomeHarvesting.vue";
export default {
  name: "Home",
  components: {
    NavBar,
    Map,
    HomeSeeding,
    HomeFertilizing,
    HomeHarvesting,
  },
  data() {
    return {
      actionStarted: false,
      defaultAction: "seeding",
      actionName: null,
      mapData: [],
      prevGPS: [],
      fieldData: {},
    };
  },
  methods: {
    /** change the type of action performed by the farmer */
    changeAction(action) {
      if (this.actionStarted) {
        $('#errorChangeAction').modal('show');
        return false;
      }

      /** reset mapData */
      this.mapData = [];

      /** set current action */
      this.actionName = action;
    },
    /** Retrieve data from child component */
    stopAction(event) {
      this.actionStarted = event;
    },
    startAction(actionData) {
      this.actionStarted = actionData.actionStarted;
      this.mapData = actionData.mapData;
      this.fieldData = actionData.fieldData;
      this.prevGPS = actionData.prevGPS;
    },
  },
  mounted() {
    /** Default farmers action on entering the page */
    this.changeAction(this.defaultAction);
  },
};
</script>
