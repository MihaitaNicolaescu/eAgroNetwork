<template>
  <div class="container">
    <nav class="navbar navbar-light bg-light">
      <button class="btn btn-outline-secondary" v-on:click="back">Back</button>
    </nav>
    <div v-if="applicationDetails != null && applicationDetails === false">
      <p class="not-found">Aplicatie invalida.</p>
      <p class="go-home">Apasa
        <router-link :to="{path: '/admin/applications'}">aici</router-link>
        pentru a te intoarce la tabelul cu aplicatii.
      </p>
    </div>
    <div v-if="applicationDetails!=null">
      <div style="margin-top: 20px; background-color: #f8f9fa; height: 80%;" class="container">
        <div class="row">
          <div class="col">
            <img alt="profile image" class="profile-image" :src="backend + applicationDetails.link_profile">
            <div style="margin-top: 20px;" class="text-center">
              <button style="margin-right: 10px;" data-toggle="modal" data-target="#rejectModal" class="btn btn-danger"
                      type="button">Respinge
              </button>
              <button class="btn btn-success" data-toggle="modal" data-target="#acceptModal" type="button">Aproba
              </button>
            </div>
          </div>
          <div class="col-6">
            <ul class="list-group">
              <li class="list-group-item">Numele: {{ applicationDetails.user_firstName }}</li>
              <li class="list-group-item">Prenumele: {{ applicationDetails.user_lastName }}</li>
              <li class="list-group-item">{{ applicationDetails.user_email }}</li>
              <li class="list-group-item">Judet: {{ applicationDetails.judet }}</li>
              <li class="list-group-item">Localitate: {{ applicationDetails.localitate }}</li>
              <li class="list-group-item">Adresa: {{ applicationDetails.adresa }}</li>
              <li class="list-group-item">Serie carte identitate: {{ applicationDetails.serie_ci }}
                {{ applicationDetails.numar_serie_ci }}
              </li>
              <li class="list-group-item">CNP: {{ applicationDetails.cnp }}</li>
              <li class="list-group-item">Numar certficat producator: {{ applicationDetails.nr_certificat }}</li>
              <li class="list-group-item">Primaria: {{ applicationDetails.primaria }}</li>
              <li class="list-group-item">Alte precizari: {{ applicationDetails.alte_precizari }}</li>
              <li class="list-group-item">Data trimiterii: {{ formatDate(applicationDetails.updated_at) }}</li>
            </ul>
            <button style="width: 100%; margin-bottom: 5px; margin-top:5px;" type="button" class="btn btn-primary"
                    data-toggle="modal" data-target="#CImodal">Copie carte identitate
            </button>
            <button style="width: 100%" type="button" class="btn btn-primary" data-toggle="modal"
                    data-target="#CCmodal">Copie certificat producator
            </button>
          </div>
        </div>
      </div>
    </div>


    <!-- Modal -->
    <div v-if="applicationDetails!==null" class="modal fade" id="CCmodal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="CCTitle">Copie carte identitate</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <img style="width: 470px; height: 629px;" alt="docs-image"
                 :src="backend + applicationDetails.copie_certificat">
          </div>
        </div>
      </div>
    </div>
    <!-- Modal -->
    <div v-if="applicationDetails!==null" class="modal fade" id="CImodal" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Copie carte identitate</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <img style="width: 470px; height: 629px;" alt="docs-image" :src="backend + applicationDetails.copie_ci">
          </div>
        </div>
      </div>
    </div>
    <!--Modal pentru confirmarea aplicatiei -->
    <div v-if="applicationDetails!==null" class="modal fade" id="acceptModal" tabindex="-1" role="dialog"
         aria-labelledby="confirmModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLongTitle">Confirmare</h5>
          </div>
          <div class="modal-body">
            <p>Dupa confirmare {{ applicationDetails.user_firstName }} {{ applicationDetails.user_lastName }} cu emailul
              {{ applicationDetails.user_email }} va primi gradul de producator in aplicatie.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuleaza</button>
            <button type="button" class="btn btn-success" data-dismiss="modal" v-on:click="acceptApplication">Accepta
              aplicatia
            </button>
          </div>
        </div>
      </div>
    </div>
    <!--Modal pentru respingerea aplicatiei -->
    <div v-if="applicationDetails!==null" class="modal fade" id="rejectModal" tabindex="-1" role="dialog"
         aria-labelledby="confirmModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="jerectModalLongTitle">Confirmare respingere aplicatie</h5>
          </div>
          <div class="modal-body">
            <p id="infoRespingere">Obligatoriu specificati motivul respingeri!</p>
            <div class="form-group">
              <textarea v-model="motiv" class="form-control" id="motivRespingere" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuleaza</button>
            <button type="button" class="btn btn-success" id="reject" v-on:click="rejectApplication">Confirma
              respingerea aplicatiei
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import {backend} from "@/constants";


export default {
  data() {
    return {
      applicationDetails: null,
      userId: this.$route.params.id,
      motiv: null,
      backend: backend,
    }
  },
  mounted() {
    axios.get(backend + '/api/verifyData', {
      params: {
        token: localStorage.getItem('token'),
      }
    }).then((res) => {
      localStorage.setItem('admin', res.data['isAdmin']);
      if (res.data['isAdmin'] === 0) this.$router.push('/');
      else this.getApplicationInfo();
    }).catch((error) => {
      console.log(error);
      this.$router.push('/');
    })
    //
  },
  methods: {
    formatDate: function (date) {
      let newDate = new Date(date);
      let months = ['Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie'];
      return newDate.getDate() + ' ' + months[newDate.getMonth()] + ' ' + newDate.getFullYear();
    },
    rejectApplication: function () {
      if (this.motiv !== null && this.motiv !== '') {
        axios.post(backend + '/api/rejectApplication', {
          token: localStorage.getItem('token'),
          user_id: this.userId,
          motiv: this.motiv,
        }).then(() => {
          // eslint-disable-next-line no-undef
          $('#rejectModal').modal('hide');
          this.$router.push('/admin/applications');
        })
      } else {
        document.getElementById('infoRespingere').style.color = "red";
      }
    },
    acceptApplication: function () {
      axios.post(backend + '/api/acceptApplication', {
        token: localStorage.getItem('token'),
        user_id: this.userId,
      }).then(() => {
        // eslint-disable-next-line no-undef
        $('#rejectModal').modal('hide');
        this.$router.push('/admin/applications');
      })
    },
    getApplicationInfo: function () {
      axios.get(backend + '/api/getApplication', {
        params: {
          token: localStorage.getItem('token'),
          id: this.userId,
        }
      }).then((response) => {
        this.applicationDetails = response.data['application']
      }).catch((error) => {
        console.log(error)
      })
    },
    back: function () {
      this.$router.push('/admin/applications');
    },
  }
}
</script>

<style scoped>
/*For invalid user*/
.not-found {
  font-size: 80px;
  font-family: "Courier New";
  text-align: center;
}

.go-home {
  font-size: 20px;
  font-family: "Courier New";
  text-align: center;
  font-weight: bold;
}

.profile-image {
  border-radius: 50%;
  object-fit: cover;
  width: 250px;
  height: 250px;
}
.list-group li{
  border: grey solid 1px;
}
.col {
  flex-grow: 0;
}
</style>