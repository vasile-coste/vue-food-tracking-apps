<template>
  <div class="container">
    <h2 class="actionHeader">Product Details</h2>
    <div class="row">
      <div class="col-md-4 col-sm-4">
        <img class="productImg" src="@/assets/images/product.png" />
      </div>
      <div class="col-md-8 col-sm-8">
        <div class="prod-label">Product Name</div>
        <div class="prod-value">{{ timelineData.product.product_name }}</div>
        <div class="prod-label">Product Weight</div>
        <div class="prod-value">{{ timelineData.product.product_weight }}</div>

        <hr />

        <div class="prod-label">Field Name</div>
        <div class="prod-value">{{ timelineData.field.field_name }}</div>
        <div class="prod-label">Seed Name</div>
        <div class="prod-value">{{ timelineData.field.seed_name }}</div>
        <div class="prod-label">Seed Company Name</div>
        <div class="prod-value">{{ timelineData.field.seed_company_name }}</div>
        <div class="prod-label">Fertilizer Name</div>
        <div class="prod-value">{{ timelineData.field.fertilizer_name }}</div>
        <div class="prod-label">Fertilizer Company Name</div>
        <div class="prod-value">{{ timelineData.field.fertilizer_company_name }}</div>
        <div class="prod-label">Harvesting Company Name</div>
        <div class="prod-value">{{ timelineData.field.harvesting_company_name }}</div>

        <hr />

        <div class="prod-label">Pack Name</div>
        <div class="prod-value">{{ timelineData.pack.pack_name }}</div>

        <hr />

        <div class="prod-label">Transport From</div>
        <div class="prod-value">{{ timelineData.pack.ship_from }}</div>
        <div class="prod-label">Transport To</div>
        <div class="prod-value">{{ timelineData.pack.ship_to }}</div>
        <div class="prod-label">Transport Company</div>
        <div class="prod-value">{{ timelineData.pack.ship_company }}</div>
        <div class="prod-label">Market</div>
        <div class="prod-value">{{ timelineData.pack.market }}</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Product",
  props: {
    timelineData: {},
  },
  data() {
    return {};
  },
  methods: {},
};
</script>
