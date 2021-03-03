<template>
  <div class="container-fluid background-fundal">
    <div class="d-flex align-items-center flex-column">
      <nav style="width: 1450px" v-if="invalidUser === false" class="navbar navbar-light bg-light">
        <a href="/"><img src="@/assets/Logo.png" alt="Logo" width="50px"></a>
      </nav>
    </div>
    <div style="background-color: #f8f9fa !important; height: 500px; margin-top: 100px;" v-if="invalidUser === true || isBanned === 1" class="container">
      <p class="not-found">Reclamatie invalida.</p>
      <p class="go-home">Apasa
        <router-link :to="{path: '/admin/reports'}">aici</router-link>
        pentru a te intoarce la pagina cu reclamatii.
      </p>
    </div>
    <div style="margin-left: 10%; width: 100%" class="container">
      <div v-if="invalidUser === false && isBanned === 0" class="d-flex align-items-left flex-column"
           style="height: 100px;">
        <div class="d-flex">
          <div style="height: 900px;" class="col-5">
            <div class="container-reports">
              <div class="buttons-report d-inline" style="margin-left: 30%;">
                <button data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Baneaza utilizatorul"  type="button" class="btn btn-secondary btn-modal" v-on:click="confirm_ban(reports[0].reported_id, reports[0].id)"><span class="material-icons">do_disturb_on</span></button>
                <button data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Acorda avertisment"  type="button" class="btn btn-secondary btn-modal" v-on:click="confirm_warn(reports[0].reported_id, reports[0].id)"><span class="material-icons">warning_amber</span></button>
                <button data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Inchide reclamatia fara a lua vreo masura" type="button" class="btn btn-secondary btn-modal" v-on:click="markSolved(reports[0].id)"><span class="material-icons">beenhere</span></button>
              </div>
              <div class="reports-msg">
                <ul class="list-group">
                  <li v-for="(report, index) in reports" :key="index" class="list-group-item">
                    <div style="margin: 0" class="row">
                      <div class="col-1">
                        <div style="margin-left: -25px;" class="user-info">
                          <img style="width: 50px; height: 50px;" class="profile-image" :src="backend + report.senderProfile">
                        </div>
                      </div>
                      <div style="margin-left: -10px;" class="col-7">
                        <p style="margin-left: 20px; word-wrap: break-word;"><a  style="color: black; text-decoration: none; font-weight: bold" :href="'/profile/' + report.sender_id">{{ report.senderFName }} {{ report.senderLName }}</a></p>
                        <p style="margin-left: 20px; margin-top: -15px; word-wrap: break-word; width: 160%;">{{ report.reason }}</p>
                      </div>
                      <div style="width: 105px;" class="col-4">
                        <p style="font-size: 13px; width: 115%;">{{formatDate(report.updated_at)}}</p>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-3 left-info">
            <div id="profile">
              <img v-if="user !== null" style="margin-top: 10px; margin-left: 10%" alt="profile image" class="profile-image"
                   :src="backend + user.link_profile">
              <div id="profile-info">
                <p style="font-weight: bold; font-size: 14px; margin: 0;">Nume</p>
                <p class="info">{{ firstName }}</p>
                <p style="font-weight: bold; font-size: 14px; margin: 0;">Prenume</p>
                <p class="info">{{ lastName }}</p>
                <p style="font-weight: bold; font-size: 14px; margin: 0;">Email</p>
                <p class="info">{{ email }}</p>
                <p style="font-weight: bold; font-size: 14px; margin: 0;">Judet</p>
                <p class="info">{{ judet }}</p>
                <p style="font-weight: bold; font-size: 14px; margin: 0;">Data nasterii</p>
                <p class="info">{{ formatDate(birthday) }}</p>
                <p v-if="isProducer === 1" class="info">Producator atestat <img style="width:45px"
                                                                                src="../assets/Logo.png"></p>
                <p v-if="isProducer === 1 && fallowers > 0" class="info">Urmaritori <strong>{{ fallowers }}</strong>
                </p>
              </div>
              <div v-if="reviews!==null" class="review-form-users">
                <div v-for="review in reviews" :key="review.id">
                  <div class="row" style="margin-top: 5px">
                    <div class="col-sm-1">
                      <div class="user-info">
                        <img v-if="review !== null" style="width: 50px; height: 50px;" class="profile-image"
                             :src="backend + review.link_profile">
                      </div>
                    </div>
                    <div class="col-9">
                      <p style="margin-left: 25px; word-wrap: break-word; font-size: 12px;"><a style="color: black;
                                   text-decoration: none; font-weight: bold" :href="'/profile/'+review.reviewer_id">{{
                          review.firstName
                        }}
                        {{ review.lastName }}</a><br>{{ review.message }}
                        <br><span v-for="n in review.rating" :key="n" style="font-size: 15px;" class="material-icons"
                                  :id="'starRev'+ n">star_rate</span></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="posts" class="col-7">
            <div v-for="(post, index) in userPosts" :key="post.id">
              <div class="container-post sn p-3">
                <div class="user-info">
                  <div class="row">
                    <div class="col-3">
                      <img alt="user profile photo" class="user-info-img" :src="backend + user.link_profile">
                    </div>
                    <div class="col">
                      <p style="margin: 0">{{ firstName + " " + lastName }}</p>
                      <p style="margin: 0; font-weight: normal; font-size: 13px">{{ formatDate(post.created_at) }}</p>
                    </div>
                  </div>
                </div>
                <div class="post-description">
                  {{ post.description }}
                </div>
                <div class="d-flex align-items-center flex-column image-post">
                  <img v-if="post.has_photo === 1" class="post-image" :src="backend + post.link">
                </div>
                <button v-show="post.vote === 0 || post.vote == null || post.vote === -1" class="btn btn-react"
                        type="button" v-on:click="voteUp(post.id, index, 1)"><span
                    class="material-icons">thumb_up_alt</span></button>
                <button v-show="post.vote === 1" class="btn btn-react" type="button"
                        v-on:click="cancelVoteUp(post.id, index, 0)"><span class="material-icons"
                                                                           style="color: blue;">thumb_up_alt</span>
                </button>
                <span>{{ post.votes }}</span>
                <button class="btn btn-react" type="button" v-on:click="gotToComments(post.id)"><span
                    class="material-icons">insert_comment</span></button>
              </div>
            </div>
            <div v-if="isProducer === 0 && isProducer!=null"  class="container-info sn p-3">
              <img alt="logo_cos" class="logo d-flex align-items-center flex-column" src="../assets/Logo_cos.png">
              <p v-if="isProducer === 0 && isProducer!=null" class="info-paragraph">Daca esti un producator local poti sa
                trimiti un formular pentru a primi gradul de producator pe aplicatie. Detinatorii acestui grad au
                posibilitatea sa posteze fotografii
                cu produsele agricole si sa poata primi recenzi de la alti utilizatori.
              </p>
            </div>
            <div v-if="userPosts.length === 0 && userPosts!=null"  class="container-info sn p-3">
              <img alt="logo_cos" class="logo d-flex align-items-center flex-column" src="../assets/Logo_cos.png">
              <p v-if="userPosts.length === 0 && userPosts!=null" class="info-paragraph">Acest producator nu are momentan postari.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <alert-box></alert-box>
  </div>

