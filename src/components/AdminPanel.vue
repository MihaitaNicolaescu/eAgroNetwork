<template>
    <div class="container">
        <nav class="navbar navbar-light bg-light d-flex justify-content-center">
           <button class="btn btn-outline-secondary" v-on:click="back">Back</button>
            <button class="btn btn-outline-secondary" >Reports</button>
            <button class="btn btn-outline-secondary" v-on:click="aplications">Applications</button>
            <form class="search-user d-flex justify-content-center">
                <input v-model="searchedUser" class="form-control mr-sm-2 input-search" type="search" placeholder="Search a user here" aria-label="Search">
                <button v-if="!active || active != 1" class="btn search-btn" type="button" v-on:click="searchUser()"><i class="material-icons icon-search">search</i></button>
            </form>
            <button v-if="active" class="btn btn-outline-secondary cancel-search" type="button" v-on:click="usersButton();"><span class="material-icons">search_off</span></button>
        </nav>
        <div class="users">
            <table class="table">
                <thead>
                    <tr>
                    <th scope="col">ID</th>
                    <th scope="col">ID_DB</th>
                    <th scope="col">NUME</th>
                    <th scope="col">PRENUME</th>
                    <th scope="col">EMAIL</th>
                    <th scope="col">PRODUCATOR</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(user, index) in users" :key="user.id">
                        <td>{{index}}</td>
                        <td>{{user.id}}</td>
                        <td>{{user.firstName}}</td>
                        <td>{{user.lastName}}</td>
                        <td>{{user.email}}</td>
                        <td v-if="user.producer == true"><span class="material-icons">done_outline</span></td>
                        <td v-else><span class="material-icons">clear</span></td>
                        <td>
                            <button class="btn btn-outline-danger" type="button" v-on:click="deleteUser(user.id)">Sterge utilizator</button>
                        </td>
                        <td>
                          <button v-if="user.producer == true" style="width: 170px" class="btn btn-outline-danger" type="button" v-on:click="cancelProducer(user.id)">Anulare producator</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="pagination d-flex justify-content-center">
                <button class="btn btn-default" v-on:click="fetchPaginateUsers(pagination.prev_page)" :disabled="!pagination.prev_page">Previos</button>
                <span>Page {{pagination.current_page}} of {{pagination.last_page}}</span>
                <button class="btn btn-default" v-on:click="fetchPaginateUsers(pagination.next_page)" :disabled="!pagination.next_page">Next</button>
            </div>
        </div>
        <alert-box></alert-box>
        <div id="confirm-box">
            <div class="d-flex align-items-center flex-column">
                <p id="alert-text">A aparut o eroare la incarcarea informatiilor, este necesara reconecarea in aplicatie.</p>
                <button class="btn btn-danger btn-profile" v-on:click="reconectError()">OK</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .btn{
        margin-right: 10px;
        width:130px
    }
    .input-search{
        width: 300px;
    }
</style>

<script>
    import alertBox from './templates/invalidToken'; 
    import axios from 'axios';
    import {backend} from '../constants.js';

    export default{
        data(){
            return{
                url: backend+'/api/admin/users',
                pagination: [],
                users: null,
                searchedUser: '',
                active: 0,
            }
        }, 
        
        mounted(){
            //verificarea userului pentru a se determina daca este un administrator valid
            axios.get(backend+'/api/verifyData', {
                params: { 
                    token: localStorage.getItem('token'),
                }
            }).then((res) =>{
                localStorage.setItem('admin', res.data['isAdmin']);
            }).catch((error)=>{
                console.log(error);
            })
            //
            if(localStorage.getItem('admin') == false) this.$router.push('/'); // daca userul nu este administrator regasit in baza de date atunci va fi redirectonat
            this.getUsers();
        },
        components:{
            'alert-box': alertBox,
        },
        methods: {
            cancelProducer: function(producerID){
              axios.post(backend+ '/api/cancelProducer',{
                token: localStorage.getItem('token'),
                producer_id: producerID,
              }).then(()=>{
                this.getUsers()
              }).catch((e)=>{
                console.log(e)
              })
            },
            aplications: function(){
              this.$router.push('/admin/applications');
            },
            deleteUser: function(id){
                axios.post(backend+'/api/admin/delete', {
                    id: id,
                }).then(() =>{
                    alert('Utilizatorul a fost sters cu succes!')
                    this.getUsers();
                }).catch((error) =>{
                    alert('Incercarea a esuat: ' + error['message'])
                })
            },
            searchUser: function(){ // functie pentru a cauta un anumit utilizator
                if(this.searchedUser != ''){
                this.getUsers();
                this.active = 1;
                }
            },
            usersButton: function(){ // functie pentru resetarea cautarii si afisarea listei cu utilizatorii inregistrati
                this.active = 0;
                this.searchedUser= '';
                this.getUsers();
            },
            back: function(){
                this.$router.push('/');
            },
            getUsers(){ // functie pentru preluarea userilor din backend
                let $this = this;
                axios.get(this.url,{params: {searchedUser: this.searchedUser}}).then(response=>{
                    this.users = response.data.data
                    $this.makePagination(response.data)
                    if(this.users.length == 0){
                        alert("Utilizatorul cautat nu exista in baza de date");
                        this.usersButton();
                    }
                })
            },
            makePagination(data){ // realizarea link-urilor pentru paginare
                let pagination = { 
                    current_page: data.current_page,
                    last_page: data.last_page,
                    next_page: data.next_page_url,
                    prev_page: data.prev_page_url
                };
                this.pagination = pagination;
            },
            fetchPaginateUsers(url){ // generarea urmatoarelor pagini pentru "next" sau "prev" si incarcarea userilor de pe o anumita pagina
                this.url = url;
                this.getUsers();
            }
        }
    }
</script>

<style scoped>
    .search-btn{
        background-color: transparent;
        margin-left: 0px;
        width: 50px;
    }
    #confirm-text{
        color:white;
        font-size: 2rem;
        text-align: center;
        padding-top: 110px;
    }
     #confirm-box{
        position: fixed;
        display: none;
        width: 500px;
        height: 500px;
        background-color: rgb(77,74,73);
        z-index: 2;
        cursor: pointer;
        margin-left: 300px;
        margin-top: -140px;
    }
    .cancel-search{
        width: 50px;
    }
</style>