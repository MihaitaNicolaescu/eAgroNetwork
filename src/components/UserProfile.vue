<template>
    <div class="container">
        <nav v-if="invalidUser == false" class="navbar navbar-light bg-light">
           <button style="" class="btn btn-outline-secondary" v-on:click="home">Home</button>
        </nav> 
        <div class="container">
            <div v-if="invalidUser == false" class="d-flex align-items-left flex-column" style="height: 100px; margin-top: 50px;">
                <div class="row">
                    <div class="col-3">
                        <div id="profile">
                            <img alt="profile image" class="profile-image" :src="require('@/assets/profiles/' + userPhoto)">  
                            <div id="profile-info">
                                <p class="info">First name: {{ firstName }}</p>
                                <p class="info">Last name: {{ lastName }}</p>
                                <p class="info">Email: {{ email }}</p>
                                <p class="info">Birthday: {{ birthday }}</p>
                                <p class="info">Producator atestat <img style="width:45px" src="../assets/Logo.png"></p>
                            </div>
                            <button v-if="!fallowed && fallowed!=null && isProducer == 1" class="btn btn-success btn-sm" type="button" v-on:click="fallow()">Urmareste</button>
                            <button v-if="fallowed && fallowed!=null && isProducer == 1" class="btn btn-danger btn-sm" type="button" v-on:click="cancelFallow()">Nu mai urmari</button>
                        </div>
                    </div>
                    <div id="posts" class="col-7">
                        <div v-for="(post, index) in userPosts" :key="post.id">
                            <div class="container-post sn p-3">
                                <div class="user-info">
                                    <img class="user-info-img" :src="require('@/assets/profiles/' + userPhoto)">
                                    <p>{{firstName + " " + lastName}}</p>
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
                                <button class="btn btn-react" type="button"><span class="material-icons">insert_comment</span></button>
                            </div>
                        </div>
                        <div v-if="isProducer == 0">
                            <img class="logo" src="../assets/Logo_cos.png">
                        </div>
                        
                    </div>
                        <p v-if="isProducer == 0" class="info-paragraph">Acest utlizator nu este inca inregistrat pe aplicatie ca fiind un producator.</p>
                </div>
            </div>
            <div class="container">
                <p style="margin-top: 200px;" class="not-found">（ ﾟДﾟ）</p>
                <p class="not-found">Utilizator invalid.</p>
                <p class="go-home">Apasa <router-link :to="{path: '/'}">aici</router-link> pentru a te intoarce la pagina principala.</p>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios';
import {backend} from '../constants.js';
    export default{
        data(){
            return{
                id: -1,
                userPhoto: 'default.jpg',
                firstName: '',
                lastName: '',
                email: '',
                birthday: '',
                tempFName: '',
                tempLName: '',
                tempEmail: '',
                tempBirthday: '',
                userPosts: {},
                fallowed:null,
                isProducer: null,
                invalidUser: true,
            }
        },
        async created(){
            await this.getUserInformations();
        },
        mounted() {
            this.verifyToken();
        },
        watch:{
            id: function(){
                this.getUserPosts();
                this.getFallowed();
            }
        },
        methods: {
            getFallowed:function(){
                axios.get(backend + '/api/getFallow', {
                    params:{
                        token: localStorage.getItem('token'),
                        id_user: this.id,
                    }
                }).then((response)=>{
                    this.fallowed = response.data;
                })
            },
            fallow: function(){
                 axios.post(backend + '/api/fallowUser', {
                    token: localStorage.getItem('token'),
                    fallowId: this.id,
                }).then(
                    this.fallowed = true,
                ).catch((error)=>{
                    if(error['message'] == "token") document.getElementById('overlay-alert').style.display ="block";
                    else console.log(error);
                })
            },
            cancelFallow: function(){
                 axios.post(backend + '/api/cancelFallowUser', {
                    token: localStorage.getItem('token'),
                    fallowId: this.id,
                }).then(
                    this.fallowed = false,
                ).catch((error)=>{
                    if(error['message'] == "token") document.getElementById('overlay-alert').style.display ="block";
                    else console.log(error);
                })
            },
            modifyVote: function(userId, postId, vote){ // functia modifica reactia userului de la o anumita postare cand reactioneaza cu up sau down
                axios.get(backend + '/api/modifyVote',{
                    params:{
                        token: localStorage.getItem('token'),
                        userId: userId,
                        postId: postId,
                        vote: vote,
                    }
                })
            },
            cancelVoteUp: function(postId, index, vote){
                axios.get(backend + '/api/vote', {
                    params:{
                        token: localStorage.getItem('token'),
                        postId: postId,
                        vote: -1,
                    }
                }).then(
                    this.userPosts[index].votes--,
                    this.userPosts[index].vote = 0,
                ).catch((error)=>{
                    console.log(error);
                })
                this.modifyVote(this.id, postId, vote)
            },
            voteUp: function(postId, index, vote){
                let response = false;
                axios.get(backend + '/api/vote', {
                    params:{
                        token: localStorage.getItem('token'),
                        postId: postId,
                        vote: 1,
                    }
                }).then(
                    response = true,
                ).catch((error)=>{
                    console.log(error);
                })
                if(response == true){
                    this.modifyVote(this.id, postId, vote)
                    if(this.userPosts[index].vote == -1){
                        this.cancelVoteDown(postId, index, 0);
                        this.userPosts[index].votes++,
                        this.userPosts[index].vote = 1
                    }
                }
            },
            getUserPosts: function(){
               const sendGetRequest = async() => {
                    try{
                        const resp = await axios.get(backend + '/api/fetchUserPosts', {
                    params:{
                        user_id: this.id,
                        token: localStorage.getItem('token'),
                    }});
                    this.userPosts = resp.data['posts'];
                    }catch(error){
                        console.log(error);
                    }
                }
                sendGetRequest();

            },
            getUserInformations: function(){
                axios.get(backend + '/api/fetchProfile', {
                params: {
                    id: this.$route.params.id
                }
                }).then((response)=>{
                    if(response.data == "INVALID_USER"){
                        this.invalidUser = true;
                    }else{
                        this.invalidUser = false;
                        this.firstName = response.data['firstName'],
                        this.lastName = response.data['lastName'],
                        this.email = response.data['email'],
                        this.birthday = response.data['birthday'],
                        this.id = response.data['id'],
                        this.userPhoto = response.data['profile_image'],
                        this.isProducer = response.data['producer']
                        if(this.userPhoto == null)
                            this.userPhoto = 'default.jpg'
                    }
                }).catch((error) =>{
                    console.log(error)
                })
            },
            verifyToken: function(){
                axios.get(backend + '/api/fetchUserData', {
                    params: {
                        token: localStorage.getItem('token'),
                    }
                }).catch(() =>{
                    document.getElementById('overlay-alert').style.display ="block";    
                })
            },
            home: function(){
                this.$router.push('/');
            },
        }
    }
</script>
<style scoped>
    /*Fonts from frontend server*/
    @font-face {
        font-family: "NerkoOne";
        src: url("../fonts/NerkoOne-Regular.ttf");
    }
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

    .info-paragraph{
        font-family: "NerkoOne";
        font-size: 55px;
        margin-top: -450px;
        text-align: center;
        
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
    .logo{
        z-index: 2;
        width: 500px;
    }
</style>