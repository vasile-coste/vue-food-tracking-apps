<template>
  <div class="authBackground">
    <div class="authBox">
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
      login: {
        email: null,
        password: null,
        user_type: "farmer",
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
        user_type: "farmer",
      },
    };
  },
  methods: {
    loginAction() {
      if (!this.login.email || !this.login.password) {
        this.showWarning("Please fill in the email and password.");
        return;
      } else {
        this.$axios.post("auth/login", this.login).then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.$session.start();
            this.$session.set("user", result.data);
            this.showSuccess(result.message);
            this.redirect();
          } else {
            this.message.warning = result.message;
            this.showWarning(result.message);
          }
        });
      }
    },
    registerAction() {
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
        let errMsg = "";

        if (requireInput.length > 0) {
          errMsg += "Please fill: " + requireInput.join(", ") + ". ";
        }
        if (passwordMatch) {
          errMsg += passwordMatch;
        }

        this.showWarning(errMsg);

        return;
      } else {
        this.$axios.post("auth/register", this.register).then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.login.email = this.register.email;
            this.login.password = this.register.password1;
            this.signUp = false;
            this.showSuccess(result.message);
          } else {
            this.showWarning(result.message);
          }
        });
      }
    },
    toggleForms() {
      this.signUp = !this.signUp;
    },
    redirect() {
      this.$router.push({ name: "Home" });
    },
    showWarning(msg) {
      this.$notify({
        group: "app",
        text: msg,
        type: "error",
      });
    },
    showSuccess(msg) {
      this.$notify({
        group: "app",
        text: msg,
        type: "success",
      });
    },
  },
};
</script>
