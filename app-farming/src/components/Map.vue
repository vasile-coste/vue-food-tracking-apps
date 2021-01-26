<template>
  <div id="map" class="mb-3">
    <div class="row mt-1 mb-1">
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
    <div id="mapContainer" class="mapContainer"></div>
    <div
      class="mapJoystick"
      v-if="user.map_settings.show_joystick == 1 && actionStarted == true"
    >
      <div class="row">
        <div class="col-12 text-center">
          <button type="button" class="mapJoystick-up" @click="manualMoveUp">Up</button>
        </div>
        <div class="col-6 text-center">
          <button type="button" class="mapJoystick-left" @click="manualMoveLeft">
            Left
          </button>
        </div>
        <div class="col-6 text-center">
          <button type="button" class="mapJoystick-right" @click="manualMoveRight">
            Right
          </button>
        </div>
        <div class="col-12 text-center">
          <button type="button" class="mapJoystick-down" @click="manualMoveDown">
            Down
          </button>
        </div>
      </div>
    </div>

    <div
      class="modal fade"
      id="confirmStop"
      tabindex="-1"
      role="dialog"
      aria-labelledby="confirmStopLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmStopLabel">Confirm Action</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="text-primary">Complete the {{ actionName }} proces?</div>
            <small class="text-muted">
              If <b>yes</b>, the seeding will be set to <b>completed</b> and the field
              cannot be selected for this action again.
            </small>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="saveAction(false)">
              No
            </button>
            <button type="button" class="btn btn-primary" @click="saveAction(true)">
              Yes
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import helper from "@/js/helper";
import "leaflet/dist/leaflet.css";
import L from "leaflet";
import $ from "jquery";
export default {
  name: "Map",
  props: {
    actionName: String,
    actionStarted: Boolean,
    mapData: Array,
    prevGPS: Array,
    fieldData: Object,
  },
  watch: {
    actionStarted() {
      console.log("trigger start action");
      if (this.actionStarted) {
        console.log("start action");
        if (!this.map) {
          this.initMap();
        }
        this.clearMap();

        /** reset data */
        this.locations = [];
        this.addedMarkers = [];
        this.oldCurrentMarker = null;
        this.previousMarkers = [];

        if (this.prevGPS && this.prevGPS.length > 0) {
          this.addPreviousMarkers(this.prevGPS);
        }

        /** redraw map */
        this.redrawMap();
        console.log("---------------start action");
        this.addMarker(this.location);

        /** start moving tractor on map only if user has selected GPS location */
        if (this.user.map_settings.show_joystick == 0) {
          this.moveTractorOnMap();
        }
      }
    },
  },
  data() {
    return {
      manualMoveDistanceLat: 0.0001,
      manualMoveDistanceLong: 0.00015,
      user: this.$session.get("user"),
      map: null,
      markerGroup: null,
      oldCurrentMarker: null,
      addedMarkers: [],
      previousMarkers: [],
      locations: [],
      location: {
        latitude: 0,
        longitude: 0,
      },
    };
  },
  methods: {
    stopAction() {
      if (this.locations.length > 1) {
        /** tractor has moved and we need to save progress */
        $("#confirmStop").modal("show");
      } else {
        this.redrawMap();
        /** send data to parrent component */
        this.$emit("stopAction", false);
      }
    },
    saveAction(completed) {
      if (completed) {
        /** complete action */
      }
      console.log("mapData", this.fieldData, this.locations);

      this.$emit("stopAction", false);
      $("#confirmStop").modal("hide");
    },
    manualMoveUp() {
      this.location.latitude += this.manualMoveDistanceLat;
      this.addMarker(this.location);
    },
    manualMoveDown() {
      this.location.latitude -= this.manualMoveDistanceLat;
      this.addMarker(this.location);
    },
    manualMoveLeft() {
      this.location.longitude -= this.manualMoveDistanceLong;
      this.addMarker(this.location);
    },
    manualMoveRight() {
      this.location.longitude += this.manualMoveDistanceLong;
      this.addMarker(this.location);
    },
    initMap() {
      console.log("init map");

      this.map = L.map("mapContainer");

      /** add title layer - will not work without it */
      L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "",
      }).addTo(this.map);

      this.markerGroup = L.layerGroup().addTo(this.map);
    },
    addMarker(currentMarker) {
      this.locations.push(currentMarker);
      console.log("add marker", currentMarker);

      /** replace old marker with a new one */
      if (this.addedMarkers.length > 0) {
        let oldMarker = this.addedMarkers.pop();
        console.log("remove oldMarker (tractor) to add it as red cicle", oldMarker);
        /** remove marker on demand */
        this.map.removeLayer(oldMarker);

        let tmpMapLAyer = L.marker(
          [this.oldCurrentMarker.latitude, this.oldCurrentMarker.longitude],
          {
            icon: L.divIcon({
              className: "map-bullet-circle", //tractor
            }),
          }
        ).addTo(this.markerGroup);
        this.addedMarkers.push(tmpMapLAyer);
      }

      /** set current marker as old marker to replace it next time a marker gets added */
      this.oldCurrentMarker = currentMarker;

      console.log("add marker tractor", currentMarker);
      /** add current marker */
      let tmpMarker = L.marker([currentMarker.latitude, currentMarker.longitude], {
        icon: L.divIcon({
          className: "map-bullet-tractor",
        }),
      }).addTo(this.markerGroup);

      this.addedMarkers.push(tmpMarker);

      /** set map to be certered to current marker */
      this.map.setView(new L.LatLng(currentMarker.latitude, currentMarker.longitude), 19);
    },
    addPreviousMarkers(markers) {
      let self = this;
      console.log("add prev markers");
      /** create a variable to store the markers and to draw a line between them */
      markers.forEach((marker) => {
        /** prepare marker for map */
        let tmpMarker = L.marker([marker.latitude, marker.longitude], {
          icon: L.divIcon({
            className: `map-bullet map-bullet-${self.actionName}`,
          }),
        }).addTo(this.markerGroup);

        this.previousMarkers.push(tmpMarker);
      });
    },
    clearMap() {
      let self = this;
      if (this.addedMarkers.length > 0) {
        console.log("clear map - addedMarkers", this.addedMarkers);
        self.addedMarkers.forEach(function (marker) {
          self.map.removeLayer(marker);
        });
      }

      if (this.previousMarkers.length > 0) {
        console.log("clear map - previousMarkers");
        self.previousMarkers.forEach(function (marker) {
          self.map.removeLayer(marker);
        });
      }
    },
    redrawMap() {
      let self = this;
      setTimeout(function () {
        console.log("reload map");
        self.map.invalidateSize(true);
      }, 1000);
    },
    moveTractorOnMap() {
      let self = this;
      /** this will be active when the app should be in production */
      setTimeout(() => {
        if (self.actionStarted) {
          helper.getLocation().then((pos) => {
            self.location = pos;
            console.log("getting new position... every 1 second");
            console.log("current pos:", self.location);
            self.addMarker(self.location);

            /** call again */
            self.moveTractorOnMap();
          });
        }
      }, 1000);
    },
  },
  mounted() {
    /** load map and add the current location */
    if (this.user.map_settings.show_joystick == 0) {
      let self = this;
      helper.getLocation().then((pos) => {
        self.location = pos;
        console.log("init current pos:", self.location);

        if (!self.map) {
          self.initMap();
          self.addMarker(self.location);
          self.redrawMap();
        }
      });
    } else {
      this.location.latitude = this.user.map_settings.latitude;
      this.location.longitude = this.user.map_settings.longitude;
      if (!this.map) {
        this.initMap();
        this.addMarker(this.location);
        this.redrawMap();
      }
    }
  },
};
</script>
