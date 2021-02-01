<template>
  <div class="view-transport">
    <NavBar location="view-transport" />

    <!-- rest of the page here -->
    <div class="headerMenu">
      <div class="container">
        <h2 class="headerMenu-title"><img src="@/assets/images/icons/view-transport-round.png" alt="">View Transport</h2>
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
  name: "ViewTransport",
  components: {
    NavBar,
  },
};
</script>
