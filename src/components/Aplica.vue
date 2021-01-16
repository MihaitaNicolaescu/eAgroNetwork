<template>
    <div class="container">
        <nav class="navbar navbar-light bg-light">
           <button class="btn btn-home" v-on:click="home"><span class="material-icons">home</span></button>              
        </nav>
        <h3 style="text-align: center;">Aplicatie pentru gradul de producator</h3>
        <div class="edit" v-if="checkIfHasAplication === false || (application !== null && application.pending === 0 && application.status !== -1)">
            <form class="form-group">
                <div class="d-flex align-items-center flex-column">
                    <div class="d-flex flex-row">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Nume de familie</span>
                        </div>
                        <input v-model="firstName" style="width: 200px" type="text" class="form-control" id="firstNameInput" required>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Prenume</span>
                        </div>
                        <input v-model="lastName" style="width: 200px" type="text" class="form-control" id="lastNameInput" required>
                    </div>
                    </div>
                    <div class="d-flex flex-row adresa">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Judet</span>
                            </div>
                            <input v-model="judet" style="width: 200px;" type="text" class="form-control" id="judet" required>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span style="margin-left: 75px" class="input-group-text">Localitate</span>
                            </div>
                            <input v-model="localitate" style="width: 200px;" type="text" class="form-control" id="localitate" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Adresa completa</span>
                        </div>
                        <input v-model="adresa" style="width: 488px;" type="text" class="form-control" id="adress" required>
                    </div>
                    <div class="d-flex flex-row">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Seria</span>
                            </div>
                            <input v-model="seria" style="width: 50px;" type="text" class="form-control" id="serie_ci" maxlength="2" required>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span style="margin-left: 40px;" class="input-group-text">Numarul</span>
                            </div>
                            <input v-model="numar_serie" style="width: 100px;" type="number" class="form-control" id="numar_serie_ci" minlength="6" maxlength="6" required>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span style="margin-left: 40px;" class="input-group-text">CNP</span>
                            </div>
                            <input v-model="cnp" style="width: 200px" type="number" class="form-control" id="cnp" minlength="15" maxlength="15" required>
                        </div>
                    </div>
                    <div class="d-flex flex-row">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Nr. certificat</span>
                            </div>
                            <input v-model="nr_certificat" style="width: 170px;" type="number" class="form-control" id="serie_certificat" required>
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span style="margin-left: 70px;" class="input-group-text">Primaria</span>
                            </div>
                            <input v-model="primaria" style="width: 200px;" type="text" class="form-control" id="primaria" required>
                        </div>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="copie_certificat" @change="onCopieCertificatSelected" accept=".jpg, .png, .pdf" required>
                        <label class="custom-file-label" for="copie_certificat">Selectati copia certificatului de producator</label>
                        <div class="invalid-feedback">Invalid file</div>
                    </div>
                    <div class="custom-file">
                        <input type="file"  class="custom-file-input" id="copie_ci" @change="onCopieCISelected" required accept=".jpg, .png,.pdf">
                        <label class="custom-file-label" for="copie_ci">Selectati copia C.I/B.I</label>
                        <div class="invalid-feedback">Invalid file</div>
                    </div>
                    <div class="input-group">
                        <textarea v-model="alte_precizari" placeholder="*Alte precizari" class="form-control textarea" id="altele" rows="3"></textarea>
                    </div>
                </div>
                <button class="btn btn-success" type="button" data-toggle="modal" data-target="#confirmModal" v-on:click="verifyForm">Trimite aplicatia</button>
            </form>
        </div>
      <div v-if="checkIfHasAplication === true && checkIfHasAplication!=null && application.pending === 1">
        <p>Aplicatia este in curs de procesare, va rugam sa asteptati raspunsul!</p>
        <p>Pentru a anula aplicatia trimisa va rugam sa apasati butonul de mai jos.</p>
        <button data-toggle="modal" class="btn btn-danger" data-target="#deleteModal">Anuleaza aplicatia</button>
      </div>
      <div v-if="checkIfHasAplication === true && checkIfHasAplication!=null && application.pending === 0 && application.status === -1">
        <p>Aplicatia a fost respinsa, motivul se poate regasi mai jos!</p>
        <p>Puteti trimite o noua aplicatie corectand greselile specificate in motivul respingeri, daca trimiteti o aplicatie de mai multe ori
          puteti primi interdictie la acest grad.</p>
          <p>Motivul respingeri: {{application.motiv_respingere}}</p>
        <button class="btn btn-danger" v-on:click="enableEditApplication">Editeaza aplicatia</button>
      </div>
      <!--Modal pentru confirmarea stergerii aplicatiei -->
      <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="deleteModalLongTitle">Confirmare</h5>
            </div>
            <div class="modal-body">
              <p>Sunteti sigur ca doriti sa stergeti aplicatia trimisa?</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuleaza</button>
              <button type="button" class="btn btn-primary" data-dismiss="modal" v-on:click="deleteApplication">Sterge</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal pentru confirmare trimitere aplicatie -->
      <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="confirmModalLongTitle">Confirmare</h5>
            </div>
            <div class="modal-body">
                <p>{{infoForm}}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Continuă editarea</button>
              <button v-if="validForm" type="button" class="btn btn-primary" data-dismiss="modal" v-on:click="sendForm">Trimite formularul</button>
            </div>
          </div>
        </div>
      </div>
        <alert-box></alert-box>
    </div>
