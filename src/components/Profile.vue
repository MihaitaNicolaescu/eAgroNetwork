<template>
    <div class="container-fluid">
      <div class="d-flex align-items-center flex-column">
        <nav style="width: 1000px" class="navbar navbar-light bg-light">
           <button style="" class="btn btn-outline-secondary" v-on:click="home">Home</button>
        </nav>
      </div>
        <div class="container">
            <div class="d-flex align-items-left flex-column" style="height: 100px; margin-top: 50px;">
                <div class=" d-flex align-items-center">
                    <div class="col-3">
                        <div v-if="user !== null" id="profile">
                            <img alt="profile image" class="profile-image" :src="backend + user.link_profile">
                            <div id="profile-info">
                                <p class="info">First name: {{ firstName }}</p>
                                <p class="info">Last name: {{ lastName }}</p>
                                <p class="info">Email: {{ email }}</p>
                                <p class="info">Judet: {{ judet }}</p>
                                <p class="info">Birthday: {{ birthday }}</p>
                            </div>
                        </div>
                        <button class="btn btn-secondary btn-sm btn-profile" v-on:click="on()">Edit profile</button>
                        <button v-show="isProducer" class="btn btn-secondary btn-sm btn-profile" type="button" v-on:click="post_on()">Add a new post</button>
                        <div v-if="reviews!==null" class="review-form-users">
                          <div v-for="review in reviews" :key="review.id">
                            <div class="row" style="margin-top: 5px">
                              <div class="col-sm-1">
                                <div class="user-info">
                                  <img alt="user profile photo" style="width: 50px; height: 50px;" class="profile-image" :src="backend + review.link_profile">
                                </div>
                              </div>
                              <div class="col-9">
                                <p style="margin-left: 25px; word-wrap: break-word; font-size: 12px;"><a  style="color: black;
                                       text-decoration: none; font-weight: bold" :href="'/profile/'+review.reviewer_id">{{review.firstName}} {{review.lastName}}</a><br>{{review.message}}
                                  <br><span v-for="n in review.rating" :key="n" style="font-size: 15px;" class="material-icons" :id="'starRev'+ n">star_rate</span></p>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                    <div id="posts" class="col-9">
                        <div v-for="(post, index) in userPosts" :key="post.id">
                            <div class="container-post sn p-3">
                                <div class="user-info">
                                    <img  alt="user profile photo" class="user-info-img" :src="backend + link">
                                    <p>{{firstName + " " + lastName}}</p>
                                </div>
                                <div class="post-description">
                                {{post.description}} 
                                </div>
                                <div class="d-flex align-items-center flex-column image-post">
                                    <img v-if="post.has_photo === 1" alt="user post photo" class="post-image" :src="backend + post.link">
                                </div>
                                <button v-show="post.vote === 0 || post.vote === null || post.vote === -1" class="btn btn-react" type="button" v-on:click="voteUp(post.id, index, 1)"><span class="material-icons">thumb_up_alt</span></button>
                                <button v-show="post.vote === 1" class="btn btn-react" type="button" v-on:click="cancelVoteUp(post.id, index, 0)"><span class="material-icons" style="color: blue;">thumb_up_alt</span></button>
                                <span>{{post.votes}}</span>
                                <button class="btn btn-react" type="button" v-on:click="gotToComments(post.id)"><span class="material-icons">insert_comment</span></button>
                                <button class="btn btn-react" type="button" style="float: right;" v-on:click="confirm_post(post.id, index)"><span><span class="material-icons">edit</span></span></button>
                            </div>
                        </div>
                        <div v-if="isProducer === 0 && isProducer!==null">
                            <img alt="logo_cos" class="logo d-flex align-items-center flex-column" src="../assets/Logo_cos.png">
                        </div>
                      <p v-if="isProducer === 0 && isProducer!=null" class="info-paragraph">Daca esti un producator local poti sa trimiti un formular pentru a primi gradul de producator pe aplicatie. Detinatorii acestui grad au posibilitatea sa posteze fotografii
                        cu produsele agricole si sa poata primi recenzi de la alti utilizatori.
                      </p>
                    </div>
                </div>
            </div>
          <!-- Modal setari postare -->
          <div class="modal fade bd-example-modal-sm" id="modalPostare" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLongTitle">Actiuni postare</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div style="display: grid;" class="modal-body">
                  <button style="margin-top: 5px;" class="btn btn-secondary" type="button" v-on:click="editPost">Editeaza postarea</button>
                  <button style="margin-top: 5px;" class="btn btn-secondary" type="button" v-on:click="deletePost">Sterge postarea</button>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Inchide</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal editare descriere postare -->
          <div class="modal fade" id="modalPostareEdit" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="modalPostareEditTitle">Actiuni postare</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div style="display: grid;" class="modal-body">
                  <div class="form-group">
                    <textarea v-model="newDescription" style="resize: none; height: 60px;" class="form-control" id="exampleFormControlTextarea1" required></textarea>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Inchide</button>
                  <button style="margin-top: 5px;" class="btn btn-success btn-sm" type="button" v-on:click="modifyDescription">Modifica descrierea</button>
                </div>
              </div>
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
                                <span class="input-group-text">Judet</span>
                            </div>
                            <input type="text" class="form-control" v-model="tempJudet"  placeholder="Judet" aria-label="judet" aria-describedby="basic-addon1">
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
import {backend} from '@/constants.js';
import axios from 'axios';
    export default{
        data(){
            return{
                isProducer: null,
                id: -1,
                user: null,
                userPhoto: 'default.jpg',
                firstName: '',
                lastName: '',
                email: '',
                judet: '',
                birthday: '',
                tempFName: '',
                tempLName: '',
                tempJudet: '',
                tempBirthday: '',
                description: '',
                selectedFile: null,
                userPosts: {},
                actualPost: null,
                actualIndex: null,
                newDescription: '',
                reviews: null,
                link: null,
                backend: backend,
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
            getReviews: function(){
              axios.get(backend+'/api/getReviews',{
                params:{
                  token: localStorage.getItem('token'),
                  userID: this.id,
                }
              }).then((res)=>{
                this.reviews = res.data['reviews'];
              }).catch((error)=>{
                console.log(error);
              })
            },
            editPost: function(){
              // eslint-disable-next-line no-undef
              $('#modalPostare').modal('hide');
              // eslint-disable-next-line no-undef
              $('#modalPostareEdit').modal('show');
              this.newDescription = this.userPosts[this.actualIndex].description;
            },
            deletePost: function(){
              axios.post(backend+'/api/deletePost',{
                token: localStorage.getItem('token'),
                postID: this.actualPost,
              }).then(()=>{
                // eslint-disable-next-line no-undef
                $('#modalPostare').modal('hide');
                // eslint-disable-next-line no-undef
                this.getUserPosts();
              }).catch((error)=>{
                console.log(error);
              })
            },
            modifyDescription: function(){
              axios.post(backend+'/api/modifyDescriptionPost',{
                token: localStorage.getItem('token'),
                postID: this.actualPost,
                description: this.newDescription,
              }).then(()=>{
                // eslint-disable-next-line no-undef
                $('#modalPostareEdit').modal('hide');
                // eslint-disable-next-line no-undef
                this.getUserPosts();
              }).catch((error)=>{
                console.log(error);
              })
            },
            confirm_post: function(postID, index){
              this.actualPost = postID;
              this.actualIndex = index;
              // eslint-disable-next-line no-undef
              $('#modalPostare').modal('show');
            },
            gotToComments: function(postID){
              this.$router.push('/post/'+postID);
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
                    this.user = response.data;
                    this.firstName = response.data['firstName'];
                    this.lastName = response.data['lastName'];
                    this.email = response.data['email'];
                    this.birthday = response.data['birthday'];
                    this.id = response.data['id'];
                    this.userPhoto = response.data['profile_image'];
                    this.isProducer = response.data['producer'];
                    this.link = response.data['link_profile'];
                    this.judet = response.data['judet'];
                    if(this.userPhoto == null)
                        this.userPhoto = 'default.jpg';
                    this.getReviews();
                    }catch(error){
                        console.log(error);
                    }
                }
                sendGetRequest();
            },
            add_post: function(){
                const fd = new FormData();
                console.log(this.selectedFile);
                if(this.selectedFile === null) {
                  fd.append('image', "null");
                  fd.append('hasPhoto', 0);
                }
                else {
                  fd.append('image', this.selectedFile, 'post_image_'+this.id);
                  fd.append('hasPhoto', 1);
                }
                fd.append('token', localStorage.getItem('token'));
                fd.append('description', this.description);
                fd.append('user_id', this.id);
                axios.post(backend+'/api/addPost',fd, {
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
                this.tempJudet = this.judet;
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
                this.judet = this.tempJudet;
                this.birthday = this.tempBirthday;
                axios.post(backend+'/api/updateUserData', {
                        firstName: this.firstName,
                        lastName: this.lastName,
                        judet: this.judet,
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
                axios.post(backend+'/api/updateUserProfilePhoto', fd, {
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
                this.tempJudet = this.judet;
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
        font-family: "NerkoOne",serif;
        font-size: 30px;
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
    .review-form-users{
      overflow: scroll;
      -ms-overflow-style: none;  /* IE and Edge */
      scrollbar-width: none;  /* Firefox */
      height: 360px;
    }
    ::-webkit-scrollbar {
      display: none;
    }
    .btn-profile{
        width: 100%;
        margin-top: 5px;
        margin-bottom: 5px;
    }
    .info{
        margin: 0;
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
      width: 100%;
      max-height: 600px;
      margin: auto;
      object-fit: cover;
    }
    .image-post{
      width: 450px;
      margin-left: auto;
      margin-right: auto;
    }
</style>