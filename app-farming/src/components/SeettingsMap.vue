<template>
  <div id="Settingsfertilizer">
    <div class="col-md-12 col-sm-12">
      <h2 class="actionHeader">Map Settings</h2>
      <div class="row">
        <div class="col-md-12 col-sm-12 form-check">
          <input
            type="radio"
            id="show_joystick-false"
            name="show_joystick"
            value="0"
            class="form-check-input"
            v-model="show_joystick"
          />
          <label for="show_joystick-false" class="form-check-label"
            >Use GPS for Location</label
          >
        </div>
        <div class="col-md-12 col-sm-12 form-check">
          <input
            type="radio"
            id="show_joystick-true"
            name="show_joystick"
            value="1"
            class="form-check-input"
            v-model="show_joystick"
          />
          <label for="show_joystick-true" class="form-check-label"
            >Use joystick for Location</label
          >
        </div>
        <div class="col-md-12 col-sm-12 form-group" v-if="show_joystick == 0">
          <label for="precision" class="col-form-label">GPS Precision:</label>
          <select
            type="text"
            class="col-md-5 col-sm-12 form-control"
            id="precision"
            aria-describedby="precisionHelpBlock"
            v-model="precision"
          >
            <option value="2">2 seconds</option>
            <option value="3">3 seconds (default)</option>
            <option value="4">4 seconds</option>
            <option value="5">5 seconds</option>
            <option value="6">6 seconds</option>
          </select>
          <small id="precisionHelpBlock" class="form-text text-muted">
            Select GPS precision depending on the tractor speed.
          </small>
        </div>
        <div class="col-md-12 col-sm-12 form-group" v-if="show_joystick == 1">
          <label for="latitude" class="col-form-label">Latitude:</label>
          <input
            type="text"
            class="col-md-5 col-sm-12 form-control"
            id="latitude"
            placeholder="eg: 47.4132684"
            v-model="latitude"
          />
        </div>
        <div class="col-md-12 col-sm-12 form-group" v-if="show_joystick == 1">
          <label for="longitude" class="col-form-label">Longitude:</label>
          <input
            type="text"
            class="col-md-5 col-sm-12 form-control"
            id="longitude"
            placeholder="eg: 22.3134664"
            v-model="longitude"
          />
        </div>
        <div class="col-md-12 col-sm-12 form-group">
          <button type="button" class="btn btn-primary" @click="updateMapSettings">
            Update Settings
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import helper from "@/js/helper";
export default {
  name: "SeettingsMap",
  props: {},
  data() {
    return {
      user: this.$session.get("user"),
      show_joystick: this.$session.get("user").map_settings.show_joystick,
      precision: this.$session.get("user").map_settings.precision,
      latitude: this.$session.get("user").map_settings.latitude,
      longitude: this.$session.get("user").map_settings.longitude,
    };
  },
  methods: {
    updateMapSettings() {
      /** prepare form for updatting */
      let mapObj = {
        user_id: this.user.id,
        id: this.user.map_settings.id,
        show_joystick: this.show_joystick,
        precision: this.precision,
        latitude: this.latitude,
        longitude: this.longitude,
      };

      let err = false;
      if (mapObj.show_joystick == 1) {
        if (!mapObj.latitude || mapObj.latitude == "") {
          helper.showWarning("Please enter default latitude.");
          err = true;
        }
        if (!mapObj.latitude || mapObj.longitude == "") {
          helper.showWarning("Please enter default longitude.");
          err = true;
        }
      }

      if (err) {
        return;
      }

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("farming/map/update", mapObj)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            helper.showSuccess(result.message);
            this.user.map_settings = result.data;
            this.$session.remove("user");
            this.$session.set("user", this.user);
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
  mounted() {},
};
</script>
