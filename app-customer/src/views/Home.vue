<template>
  <div class="home">
    <!-- Content -->
    <div class="mainNavBar">
      <div class="container">
        Customer App
      </div>
    </div>
    <div class="headerMenu">
      <div class="container">
        <h2 class="headerMenu-title">QR Result</h2>
      </div>
    </div>

    <div class="container">
      <Product :timelineData="timelineData" v-if="urlPath == 'product' && hasData" />
      <Package :timelineData="timelineData" v-if="urlPath == 'package' && hasData" />
      <Transport :timelineData="timelineData" v-if="urlPath == 'transport' && hasData" />
      <QrNotFound v-if="!hasData" />
    </div>
  </div>
</template>

<script>
import helper from "@/js/helper";
import Product from "@/components/Product.vue";
import Package from "@/components/Package.vue";
import Transport from "@/components/Transport.vue";
import QrNotFound from "@/components/QrNotFound.vue";
export default {
  name: "Home",
  components: {
    Product,
    Package,
    Transport,
    QrNotFound,
  },
  data() {
    return {
      urlPath: null,
      timelineData: {},
      hasData: false,
    };
  },
  methods: {
    getData() {
      this.hasData = false;
      let param = this.$route.params.qr;
      let action = param.split("-");
      this.urlPath = action[0];

      helper.toggleLoadingScreen(true);
      this.$axios
        .get(`customer/${param}`)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.timelineData = result.data;
            this.hasData = true;
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
  mounted() {
    this.getData();
  },
};
</script>