</template>

<script>

import axios from 'axios';
import {backend} from '../constants.js';
import alertBox from "@/components/templates/invalidToken";

// eslint-disable-next-line no-undef
$(function(){
  // eslint-disable-next-line no-undef
  $('body').tooltip({ selector: '[data-toggle="tooltip"]' });
});

export default {
  data() {
    return {
      id: -1,
      user: null,
      userPhoto: 'default.jpg',
      firstName: '',
      lastName: '',
      email: '',
      birthday: '',
      userPosts: {},
      fallowed: null,
      isProducer: null,
      invalidUser: null,
      reportReason: null,
      isBanned: null,
      visitorID: null,
      stars: null,
      review_text: "",
      reviews: null,
      myReview: null,
      backend: backend,
      fallowers: null,
      judet: '',
      reports: null,
      actualUserId: null,
      reason: ''
    }
  },
  components: {
    'alert-box': alertBox,
  },
  async created() {
    await this.getUserInformations();
  },
  mounted() {
    //verificarea userului pentru a se determina daca este un administrator valid
    axios.get(backend + '/api/verifyData', {
      params: {
        token: localStorage.getItem('token'),
      }
    }).then((res) => {
      localStorage.setItem('admin', res.data['isAdmin']);
      if (res.data['isAdmin'] === 0) this.$router.push('/*');
      else{
        this.verifyToken();
        this.getID();
      }
    }).catch((error) => {
      console.log(error);
      this.$router.push('/*');
    })
  },
  watch: {
    id: function () {
      this.getUserPosts();
      this.getFallowed();
      this.getReviews();
      this.getReports();
    }
  },
  methods: {
    getID: function(){
      const sendGetRequest = async() => {
        try{
          const response = await axios.get(backend + '/api/fetchUserData', {
            params: {
              token: localStorage.getItem('token'),
            }});
          this.id = response.data['id'];
        }catch(error){
          console.log(error);
        }
      }
      sendGetRequest();
    },
    banUser: function(userID, reason, reportID){
      axios.post(backend +'/api/banUser', {
        token: localStorage.getItem('token'),
        userID: userID,
        reason: reason,
      }).then(()=>{
        // eslint-disable-next-line no-undef
        $('#modal-confirm-ban').modal('hide');
        // eslint-disable-next-line no-undef
        $('#modal-ban-user').modal('show');
        this.markSolved(reportID)
        this.actualUserId = null;
        this.actualUserIndex = null;
        this.reason = '';
      }).catch((error) => {
        console.log(error)
      })
    },
    confirm_ban: function(reportedID, reportID){
      this.actualUserId = reportedID;
      this.reportID = reportID;
      // eslint-disable-next-line no-undef
      $('#modal-confirm-ban').modal('show');
    },
    warnUser: function(userID, reason, reportID){
      axios.post(backend +'/api/warnUser', {
        token: localStorage.getItem('token'),
        userID: userID,
        reason: reason,
      }).then(()=>{
        // eslint-disable-next-line no-undef
        $('#modal-confirm-warn').modal('hide');
        // eslint-disable-next-line no-undef
        this.markSolved(reportID)
        this.actualUserId = null;
        this.actualUserIndex = null;
        this.reason = '';
      }).catch((error) => {
        console.log(error)
      })
    },
    confirm_warn: function(reportedID, reportID){
      this.actualUserId = reportedID;
      this.reportID = reportID;
      // eslint-disable-next-line no-undef
      $('#modal-confirm-warn').modal('show');
    },
    markSolved: function(reportID){
      axios.post(backend + '/api/markSolved',{
        token: localStorage.getItem('token'),
        reportID: reportID,
      }).then(()=>{
        this.$router.push('/admin/reports');
      }).catch((error)=>{
        console.log(error);
      })
    },
    getReports: function(){
      axios.get(backend+'/api/getReportsResponse',{
        params:{
          token: localStorage.getItem('token'),
          forID: this.$route.params.id,
          type: "PROFILE",
        }
      }).then((res)=>{
        this.reports = res.data['reports'];
      }).catch((error)=>{
        console.log(error);
      })
    },
    formatDate: function (date) {
      let newDate = new Date(date);
      let months = ['Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie'];
      return newDate.getDate() + ' ' + months[newDate.getMonth()] + ' ' + newDate.getFullYear();
    },
    getReviews: function () {
      axios.get(backend + '/api/getReviews', {
        params: {
          token: localStorage.getItem('token'),
          userID: this.id,
        }
      }).then((res) => {
        this.reviews = res.data['reviews'];
      }).catch((error) => {
        console.log(error);
      })
    },
    gotToComments: function (postID) {
      this.$router.push('/post/' + postID);
    },
    sendReport: function () {
      axios.post(backend + '/api/sendReport', {
        token: localStorage.getItem('token'),
        reason: this.reportReason,
        link: this.$route.params.id,
        type: 1,
        reported_id: this.id,
      }).then(() => {
        // eslint-disable-next-line no-undef
        $('#reportModal').modal('hide');
        // eslint-disable-next-line no-undef
        $('#succesReportSend').modal('show');

      }).catch((error) => {
        // eslint-disable-next-line no-undef
        $('#reportModal').modal('hide');
        // eslint-disable-next-line no-undef
        $('#failedReportSend').modal('show');
        console.log(error);
      })
    },
    getFallowed: function () {
      axios.get(backend + '/api/getFallow', {
        params: {
          token: localStorage.getItem('token'),
          id_user: this.id,
        }
      }).then((response) => {
        this.fallowed = response.data;
      })
    },
    fallow: function () {
      axios.post(backend + '/api/fallowUser', {
        token: localStorage.getItem('token'),
        fallowId: this.id,
      }).then(
          this.fallowed = true,
          this.fallowers += 1,
      ).catch((error) => {
        if (error['message'] === "token") document.getElementById('overlay-alert').style.display = "block";
        else console.log(error);
      })
    },
    cancelFallow: function () {
      axios.post(backend + '/api/cancelFallowUser', {
        token: localStorage.getItem('token'),
        fallowId: this.id,
      }).then(
          this.fallowed = false,
          this.fallowers -= 1,
      ).catch((error) => {
        if (error['message'] == "token") document.getElementById('overlay-alert').style.display = "block";
        else console.log(error);
      })
    },
    modifyVote: function (userId, postId, vote) { // functia modifica reactia userului de la o anumita postare cand reactioneaza cu up sau down
      axios.get(backend + '/api/modifyVote', {
        params: {
          token: localStorage.getItem('token'),
          userId: userId,
          postId: postId,
          vote: vote,
        }
      })
    },
    cancelVoteUp: function (postId, index, vote) {
      axios.get(backend + '/api/vote', {
        params: {
          token: localStorage.getItem('token'),
          postId: postId,
          vote: -1,
        }
      }).then(
          this.userPosts[index].votes--,
          this.userPosts[index].vote = 0,
      ).catch((error) => {
        console.log(error);
      })
      this.modifyVote(this.id, postId, vote)
    },
    voteUp: function (postId, index, vote) {
      axios.get(backend + '/api/vote', {
        params: {
          token: localStorage.getItem('token'),
          postId: postId,
          vote: 1,
        }
      }).then(
          this.userPosts[index].votes++,
          this.userPosts[index].vote = 1,
      ).catch((error) => {
        console.log(error);
      })
      this.modifyVote(this.id, postId, vote)
    },
    getUserPosts: function () {
      const sendGetRequest = async () => {
        try {
          const resp = await axios.get(backend + '/api/fetchUserPosts', {
            params: {
              user_id: this.id,
              token: localStorage.getItem('token'),
            }
          });
          this.userPosts = resp.data['posts'];
        } catch (error) {
          console.log(error);
        }
      }
      sendGetRequest();

    },
    getUserInformations: function () {
      axios.get(backend + '/api/fetchProfile', {
        params: {
          id: this.$route.params.id
        }
      }).then((response) => {
        if (response.data === "INVALID_USER") {
          this.invalidUser = true;
        } else {
          this.user = response.data;
          this.invalidUser = false;
          this.firstName = response.data['firstName'];
          this.lastName = response.data['lastName'];
          this.email = response.data['email'];
          this.birthday = response.data['birthday'];
          this.id = response.data['id'];
          this.userPhoto = response.data['profile_image'];
          this.isProducer = response.data['producer'];
          this.isBanned = response.data['banned'];
          this.judet = response.data['judet'];
          this.fallowers = response.data['fallowers'];

        }
      }).catch((error) => {
        console.log(error)
      })
    },
    verifyToken: function () {
      axios.get(backend + '/api/fetchUserData', {
        params: {
          token: localStorage.getItem('token'),
        }
      }).then((res) => {
        this.visitorID = res.data['id'];
        if (this.id === this.visitorID) {
          console.log("intra");
          this.$router.push('/profile');
        }
      }).catch(() => {
        this.$router.push('/');
      })
    },
    home: function () {
      this.$router.push('/');
    },
  }
}
</script>
<style scoped>

