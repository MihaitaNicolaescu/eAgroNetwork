<template>
  <div id="login">
    <div class="container d-flex align-items-center flex-column">
      <div v-if="valid === false && valid !== null" class="container">
        <p class="not-found">Link invalid.</p>
      </div>
      <div v-if="valid === true && valid !== null" class="container my-5 d-flex align-items-center flex-column">
        <img class="logo" src="../assets/Logo.png">
        <h2>Resetare parola</h2>
        <div class="container my-5">
          <div class="row">
            <div class="col-md-6 mx-auto">
              <div class="form-group">
                <input v-model="password" type="text" id="name" class="form-control" required>
                <label class="form-control-placeholder" for="name">Parola</label>
              </div>
              <div>
                <button type="button" class="btn-login d-inline p-2" v-on:click="resetPassword">Resetare parola</button>
              </div>
            </div>
          </div>
        </div>
        <!-- Modal pentru verificare -->
        <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Este necesara verificarea contului.</h5>
              </div>
              <div class="modal-body">
                <div class="pre-formatted">Parola a fost resetata cu succes!<br>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" v-on:click="goToLogin">Login</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>

import axios from 'axios';
import {backend} from '@/constants';

export default {
  data(){
    return {
      valid: null,
      email: decodeURIComponent(this.$route.params.email),
      code: this.$route.params.code,
      password: null,
    }
  },
  mounted() {
    this.verify();
  },
  methods: {
    goToLogin: function(){
      // eslint-disable-next-line no-undef
      $('#confirmModal').modal('hide');
      this.$router.push('/login');
    },
    resetPassword: function(){
      if(this.password !== null && this.password !== ''){
        axios.post(backend + '/api/resetPassword', {
          email: this.email,
          newPassword: this.password,
        }).then(()=>{
          // eslint-disable-next-line no-undef
          $('#confirmModal').modal('show',{
            backdrop: 'static',
            keyboard: false,
          })
        }).catch((error)=>{
          console.log(error);
        })
      }
    },
    verify: function(){
      axios.post(backend + '/api/verifyRecovery', {
        email: this.email,
        code: this.code,
      }).then((respond)=>{
        if(respond.data['message'] === "INVALID") this.valid = false;
        if(respond.data['message'] === "success") this.valid = true;
      }).catch(()=>{
         this.valid = false;
      })
    },
  }
}
</script>

<style scoped>
.pre-formatted {
  white-space: pre;
}
button {
  position: relative;
  height: 45px;
  width: 250px;
  margin: 10px 7px;
  padding: 5px 5px;
  font-weight: 700;
  font-size: 15px;
  letter-spacing: 2px;
  color:#1b941d;
  border: 2px#1b941d solid;
  border-radius: 4px;
  outline: 0;
  overflow:hidden;
  background: none;
  z-index: 1;
  cursor: pointer;
  transition:         0.38s ease-in;
  -o-transition:      0.38s ease-in;
  -ms-transition:     0.38s ease-in;
  -moz-transition:    0.38s ease-in;
  -webkit-transition: 0.38s ease-in;
  box-shadow: 0  17px 10px -10px rgba(0, 0, 0, 0.4)
}
.form-control:focus{
  border-color: #1b941d;
  box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 8px rgba(35, 196, 38, 0.5);
}
.btn-login:hover {
  color: white;
  box-shadow: 0  37px 20px -20px rgba(0, 0, 0, 0.5);
  transform: translate(0, -10px);

}
button.btn-login:after {
  content: '';
  position: absolute;
  z-index: -1;
  -webkit-transition: all 0.4s;
  -moz-transition: all 0.4s;
  transition: all 0.4s;
  width: 100%;
  height: 0;
  top: 0;
  left: 0;
  background:#1b941d;
}
button.btn-login:hover:after,
button.btn-login:active:after {
  height: 100%;
}

.fill:hover:before {
  top: 0;
}
.input-group-text{
  width: 90px;
}
input {
  border-radius: 0;
  border: none;
  border-bottom: 2px solid #1b941d;
}
.logo{
  margin-bottom: 10px;
  width: 350px;
  height: 300px;
}
.form-group {
  position: relative;
  margin-bottom: 1.5rem;
}

.form-control-placeholder {
  position: absolute;
  top: 0;
  padding: 7px 0 0 13px;
  transition: all 200ms;
  opacity: 0.5;
}

.form-control:focus + .form-control-placeholder,
.form-control:valid + .form-control-placeholder {
  font-size: 20px;
  transform: translate3d(0, -100%, 0);
  opacity: 1;
}
</style>