</template>

<script>
    import {backend} from '@/constants.js';
    import axios from 'axios';
    import alertBox from './templates/invalidToken';

    export default{
        data(){
            return{
                infoForm: 'Va rog sa confirmați trimiterea formularului. Daca formularul nu contine date reale riscati sa primiti interdictie pentru a accesa acest grad.',
                id: null,
                email: null,
                firstName: '',
                lastName: '',
                judet: '',
                localitate: '',
                adresa: '',
                seria: '',
                numar_serie: '',
                cnp: '',
                nr_certificat: '',
                primaria: '',
                alte_precizari: '',
                copie_ci: null,
                copie_certificat: null,
                errors: [],
                checkIfHasAplication: null,
                application: null,
                validForm: false,

            }
        }, 
        components:{
            'alert-box': alertBox,
        },
        mounted(){
            this.verifyToken();
            this.getUserData();
            this.verifyIfAplciationExists();
        },
        methods:{

            deleteApplication: function(){
                axios.post(backend + '/api/deleteApplication', {
                    token: localStorage.getItem('token'),
                }).then(()=>{
                  this.$router.go(0);
                }).catch((error)=>{
                  console.log(error)
              })
            },

            enableEditApplication: function(){
              axios.post(backend + '/api/enableEditApplication',{
                token: localStorage.getItem('token'),
              }).then(()=>{
                window.location.reload();
              })
            },

            verifyIfAplciationExists: function(){
                axios.get(backend + '/api/checkApplication', {
                  params:{
                    token: localStorage.getItem('token'),
                  }
                }).then((response)=>{
                  if(response.data['message'] === 'NO_APPLICATION') this.checkIfHasAplication = false;
                  else {
                    this.application = response.data['data'];
                    this.checkIfHasAplication = true;
                    this.email = this.application.user_email;
                    this.firstName = this.application.user_firstName;
                    this.lastName = this.application.user_lastName;
                    this.judet = this.application.judet;
                    this.localitate = this.application.localitate;
                    this.adresa = this.application.adresa;
                    this.seria = this.application.serie_ci;
                    this.numar_serie = this.application.numar_serie_ci;
                    this.cnp = this.application.cnp;
                    this.nr_certificat =  this.application.nr_certificat;
                    this.primaria = this.application.primaria;
                    this.alte_precizari = this.application.alte_precizari;
                    this.checkIfHasAplication = true;
                  }
                }).catch((error)=>{
                  console.log(error);
                })
            },
            sendForm: function(){
                if(this.validForm === true) {
                    //this.onUploadCertificat();
                    //this.onUploadCI();
                    axios.post(backend+'/api/addApplication', {
                      firstName: this.firstName,
                      lastName: this.lastName,
                      judet: this.judet,
                      localitate: this.localitate,
                      adresa: this.adresa,
                      serie_ci: this.seria,
                      numar_ci: this.numar_serie,
                      cnp: this.cnp,
                      nr_certificat: this.nr_certificat,
                      primaria: this.primaria,
                      email: this.email,
                      altele: this.alte_precizari,
                      token: localStorage.getItem('token'),
                      existApp: this.checkIfHasAplication,
                    }).then(()=>{
                      //this.$router.go(0);
                      this.onUploadDocs();
                    })
                }else{
                  console.log('Urmeaza afisarea erorilor :D');
                }
            },
            verifyForm: function(){
                if(this.firstName!=='' && this.lastName!=='' && this.judet!=='' && this.localitate!==''
                && this.adresa!=='' && this.seria!=='' && this.numar_serie!=='' && this.cnp!==''
                && this.nr_certificat!=='' && this.primaria!=='' && this.copie_ci !== null && this.copie_certificat !== null){
                  this.infoForm = 'Va rog sa confirmați trimiterea formularului. Daca formularul nu contine date reale riscati sa primiti interdictie pentru a accesa acest grad.';
                  this.validForm = true;
                }else{
                  this.errors = [];
                  if(this.firstName === '') this.errors.push('Campul nume de familie este necesar.');
                  if(this.lastName === '') this.errors.push('Campul prenume este necesar.');
                  if(this.judet === '') this.errors.push('Campul judet este necesar.');
                  if(this.localitate === '') this.errors.push('Campul localitate este necesar.');
                  if(this.adresa === '') this.errors.push('Capul adresa este necesar.');
                  if(this.seria === '') this.errors.push('Campul adresa este necesar.');
                  if(this.numar_serie === '') this.errors.push('Campul numar serie este necesar');
                  if(this.cnp === '') this.errors.push('Campul CNP este necesar.');
                  if(this.nr_certificat === '') this.errors.push('Campul Nr.Certificat este necesar');
                  if(this.primaria === '') this.errors.push('Campul primaria este necesar');
                  if(this.copie_certificat === null) this.errors.push('Este necesara selectarea unei copii in format jpg sau png a certificatului de producator');
                  if(this.copie_ci === null) this.errors.push('Este necesara selectarea unei copii in format jpg sau png a C.I');
                  this.infoForm = 'Aplicatia are campuri necompletate sau nu sunt selectate documentele cerute!';
                  this.validForm = false;
                }
            },
            onCopieCISelected(){
              this.copie_ci = null;
              this.copie_ci = document.getElementById("copie_ci").files[0];
            },
            onCopieCertificatSelected(){
              this.copie_certificat = null;
              this.copie_certificat = document.getElementById("copie_certificat").files[0];
            },
            onUploadDocs: function() {
              const fd = new FormData();
              fd.append('copie_ci', this.copie_ci, 'copie_ci_' + this.id + '.jpg');
              fd.append('copie_certificat', this.copie_certificat, 'copie_certificat_' + this.id+ '.jpg');
              fd.append('token', localStorage.getItem('token'));
              axios.post(backend + '/api/addApplicationDocs', fd, {
                headers: {
                  'Content-Type': 'multipart/form-data'
                }
              }).then(() =>{
                this.$router.push("/")
              }).catch((error) => {
                console.log(error);
              })
            },
            verifyToken: function(){
                axios.get(backend + '/api/fetchUserData', {
                    params: {
                        token: localStorage.getItem('token'),
                    }
                }).catch(() =>{
                    document.getElementById('overlay-alert').style.display ="block";    
                })
            },
            home: function(){
                this.$router.push('/');
            },
            getUserData: function(){
                axios.get(backend+'/api/fetchUserData', {
                    params: {
                        token: localStorage.getItem('token'),
                    }
                }).then((response)=>{
                    if(response.data['producer'] === 1)
                        this.$router.push("/");
                    this.id = response.data['id'];
                    this.firstName = response.data['firstName'];
                    this.lastName = response.data['lastName'];
                    this.email = response.data['email'];
                }).catch(() =>{
                    this.$router.push('login');    
                })
            },
        }
    }
</script>
<style scoped>
    .form-control{
        flex: none;
        display:flex;
    }
    .input-group{
        width: auto;
        margin-top: 10px;

    }
    .textarea{
        resize: none;
        width: 636px;
        font-weight: normal;
    }
    .custom-file{
        width: 636px;
        margin-top: 10px;
    }
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance:textfield;
    }
    .overlay{
        position: fixed;
        display: none;
        width: 100%;
        height: 100%;
        cursor: pointer;
        margin-top: -605px;
        margin-left: -405px;
    }
</style>