<template>
  <div style="min-width: 650px;" class="container-fluid container-view">
    <div class="d-flex align-items-center flex-column">
      <nav style="width: 888px; z-index: 2; position: fixed;" class="navbar navbar-light bg-light">
        <button class="btn btn-outline-secondary" v-on:click="logout">Deconectare</button>
        <form style="float: left" class="form-inline my-2 my-lg-0">
          <input class="search-input form-control mr-sm-2 input-search" type="search" placeholder="Căutare utilizator"
                 aria-label="Search" v-model="query">
          <button class="btn btn-icons  icon-search" type="button"><i class="material-icons">search</i></button>
        </form>
        <div class="ml-auto">
          <button v-if="isProducer === 0 && isProducer!==null " class="btn btn-success btn-sm" v-on:click="aplica()">Devino
            producător
          </button>
          <button v-if="admin" class="btn btn-icons" v-on:click="adminPanel"><span class="material-icons">admin_panel_settings</span>
          </button>
          <button class="btn btn-icons" v-on:click="profile"><span class="material-icons">account_circle</span></button>
          <button v-if="this.hasNotifications === 1" v-on:click="showNotifications()" class="btn btn-icons"><span
              class="material-icons">mark_email_unread</span></button>
          <button v-if="this.hasNotifications === 0" class="btn btn-icons" v-on:click="showNotifications()"><span
              class="material-icons">email</span></button>
        </div>
      </nav>
    </div>
    <div class="d-flex align-items-center flex-column">
      <ul style="margin-top: 60px; box-shadow: 0 8px 8px rgba(79,79,79, 0.5);" class="list-group list-search" v-if="results.length > 0 && query">
        <li class="list-group-item search-item" v-for="result in results.slice(0,10)" :key="result.id">
          <a class="link" :href="'/profile/' + result.searchable.id">
            <div class="link-profile" style="display: block ruby;">
              <img class="search-image" :src="backend + result.searchable.link_profile" alt="Prof. img" width="50"
                   height="50">
              <p style="color:black; display: inline-block;"
                 v-text="result.searchable.lastName + ' ' + result.title"></p>
            </div>
          </a>
        </li>
      </ul>
    </div>

    <div class="align-items-center flex-column" id="notifications-box">
      <ul style="box-shadow: 0 8px 8px rgba(79,79,79, 0.5);" class="list-group list-search list-notifications">
        <div v-if="hasNotifications === 0">
          <li class="list-group-item">
            <p>Nu ai notificari necitite</p>
            <button class="btn btn-danger btn-sm" type="button" v-on:click="closeNotifications()">Close</button>
            <button style="margin-left: 10px;" class="btn btn-danger btn-sm" type="button"
                    v-on:click="goToNotifications">Afisare notificari mai vechi
            </button>
          </li>
        </div>
        <div v-else>
          <li class="list-group-item">
            <button style="" class="btn btn-danger btn-sm" type="button" v-on:click="closeNotifications()">Close
            </button>
            <button style="float:right;" class="btn btn-danger btn-sm" type="button" v-on:click="markReadNotifications">
              Marcheaza tot ca citit
            </button>
            <button style="margin-left: 10px;" class="btn btn-danger btn-sm" type="button"
                    v-on:click="goToNotifications">Afisare notificari
            </button>
          </li>
          <div v-for="notification in notifications.slice(0,5)" :key="notification.id">
            <div v-if="notification.type === 1">
              <li class="list-group-item">
                <div style="display: block;" class="user-info">
                  <div style="margin-bottom: 5px;" class="row">
                    <div class="col-2">
                      <img alt="user profile photo" class="user-info-img" :src="backend + notification.link_profile">
                    </div>
                    <div class="col-10">
                      <p style="margin-bottom: 0;">{{ notification.firstName }} {{ notification.lastName }}</p>
                      <p style="display: block; font-weight: normal;">{{ notification.message }}</p>
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
                      <p style="display: block; font-weight: normal;">{{ notification.message }}</p>
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
                      <p class="pre-formatted" style="display: block; font-weight: normal;">
                        {{ notification.message }}</p>
                    </div>
                  </div>
                </div>
              </li>
            </div>
            <div v-if="notification.type === -122">
              <li class="list-group-item">
                <div style="display: block;" class="user-info">
                  <div style="margin-bottom: 5px;" class="row">
                    <div class="col-2">
                      <p style="font-size: 30px">⚠️</p>
                    </div>
                    <div class="col-10">
                      <p class="pre-formatted" style="display: block; font-weight: normal;">
                        {{ notification.message }}</p>
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
                      <p class="pre-formatted" style="display: block; font-weight: normal;">
                        {{ notification.message }}</p>
                    </div>
                  </div>
                </div>
              </li>
            </div>
          </div>
        </div>
      </ul>
    </div>
    <div style="margin-top: 60px;" class="d-flex align-items-center flex-column">
      <div class="row">
        <div class="col-8 posts-container">
          <div id="posts">
            <div v-for="(post, index) in fallowPosts" :key="post.id">
              <div class="container-post sn p-3">
                <div class="user-info">
                  <div class="row">
                    <div class="col-3">
                      <img alt="user profile photo" class="user-info-img" :src="backend + post.link_profile">
                    </div>
                    <div class="col">
                      <a style="color: black; text-decoration: none;"
                         :href="'/profile/' + post.user_id">{{ post.firstName + " " + post.lastName }}</a>
                      <p style="margin: 0; font-weight: normal; font-size: 13px">{{ formatDate(post.created_at) }}</p>
                    </div>
                  </div>
                </div>
                <div style="margin-bottom: 10px; margin-top: 10px;" class="post-description">
                  {{ post.description }}
                </div>
                <div class="d-flex align-items-center flex-column image-post">
                  <img v-if="post.has_photo === 1" alt="user post photo" class="post-image" :src="backend + post.link">
                </div>
                <button v-show="post.vote === 0 || post.vote === null || post.vote === -1" class="btn btn-react"
                        type="button" v-on:click="voteUp(post.id, index, 1)"><span
                    class="material-icons">thumb_up_alt</span></button>
                <button v-show="post.vote === 1" class="btn btn-react" type="button"
                        v-on:click="cancelVoteUp(post.id, index, 0)"><span class="material-icons" style="color: blue;">thumb_up_alt</span>
                </button>
                <span>{{ post.votes }}</span>
                <button class="btn btn-react" type="button" v-on:click="gotToComments(post.id)"><span
                    class="material-icons">insert_comment</span></button>
              </div>
            </div>
          </div>
        </div>
        <div style="left: -3%;" class="col-2">
          <div class="recomandari">
            <p v-if="producers !== null && producers.length !== 0" style="text-align: center; font-weight: bold;">Topul producătorilor în funcție de urmăritori</p>
            <ul v-if="producers !== null && producers.length !== 0" class="list-group list-group list-group-flush">
              <li v-for="producer in producers" :key="producer.id" class="list-group-item list-group-item-dark list-group-flush">
                <div class="row" style="margin-top: 5px">
                  <div class="col-sm-2">
                    <div class="user-info">
                      <img data-v-8dc7cce2="" alt="user profile photo" :src="backend + producer.link_profile"
                           class="user-info-img">
                    </div>
                  </div>
                  <div style="margin-left: 20px" class="col-8">
                    <p style="margin: 0; word-wrap: break-word; font-size: 15px; font-weight: bold;">
                      {{ producer.firstName }} {{ producer.lastName }}</p>
                    <button style="width: 100%" class="btn btn-sm btn-success"><a
                        style="text-decoration: none; color: white" :href="'/profile/' + producer.id">Vizualizare
                      profil</a></button>
                  </div>
                </div>
              </li>
            </ul>
            <p style="text-align: center;" v-if="producers !== null && producers.length === 0">Momentan nu sunt
              inregistrati producatori in acest judet.</p>
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

