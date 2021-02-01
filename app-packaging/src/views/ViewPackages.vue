<template>
  <div class="view-packages">
    <NavBar location="view-packages" />

    <!-- rest of the page here -->
    <div class="headerMenu">
      <div class="container">
        <h2 class="headerMenu-title"><img src="@/assets/images/icons/view-packages-round.png" alt="">View Packages</h2>
      </div>
    </div>

    <!-- Content -->
    <div class="container">
      <div class="row">
        Content
      </div>
    </div>
  </div>
</template>

<script>
import NavBar from "@/components/NavBar.vue";
export default {
  name: "ViewPackages",
  components: {
    NavBar,
  },
};
</script>
