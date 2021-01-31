<template>
  <div class="container-fluid">
    <div class="d-flex align-items-center flex-column">
      <nav style="width: 700px" class="navbar navbar-light bg-light">
        <button class="btn btn-home" v-on:click="home"><span class="material-icons">home</span></button>
      </nav>
    </div>
    <div class="container aplication">
      <h3 style="text-align: center; margin-bottom: 35px;">Aplicatie pentru gradul de producator</h3>
      <div class="edit"
           v-if="checkIfHasAplication === false || (application !== null && application.pending === 0 && application.status !== -1)">
        <form class="form-group">
          <div class="d-flex align-items-center flex-column">
            <div class="d-flex flex-row">

              <div class="form-group">
                <input style="width: 335px;" v-model="firstName" type="text" id="firstName" class="form-control" required>
                <label class="form-control-placeholder" for="firstName">Nume</label>
              </div>
              <div class="form-group">
                <input style="width: 335px;" v-model="lastName" type="text" id="lastName" class="form-control" required>
                <label class="form-control-placeholder" for="lastName">Prenume</label>
              </div>

            </div>
            <div class="d-flex flex-row adresa">
              <div class="form-group">
                <input style="width: 335px;" v-model="judet" type="text" id="judet" class="form-control" required>
                <label class="form-control-placeholder" for="judet">Judet</label>
              </div>
              <div class="form-group">
                <input style="width: 335px;" v-model="localitate" type="text" id="localitate" class="form-control" required>
                <label class="form-control-placeholder" for="localitate">Localitate</label>
              </div>
            </div>
            <div class="form-group">
              <input style="width: 670px;" v-model="adresa" type="text" id="adresa" class="form-control" required>
              <label class="form-control-placeholder" for="adresa">Adresa completa</label>
            </div>

            <div class="d-flex flex-row">
              <div class="form-group">
                <input v-model="seria" type="text" id="seria" class="form-control" required>
                <label class="form-control-placeholder" for="seria">Serie</label>
              </div>

              <div class="form-group">
                <input v-model="numar_serie" type="text" id="numar_serie" class="form-control" required>
                <label class="form-control-placeholder" for="numar_serie">Numar serie</label>
              </div>
              <div class="form-group">
                <input v-model="cnp" type="text" id="cnp" class="form-control" required>
                <label class="form-control-placeholder" for="cnp">CNP</label>
              </div>
            </div>


            <div class="d-flex flex-row">
              <div class="form-group">
                <input style="width: 335px;" v-model="nr_certificat" type="text" id="nr_certificat" class="form-control" required>
                <label class="form-control-placeholder" for="nr_certificat">Numar Certificat</label>
              </div>
              <div class="form-group">
                <input style="width: 335px;" v-model="primaria" type="text" id="primaria" class="form-control" required>
                <label class="form-control-placeholder" for="primaria">Primaria</label>
              </div>
            </div>

            <div class="custom-file">
              <input type="file" class="custom-file-input" id="copie_certificat" @change="onCopieCertificatSelected"
                     accept=".jpg, .png, .pdf" required>
              <label class="custom-file-label" for="copie_certificat">Selectati copia certificatului de producator</label>
              <div class="invalid-feedback">Invalid file</div>
            </div>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="copie_ci" @change="onCopieCISelected" required
                     accept=".jpg, .png,.pdf">
              <label class="custom-file-label" for="copie_ci">Selectati copia C.I/B.I</label>
              <div class="invalid-feedback">Invalid file</div>
            </div>
            <div class="input-group">
            <textarea v-model="alte_precizari" placeholder="*Alte precizari" class="form-control textarea" id="altele"
                      rows="3"></textarea>
            </div>
          </div>
          <div class="d-flex align-items-left">
            <button style="width: 100%; margin-top: 10px;" class="btn btn-success" type="button" data-toggle="modal" data-target="#confirmModal"
                    v-on:click="verifyForm">Trimite aplicatia
            </button>
          </div>
        </form>
      </div>
      <div v-if="checkIfHasAplication === true && checkIfHasAplication!=null && application.pending === 1">
        <p>Aplicatia este in curs de procesare, va rugam sa asteptati raspunsul!</p>
        <p>Pentru a anula aplicatia trimisa va rugam sa apasati butonul de mai jos.</p>
        <button data-toggle="modal" class="btn btn-danger" data-target="#deleteModal">Anuleaza aplicatia</button>
      </div>
      <div
          v-if="checkIfHasAplication === true && checkIfHasAplication!=null && application.pending === 0 && application.status === -1">
        <p>Aplicatia a fost respinsa, motivul se poate regasi mai jos!</p>
        <p>Puteti trimite o noua aplicatie corectand greselile specificate in motivul respingeri, daca trimiteti o
          aplicatie de mai multe ori
          puteti primi interdictie la acest grad.</p>
        <p>Motivul respingeri: {{ application.motiv_respingere }}</p>
        <p style="font-weight: bold;">Este necesara reincarcarea documentelor!</p>
        <button class="btn btn-danger" v-on:click="enableEditApplication">Editeaza aplicatia</button>
      </div>
    </div>
    <!--Modal pentru confirmarea stergerii aplicatiei -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalCenterTitle"
         aria-hidden="true">
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
            <button type="button" class="btn btn-primary" data-dismiss="modal" v-on:click="deleteApplication">Sterge
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal pentru confirmare trimitere aplicatie -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalCenterTitle"
         aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmModalLongTitle">Confirmare</h5>
          </div>
          <div class="modal-body">
            <p>{{ infoForm }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Continuă editarea</button>
            <button v-if="validForm" type="button" class="btn btn-primary" data-dismiss="modal" v-on:click="sendForm">
              Trimite formularul
            </button>
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

export default {
  data() {
    return {
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
  components: {
    'alert-box': alertBox,
  },
  mounted() {
    this.verifyToken();
    this.getUserData();
    this.verifyIfAplciationExists();
  },
  methods: {

    deleteApplication: function () {
      axios.post(backend + '/api/deleteApplication', {
        token: localStorage.getItem('token'),
      }).then(() => {
        this.$router.go(0);
      }).catch((error) => {
        console.log(error)
      })
    },

    enableEditApplication: function () {
      axios.post(backend + '/api/enableEditApplication', {
        token: localStorage.getItem('token'),
      }).then(() => {
        window.location.reload();
      })
    },

    verifyIfAplciationExists: function () {
      axios.get(backend + '/api/checkApplication', {
        params: {
          token: localStorage.getItem('token'),
        }
      }).then((response) => {
        if (response.data['message'] === 'NO_APPLICATION') this.checkIfHasAplication = false;
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
          this.nr_certificat = this.application.nr_certificat;
          this.primaria = this.application.primaria;
          this.alte_precizari = this.application.alte_precizari;
          this.checkIfHasAplication = true;
        }
      }).catch((error) => {
        console.log(error);
      })
    },
    sendForm: function () {
      if (this.validForm === true) {
        //this.onUploadCertificat();
        //this.onUploadCI();
        axios.post(backend + '/api/addApplication', {
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
        }).then(() => {
          //this.$router.go(0);
          this.onUploadDocs();
        })
      } else {
        console.log('Urmeaza afisarea erorilor :D');
      }
    },
    verifyForm: function () {
      if (this.firstName !== '' && this.lastName !== '' && this.judet !== '' && this.localitate !== ''
          && this.adresa !== '' && this.seria !== '' && this.numar_serie !== '' && this.cnp !== ''
          && this.nr_certificat !== '' && this.primaria !== '' && this.copie_ci !== null && this.copie_certificat !== null) {
        this.infoForm = 'Va rog sa confirmați trimiterea formularului. Daca formularul nu contine date reale riscati sa primiti interdictie pentru a accesa acest grad.';
        this.validForm = true;
      } else {
        this.errors = [];
        if (this.firstName === '') this.errors.push('Campul nume de familie este necesar.');
        if (this.lastName === '') this.errors.push('Campul prenume este necesar.');
        if (this.judet === '') this.errors.push('Campul judet este necesar.');
        if (this.localitate === '') this.errors.push('Campul localitate este necesar.');
        if (this.adresa === '') this.errors.push('Capul adresa este necesar.');
        if (this.seria === '') this.errors.push('Campul adresa este necesar.');
        if (this.numar_serie === '') this.errors.push('Campul numar serie este necesar');
        if (this.cnp === '') this.errors.push('Campul CNP este necesar.');
        if (this.nr_certificat === '') this.errors.push('Campul Nr.Certificat este necesar');
        if (this.primaria === '') this.errors.push('Campul primaria este necesar');
        if (this.copie_certificat === null) this.errors.push('Este necesara selectarea unei copii in format jpg sau png a certificatului de producator');
        if (this.copie_ci === null) this.errors.push('Este necesara selectarea unei copii in format jpg sau png a C.I');
        this.infoForm = 'Aplicatia are campuri necompletate sau nu sunt selectate documentele cerute!';
        this.validForm = false;
      }
    },
    onCopieCISelected() {
      this.copie_ci = null;
      this.copie_ci = document.getElementById("copie_ci").files[0];
    },
    onCopieCertificatSelected() {
      this.copie_certificat = null;
      this.copie_certificat = document.getElementById("copie_certificat").files[0];
    },
    onUploadDocs: function () {
      const fd = new FormData();
      fd.append('copie_ci', this.copie_ci, 'copie_ci_' + this.id + '.jpg');
      fd.append('copie_certificat', this.copie_certificat, 'copie_certificat_' + this.id + '.jpg');
      fd.append('token', localStorage.getItem('token'));
      axios.post(backend + '/api/addApplicationDocs', fd, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }).then(() => {
        this.$router.push("/")
      }).catch((error) => {
        console.log(error);
      })
    },
    verifyToken: function () {
      axios.get(backend + '/api/fetchUserData', {
        params: {
          token: localStorage.getItem('token'),
        }
      }).catch(() => {
        document.getElementById('overlay-alert').style.display = "block";
      })
    },
    home: function () {
      this.$router.push('/');
    },
    getUserData: function () {
      axios.get(backend + '/api/fetchUserData', {
        params: {
          token: localStorage.getItem('token'),
        }
      }).then((response) => {
        if (response.data['producer'] === 1)
          this.$router.push("/");
        this.id = response.data['id'];
        this.firstName = response.data['firstName'];
        this.lastName = response.data['lastName'];
        this.email = response.data['email'];
      }).catch(() => {
        this.$router.push('login');
      })
    },
  }
}
</script>
<style scoped>
input {
  border-radius: 0;
  border: none;
  border-bottom: 2px solid #9f5ac7;
}
.form-control:focus{
  border-color: #9f5ac7;
  box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 8px rgba(255, 100, 255, 0.5);
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
.aplication{
  background-color: #f8f9fa !important;
  width: 700px;
  height: 670px;
  margin-top: 10px;
}
.form-control {
  flex: none;
  display: flex;
}

.input-group {
  width: auto;
  margin-top: 10px;

}

.textarea {
  resize: none;
  width: 636px;
  font-weight: normal;
}

.custom-file {
  width: 636px;
  margin-top: 10px;
}

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type=number] {
  -moz-appearance: textfield;
}

.container-fluid {
  background-image: url('../assets/background.png');
  background-repeat: no-repeat;
  background-size: cover;
  height: 100vh;
}
</style>