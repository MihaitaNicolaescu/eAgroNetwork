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
                        </div>
                        <button class="btn btn-outline-danger btn-profile" v-on:click="on()">Edit profile</button>
                        <button v-show="isProducer" class="btn btn-outline-success btn-profile" type="button" v-on:click="post_on()">Add a new post</button>
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
                                <button class="btn btn-react" tyoe="button" style="float: right;"><span><span class="material-icons">edit</span></span></button>
                            </div>
                        </div>
                        
                        <div v-if="isProducer == 0 && isProducer!=null">
                            <img class="logo d-flex align-items-center flex-column" src="../assets/Logo_cos.png">
                        </div>
                        
                    </div>
                        <p v-if="isProducer == 0 && isProducer!=null" class="info-paragraph">Daca detii esti un producator local poti sa trimiti un formular pentru a primi gradul de producator pe aplicatie. Detinatorii acestui grad au posibilitatea sa posteze fotografii
                            cu produsele agricole si sa poata primi recenzi de la alti utilizatori.
                        </p>
                </div>
            </div>
        </div>
        <div id="overlay">
            <div class="container">
                <div class="d-flex align-items-center flex-column">
                    <div class="input" style="margin-top: 60px;">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">First name</span>
                            </div>
                            <input type="text" class="form-control" v-model="tempFName"  placeholder="First name" aria-label="firstName" aria-describedby="basic-addon1">
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Last name</span>
                            </div>
                            <input type="text" class="form-control" v-model="tempLName" placeholder="Last name" aria-label="lastName" aria-describedby="basic-addon1">
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Email</span>
                            </div>
                            <input type="text" class="form-control" v-model="tempEmail"  placeholder="Email" aria-label="email" aria-describedby="basic-addon1">
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Birthday</span>
                            </div>
                            <input type="date" class="form-control" v-model="tempBirthday"  aria-label="birthday" aria-describedby="basic-addon1">
                        </div>
                        <div class="custom-file">
                            <input @change="onFileSelected" type="file" class="custom-file-input" id="inputGroupFile01" accept="image/*">
                            <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                        </div>
                    </div>
                    <button class="btn btn-success btn-profile" v-on:click="save()">Save data</button>
                    <button class="btn btn-danger btn-profile" v-on:click="cancel()">Cancel</button>
                </div>
            </div>
        </div>
        <div id="post-overlay">
            <div class="container">
                <div class="d-flex align-items-center flex-column">
                    <div class="form-group post-add">
                        <div class="custom-file">
                            <input @change="onFileSelected" type="file" class="custom-file-input" id="photo-post" accept="image/*">
                            <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                        </div>
                        <textarea v-model="description" class="form-control description-post" id="description-post" rows="3"></textarea>
                    </div>
                    <button class="btn btn-success btn-profile" type="button" v-on:click="add_post()">Continue</button>
                    <button class="btn btn-danger btn-profile" type="button" v-on:click="off()">Cancel</button>
                </div>
            </div>
        </div>
        <alert-box></alert-box>
        <div class="container overlay" id="confirm">
            
        </div>
    </div>
</template>

<script>
import alertBox from './templates/invalidToken';
import {backend} from '../constants.js';
import axios from 'axios';
    export default{
        data(){
            return{
                isProducer: null,
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
                description: '',
                selectedFile: null,
                userPosts: {},
            }
        },
        components:{
            'alert-box': alertBox,
        },

        async created() {
            await this.getUserInformatios();
        },
        watch:{
            id: function(){
                this.getUserPosts();
            }
        },
        mounted(){
            this.verifyToken();
        },
        methods: {
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
                axios.get(backend + '/api/vote', {
                    params:{
                        token: localStorage.getItem('token'),
                        postId: postId,
                        vote: 1,
                    }
                }).then(
                    this.userPosts[index].votes++,
                    this.userPosts[index].vote = 1,
                ).catch((error)=>{
                    console.log(error);
                })
                this.modifyVote(this.id, postId, vote)
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
            getUserInformatios: function(){
                 const sendGetRequest = async() => {
                    try{
                        const response = await axios.get(backend + '/api/fetchUserData', {
                    params: {
                        token: localStorage.getItem('token'),
                    }});
                    this.firstName = response.data['firstName'],
                    this.lastName = response.data['lastName'],
                    this.email = response.data['email'],
                    this.birthday = response.data['birthday'],
                    this.id = response.data['id'],
                    this.userPhoto = response.data['profile_image'],
                    this.isProducer = response.data['producer']
                    if(this.userPhoto == null)
                        this.userPhoto = 'default.jpg'
                    }catch(error){
                        console.log(error);
                    }
                }
                sendGetRequest();
            },
            add_post: function(){
                const fd = new FormData();
                fd.append('image', this.selectedFile, 'post_image_'+this.id);
                fd.append('token', localStorage.getItem('token'));
                fd.append('description', this.description);
                fd.append('user_id', this.id);
                axios.post('http://127.0.0.1:8000/api/addPost',fd, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
    
                }).then(()=>{
                    this.description = "";
                    this.selectedFile = "";
                }).catch((error) =>{
                    console.log(error)
                });
                this.off();
                this.selectedFile = null;
                this.getUserPosts();
            },
            on: function(){
                this.tempFName = this.firstName;
                this.tempLName = this.lastName;
                this.tempEmail = this.email;
                this.tempBirthday = this.birthday;
                document.getElementById('overlay').style.display ="block";
            },
            post_on: function(){
                document.getElementById('post-overlay').style.display = "block";
            },
            off: function(){
                document.getElementById('overlay').style.display ="none";
                document.getElementById('post-overlay').style.display ="none";
            },
            home: function(){
                this.$router.push('/');
            },
            onFileSelected(event){
                this.selectedFile = null;
                this.selectedFile = event.target.files[0]
            },
            save: function(){
                this.firstName = this.tempFName;
                this.lastName = this.tempLName;
                this.email = this.tempEmail;
                this.birthday = this.tempBirthday;
                axios.post('http://127.0.0.1:8000/api/updateUserData', {
                        firstName: this.firstName,
                        lastName: this.lastName,
                        email: this.email,
                        birthday: this.birthday,
                        token: localStorage.getItem('token'),
                    }).catch(() =>{
                        document.getElementById('overlay-alert').style.display ="block";
                });
                this.off();
                if(this.selectedFile != null){
                    this.onUpload();
                    this.selectedFile = null;
                }
            },
            onUpload: function(){
                const fd = new FormData();
                fd.append('image', this.selectedFile, 'profile_image_'+this.id);
                fd.append('token', localStorage.getItem('token'));
                axios.post('http://127.0.0.1:8000/api/updateUserProfilePhoto', fd, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(() => {
                    alert('Imaginea a fost actualizata cu succes!');
                    this.userPhoto = 'profile_image_'+this.id+'.jpg';
                })
            },
            cancel: function(){
                this.tempFName = this.firstName;
                this.tempLName = this.lastName;
                this.tempEmail = this.email;
                this.tempBirthday = this.birthday;
                this.off();
            }
        }
    }
</script>
<style scoped>
    /*Fonts from frontend server*/
    @font-face {
        font-family: "NerkoOne";
        src: url("../fonts/NerkoOne-Regular.ttf");
    }
    .logo{
        width: 400px;
        margin: auto;
    }
    .info-paragraph{
        font-family: "NerkoOne";
        font-size: 30px;
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
</style>