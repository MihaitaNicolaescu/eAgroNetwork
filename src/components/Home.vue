<template>
    <div class="container">
        <nav class="navbar navbar-light bg-light">
           <button class="btn btn-outline-secondary" v-on:click="logout">Log out</button>
              <form style="float: left" class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2 input-search" type="search" placeholder="Search" aria-label="Search" v-model="query">
                <i class="material-icons icon-search">search</i>
              </form>
           <button class="btn btn-outline-secondary" v-on:click="profile">My profile</button>
        </nav>
        <ul class="list-group list-search" v-if="results.length > 0 && query">
                
                    <li style="display: block ruby !important;" class="list-group-item" v-for="result in results.slice(0,10)" :key="result.id">
                        <a class="link" :href="'/profile/' + result.searchable.id">
                            <div class="link-profile" style="display: block ruby;">
                                <img v-if="result.searchable.profile_image !== 'default.jpg'" id="search-image" :src="require('@/assets/profiles/profile_image_' + result.searchable.id + '.jpg')" alt="Prof. img" width="50" height="50">
                                <img v-else id="search-image" :src="require('@/assets/profiles/default.jpg')" alt="Prof. img" width="50" height="50">
                                <p style="color:black;" v-text="result.searchable.lastName + ' ' + result.title"></p>
                            </div>
                        </a>
                    </li>
        </ul>
        <div class="container">
            <h1>Pagina in lucru!</h1>
        </div>
    </div>
</template>

<script>

    import axios from 'axios';

    export default{
        data(){
            return{
                query: null,
                results: []
            }
        },
        watch: {
            query: function(){
                if(this.query.length > 2){
                    this.searchMembers();
                }
            }
        },
        methods:{
            profile: function(){
                this.$router.push('profile');
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
                    this.results = response.data,console.log(response.data)})
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
    .list-group-item:hover{
        background-color: #d1d1d1 !important;
    }
    .link{
        display: block;
        width: 400px;
        height: 52px;
        margin-bottom: -13px;
        margin-top: -10px;
        
    }

</style>