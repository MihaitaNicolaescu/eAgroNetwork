<template>
    <div class="container">
        <nav class="navbar navbar-light bg-light">
           <button class="btn btn-outline-secondary" v-on:click="logout">Log out</button>
              <form style="float: left" class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2 input-search" type="search" placeholder="Search" aria-label="Search" v-model="query">
                <button class="btn btn-icons  icon-search" type="button" v-on:click="this.searchMembers()"><i class="material-icons">search</i></button>
              </form>
            <div class="ml-auto">
                <button v-if="admin" class="btn btn-icons" v-on:click="adminPanel"><span class="material-icons">admin_panel_settings</span></button>
                <button class="btn btn-icons" v-on:click="profile"><span class="material-icons">account_circle</span></button>
                <button v-if="this.hasNotifications > 0" v-on:click="showNotifications()" class="btn btn-icons"><span class="material-icons">mark_email_unread</span></button>
                <button v-else class="btn btn-icons" v-on:click="showNotifications()"><span class="material-icons">email</span></button>
            </div>
        </nav>
        <ul class="list-group list-search" v-if="results.length > 0 && query">
                
                <li style="display: block ruby !important;" class="list-group-item search-item" v-for="result in results.slice(0,10)" :key="result.id">
                    <a class="link" :href="'/profile/' + result.searchable.id">
                        <div class="link-profile" style="display: block ruby;">
                            <img v-if="result.searchable.profile_image !== 'default.jpg'" id="search-image" :src="require('@/assets/profiles/profile_image_' + result.searchable.id + '.jpg')" alt="Prof. img" width="50" height="50">
                            <img v-else id="search-image" :src="require('@/assets/profiles/default.jpg')" alt="Prof. img" width="50" height="50">
                            <p style="color:black;" v-text="result.searchable.lastName + ' ' + result.title"></p>
                        </div>
                    </a>
                </li>
        </ul>
        <div id="notifications-box">
             
            <ul class="list-group list-search list-notifications" v-if="hasNotifications == 0">
                <li class="list-group-item">
                    <p>Nu ai notificari necitite</p>
                    <button style="float:right;" class="btn btn-danger btn-sm" type="button" v-on:click="closeNotifications()">Close</button>
                </li>
            </ul>
        </div>
        <div class="container">
            <h1>Pagina in lucru!</h1>
        </div>
        <alert-box></alert-box>
    </div>
</template>

<script>
    import alertBox from './templates/invalidToken'; 
    import axios from 'axios';

    export default{
        data(){
            return{
                query: null,
                results: [],
                admin: 0,
                hasNotifications: 0,
            }
        },
        components: {
            'alert-box': alertBox,
        },
        mounted(){
            

            axios.get('http://127.0.0.1:8000/api/fetchProfile', {
                params: {
                    id: this.$route.params.id
                }
            }).then((response)=>{
                this.firstName = response.data['firstName'],
                this.lastName = response.data['lastName'],
                this.email = response.data['email'],
                this.birthday = response.data['birthday'],
                this.id = response.data['id'],
                this.userPhoto = response.data['profile_image']
                if(this.userPhoto == null)
                    this.userPhoto = 'default.jpg'
            }).catch((error) =>{
                console.log(error)
            })

            axios.get('http://127.0.0.1:8000/api/fetchUserData', {
                params: {
                    token: localStorage.getItem('token'),
                }
            }).then((response)=>{
               this.admin = response.data['admin'];
               this.hasNotifications = response.data['notifications'];
            }).catch(() =>{
                document.getElementById('overlay-alert').style.display ="block";    
            })
        },
        watch: {
            query: function(){
                if(this.query.length >= 1){
                    this.searchMembers();
                }
            }
        },
        methods:{
            closeNotifications: function(){
                document.getElementById('notifications-box').style.display ="none";
            },
            showNotifications: function(){
                document.getElementById('notifications-box').style.display ="block";
            },
            profile: function(){
                this.$router.push('profile');
            },
            adminPanel: function(){
                this.$router.push('admin');
            },
            logout: function(){
                localStorage.clear();
                this.$router.push('login');
            },
            searchMembers() {
                axios.get('http://127.0.0.1:8000/api/user/search',  { params :{
                    query: this.query 
                }})
                .then(response => {
                    this.results = response.data
                })
                .catch(error => {
                    console.log(error)
                });
            }
        }
    }
</script>

<style scoped>
    #search-image{
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }
    .icon-search{
        margin-left: -50px;
        background: transparent;
    }
    .input-search{
        width: 400px;
    }
    .list-search{
        position: fixed;
        width: 400px;
        margin-left: 342px;
        z-index: 2;
    }
    .search-item:hover{
        background-color: #d1d1d1 !important;
    }
    .link{
        display: block;
        width: 400px;
        height: 52px;
        margin-bottom: -13px;
        margin-top: -10px;
    }
    .btn-icons{
        background: transparent;
    }
    #notifications-box{
        position: fixed;
        width: 400px;
        margin-left: 342px;
        z-index: 2;
        display: none;
    }
    .list-notifications{
        background-color: #f8f9fa !important;
        margin-left: 368px;
    }

</style> 

