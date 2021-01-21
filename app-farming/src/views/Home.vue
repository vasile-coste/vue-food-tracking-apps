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
          <h2 class="actionHeader">Seeding</h2>
          <div class="form-group">
            <label for="chooseBatch">Choose batch</label>
            <select
              class="form-control col-md-7 col-sm-12"
              id="chooseBatch"
              v-model="actionData.batch"
              @change="showCreateBatch"
            >
              <option>1</option>
              <option>2</option>
            </select>
          </div>
          <div class="form-group">
            <label for="chooseSeed">Choose Seed</label>
            <select
              class="form-control col-md-7 col-sm-12"
              id="chooseSeed"
              v-model="actionData.seed"
            >
              <option>1</option>
              <option>2</option>
            </select>
          </div>
          <div class="form-group">
            <label for="chooseSeedCompany">Choose Seeding Company</label>
            <select
              class="form-control col-md-7 col-sm-12"
              id="chooseSeedCompany"
              v-model="actionData.company"
            >
              <option>1</option>
              <option>2</option>
            </select>
          </div>
          <div class="form-group">
            <button type="button" class="btn btn-success" @click="startAction">
              Start Seeding
            </button>
          </div>
        </div>

        <div
          class="col-md-8 col-sm-12"
          :class="{ 'd-none': actionName != 'fertilizing' || actionStarted }"
        >
          <h2 class="actionHeader">Fertilizing</h2>
          <div class="form-group">
            <label for="chooseBatch">Choose batch</label>
            <select
              class="form-control col-md-7 col-sm-12"
              id="chooseBatch"
              v-model="actionData.batch"
              @change="showCreateBatch"
            >
              <option>1</option>
              <option>2</option>
            </select>
          </div>
          <div class="form-group">
            <button type="button" class="btn btn-success" @click="startAction">
              Start Seeding
            </button>
          </div>
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
          <Map :mapData="mapData" @stopAction="stopAction($event)" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// @ is an alias to /src
import NavBar from "@/components/NavBar.vue";
import Map from "@/components/Map.vue";
export default {
  name: "Home",
  components: {
    NavBar,
    Map,
  },
  data() {
    return {
      actionStarted: false,
      defaultAction: "seeding",
      actionName: "loading",
      actionData: {
        batch: null,
        seed: null,
        company: null,
      },
      mapData: {
        startAction: false,
        status: [],
        map: [],
      },
    };
  },
  methods: {
    /** change the type of action performed by the farmer */
    changeAction(action) {
      if (this.actionStarted) {
        alert("Please finish current action!");
        return false;
      }
      this.actionName = action;
    },
    /** Start current action */
    startAction() {
      this.actionStarted = true;
      this.mapData.startAction = this.actionStarted;
      this.mapData.status = [
        {
          label: "Action",
          value: this.actionName,
        },
      ];
      
      if (this.actionName == "seeding") {
        this.mapData.status.push({
          label: "Batch",
          value: this.actionData.batch,
        });
        this.mapData.status.push({ label: "Seed", value: this.actionData.seed });
        this.mapData.status.push({
          label: "Company",
          value: this.actionData.company,
        });
      }

      if (this.actionName == "fertilizing") {
        this.mapData.status.push({
          label: "Company",
          value: this.actionData.company,
        });

      }
      if (this.actionName == "harvesting") {
        this.mapData.status.push({
          label: "Company",
          value: this.actionData.company,
        });
      }
    },
    /** Retrieve data from child component */
    stopAction(event) {
      console.log("mapEvent", event);
      this.actionStarted = event;
    },
    showCreateBatch(event) {
      console.log(event.target.value);
    },
  },
  mounted() {
    /** Default farmers action on entering the page */
    this.changeAction(this.defaultAction);
  },
};
</script>
