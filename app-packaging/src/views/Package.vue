<template>
  <div class="package">
    <NavBar location="package" />

    <!-- rest of the page here -->
    <div class="headerMenu">
      <div class="container">
        <h2 class="headerMenu-title"><img src="@/assets/images/icons/create-package-round.png" alt="">Package</h2>
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
  name: "Package",
  components: {
    NavBar,
  },
};
</script>