.container-info {
  background-color: #f8f9fa !important;
  width: 600px;
  height: auto;
  padding: 10px;
  margin-top: 10px;
}
/*Review style*/

@import url(https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css);

fieldset, label {
  margin: 0;
  padding: 0;
}

body {
  margin: 20px;
}

h1 {
  font-size: 1.5em;
  margin: 10px;
}

/****** Style Star Rating Widget *****/

#profile-info {
  word-wrap: break-word;
  margin: 0;
}

.rating > input {
  display: none;
}

.rating > label:before {
  margin: 5px;
  font-size: 2.5em;
  font-family: FontAwesome;
  display: inline-block;
  content: "\f005";
}

.rating > label {
  color: gray;
  float: right;
}

/***** CSS Magic to Highlight Stars on Hover *****/

.rating > input:checked ~ label, /* show gold star when clicked */
.rating:not(:checked) > label:hover, /* hover current star */
.rating:not(:checked) > label:hover ~ label {
  color: #FFD700;
}

/* hover previous stars in list */

.rating > input:checked + label:hover, /* hover current star when changing rating */
.rating > input:checked ~ label:hover,
.rating > label:hover ~ input:checked ~ label, /* lighten current selection */
.rating > input:checked ~ label:hover ~ label {
  color: #FFED85;
}

