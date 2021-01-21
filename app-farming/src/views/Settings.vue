<template>
  <div id="settings">
    <NavBar location="settings" />

    <!-- rest of the page here -->
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

    <div class="container">
      <div class="row">
        <div class="col-md-12 col-sm-12" :class="{ 'd-none': actionName != 'seeding' }">
          <SeettingsSeed
            @showWarning="showWarning($event)"
            @showSuccess="showSuccess($event)"
          />
        </div>

        <div
          class="col-md-12 col-sm-12"
          :class="{ 'd-none': actionName != 'fertilizing' }"
        >
          <SeettingsFertilizing
            @showWarning="showWarning($event)"
            @showSuccess="showSuccess($event)"
          />
        </div>
        <div
          class="col-md-12 col-sm-12"
          :class="{ 'd-none': actionName != 'harvesting' }"
        >
          <SeettingsHarvesting
            @showWarning="showWarning($event)"
            @showSuccess="showSuccess($event)"
          />
        </div>

        <!-- /row div -->
      </div>

      <!-- /container div -->
    </div>

    <!-- /page div -->
  </div>
</template>

<script>
import NavBar from "@/components/NavBar.vue";
import SeettingsSeed from "@/components/SeettingsSeed.vue";
import SeettingsFertilizing from "@/components/SeettingsFertilizing.vue";
import SeettingsHarvesting from "@/components/SeettingsHarvesting.vue";
export default {
  name: "Settings",
  components: {
    NavBar,
    SeettingsSeed,
    SeettingsFertilizing,
    SeettingsHarvesting,
  },
  data() {
    return {
      actionName: "seeding",
    };
  },
  methods: {
    changeAction(action) {
      this.actionName = action;
    },
    showWarning(msg) {
      this.$notify({
        group: "app",
        text: msg,
        type: "error",
      });
    },
    showSuccess(msg) {
      this.$notify({
        group: "app",
        text: msg,
        type: "success",
      });
    },
  },
  mounted() {},
};
</script>
