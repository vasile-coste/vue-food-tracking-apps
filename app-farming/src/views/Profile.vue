<template>
  <div id="profile">
    <NavBar location="profile" />

    <!-- rest of the page here -->
    <div class="headerMenu">
      <div class="container">
        <h2 class="headerMenu-title">Profile</h2>
      </div>
    </div>

    <!-- Content -->
    <div class="container">
      <div class="row">
        <div class="col-md-7 col-sm-12">
          <h2 class="actionHeader">Update Profile</h2>
          <div class="form-group">
            <label for="user.email" class="col-form-label">Email:</label>
            <input
              type="text"
              class="form-control"
              id="user.email"
              v-model="user.email"
              disabled
            />
          </div>
          <div class="form-group">
            <label for="first_name" class="col-form-label">First Name:</label>
            <input
              type="text"
              class="form-control"
              id="first_name"
              v-model="profile.first_name"
            />
          </div>
          <div class="form-group">
            <label for="last_name" class="col-form-label">Last Name:</label>
            <input
              type="text"
              class="form-control"
              id="last_name"
              v-model="profile.last_name"
            />
          </div>
          <div class="form-group">
            <label for="company" class="col-form-label">Company:</label>
            <input
              type="text"
              class="form-control"
              id="company"
              v-model="profile.company"
            />
          </div>
          <div class="form-group">
            <label for="phone" class="col-form-label">Phone:</label>
            <input type="text" class="form-control" id="phone" v-model="profile.phone" />
          </div>
          <div class="form-group">
            <label for="address" class="col-form-label">Address:</label>
            <input
              type="text"
              class="form-control"
              id="address"
              v-model="profile.address"
            />
          </div>
          <div class="form-group">
            <label for="password" class="col-form-label">Current Password:</label>
            <input
              type="password"
              class="form-control"
              id="password"
              v-model="profile.current"
            />
          </div>
          <div class="form-group">
            <button type="button" class="btn btn-primary" @click="updateProfile">
              Update Profile
            </button>
          </div>
        </div>
        <div class="col-md-5 col-sm-12">
          <h2 class="actionHeader">Update Password</h2>
          <div class="form-group">
            <label for="current" class="col-form-label">Current Password:</label>
            <input
              type="password"
              class="form-control"
              id="current"
              v-model="password.current"
            />
          </div>
          <div class="form-group">
            <label for="new1" class="col-form-label">New Password:</label>
            <input type="password" class="form-control" id="new1" v-model="password.new1" />
          </div>
          <div class="form-group">
            <label for="new2" class="col-form-label">Re-type new Password:</label>
            <input type="password" class="form-control" id="new2" v-model="password.new2" />
          </div>
          <div class="form-group">
            <button type="button" class="btn btn-primary" @click="updatePassword">
              Update Password
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import helper from "@/js/helper";
import NavBar from "@/components/NavBar.vue";
export default {
  name: "Profile",
  components: {
    NavBar,
  },
  data() {
    return {
      message: null,
      user: this.$session.get("user"),
      profile: {
        first_name: null,
        last_name: null,
        company: null,
        phone: null,
        address: null,
        current: null,
      },
      password: {
        current: null,
        new1: null,
        new2: null,
      },
    };
  },
  methods: {
    updateProfile() {
      let err = false;
      if (!this.profile.first_name) {
        helper.showWarning("Please complete First name");
        err = true;
      }
      if (!this.profile.last_name) {
        helper.showWarning("Please complete Last name");
        err = true;
      }
      if (!this.profile.company) {
        helper.showWarning("Please complete Company");
        err = true;
      }
      if (!this.profile.phone) {
        helper.showWarning("Please complete Phone");
        err = true;
      }
      if (!this.profile.address) {
        helper.showWarning("Please complete Address");
        err = true;
      }
      if (!this.profile.current) {
        helper.showWarning("Please complete Current Password");
        err = true;
      }

      if (err) {
        return;
      }

      this.profile.id = this.user.id;

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("auth/update-profile", this.profile)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.user = result.data;
            this.$session.remove("user");
            this.$session.set("user", this.user);
            helper.showSuccess(result.message);
          } else {
            helper.showWarning(result.message);
          }
          helper.toggleLoadingScreen(false);
        })
        .catch(() => {
          helper.toggleLoadingScreen(false);
        });
    },
    updatePassword() {
      let err = false;

      if (!this.password.current) {
        helper.showWarning("Please complete Current Password");
        err = true;
      }
      if (!this.password.new1) {
        helper.showWarning("Please complete New Password");
        err = true;
      }
      if (!this.password.new2) {
        helper.showWarning("Please re-enter new password");
        err = true;
      }

      if (this.password.new1 && this.password.new2) {
        if (this.password.new1 !== this.password.new2) {
          helper.showWarning("Passwords don't match");
          err = true;
        }
      }

      if (err) {
        return;
      }

      this.password.id = this.user.id;

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("auth/update-password", this.password)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            helper.showSuccess(result.message);
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
    this.profile.first_name = this.user.first_name;
    this.profile.last_name = this.user.last_name;
    this.profile.company = this.user.company;
    this.profile.phone = this.user.phone;
    this.profile.address = this.user.address;
  },
};
</script>