export default {
  data() {
    return {
      query: null,
      results: [],
      admin: 0,
      hasNotifications: null,
      fallowList: [],
      fallowPosts: [],
      isProducer: null,
      notifications: [],
      producers: null,
      backend: backend,
    }
  },
  components: {
    'alert-box': alertBox,
  },
  created() {
    setInterval(() => {
      this.getNotifications();
    }, 30000)
  },
  mounted() {
    axios.get(backend + '/api/fetchProfile', {
      params: {
        id: this.$route.params.id
      }
    }).then((response) => {
      this.firstName = response.data['firstName'];
      this.lastName = response.data['lastName'];
      this.email = response.data['email'];
      this.birthday = response.data['birthday'];
      this.id = response.data['id'];
      this.userPhoto = response.data['profile_image'];
      if (this.userPhoto == null)
        this.userPhoto = 'default.jpg';
    }).catch((error) => {
      console.log(error)
    })

    axios.get(backend + '/api/fetchUserData', {
      params: {
        token: localStorage.getItem('token'),
      }
    }).then((response) => {
      this.admin = response.data['admin'];
      this.isProducer = response.data['producer'];
    }).catch(() => {
      this.$router.push('login');
    })
    this.getNotifications();
    this.getUserFallowList();
    this.getAllProducers();
  },
  watch: {
    query: function () {
      if (this.query.length >= 1) {
        this.searchMembers();
      }
    },
    fallowList: function () {
      this.getFallowListPosts();
    }
  },
  methods: {
    formatDate: function (date) {
      let newDate = new Date(date);
      let months = ['Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie'];
      return newDate.getDate() + ' ' + months[newDate.getMonth()] + ' ' + newDate.getFullYear();
    },
    getAllProducers: function () {
      axios.get(backend + '/api/localProducers', {
        params: {
          token: localStorage.getItem('token'),
        }
      }).then((res) => {
        this.producers = res.data['producers'];
      }).catch((error) => {
        console.log(error);
      })
    },
    gotToComments: function (postID) {
      this.$router.push('/post/' + postID);
    },
    goToNotifications: function () {
      this.$router.push('/notifications');
      this.markReadNotifications();
    },
    markReadNotifications: function () {
      axios.post(backend + '/api/markReadNotifications', {
        token: localStorage.getItem('token'),
      }).then(() => {
        this.notifications = [];
        this.hasNotifications = 0;
      }).catch((error) => {
        console.log(error);
      })
    },
    getNotifications: function () {
      axios.get(backend + '/api/getUnreadNotifications', {
        params: {
          token: localStorage.getItem('token'),
        }
      }).then((res) => {
        this.notifications = res.data['notifications'];
        if (this.notifications.length > 0) this.hasNotifications = 1;
        else this.hasNotifications = 0;
      }).catch((error) => {
        console.log(error);
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
          this.fallowPosts[index].votes--,
          this.fallowPosts[index].vote = 0,
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
          this.fallowPosts[index].votes++,
          this.fallowPosts[index].vote = 1,
      ).catch((error) => {
        console.log(error);
      })
      this.modifyVote(this.id, postId, vote)
    },
    getFallowListPosts: function () {
      let listConverted = JSON.stringify(this.fallowList);
      const sendGetRequest = async () => {
        try {
          const resp = await axios.get(backend + '/api/fetchFallowPosts', {
            params: {
              fallowList: listConverted,
              token: localStorage.getItem('token'),
            }
          });
          this.fallowPosts = resp.data['posts'];
        } catch (error) {
          console.log(error);
        }
      }
      sendGetRequest();
    },
    getUserFallowList: function () {
      const sendGetRequest = async () => {
        try {
          const response = await axios.get(backend + '/api/fetchFallowList', {
            params: {
              token: localStorage.getItem('token'),
            }
          });
          this.fallowList = response.data;
        } catch (error) {
          console.log(error);
        }
      }
      sendGetRequest();
    },
    closeNotifications: function () {
      document.getElementById('notifications-box').style.display = "none";
    },
    showNotifications: function () {
      document.getElementById('notifications-box').style.display = "block";
    },
    profile: function () {
      this.$router.push('profile');
    },
    adminPanel: function () {
      this.$router.push('admin');
    },
    aplica: function () {
      this.$router.push('aplica');
    },
    logout: function () {
      localStorage.clear();
      this.$router.push('login');
    },
    searchMembers() {
      axios.get(backend + '/api/user/search', {
        params: {
          query: this.query
        }
      })
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

.form-control:focus {
  border-color: #1b941d;
  box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(35, 196, 38, 0.5);
}

input {
  margin-left: 20px;
  border-radius: 0;
  border: none;
  border-bottom: 2px solid #1b941d;
  background: #f8f9fa !important
}

.recomandari {
  background-color: #f8f9fa !important;
  width: 300px;
  max-height: 900px;
  overflow: scroll;
  -ms-overflow-style: none; /* IE and Edge */
  scrollbar-width: none; /* Firefox */
  margin-top: 10px;
}
.list-group-item-dark{
  background-color: #f8f9fa !important;
}
.posts-container {
  height: 900px;
  left: 0.9%;
}

.container-fluid{
  background-image: url('../assets/background.png');
  background-repeat: no-repeat;
  background-size: cover;
  height: 100vh;
}
</style> 

