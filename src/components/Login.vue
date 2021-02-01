<template>
    <div id="login">
        <div class="container d-flex align-items-center flex-column">
            <img class="logo" src="../assets/Logo.png">
            <h2>Conectează-te în cont</h2>
            <div class="container my-5">
                <div class="row">
                    <div class="col-md-6 mx-auto">
                        <div class="form-group">
                            <input v-model="email" type="text" id="name" class="form-control" required>
                            <label class="form-control-placeholder" for="name">Email</label>
                        </div>
                        <div class="form-group">
                            <input v-model="password" type="password" id="password" class="form-control" required>
                            <label class="form-control-placeholder" for="password">Parola</label>
                        </div>
                        <div>
                            <button type="button" class="btn-login d-inline p-2" v-on:click="checkForms">Conectează-te</button>
                            <p class="d-inline p-2">Nu ai un cont? <router-link :to="{path: 'register'}">   Apasă aici</router-link></p>
                          <p class="p-2">Ai uitat parola? <router-link :to="{path: 'recovery'}">   Apasă aici</router-link></p>
                        </div>
                    </div>
                </div>
            </div>      
        </div>
      <!-- Modal pentru invalid credentials -->
        <div class="modal fade" id="modalError" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Date incorecte</h5>
              </div>
              <div class="modal-body">
                Adresa de email sau parola nu sunt corecte!
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
      <!-- Modal pentru ban -->
      <div class="modal fade" id="bannedModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">BANNED</h5>
            </div>
            <div class="modal-body">
              <div class="pre-formatted">{{ info }}</div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Inchide</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal pentru verificare -->
      <div class="modal fade" id="verifyModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Este necesara verificarea contului.</h5>
            </div>
            <div class="modal-body">
              <div class="pre-formatted">Pentru a v-a putea accesa contul este necesara activarea acestuia.
                <br>Pe email a fost trimis un link care trebuie accesat pentru a verifica<br> daca dumneavoastra detineti acel email.
                <br>Daca nu regasiti emailul in lista, cautati si in spam, daca nu sa trimis<br>va rugam sa apasati butonul de mai jos pentru a retrimite un email<br>care contine acel link.</div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" v-on:click="sendEmail">Retrimite</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Inchide</button>
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
                password: '',
                //eslint-disable-next-line
                reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/,
                info: '',
            }
        },
        methods: {
            sendEmail: function(){
              axios.post(backend + '/api/sendEmail', {
                email: this.email,
              }).then(()=>{
                // eslint-disable-next-line no-undef
                $('#verifyModal').modal('hide');
              }).catch((error)=>{
                console.log(error);
              })
            },
            login: function(){
                axios.post(backend + '/api/login', {
                    email: this.email,
                    password: this.password,
                    headers:{
                        'Access-Control-Allow-Origin': '*',
                        'Content-Type': 'application/json',
                    }
                }).then((res) =>{
                  if(res.data['message'] === "failed"){
                    // eslint-disable-next-line no-undef
                    $('#modalError').modal('show');
                  }else if(res.data['message'] === "IS_BANNED"){
                     this.info = res.data['info'];
                    // eslint-disable-next-line no-undef
                    $('#bannedModal').modal('show');
                  }else if(res.data['message'] === "NOT_VERIFY"){
                    this.info = res.data['info'];
                    // eslint-disable-next-line no-undef
                    $('#verifyModal').modal('show');
                  }else{
                    localStorage.setItem('token', res.data['token']);
                    localStorage.setItem('admin', res.data['admin']);
                    this.$router.push('/');
                  }
                }).catch((error)=>{
                    console.log(error);
                });
            },
            checkForms: function(){
                if(!this.email && !this.password)   // eslint-disable-next-line no-undef
                  $('#modalError').modal('show');
                else if(!this.password)  // eslint-disable-next-line no-undef
                  $('#modalError').modal('show');
                else if(!this.email){
                  // eslint-disable-next-line no-undef
                  $('#modalError').modal('show');
                }
                else if(!this.reg.test(this.email))  // eslint-disable-next-line no-undef
                  $('#modalError').modal('show');
                else this.login();
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
        width: 150px;
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
    .logo{
        margin-bottom: 10px;
        width: 350px;
        height: 300px;
    }
    input {
      border-radius: 0;
      border: none;
      border-bottom: 2px solid #1b941d;
    }
    .form-control:focus{
      border-color: #1b941d;
      box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 8px rgba(35, 196, 38, 0.5);
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