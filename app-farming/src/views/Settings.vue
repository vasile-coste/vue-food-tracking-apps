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
        <div
          class="submenuBtn"
          @click="changeAction('settings')"
          :class="{ active: actionName == 'settings' }"
        >
          <div class="submenuBtn-img settings"></div>
          <div class="submenuBtn-text">Map Settings</div>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row">
        <div class="col-md-12 col-sm-12" :class="{ 'd-none': actionName != 'seeding' }">
          <SeettingsSeed />
        </div>

        <div
          class="col-md-12 col-sm-12"
          :class="{ 'd-none': actionName != 'fertilizing' }"
        >
          <SeettingsFertilizing />
        </div>
        <div
          class="col-md-12 col-sm-12"
          :class="{ 'd-none': actionName != 'harvesting' }"
        >
          <SeettingsHarvesting />
        </div>
        <div
          class="col-md-12 col-sm-12"
          :class="{ 'd-none': actionName != 'settings' }"
        >
          <SeettingsMap />
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
import SeettingsMap from "@/components/SeettingsMap.vue";
export default {
  name: "Settings",
  components: {
    NavBar,
    SeettingsSeed,
    SeettingsFertilizing,
    SeettingsHarvesting,
    SeettingsMap,
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
  },
  mounted() {},
};
</script>
