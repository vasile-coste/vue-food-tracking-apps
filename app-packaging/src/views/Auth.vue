<template>
  <div class="authBackground">
    <div class="authBox">
      <!-- Login form -->
      <form class="loginForm" :class="{ activeForm: !signUp }">
        <div class="row authHeader">
          <div class="col-md-12">
            <h2 class="text-center">Login</h2>
            <h4 class="text-center">Packaging Solution App</h4>
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
            <h4 class="text-center">Packaging Solution App</h4>
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
import helper from "@/js/helper";
export default {
  data() {
    return {
      signUp: false,
      login: {
        email: null,
        password: null,
        user_type: "transport",
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
        user_type: "transport",
      },
    };
  },
  methods: {
    loginAction() {
      if (!this.login.email || !this.login.password) {
        helper.showWarning("Please fill in the email and password.");
        return;
      } else {
        helper.toggleLoadingScreen(true);
        this.$axios
          .post("auth/login", this.login)
          .then((res) => {
            let result = JSON.parse(res.request.response);
            if (result.success) {
              this.$session.start();
              this.$session.set("user", result.data);
              helper.showSuccess(result.message);
              this.redirect();
            } else {
              helper.showWarning(result.message);
            }
            helper.toggleLoadingScreen(false);
          })
          .catch(() => {
            helper.toggleLoadingScreen(false);
          });
      }
    },
    registerAction() {
      let err = false;

      if (!this.register.first_name) {
        helper.showWarning("Please complete First name");
        err = true;
      }
      if (!this.register.last_name) {
        helper.showWarning("Please complete Last name");
        err = true;
      }
      if (!this.register.company) {
        helper.showWarning("Please complete Company");
        err = true;
      }
      if (!this.register.email) {
        helper.showWarning("Please complete Email");
        err = true;
      }
      if (!this.register.phone) {
        helper.showWarning("Please complete Phone");
        err = true;
      }
      if (!this.register.password1) {
        helper.showWarning("Please complete Password");
        err = true;
      }
      if (!this.register.password2) {
        helper.showWarning("Please complete Confirm password");
        err = true;
      }
      if (!this.register.address) {
        helper.showWarning("Please complete Address");
        err = true;
      }
      if (this.register.password1 && this.register.password2) {
        if (this.register.password1 !== this.register.password2) {
          helper.showWarning("Passwords don't match");
          err = true;
        }
      }

      if (err) {
        return;
      }

      helper.toggleLoadingScreen(true);
      this.$axios
        .post("auth/register", this.register)
        .then((res) => {
          let result = JSON.parse(res.request.response);
          if (result.success) {
            this.login.email = this.register.email;
            this.login.password = this.register.password1;
            this.signUp = false;
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
    toggleForms() {
      this.signUp = !this.signUp;
    },
    redirect() {
      this.$router.push({ name: "Home" });
    },
  },
};
</script>
