<template>
  <div id="settings">
    <NavBar location="settings" />

    <!-- rest of the page here -->
    <div class="headerMenu">
      <div class="container">
        <h2 class="headerMenu-title">Settings</h2>
      </div>
    </div>
  </div>
</template>

<script>
import NavBar from "@/components/NavBar.vue";
export default {
  name: "Settings",
  components: {
    NavBar,
  },
  data() {
    return {
      message: null,
      user: this.$session.get("user"),
    };
  },
  methods: {},
  mounted() {},
};
</script>
