<template>
  <div id="login">
    <div class="container d-flex align-items-center flex-column">
      <img class="logo" src="../assets/Logo.png">
      <h2>Recuperare parola</h2>
      <div class="container my-5">
        <div class="row">
          <div class="col-md-6 mx-auto">
            <div class="form-group">
              <input v-model="email" type="text" id="name" class="form-control" required>
              <label class="form-control-placeholder" for="name">Email</label>
              <div style="margin-top: 10px;" class="g-recaptcha" data-sitekey="6LdQBjgaAAAAAEWxNTAJS2_l0AY2rqwbl_QO-dwy" ></div>
            </div>
            <div>
              <button type="button" class="btn-login d-inline p-2" v-on:click="back">Login</button>
              <button id="sendRecovery" type="button" class="btn-login d-inline p-2" v-on:click="recoveryPassword">Trimite email</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal -->
      <div class="modal fade" id="verifyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ title }}</h5>
            </div>
            <div class="modal-body">
              {{content}}
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Inchide</button>
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
      email: '',
      //eslint-disable-next-line
      reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/,
      info: '',
      title: '',
      content: '',
    }
  },
  mounted(){
    let recaptchaScript = document.createElement('script')
    recaptchaScript.setAttribute('src', 'https://www.google.com/recaptcha/api.js')
    document.head.appendChild(recaptchaScript)
  },
  methods: {
    recoveryPassword: function(){
      // eslint-disable-next-line no-undef
      if(grecaptcha.getResponse().length === 0){
        this.title = "Eroare";
        this.content = "Va rugam sa completati campul captcha!";
        // eslint-disable-next-line no-undef
        $('#verifyModal').modal('show');
      }
      if(this.email !== null && this.email !== '' && this.reg.test(this.email)){
        // eslint-disable-next-line no-undef
        axios.post(backend + '/api/recoveryPassword', {
          email: this.email,
        }).then(()=>{
          this.title = "Resetare parola";
          this.content = "Pe email-ul introdus de dumneavoastra a fost trimis un email care contine datele pentru resetarea aprolei." +
              " Daca nu primiti email-ul verificati si in spam."
          // eslint-disable-next-line no-undef
          $('#verifyModal').modal('show');
        }).catch((error)=>{
          console.log(error);
        })
      }else{
        this.title = "Eroare";
        this.content = "Introduceti un email valid.";
        // eslint-disable-next-line no-undef
        $('#verifyModal').modal('show');
      }
    },
    back: function(){
      this.$router.go(-1);
    }
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
  width: 150px;
  margin: 10px 7px;
  padding: 5px 5px;
  font-weight: 700;
  font-size: 15px;
  letter-spacing: 2px;
  color:#9f5ac7;
  border: 2px#9f5ac7 solid;
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
  border-color: #9f5ac7;
  box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 8px rgba(255, 100, 255, 0.5);
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
  background:#9f5ac7;
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
  border-bottom: 2px solid #9f5ac7;
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