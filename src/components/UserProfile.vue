<template>
  <div class="container-fluid">
    <div class="d-flex align-items-center flex-column">
      <nav style="width: 896px" v-if="invalidUser === false" class="navbar navbar-light bg-light">
        <button style="" class="btn btn-outline-secondary" v-on:click="home">Home</button>
      </nav>
    </div>
    <div class="container">
      <div v-if="invalidUser === false && isBanned === 0" class="d-flex align-items-left flex-column"
           style="height: 100px;">
        <div class="d-flex align-items-left">
            <div style="left: 9.65%" class="col-3 left-info">
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
                <button v-if="!fallowed && fallowed!=null && isProducer === 1 && visitorID !== id" style="width: 100%"
                        class="btn btn-success btn-sm" type="button" v-on:click="fallow()">Urmareste
                </button>
                <button v-if="fallowed && fallowed!=null && isProducer === 1 && visitorID !== id" style="width: 100%"
                        class="btn btn-danger btn-sm" type="button" v-on:click="cancelFallow()">Nu mai urmari
                </button>
                <button v-if="visitorID !== id" class="btn btn-danger btn-sm" type="button"
                        style="margin-top: 5px; width: 100%;" data-toggle="modal" data-target="#reportModal">Raporteaza
                  utilizatorul
                </button>
                <button v-if="isProducer === 1 && this.id !== this.visitorID"
                        style="margin-bottom: 10px; margin-top: 5px; width: 100%;" type="button"
                        class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalReview"
                        v-on:click="show_MyReview">Scrie o recenzie producatorului
                </button>
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
            </div>
        </div>
      </div>
      <div v-if="invalidUser === true || isBanned === 1" class="container">
        <p class="not-found">Utilizator invalid.</p>
        <p class="go-home">Apasa
          <router-link :to="{path: '/'}">aici</router-link>
          pentru a te intoarce la pagina principala.
        </p>
      </div>
    </div>
    <!-- Modal raportare -->
    <div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="reportModalTitle">Raportare utilizator</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input v-model="reportReason" type="text" class="input-group-text" style="width: 100%; font-weight: normal"
                   placeholder="Motivul raportari. Ex: Postari cu continut inadecvat." required>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Anuleaza</button>
            <button type="button" class="btn btn-primary" v-on:click="sendReport">Trimite</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal pentru review -->
    <div v-if="isProducer === 1" class="modal fade" id="modalReview" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Scrie un review</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="review">
              <fieldset class="rating">
                <input type="radio" v-model="stars" id="star5" name="rating" value="5"/><label class="full"
                                                                                               for="star5"></label>
                <input type="radio" v-model="stars" id="star4" name="rating" value="4"/><label class="full"
                                                                                               for="star4"></label>
                <input type="radio" v-model="stars" id="star3" name="rating" value="3"/><label class="full"
                                                                                               for="star3"></label>
                <input type="radio" v-model="stars" id="star2" name="rating" value="2"/><label class="full"
                                                                                               for="star2"></label>
                <input type="radio" v-model="stars" id="star1" name="rating" value="1"/><label class="full"
                                                                                               for="star1"></label>
              </fieldset>
              <input v-model="review_text" type="text" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            <button class="btn btn-success btn-sm" v-on:click="sendReview">Trimite parerea</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal succes send raportare -->
    <div class="modal fade" id="succesReportSend" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="reportSendModalTitle">Success</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Raportul a fost trimis cu succes, in scurt timp un administrator se va ocupa de acest lucru.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Inchide</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal failed send raportare -->
    <div class="modal fade" id="failedReportSend" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="reportFailedSendModalTitle">Success</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Am intampinat o problema la trimiterea sesizari tale, te rugam sa incerci mai tarziu, iti multumim!</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Inchide</button>
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
    }
  },
  components: {
    'alert-box': alertBox,
  },
  async created() {
    await this.getUserInformations();
  },
  mounted() {
    this.verifyToken();
  },
  watch: {
    id: function () {
      this.getUserPosts();
      this.getFallowed();
      this.getReviews();
    }
  },
  methods: {
    formatDate: function (date) {
      let newDate = new Date(date);
      let months = ['Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie'];
      return newDate.getDate() + ' ' + months[newDate.getMonth()] + ' ' + newDate.getFullYear();
    },
    show_MyReview: function () {
      this.getMyReview()
      // eslint-disable-next-line no-undef
      $('#modalReview').modal('show');
    },
    getMyReview: function () {
      axios.get(backend + '/api/getMyReview', {
        params: {
          token: localStorage.getItem('token'),
          userID: this.visitorID,
          userPageID: this.id,
        }
      }).then((res) => {
        this.myReview = res.data['review'];
        this.stars = this.myReview.rating;
        this.review_text = this.myReview.message;
      }).catch((error) => {
        console.log(error);
      })
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
    sendReview: function () {
      if (this.stars !== null) {
        axios.post(backend + '/api/sendReview', {
          token: localStorage.getItem('token'),
          scor: this.stars,
          message: this.review_text,
          for: this.id,
        }).then(() => {
          //this.getReviews(this.id);
          this.review_text = '';
          // eslint-disable-next-line no-undef
          $('#modalReview').modal('hide');
          this.getReviews();
        }).catch((error) => {
          console.log(error);
        })
      }
    },
    gotToComments: function (postID) {
      this.$router.push('/post/' + postID);
    },
    sendReport: function () {
      axios.post(backend + '/api/sendReport', {
        token: localStorage.getItem('token'),
        reason: this.reportReason,
        link: document.location.href,
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

.rating {
  border: none;
  float: left;
}

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
  font-family: "NerkoOne";
  src: url("../fonts/NerkoOne-Regular.ttf");
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
  height: 228px;
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
  left: 9.6%;
  margin-top: 10px;
}

input {
  border-radius: 0;
  border: none;
  border-bottom: 2px solid #1b941d;
}

.form-control:focus {
  border-color: #b5b1c4;
  box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.075) inset, 0px 0px 8px rgba(35, 196, 38, 0.5);
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
  font-family: "NerkoOne", serif;
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

.btn-profile {
  width: 100%;
  margin-top: 5px;
  margin-bottom: 5px;
}

.info {
  margin: 0;
  font-size: 18px;
}

#profile {
  font-size: 20px;
  padding-top: 2px;
}

.form-control {
  font-size: 20px !important;
  font-weight: bold;
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

.container-fluid {
  background-image: url('../assets/background.png');
  background-repeat: no-repeat;
  background-size: cover;
  height: 100vh;
}
</style>