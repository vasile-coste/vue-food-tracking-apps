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
              v-model="login.email"
              class="form-control"
              placeholder="Username"
              required
            />
          </div>
          <div class="col-md-12 form-group">
            <input
              type="password"
              v-model="login.password"
              class="form-control"
              placeholder="Password"
              required
            />
          </div>
          <div class="col-md-12 text-center mt-2 authAction">
            <button type="submit" class="btn btn-primary" @click.prevent="loginAction">
              Submit
            </button>
            <button id="register" type="button" class="btn btn-link" @click="toggleForms">
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
              v-model="register.first_name"
              type="text"
              class="form-control"
              placeholder="First Name"
              required
            />
          </div>
          <div class="col-sm-12 col-md-6 form-group">
            <input
              v-model="register.last_name"
              type="text"
              class="form-control"
              placeholder="Last Name"
              required
            />
          </div>
          <div class="col-sm-12 col-md-6 form-group">
            <input
              v-model="register.company"
              type="text"
              class="form-control"
              placeholder="Company"
              required
            />
          </div>
          <div class="col-sm-12 col-md-6 form-group">
            <input
              v-model="register.phone"
              type="text"
              class="form-control"
              placeholder="Phone"
              required
            />
          </div>
          <div class="col-sm-12 col-md-6 form-group">
            <input
              v-model="register.email"
              type="email"
              class="form-control"
              placeholder="Email"
              required
            />
          </div>
          <div class="col-sm-12 col-md-6 form-group">
            <input
              v-model="register.password1"
              type="password"
              class="form-control"
              placeholder="Password"
              required
            />
          </div>
          <div class="d-none d-md-block col-md-6 form-group"></div>
          <div class="col-sm-12 col-md-6 form-group">
            <input
              v-model="register.password2"
              type="password"
              class="form-control"
              placeholder="Re-type Password"
              required
            />
          </div>
          <div class="col-sm-12 col-md-12 form-group">
            <textarea
              v-model="register.address"
              class="form-control"
              placeholder="Address"
              required
            ></textarea>
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
        email: null,
        password: null,
        user_type: "farmer"
      },
      register: {
        first_name: null,
        last_name: null,
        company: null,
        phone: null,
        email: null,
        password1: null,
        password2: null,
        address: null,
        user_type: "farmer"
      },
    };
  },
  methods: {
    loginAction() {
      this.hideWarning();
      this.hideSuccess();
      if (!this.login.email || !this.login.password) {
        this.message.warning = "Please fill in the email and password.";
        return;
      } else {
        this.$axios.post("auth/login", this.login).then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.$session.start();
            this.$session.set("user", result.data);
            this.message.success = result.data;
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
      let passwordMatch = null;

      if (!this.register.first_name) {
        requireInput.push("First name");
      }
      if (!this.register.last_name) {
        requireInput.push("Last name");
      }
      if (!this.register.company) {
        requireInput.push("Company");
      }
      if (!this.register.email) {
        requireInput.push("Email");
      }
      if (!this.register.phone) {
        requireInput.push("Phone");
      }
      if (!this.register.password1) {
        requireInput.push("Password");
      }
      if (!this.register.password2) {
        requireInput.push("Confirm password");
      }
      if (!this.register.address) {
        requireInput.push("Address");
      }
      if (this.register.password1 && this.register.password2) {
        passwordMatch =
          this.register.password1 !== this.register.password2
            ? "Passwords don't match."
            : "";
      }

      if (requireInput.length > 0 || passwordMatch) {
        this.message.warning = "";

        if (requireInput.length > 0) {
          this.message.warning += "Please fill: " + requireInput.join(", ") + ". ";
        }
        if (passwordMatch != "") {
          this.message.warning += passwordMatch;
        }

        return;
      } else {
        this.$axios.post("auth/register", this.register).then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.login.email = this.register.email;
            this.login.password = this.register.password1;
            this.signUp = false;
            this.message.success = "Your account was created!";
          } else {
            this.message.warning = result.message;
          }
        });
      }
    },
    toggleForms() {
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
