<template>
    <div class="container">
        <nav class="navbar navbar-light bg-light">
           <button style="" class="btn btn-outline-secondary" v-on:click="home">Home</button>
        </nav> 
        <div class="container">
            <div class="d-flex align-items-left flex-column" style="height: 100px; margin-top: 50px;">
                <div class="row">
                    <div class="col-3">
                        <div id="profile">
                            <img alt="profile image" class="profile-image" :src="require('@/assets/profiles/' + userPhoto)">  
                            <div id="profile-info">
                                <p class="info">First name: {{ firstName }}</p>
                                <p class="info">Last name: {{ lastName }}</p>
                                <p class="info">Email: {{ email }}</p>
                                <p class="info">Birthday: {{ birthday }}</p>
                            </div>
                            <button v-if="!fallowed && fallowed!=null" class="btn btn-success btn-sm" type="button" v-on:click="fallow()">Urmareste</button>
                            <button v-if="fallowed && fallowed!=null" class="btn btn-danger btn-sm" type="button" v-on:click="cancelFallow()">Nu mai urmari</button>
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
                        
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>

import axios from 'axios';
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
                axios.get('http://127.0.0.1:8000/api/getFallow', {
                    params:{
                        token: localStorage.getItem('token'),
                        id_user: this.id,
                    }
                }).then((response)=>{
                    this.fallowed = response.data;
                })
            },
            fallow: function(){
                 axios.post('http://127.0.0.1:8000/api/fallowUser', {
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
                 axios.post('http://127.0.0.1:8000/api/cancelFallowUser', {
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
                axios.get('http://127.0.0.1:8000/api/modifyVote',{
                    params:{
                        token: localStorage.getItem('token'),
                        userId: userId,
                        postId: postId,
                        vote: vote,
                    }
                })
            },
            cancelVoteUp: function(postId, index, vote){
                axios.get('http://127.0.0.1:8000/api/vote', {
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
                axios.get('http://127.0.0.1:8000/api/vote', {
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
                        const resp = await axios.get('http://127.0.0.1:8000/api/fetchUserPosts', {
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
            },
            verifyToken: function(){
                axios.get('http://127.0.0.1:8000/api/fetchUserData', {
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