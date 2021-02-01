<template>
  <div class="home">
    <NavBar location="" />

    <!-- rest of the page here -->
    <div class="headerMenu">
      <div class="container">
        <h2 class="headerMenu-title">Dashboard</h2>
      </div>
    </div>

    <!-- Content -->
    <div class="container">
      <div class="row">
        <div class="col-sm-6 col-md-6 p-2 text-center">
          <a href="/package" class="homeCard"><img src="@/assets/images/icons/create-package.png" alt="">Create Package</a>
        </div>
        <div class="col-sm-6 col-md-6 p-2 text-center">
          <a href="/transport" class="homeCard"><img src="@/assets/images/icons/create-transport.png" alt="">Create Transport</a>
        </div>
        <div class="col-sm-6 col-md-6 p-2 text-center">
          <a href="/view-packages" class="homeCard"><img src="@/assets/images/icons/view-packages.png" alt="">View Packages</a>
        </div>
        <div class="col-sm-6 col-md-6 p-2 text-center">
          <a href="/view-transport" class="homeCard"><img src="@/assets/images/icons/view-transport.png" alt="">View Transport</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import NavBar from "@/components/NavBar.vue";
export default {
  name: "Home",
  components: {
    NavBar,
  },
};
</script>
