<template>
    <div style="min-width: 650px;" class="container-fluid container-view">
      <div class="d-flex align-items-center flex-column">
        <nav style="width: auto; z-index: 2; position: fixed;" class="navbar navbar-light bg-light">
          <button class="btn btn-outline-secondary" v-on:click="logout">Log out</button>
          <form style="float: left" class="form-inline my-2 my-lg-0">
            <input class="search-input form-control mr-sm-2 input-search" type="search" placeholder="Search" aria-label="Search" v-model="query">
            <button class="btn btn-icons  icon-search" type="button"><i class="material-icons">search</i></button>
          </form>
          <div class="ml-auto">
            <button v-if="isProducer === 0 && isProducer!==null " class="btn btn-success" v-on:click="aplica()">Devino producator</button>
            <button v-if="admin" class="btn btn-icons" v-on:click="adminPanel"><span class="material-icons">admin_panel_settings</span></button>
            <button class="btn btn-icons" v-on:click="profile"><span class="material-icons">account_circle</span></button>
            <button v-if="this.hasNotifications === 1" v-on:click="showNotifications()" class="btn btn-icons"><span class="material-icons">mark_email_unread</span></button>
            <button v-if="this.hasNotifications === 0" class="btn btn-icons" v-on:click="showNotifications()"><span class="material-icons">email</span></button>
          </div>
        </nav>
      </div>
      <div class="d-flex align-items-center flex-column">
        <ul style="margin-top: 65px;" class="list-group list-search" v-if="results.length > 0 && query">
          <li class="list-group-item search-item" v-for="result in results.slice(0,10)" :key="result.id">
            <a class="link" :href="'/profile/' + result.searchable.id">
              <div class="link-profile" style="display: block ruby;">
                <img v-if="result.searchable.profile_image !== 'default.jpg'" class="search-image" :src="require('@/assets/profiles/profile_image_' + result.searchable.id + '.jpg')" alt="Prof. img" width="50" height="50">
                <img v-else class="search-image" :src="require('@/assets/profiles/default.jpg')" alt="Prof. img" width="50" height="50">
                <p style="color:black; display: inline-block;" v-text="result.searchable.lastName + ' ' + result.title"></p>
              </div>
            </a>
          </li>
        </ul>
      </div>

        <div class="align-items-center flex-column" id="notifications-box">
            <ul class="list-group list-search list-notifications">
              <div v-if="hasNotifications === 0">
                <li class="list-group-item">
                  <p>Nu ai notificari necitite</p>
                  <button class="btn btn-danger btn-sm" type="button" v-on:click="closeNotifications()">Close</button>
                  <button style="margin-left: 10px;" class="btn btn-danger btn-sm" type="button" v-on:click="goToNotifications">Afisare notificari mai vechi</button>
                </li>
              </div>
              <div v-else>
                <li class="list-group-item">
                  <button style="" class="btn btn-danger btn-sm" type="button" v-on:click="closeNotifications()">Close</button>
                  <button style="float:right;" class="btn btn-danger btn-sm" type="button" v-on:click="markReadNotifications">Marcheaza tot ca citit</button>
                  <button style="margin-left: 10px;" class="btn btn-danger btn-sm" type="button" v-on:click="goToNotifications">Afisare notificari</button>
                </li>
                <div v-for="notification in notifications.slice(0,5)" :key="notification.id">
                  <div v-if="notification.type === 1">
                    <li class="list-group-item">
                      <div style="display: block;" class="user-info">
                        <div style="margin-bottom: 5px;" class="row">
                          <div class="col-2">
                            <img alt="user profile photo" class="user-info-img" :src="require('@/assets/profiles/' + notification.profile_image)">
                          </div>
                          <div class="col-10">
                            <p style="margin-bottom: 0;">{{notification.firstName}} {{notification.lastName}}</p>
                            <p style="display: block; font-weight: normal;" >{{notification.message}}</p>
                          </div>
                        </div>
                      </div>
                    </li>
                  </div>
                  <div v-if="notification.type === -123">
                    <li class="list-group-item">
                      <div style="display: block;" class="user-info">
                        <div style="margin-bottom: 5px;" class="row">
                          <div class="col-2">
                            <p style="font-size: 30px">🎇</p>
                          </div>
                          <div class="col-10">
                            <p style="display: block; font-weight: normal;" >{{notification.message}}</p>
                          </div>
                        </div>
                      </div>
                    </li>
                  </div>
                  <div v-if="notification.type === -124">
                    <li class="list-group-item">
                      <div style="display: block;" class="user-info">
                        <div style="margin-bottom: 5px;" class="row">
                          <div class="col-2">
                            <p style="font-size: 30px">⚠️</p>
                          </div>
                          <div class="col-10">
                            <p  class="pre-formatted" style="display: block; font-weight: normal;" >{{notification.message}}</p>
                          </div>
                        </div>
                      </div>
                    </li>
                  </div>
                  <div v-if="notification.type === -125">
                    <li class="list-group-item">
                      <div style="display: block;" class="user-info">
                        <div style="margin-bottom: 5px;" class="row">
                          <div class="col-2">
                            <p style="font-size: 30px">❌️</p>
                          </div>
                          <div class="col-10">
                            <p  class="pre-formatted" style="display: block; font-weight: normal;" >{{notification.message}}</p>
                          </div>
                        </div>
                      </div>
                    </li>
                  </div>
                </div>
              </div>
            </ul>
        </div>
        <div style="margin-top: 65px;" class="posts-container d-flex align-items-center flex-column">
            <div id="posts">
                <div v-for="(post, index) in fallowPosts" :key="post.id">
                    <div class="container-post sn p-3">
                        <div class="user-info">
                            <img alt="user profile photo" class="user-info-img" :src="require('@/assets/profiles/' + post.profile_image)">
                            <a style="color: black; text-decoration: none; margin-left: 10px;" :href="'/profile/' + post.user_id">{{post.firstName + " " + post.lastName}}</a>
                        </div>
                        <div style="margin-bottom: 10px; margin-top: 10px;" class="post-description">
                        {{post.description}} 
                        </div>
                        <div class="d-flex align-items-center flex-column image-post">
                            <img alt="user post photo" class="post-image" :src="require('../assets/posts/' + post.filename)">
                        </div>
                        <button v-show="post.vote === 0 || post.vote === null || post.vote === -1" class="btn btn-react" type="button" v-on:click="voteUp(post.id, index, 1)"><span class="material-icons">thumb_up_alt</span></button>
                        <button v-show="post.vote === 1" class="btn btn-react" type="button" v-on:click="cancelVoteUp(post.id, index, 0)"><span class="material-icons" style="color: blue;">thumb_up_alt</span></button>
                        <span>{{post.votes}}</span>
                        <button class="btn btn-react" type="button" v-on:click="gotToComments(post.id)"><span class="material-icons">insert_comment</span></button>
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
    import {backend} from '@/constants.js';

    export default{
        data(){
            return{
                query: null,
                results: [],
                admin: 0,
                hasNotifications: null,
                fallowList: [],
                fallowPosts: [],
                isProducer: null,
                notifications: [],
            }
        },
        components: {
            'alert-box': alertBox,
        },
        created(){
          setInterval(() => {
              this.getNotifications();
            }, 30000)
        },
        mounted(){
            axios.get(backend+'/api/fetchProfile', {
                params: {
                    id: this.$route.params.id
                }
            }).then((response)=>{
                this.firstName = response.data['firstName'];
                this.lastName = response.data['lastName'];
                this.email = response.data['email'];
                this.birthday = response.data['birthday'];
                this.id = response.data['id'];
                this.userPhoto = response.data['profile_image'];
                if(this.userPhoto == null)
                    this.userPhoto = 'default.jpg';
            }).catch((error) =>{
                console.log(error)
            })

            axios.get(backend+'/api/fetchUserData', {
                params: {
                    token: localStorage.getItem('token'),
                }
            }).then((response)=>{
               this.admin = response.data['admin'];
               this.isProducer = response.data['producer'];
            }).catch(() =>{
                this.$router.push('login');    
            })
            this.getNotifications();
            this.getUserFallowList();
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
              gotToComments: function(postID){
                this.$router.push('/post/'+postID);
              },
             goToNotifications: function(){
               this.$router.push('/notifications');
             },
             markReadNotifications: function(){
               axios.post(backend +'/api/markReadNotifications', {
                 token: localStorage.getItem('token'),
               }).then(()=>{
                 this.notifications = [];
                 this.hasNotifications = 0;
               }).catch((error)=>{
                 console.log(error);
               })
             },
             getNotifications: function(){
               axios.get(backend+'/api/getUnreadNotifications', {
                 params:{
                   token: localStorage.getItem('token'),
                 }
               }).then((res) =>{
                    this.notifications = res.data['notifications'];
                    if(this.notifications.length > 0) this.hasNotifications = 1;
                    else this.hasNotifications = 0;
               }).catch((error)=>{
                 console.log(error);
               })
             },
             modifyVote: function(userId, postId, vote){ // functia modifica reactia userului de la o anumita postare cand reactioneaza cu up sau down
                axios.get(backend+'/api/modifyVote',{
                    params:{
                        token: localStorage.getItem('token'),
                        userId: userId,
                        postId: postId,
                        vote: vote,
                    }
                })
            },
            cancelVoteUp: function(postId, index, vote){
                axios.get(backend+'/api/vote', {
                    params:{
                        token: localStorage.getItem('token'),
                        postId: postId,
                        vote: -1,
                    }
                }).then(
                    this.fallowPosts[index].votes--,
                    this.fallowPosts[index].vote = 0,
                ).catch((error)=>{
                    console.log(error);
                })
                this.modifyVote(this.id, postId, vote)
            },
            voteUp: function(postId, index, vote){
                axios.get(backend+'/api/vote', {
                    params:{
                        token: localStorage.getItem('token'),
                        postId: postId,
                        vote: 1,
                    }
                }).then(
                    this.fallowPosts[index].votes++,
                    this.fallowPosts[index].vote = 1,
                ).catch((error)=>{
                    console.log(error);
                })
                this.modifyVote(this.id, postId, vote)
            },
            getFallowListPosts: function(){
                let listConverted = JSON.stringify(this.fallowList);
               const sendGetRequest = async() => {
                    try{
                        const resp = await axios.get(backend+'/api/fetchFallowPosts', {
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
            getUserFallowList: function(){
                 const sendGetRequest = async() => {
                    try{
                        const response = await axios.get(backend+'/api/fetchFallowList', {
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
                document.getElementById('notifications-box').style.display ="flex";
            },
            profile: function(){
                this.$router.push('profile');
            },
            adminPanel: function(){
                this.$router.push('admin');
            },
            aplica: function(){
                this.$router.push('aplica');
            },
            logout: function(){
                localStorage.clear();
                this.$router.push('login');
            },
            searchMembers() {
                axios.get(backend+'/api/user/search',  { params :{
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

    @import '../style/style.css';
    @import '../style/screenSizes.css';
    ::-webkit-scrollbar {
      display: none;
    }
    .form-control:focus{
      border-color: #9f5ac7;
      box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(255, 100, 255, 0.5);
    }
    input {
      margin-left: 20px;
      border-radius: 0;
      border: none;
      border-bottom: 2px solid #9f5ac7;
      background: #f8f9fa!important
    }
</style> 

