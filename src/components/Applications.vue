<template>
  <div class="container">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a href="/"><img src="@/assets/Logo.png" alt="Logo" style="width: 50px;"></a>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="/admin">Utilizatori</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/admin/reports">Raportari</a>
          </li>
          <li class="nav-item">
            <a style="font-weight: bold" class="nav-link">Aplicatii</a>
          </li>
        </ul>
      </div>
    </nav>
    <div v-if="applications != null && !applications.length">
      <h2>Momentan nu este nici o aplicatie in asteptare.</h2>
    </div>
    <div v-if="applications != null && applications.length !== 0">
      <h2 v-if="applications.length > 1">Momentan sunt {{applications.length}} aplicatii in asteptare.</h2>
      <h2 v-if="applications.length === 1">Momentan este o aplicatie in asteptare.</h2>
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
          <td><button class="btn btn-secondary btn-sm" type="button" v-on:click="viewApplicaiton(aplicant.id)">Vizualizare aplicatie</button></td>
        </tr>
        </tbody>
      </table>
      <div class="pagination d-flex justify-content-center">
        <button id="btn-prev" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Pagina anterioara" class="btn btn-outline-secondary pagination-button" v-on:click="fetchPaginateUsers(pagination.prev_page)" :disabled="!pagination.prev_page"><span class="material-icons">undo</span></button>
        <span>Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
        <button id="btn-next" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Pagina urmatoare" class="btn btn-outline-secondary pagination-button" v-on:click="fetchPaginateUsers(pagination.next_page)" :disabled="!pagination.next_page"><span class="material-icons">redo</span></button>
      </div>
    </div>
  </div>
</template>

<script>
  import axios from "axios";
  import {backend} from "@/constants";
  // eslint-disable-next-line no-undef
  $(function(){
    // eslint-disable-next-line no-undef
    $('body').tooltip({ selector: '[data-toggle="tooltip"]' });
  });
  export default{
    data(){
      return{
        applications: null,
        pagination: [],
        url: backend + '/api/admin/applications',
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
        axios.get(this.url, {
          params:{
            token: localStorage.getItem('token'),
          }
        }).then((response)=>{
          this.applications = response.data.data;
          this.makePagination(response.data);
        }).catch((e)=>{
          console.log(e)
        })
      },
      makePagination(data) { // realizarea link-urilor pentru paginare
        let pagination = {
          current_page: data.current_page,
          last_page: data.last_page,
          next_page: data.next_page_url,
          prev_page: data.prev_page_url
        };
        this.pagination = pagination;
      },
      fetchPaginateUsers(url) { // generarea urmatoarelor pagini pentru "next" sau "prev" si incarcarea userilor de pe o anumita pagina
        this.url = url;
        // eslint-disable-next-line no-undef
        $('#btn-next').tooltip('hide')
        // eslint-disable-next-line no-undef
        $('#btn-prev').tooltip('hide')
        this.getUsers();
      }
    }
  }
</script>

<style scoped>
  h2{
    text-align: center;
  }
  .pagination-button{
    background-color: transparent;
    color: black;
    font-size: 20px;
    border: none;
  }
  .pagination-button:active, .pagination-button:focus{
    outline: none!important;
    box-shadow: none;
  }
  .pagination-button:hover{
    background-color: transparent;
    color: black;
  }
</style>