/*Fonts from frontend server*/
@font-face {
  font-family: "SourceSansPro";
  src: url("../fonts/SourceSansPro-Regular.ttf");
}

/*For invalid user*/
.not-found {
  font-size: 80px;
  font-family: "Courier New";
  text-align: center;
}

.go-home {
  font-size: 20px;
  font-family: "Courier New";
  text-align: center;
  font-weight: bold;
}

.info-paragraph {
  font-size: 55px;
  text-align: center;

}

.btn-react {
  background: transparent;
}

.user-info {
  display: block ruby;
  font-size: 15px;
  font-weight: bold;
}

.review-form-users {
  overflow: scroll;
  -ms-overflow-style: none; /* IE and Edge */
  scrollbar-width: none; /* Firefox */
  height: 328px;
}

::-webkit-scrollbar {
  display: none;
}

#posts {
  overflow: scroll;
  -ms-overflow-style: none; /* IE and Edge */
  scrollbar-width: none; /* Firefox */
  height: 900px;
  left: 8.9%;
}

.left-info {
  background-color: #f8f9fa !important;
  left: 5%;
  margin-top: 10px;
}

input {
  border-radius: 0;
  border: none;
  border-bottom: 2px solid #1b941d;
}

.form-control:focus + .form-control-placeholder,
.form-control:valid + .form-control-placeholder {
  font-size: 15px;
  transform: translate3d(0, -100%, 0);
  opacity: 1;
}

