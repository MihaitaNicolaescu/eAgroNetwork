<template>
  <div class="container-flex">
    <nav class="navbar navbar-light bg-light">
      <button class="btn btn-outline-secondary" v-on:click="back">Back</button>
    </nav>
    <div v-if="applicationDetails === false && applicationDetails != null">
      <p class="not-found">Aplicatie invalida.</p>
      <p class="go-home">Apasa <router-link :to="{path: '/admin/applications'}">aici</router-link> pentru a te intoarce la tabelul cu aplicatii.</p>
    </div>
    <div v-if="applicationDetails!=null">
      <div style="margin-top: 20px;" class="container">
        <div class="row">
          <div class="col">
            <img alt="profile image" class="profile-image" :src="require('@/assets/profiles/' +applicationDetails.profile_image)">
          </div>
          <div class="col">
            <p>Numele: {{applicationDetails.user_firstName}}</p>
            <p>Prenumele: {{applicationDetails.user_lastName}}</p>
            <p>Email: {{applicationDetails.user_email}}</p>
            <p>Judet: {{applicationDetails.judet}}</p>
            <p>Localitate: {{applicationDetails.localitate}}</p>
            <p>Adresa: {{applicationDetails.adresa}}</p>
            <p>Serie carte identitate: {{applicationDetails.serie_ci}} {{applicationDetails.numar_serie_ci}}</p>
            <p>CNP: {{applicationDetails.cnp}}</p>
            <p>Numar certficat producator: {{applicationDetails.nr_certificat}}</p>
            <p>Primaria: {{applicationDetails.primaria}}</p>
            <p>Alte precizari: {{applicationDetails.alte_precizari}}</p>
          </div>
          <div class="col">
            <p>Copie carte identitate</p>
            <img alt="docs-image" class="doc-image" :src="require('../assets/applications/' + applicationDetails.copie_ci)">
            <p>Copie certificat producator</p>
            <img alt="docs-image" class="doc-image" :src="require('../assets/applications/' + applicationDetails.copie_certificat)">
          </div>
        </div>
      </div>
      <div class="col-md-12 text-center">
        <button style="margin-right: 10px;" data-toggle="modal" data-target="#rejectModal" class="btn btn-danger" type="button">Respinge</button>
        <button class="btn btn-success" data-toggle="modal" data-target="#acceptModal" type="button">Aproba</button>
      </div>
    </div>
    <!--Modal pentru confirmarea aplicatiei -->
    <div v-if="applicationDetails!=null" class="modal fade" id="acceptModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLongTitle">Confirmare</h5>
          </div>
          <div class="modal-body">
            <p>Dupa confirmare {{applicationDetails.user_firstName}} {{applicationDetails.user_lastName}} cu emailul
              {{applicationDetails.user_email}} va primi gradul de producator in aplicatie.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuleaza</button>
            <button type="button" class="btn btn-success" data-dismiss="modal" v-on:click="acceptApplication">Accepta aplicatia</button>
          </div>
        </div>
      </div>
    </div>
    <!--Modal pentru respingerea aplicatiei -->
    <div v-if="applicationDetails!=null" class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalCenterTitle" aria-hidden="true">
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
            <button type="button" class="btn btn-success" id="reject" v-on:click="rejectApplication">Confirma respingerea aplicatiei</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from "axios";
  import {backend} from "@/constants";


  export default{
    data(){
      return{
        applicationDetails: null,
        userId: this.$route.params.id,
        motiv: null,
      }
    },
    mounted(){
      axios.get(backend+'/api/verifyData', {
        params: {
          token: localStorage.getItem('token'),
        }
      }).then((res) =>{
        localStorage.setItem('admin', res.data['isAdmin']);
      }).catch((error)=>{
        console.log(error);
        this.$router.push('/');
      })
      //
      if(localStorage.getItem('admin') === false) this.$router.push('/') // daca userul nu este administrator regasit in baza de date atunci va fi redirectonat
      this.getApplicationInfo();
    },
    methods:{
      rejectApplication: function(){
        if(this.motiv !== null && this.motiv !== '') {
          axios.post(backend + '/api/rejectApplication', {
            token: localStorage.getItem('token'),
            user_id: this.userId,
            motiv: this.motiv,
          }).then(()=>{
            // eslint-disable-next-line no-undef
            $('#rejectModal').modal('hide');
            this.$router.push('/admin/applications');
          })
        }else{
          document.getElementById('infoRespingere').style.color= "red";
        }
      },
      acceptApplication: function(){
        axios.post(backend + '/api/acceptApplication', {
          token: localStorage.getItem('token'),
          user_id: this.userId,
        }).then(()=>{
          // eslint-disable-next-line no-undef
          $('#rejectModal').modal('hide');
          this.$router.push('/admin/applications');
        })
      },
      getApplicationInfo: function(){
        axios.get(backend + '/api/getApplication', {
          params:{
            token: localStorage.getItem('token'),
            id: this.userId,
          }
        }).then((response)=>{
          this.applicationDetails = response.data['application']
        }).catch((error)=>{
          console.log(error)
        })
      },
      back: function(){
        this.$router.push('/admin/applications');
      },
    }
  }
</script>

<style scoped>
/*For invalid user*/
  .not-found{
    font-size: 80px;
    font-family: "Courier New";
    text-align: center;
  }
  .go-home{
    font-size: 20px;
    font-family: "Courier New";
    text-align: center;
    font-weight: bold;
  }
  .profile-image{
    border-radius: 50%;
    object-fit: cover;
    width: 250px;
    height: 250px;
  }
  .doc-image{
    width: 200px;
    height: 200px;
    margin-bottom: 10px;
    object-fit: cover;
  }
  .doc-image:hover{
    transform: scale(2,3);
    object-fit: initial;
    display: block;
    margin:0;
  }
</style>