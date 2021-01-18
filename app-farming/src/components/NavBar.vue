<template>
  <nav class="navbar navbar-expand-md mainNavBar">
    <div
      v-bind:class="{ hamburgerClose: isHamburgerBtnActive }"
      @click="isHamburgerBtnActive = !isHamburgerBtnActive"
      class="navbar-toggler hamburgerButton"
      type="button"
      data-toggle="collapse"
      data-target="#navbarNav"
      aria-controls="navbarNav"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <div class="hamburgerButton-bar1"></div>
      <div class="hamburgerButton-bar2"></div>
      <div class="hamburgerButton-bar3"></div>
    </div>
    <a class="navbar-brand" href="/">FarmingApp</a>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mr-auto">
        <li
          class="nav-item"
          v-for="(item, index) in navItems"
          :key="index"
          :class="{ active: item.link == location }"
        >
          <a class="nav-link" :href="'/' + item.link">{{ item.text }}</a>
        </li>
      </ul>
      <div class="form-inline">
          <a class="useProfile" href="/profile">{{ user.first_name }}</a>
          <a class="userLogout" href="#" @click="logout">
            <img src="@/assets/images/header/logout.png" alt="Logout">
          </a>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: "NavBar",
  props: {
    location: String,
  },
  data() {
    return {
      isHamburgerBtnActive: false, 
      navItems: [
        {
          link: "",
          text: "Home",
        },
        {
          link: "settings",
          text: "Settings",
        }
      ],
      user: this.$session.get('user')
    };
  },
  methods: {
    logout(e) {
      e.preventDefault();
      this.$session.destroy();
      this.$router.push("/auth");
    },
  },
};
</script>
