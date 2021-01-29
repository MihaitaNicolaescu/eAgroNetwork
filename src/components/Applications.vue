<template>
  <div class="container">
    <nav class="navbar navbar-light bg-light">
      <button class="btn btn-outline-secondary" v-on:click="back">Back</button>
    </nav>
    <div v-if="applications != null && !applications.length">
      <h2>Momentan nu este nici o aplicatie in asteptare.</h2>
    </div>
    <div v-if="applications != null && applications.length !== 0">
      <h2>Momentan sunt {{applications.length}} aplicatii in asteptare.</h2>
      <table class="table">
        <thead class="thead-light">
        <tr>
          <th scope="col">#</th>
          <th scope="col">Numele de familie</th>
          <th scope="col">Prenume</th>
          <th scope="col">Email</th>
          <th scope="col">Verifica aplicatia</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(aplicant, index) in applications" :key="aplicant.id">
          <th scope="row">{{ index }}</th>
          <td>{{ aplicant.firstName }}</td>
          <td>{{ aplicant.lastName }}</td>
          <td>{{ aplicant.email }}</td>
          <td><button class="btn btn-secondary" type="button" v-on:click="viewApplicaiton(aplicant.id)">Vizualizare aplicatie</button></td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
  import axios from "axios";
  import {backend} from "@/constants";

  export default{
    data(){
      return{
        applications: null,

      }
    },
    mounted(){
      axios.get(backend+'/api/verifyData', {
        params: {
          token: localStorage.getItem('token'),
        }
      }).then((res) =>{
        localStorage.setItem('admin', res.data['isAdmin']);
        if(res.data['isAdmin'] === 0) this.$router.push('/');
        else this.getApplications();
      }).catch((error)=>{
        console.log(error);
        this.$router.push('/');
      })
    },
    methods:{
      back: function(){
        this.$router.push('/admin');
      },
      viewApplicaiton: function(id){
          this.$router.push('/admin/applications/'+id)
      },
      getApplications: function(){
        axios.get(backend+'/api/getApplications', {
          params:{
            token: localStorage.getItem('token'),
          }
        }).then((response)=>{
          this.applications = response.data['users']
        }).catch((e)=>{
          console.log(e)
        })
      }
    }
  }
</script>

<style scoped>
  h2{
    text-align: center;
  }
</style>