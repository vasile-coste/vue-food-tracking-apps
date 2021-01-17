<template>
  <div id="profile">
    <NavBar location="profile" />

    <!-- rest of the page here -->
    <div class="container">
      <div
        class="modal fade show"
        id="notificationModal"
        tabindex="-1"
        role="dialog"
        aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Response</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">{{ message }}</div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

      <h2>Personal information</h2>
      <div class="row">
        <div class="col-md-4 col-sm-12 profile">Username</div>
        <div class="col-md-8 col-sm-12 profile">
          <input type="text" v-model="user.username" class="form-control" disabled />
        </div>
        <div class="col-md-4 col-sm-12 profile">Email</div>
        <div class="col-md-8 col-sm-12 profile">
          <input type="text" v-model="user.email" class="form-control" disabled />
        </div>
        <div class="col-md-4 col-sm-12 profile">Phone</div>
        <div class="col-md-8 col-sm-12 profile">
          <input type="text" v-model="user.phone" class="form-control" />
        </div>
        <div class="col-md-4 col-sm-12 profile">Company</div>
        <div class="col-md-8 col-sm-12 profile">
          <input type="text" v-model="user.company" class="form-control" />
        </div>
        <div class="col-md-12 col-sm-12 profile">
          <button type="button" class="btn btn-success mt-2" @click="updateProfile">Update Profile</button>
        </div>
      </div>

      <h2 class="mt-2">Update password</h2>
      <div class="row">
        <div class="col-md-4 col-sm-12 profile">Old Password</div>
        <div class="col-md-8 col-sm-12 profile">
          <input type="password" v-model="password.old" class="form-control" />
        </div>
        <div class="col-md-4 col-sm-12 profile">New Password</div>
        <div class="col-md-8 col-sm-12 profile">
          <input type="password" v-model="password.password1" class="form-control" />
        </div>
        <div class="col-md-4 col-sm-12 profile">Retype Password</div>
        <div class="col-md-8 col-sm-12 profile">
          <input type="password" v-model="password.password2" class="form-control" />
        </div>
        <div class="col-md-12 col-sm-12 profile">
          <button type="button" class="btn btn-success mt-2" @click="updatePassword">Update Password</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import NavBar from "@/components/NavBar.vue";
import $ from "jquery";
export default {
  name: "Profile",
  components: {
    NavBar
  },
  data() {
    return {
      message: null,
      user: this.$session.get("user"),
      password: {
        old: null,
        password1: null,
        password2: null
      }
    };
  },
  methods: {
    logout() {
      this.$session.destroy();
      this.$router.push("/auth");
    },
    updateProfile() {
      this.message = null;
      let profileData = {
        id: this.user.id,
        company: this.user.company,
        phone: this.user.phone
      };
      this.$axios.post("/updateProfile", profileData).then(res => {
        let result = JSON.parse(res.request.response);
        if (result.success) {
          this.message = "Profile updated!";
          this.$session.remove("user");
          this.$session.set("user", result.data[0]);
          $("#notificationModal").modal("show");
        } else {
            this.message = result.message != 0 ? result.message : "Nothing to update!";
            $("#notificationModal").modal("show");
        }
      });
    },
    updatePassword() {
      this.message = null;
      let passwordData = {
        id: this.user.id,
        old: this.password.old,
        password1: this.password.password1,
        password2: this.password.password2
      };
      this.$axios.post("/updatePassword", passwordData).then(res => {
        let result = JSON.parse(res.request.response);
        if (result.success) {
          this.message = "Password updated!";
          this.$session.remove("user");
          this.$session.set("user", result.data[0]);
          $("#notificationModal").modal("show");
          this.password.old = null;
          this.password.password1 = null;
          this.password.password2 = null;
        } else {
            this.message = result.message != 0 ? result.message : "Nothing to update!";
            $("#notificationModal").modal("show");
        }
      });
    }
  },
  mounted() {}
};
</script>