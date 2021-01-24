<template>
    <div class="container d-flex align-items-center flex-column">
        <img class="logo" src="../assets/Logo.png">
        <h2>Creați un cont</h2>
        <div class="col-md-6 mx-auto">
            <div class="form-group">
              <input v-model="firstName" type="text" id="firstName" class="form-control" required>
              <label class="form-control-placeholder" for="firstName">Numele</label>
            </div>
            <div class="form-group">
              <input v-model="lastName" type="text" id="lastName" class="form-control" required>
              <label class="form-control-placeholder" for="lastName">Prenumele</label>
            </div>
          <div class="form-group">
            <input v-model="judet" type="text" id="judet" class="form-control" required>
            <label class="form-control-placeholder" for="judet">Judet</label>
          </div>
            <div class="form-group">
              <input v-model="email" type="email" id="email" class="form-control"  required>
              <label class="form-control-placeholder" for="email">Email</label>
            </div>
            <div class="form-group">
              <input v-model="password" type="password" id="password" class="form-control" required>
              <label class="form-control-placeholder" for="lastName">Parolă</label>
            </div>
            <div class="form-group">
              <input v-model="checkPassword" type="password" id="checkPassword" class="form-control" required>
              <label class="form-control-placeholder" for="checkPassword">Verificare parolă</label>
            </div>
            <div class="form-group">
              <input v-model="birthday" onfocusout="(this.type='text')" onfocus="(this.type='date')" id="birthday" class="form-control" required>
              <label class="form-control-placeholder" for="birthday">Zi de naștere</label>
            </div>
          <div class="g-recaptcha" data-sitekey="6LdQBjgaAAAAAEWxNTAJS2_l0AY2rqwbl_QO-dwy" ></div>
          <button id="register" class="btn-login d-inline p-2" v-on:click="verifyFields">Creați contul</button>
          <p class="d-inline p-2">Dețineți deja un cont? <router-link :to="{path: 'login'}">   Apasă aici</router-link></p>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="confirmEmailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 v-if="!loading" class="modal-title" id="exampleModalLongTitle">{{title}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div v-if="!loading" class="modal-body">
                {{content}}
              </div>
              <div v-else class="modal-body">
                <div class="loader">
                  <span></span>
                  <span></span>
                  <span></span>
                  <span></span>
                  <span></span>
                </div>
              </div>
              <div class="modal-footer">
                <button v-if="!error && !loading" type="button" class="btn btn-secondary" data-dismiss="modal" v-on:click="login">Am inteles</button>
                <button v-if="error && !loading" type="button" class="btn btn-secondary" data-dismiss="modal">Am inteles</button>
              </div>
            </div>
          </div>
        </div>
    </div>
</template>

<script>

// eslint-disable-next-line no-unused-vars
    function enableBtn(){
      document.getElementById("register").disabled = false;
    }

    import axios from 'axios';
import {backend} from '../constants.js';
    export default{
        data(){
            return{
                firstName: '',
                lastName: '',
                judet: '',
                email: '',
                password: '',
                birthday: '',
                title: "",
                error: 1,
                content: "",
                checkPassword: '',
                loading: 0,
                //eslint-disable-next-line
                reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/
            }
        },
      mounted(){
        let recaptchaScript = document.createElement('script')
        recaptchaScript.setAttribute('src', 'https://www.google.com/recaptcha/api.js')
        document.head.appendChild(recaptchaScript)
      },
        methods: {
            login: function(){
              this.$router.push('login');
            },
            registerUser: function(){
              // eslint-disable-next-line no-undef
              $('#confirmEmailModal').modal('show');
              this.loading = 1;
                axios.post(backend + '/api/addUser', {
                    firstName: this.firstName,
                    lastName: this.lastName,
                    email: this.email,
                    password: this.password,
                    birthday: this.birthday,
                    judet: this.judet,
                    headers: {
                        'Access-Control-Allow-Origin': '*',
                        'Content-Type': 'application/json',
                    }
                }).then((res) => {
                     if(res.data['message'] === 'success'){
                       this.title = "Contul a fost creeat cu succes";
                       this.content = "Mai este doar un pas pana sa iti poti accesa contul. Pe emailul " + this.email +" a fost trimis un link pentru activarea contului."
                       this.error = 0;
                       this.loading = 0;
                     }
                    })
                    .catch((err) => {
                        if(err){
                          this.title = "Eroare";
                          this.content = "Emailul introdus este deja folosit.Daca problema persista va rugam sa contactati un administrator la eagronetwork@gmail.com."
                          this.error = 1;
                          this.loading = 0;
                          // eslint-disable-next-line no-undef
                        }
                 });
            },
            verifyFields: function(){
              // eslint-disable-next-line no-undef
              if(grecaptcha.getResponse().length === 0){
                this.title = "Eroare";
                this.content = "Va rugam sa completati campul captcha!";
                // eslint-disable-next-line no-undef
                $('#confirmEmailModal').modal('show');
              }
                else if(!this.firstName && !this.lastName && !this.email && !this.password && !this.checkPassword && !this.birthday && !this.judet) {
                this.title = "Eroare";
                this.content = "Va rugam sa completati toate campurile pentru a inregistra un cont.";
                // eslint-disable-next-line no-undef
                $('#confirmEmailModal').modal('show');
              }
                else if(this.password !== this.checkPassword){
                this.title = "Eroare";
                this.content = "Parolele introduse nu concid";
                // eslint-disable-next-line no-undef
                $('#confirmEmailModal').modal('show');
              }
                else if(!this.reg.test(this.email)) {
                this.title = "Eroare";
                this.content = "Adresa de email introdusa este invalida. Aceasta trebuie sa aiba forma 'email@exemplu.com'";
                // eslint-disable-next-line no-undef
                $('#confirmEmailModal').modal('show');
              }
                else this.registerUser();

            }
        }
    }

</script>

<style scoped>
  .loader {
    text-align: center;
    vertical-align: middle;
    position: relative;
    display: flex;
    background: white;
    padding: 150px;
  }

  .loader span {
    display: block;
    width: 20px;
    height: 20px;
    background: #eee;
    border-radius: 50%;
    margin: 0 5px;
    box-shadow: 0 2px 2px rgba(0, 0, 0, 0.2);
  }

  .loader span:nth-child(2) {
    background: #f07e6e;
  }

  .loader span:nth-child(3) {
    background: #84cdfa;
  }

  .loader span:nth-child(4) {
    background: #5ad1cd;
  }

  .loader span:not(:last-child) {
    animation: animate 1.5s linear infinite;
  }

  @keyframes animate {
    0% {
      transform: translateX(0);
    }

    100% {
      transform: translateX(30px);
    }
  }

  .loader span:last-child {
    animation: jump 1.5s ease-in-out infinite;
  }

  @keyframes jump {
    0% {
      transform: translate(0, 0);
    }
    10% {
      transform: translate(10px, -10px);
    }
    20% {
      transform: translate(20px, 10px);
    }
    30% {
      transform: translate(30px, -50px);
    }
    70% {
      transform: translate(-150px, -50px);
    }
    80% {
      transform: translate(-140px, 10px);
    }
    90% {
      transform: translate(-130px, -10px);
    }
    100% {
      transform: translate(-120px, 0);
    }
  }

    .logo{
        margin-bottom: 10px;
        width: 450px;
        height: 400px;
        z-index: 2;
    }
     .pre-formatted {
       white-space: pre;
     }
    button.btn-login {
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

