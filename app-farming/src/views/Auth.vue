<template>
  <div class="authBackground">
    <div class="authBox">
      <div
        class="alert alert-warning alert-dismissible fade show"
        role="alert"
        v-if="message.warning"
      >
        {{ message.warning }}
        <button type="button" class="close" aria-label="Close" @click="hideWarning">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div
        class="alert alert-success alert-dismissible fade show"
        role="alert"
        v-if="message.success"
      >
        {{ message.success }}
        <button type="button" class="close" aria-label="Close" @click="hideSuccess">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Login form -->
      <form class="loginForm" :class="{ activeForm: !signUp }">
        <div class="row authHeader">
          <div class="col-md-12">
            <h2 class="text-center">Login</h2>
            <h4 class="text-center">Farming Solution App</h4>
          </div>
        </div>
        <div class="row authForm">
          <div class="col-md-12 form-group">
            <input
              type="text"
              v-model="login.username"
              name="username"
              class="form-control"
              placeholder="Username"
              required
            />
          </div>
          <div class="col-md-12 form-group">
            <input
              type="password"
              v-model="login.password"
              name="password"
              class="form-control"
              placeholder="Password"
              required
            />
          </div>
          <div class="col-md-12 text-center mt-2 authAction">
            <button type="submit" class="btn btn-primary" @click.prevent="loginAction">
              Submit
            </button>
            <button
              id="register"
              type="button"
              class="btn btn-link"
              @click="toggleForms"
            >
              Register
            </button>
          </div>
        </div>
      </form>

      <!-- Register form -->
      <form class="registerForm" :class="{ activeForm: signUp }">
        <div class="row authHeader">
          <div class="col-md-12">
            <h2 class="text-center">Register</h2>
            <h4 class="text-center">Farming Solution App</h4>
          </div>
        </div>
        <div class="row authForm">
          <div class="col-sm-12 col-md-6 form-group">
            <input
              v-model="register.username"
              type="text"
              name="username"
              class="form-control"
              placeholder="Username"
              required
            />
          </div>
          <div class="col-sm-12 col-md-6 form-group">
            <input
              v-model="register.email"
              type="email"
              name="email"
              class="form-control"
              placeholder="Email"
              required
            />
          </div>
          <div class="col-sm-12 col-md-6 form-group">
            <input
              v-model="register.password1"
              type="password"
              name="password1"
              class="form-control"
              placeholder="Password"
              required
            />
          </div>
          <div class="col-sm-12 col-md-6 form-group">
            <input
              v-model="register.password2"
              type="password"
              name="password2"
              class="form-control"
              placeholder="Re-type Password"
              required
            />
          </div>
          <div class="col-sm-12 col-md-6 form-group">
            <input
              v-model="register.company"
              type="text"
              name="company"
              class="form-control"
              placeholder="Company"
            />
          </div>
          <div class="col-sm-12 col-md-6 form-group">
            <input
              v-model="register.phone"
              type="text"
              name="phone"
              class="form-control"
              placeholder="Phone"
            />
          </div>
          <div class="col-md-12 text-center mt-2 authAction">
            <button type="button" class="btn btn-primary" @click.prevent="registerAction">
              Submit
            </button>
            <button type="button" class="btn btn-light" @click="toggleForms">
              Cancel
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
// import axios from "axios";
export default {
  data() {
    return {
      signUp: false,
      message: {
        success: null,
        warning: null,
      },
      login: {
        username: null,
        password: null,
      },
      register: {
        username: null,
        password1: null,
        password2: null,
        email: null,
        phone: null,
        company: null,
      },
    };
  },
  methods: {
    loginAction() {
      this.hideWarning();
      this.hideSuccess();
      if (!this.login.username || !this.login.password) {
        this.message.warning = "Please fill in the username and password.";
        return;
      } else {
        this.$axios.post("/login", this.login).then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.$session.start();
            this.$session.set("userName", result.data[0]);
            this.redirect();
          } else {
            this.message.warning = result.message;
          }
        });
      }
    },
    registerAction() {
      this.hideWarning();
      this.hideSuccess();
      let requireInput = [];
      if (!this.register.username) {
        requireInput.push("username");
      }
      if (!this.register.password1) {
        requireInput.push("password");
      }
      if (!this.register.password2) {
        requireInput.push("confirm password");
      }
      if (!this.register.email) {
        requireInput.push("email");
      }
      if (!this.register.phone) {
        requireInput.push("phone");
      }
      if (!this.register.company) {
        requireInput.push("company");
      }
      if (requireInput.length > 0) {
        this.message.warning = "Please fill: " + requireInput.join(", ") + ".";
        return;
      } else {
        this.$axios.post("/register", this.register).then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.login.username = this.register.username;
            this.login.password = this.register.password1;
            this.signUp = false;
            this.message.success = "Your account was created!";
          } else {
            this.message.warning = result.message;
          }
        });
      }
    },
    toggleForms(){
      this.signUp = !this.signUp;
      this.hideWarning();
    },
    redirect() {
      this.$router.push({ name: "Home" });
    },
    hideWarning() {
      this.message.warning = null;
    },
    hideSuccess() {
      this.message.success = null;
    },
  },
};
</script>