/*Fonts from frontend server*/
@font-face {
  font-family: "NerkoOne";
  src: url("../fonts/NerkoOne-Regular.ttf");
}

.logo {
  width: 400px;
  margin: auto;
}

.info-paragraph {
  font-family: "SourceSansPro", serif;
  font-size: 30px;
  text-align: center;

}

.btn-react {
  background: transparent;
}

.user-info {
  display: block ruby;
  font-size: 15px;
  font-weight: bold;
}

#posts {
  overflow: scroll;
  -ms-overflow-style: none; /* IE and Edge */
  scrollbar-width: none; /* Firefox */
  height: 900px;
  left: 9.99%;
}

.container-post {
  background-color: #f8f9fa !important;
  width: 600px;
  height: auto;
  padding: 10px;
  margin-top: 10px;
}

.user-info-img {
  width: 50px;
  height: 50px;
  border-radius: 50px;
  object-fit: cover;
}

::-webkit-scrollbar {
  display: none;
}

.info {
  margin: 0;
  font-size: 18px;
}

#profile {
  font-size: 20px;
  padding-top: 2px;
}

.profile-image {
  border-radius: 50%;
  object-fit: cover;
  width: 200px;
  height: 200px;
}

.post-image {
  width: 100%;
  max-height: 600px;
  margin: auto;
  object-fit: cover;
}

.image-post {
  width: 555px;
  margin-left: auto;
  margin-right: auto;
}

.background-fundal {
  background-image: url('../assets/background.png');
  background-repeat: no-repeat;
  background-size: cover;
  height: 100vh;
}

.container-reports{
  background-color: #f8f9fa !important;
  width: 450px;
  height: 100%;
  padding-left: 15px;
  margin-top: 10px;
}
.btn-modal {
  margin-right: 10px;
  background-color: transparent;
  color: black;
  border: none;
  margin-top: 10px;
}

.btn-modal:active, .btn-modal:focus{
  outline: none!important;
  box-shadow: none;
}
.btn-modal:hover{
  background-color: transparent;
  color: black;
}

.list-group li{
  border: gray solid 1px;
}
.list-group{
  margin-top: 20px;
  width: 96%;
}

.reports-msg{
  height:90%;
  overflow: hidden;
  overflow-y: scroll;
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
</style>