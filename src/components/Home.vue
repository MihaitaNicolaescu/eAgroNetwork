<template>
    <div class="container">
        <nav class="navbar navbar-light bg-light">
           <button class="btn btn-outline-secondary" v-on:click="logout">Log out</button>
              <form style="float: left" class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2 input-search" type="search" placeholder="Search" aria-label="Search" v-model="query">
                <button class="btn btn-icons  icon-search" type="button"><i class="material-icons">search</i></button>
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
            <div id="posts" class="col-7">
                        <div v-for="(post, index) in fallowPosts" :key="post.id">
                            <div class="container-post sn p-3">
                                <div class="user-info">
                                    <img class="user-info-img" :src="require('@/assets/profiles/' + post.profile_image)">
                                    <p>{{post.firstName + " " + post.lastName}}</p>
                                </div>
                                <div class="post-description">
                                {{post.description}} 
                                </div>
                                <div class="d-flex align-items-center flex-column image-post">
                                    <img class="post-image" :src="require('../assets/posts/' + post.filename)">
                                </div>
                                <button v-show="post.vote == 0 || post.vote == null || post.vote == -1" class="btn btn-react" type="button" v-on:click="voteUp(post.id, index, 1)"><span class="material-icons">thumb_up_alt</span></button>
                                <button v-show="post.vote == 1" class="btn btn-react" type="button" v-on:click="cancelVoteUp(post.id, index, 0)"><span class="material-icons" style="color: blue;">thumb_up_alt</span></button>
                                <span>{{post.votes}}</span>
                                <button v-show="post.vote == 0 || post.vote==null || post.vote == 1" class="btn btn-react" type="button"><span class="material-icons" v-on:click="voteDown(post.id, index, -1)">thumb_down_alt</span></button>
                                <button v-show="post.vote == -1" class="btn btn-react" type="button"><span class="material-icons" v-on:click="cancelVoteDown(post.id, index, 0)" style="color: blue;">thumb_down_alt</span></button>
                                <button class="btn btn-react" type="button"><span class="material-icons">insert_comment</span></button>
                                <button class="btn btn-react" tyoe="button" style="float: right;"><span><span class="material-icons">edit</span></span></button>
                            </div>
                        </div>
                        
                    </div>
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
                fallowList: [],
                fallowPosts: [],
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
                this.$router.push('login');    
            })

            this.getUserInformatios();
        },
        watch: {
            query: function(){
                if(this.query.length >= 1){
                    this.searchMembers();
                }
            },
            fallowList: function(){
                this.getFallowListPosts();
            }
        },
        methods:{
            getFallowListPosts: function(){
                let listConverted = JSON.stringify(this.fallowList);
               const sendGetRequest = async() => {
                    try{
                        const resp = await axios.get('http://127.0.0.1:8000/api/fetchFallowPosts', {
                    params:{
                        fallowList: listConverted,
                        token: localStorage.getItem('token'),
                    }});
                    this.fallowPosts = resp.data['posts'];
                    }catch(error){
                        console.log(error);
                    }
                }
                sendGetRequest();

            },
            getUserInformatios: function(){
                 const sendGetRequest = async() => {
                    try{
                        const response = await axios.get('http://127.0.0.1:8000/api/fetchFallowList', {
                    params: {
                        token: localStorage.getItem('token'),
                    }});
                    this.fallowList = response.data;
                    }catch(error){
                        console.log(error);
                    }
                }
                sendGetRequest();
            },
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
    #label-post{
        width: 500px;
    }
    #post-description{
        width: 500px;
    }
    textarea{
        resize:none;
    }
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
        margin-left: 98px;
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

    .btn-react{
        background: transparent;
    }
    .user-info{
        display: block ruby;
        font-size: 15px;
        font-weight: bold;
    }
    #posts{
        overflow: scroll;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
        height: 850px;
    }
    #post::-webkit-scrollbar {
        display: none;
    }
    .container-post{
        background-color: #d9d9d9!important;
        width: 600px;
        height: auto;
        padding: 10px;
        box-shadow: 5px 10px #888888;
        margin-top:20px;
        
    }
    .user-info-img{
        width: 50px;
        height: 50px;
        border-radius: 50px;
        object-fit: cover;
    }
    .post-add{
        margin-top: 100px;
    }
    .description-post{
        margin-top: 10px;
        resize: none;
        width: 460px;
    }
    .btn-profile{
        width: 225px;
        font-size: 20px;
        margin-top: 5px;
    }
    .info{
        margin: 0px;
    }
    #profile{
        font-size: 20px;
    }
    #overlay{
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
    #post-overlay{
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
    .form-control{
        font-size: 20px !important;
        font-weight: bold;
    }
    .input-group-text{
        font-size: 1.2rem !important;
        font-weight: bold;
    }
    .profile-image{
        border-radius: 50%;
        object-fit: cover;
        width: 200px;
        height: 200px;
    }
    .post-image{
        max-width: 550px;
        min-width: 550px;
        max-height: 700px;
        margin: auto;
        object-fit: scale-down;
    }

</style> 

