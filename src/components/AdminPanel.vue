<template>
  <div class="container">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="/">Home</a>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="/admin/reports">Raportari</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="/admin/applications">Aplicatii</a>
          </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
          <form class="search-user d-flex justify-content-center">
            <input v-model="searchedUser" class="form-control mr-sm-2 input-search" type="search"
                   placeholder="Cautati un utilizator" aria-label="Search">
            <button style="border: 0; background: transparent; color:black;" v-if="!active || active != 1"
                    class="btn search-btn" type="button" v-on:click="searchUser()"><i
                class="material-icons icon-search">search</i></button>
          </form>
          <button style="border: 0; background: transparent; color:black;" v-if="active" class="btn cancel-search"
                  type="button" v-on:click="usersButton();"><span class="material-icons">search_off</span></button>
        </form>
      </div>
    </nav>
    <div class="users">
      <table style="margin-top: 10px;" class="table">
        <thead class="thead-light">
        <tr>
          <th scope="col">ID</th>
          <th scope="col">ID_DB</th>
          <th scope="col">NUME</th>
          <th scope="col">PRENUME</th>
          <th scope="col">EMAIL</th>
          <th scope="col">PRODUCATOR</th>
          <th></th>

        </tr>
        </thead>
        <tbody>
        <tr v-for="(user, index) in users" :key="user.id">
          <td>{{ index }}<span v-if="user.banned === 1" class="badge badge-danger">Banned</span></td>
          <td>{{ user.id }}</td>
          <td>{{ user.firstName }}</td>
          <td>{{ user.lastName }}</td>
          <td>{{ user.email }}</td>
          <td v-if="user.producer == true"><span class="material-icons">done_outline</span></td>
          <td v-else><span class="material-icons">clear</span></td>
          <td>
            <button data-toggle="modal" data-target="#modal-user" class="btn btn-secondary"
                    v-on:click="actualUser(user.id, index)">Administrare
            </button>
          </td>
        </tr>
        </tbody>
      </table>
      <div class="pagination d-flex justify-content-center">
        <button class="btn btn-outline-secondary" v-on:click="fetchPaginateUsers(pagination.prev_page)"
                :disabled="!pagination.prev_page">Previos
        </button>
        <span>Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
        <button class="btn btn-outline-secondary" v-on:click="fetchPaginateUsers(pagination.next_page)"
                :disabled="!pagination.next_page">Next
        </button>
      </div>
      <!-- Modal ban user -->
      <div class="modal fade" id="modal-info-user" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
           aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Succes</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              Utilizatorul a primit restrictionare cu succes. Acesta nu mai are acces sa aceseze contul.
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal info delete user -->
      <div class="modal fade" id="modal-info-delete-user" tabindex="-1" role="dialog"
           aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Succes</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p>Utilizatorul nu poate fi sters deoarece este activ.</p>
              <p>Pentru a putea fi sters un cont acesta trebuie sa fie banat sau email-ul sa nu fie confirmat.</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal confirm ban user -->
      <div class="modal fade" id="modal-confirm-ban" tabindex="-1" role="dialog"
           aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-body">
              <div class="form-row">
                <div class="">
                  <label for="validationServer03">Motiv ban</label>
                  <input type="text" v-model="reason" class="form-control" id="validationServer03" required>
                  <div class="invalid-feedback">
                    Este necesara specificarea motivului pentru care utilizatorul urmeaza sa fie banat.
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" v-on:click="banUser(actualUserId, reason)">Ban utilizator
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal confirm warn user -->
      <div class="modal fade" id="modal-confirm-warn" tabindex="-1" role="dialog"
           aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-body">
              <div class="form-row">
                <div class="">
                  <label for="validationServer03">Motiv averizare</label>
                  <input type="text" v-model="reason" class="form-control" id="warnInput" required>
                  <div class="invalid-feedback">
                    Este necesara specificarea motivului pentru care utilizatorul urmeaza sa fie avertizat.
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" v-on:click="warnUser(actualUserId, reason)">Aplica
                avertizarea
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal pentru administrarea unui user -->
      <div class="modal fade bd-example-modal-sm" id="modal-user" tabindex="-1" role="dialog"
           aria-labelledby="modal-user" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modal-user-title">Administrare utilizator</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div style="display: grid;" class="modal-body">
              <button class="btn btn-danger btn-modal" type="button" v-on:click="deleteUser(actualUserId)">Sterge
                utilizator
              </button>
              <div v-if="this.actualUserIndex !== null && this.actualUserId !== null">
                <button v-if="users[this.actualUserIndex].producer === 1" class="btn btn-danger btn-modal" type="button"
                        v-on:click="cancelProducer(actualUserId)">Sterge grad de producator
                </button>
                <button v-if="users[this.actualUserIndex].producer === 0" class="btn btn-danger btn-modal" type="button"
                        v-on:click="giveProducer(actualUserId)">Adauga grad de producator
                </button>
                <button v-if="users[this.actualUserIndex].banned === 0" class="btn btn-danger btn-modal"
                        v-on:click="confirm_ban()">Ban
                </button>
                <button v-if="users[this.actualUserIndex].banned === 1" class="btn btn-danger btn-modal"
                        v-on:click="unbanUser(actualUserId)">Unban
                </button>
                <button v-if="users[this.actualUserIndex].banned === 0" class="btn btn-warning btn-modal"
                        v-on:click="confirm_warn">Avertizare
                </button>
              </div>
            </div>
            <div class="modal-footer d-flex align-items-center flex-column">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Inchide</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <alert-box></alert-box>
    <div id="confirm-box">
      <div class="d-flex align-items-center flex-column">
        <p id="alert-text">A aparut o eroare la incarcarea informatiilor, este necesara reconecarea in aplicatie.</p>
        <button class="btn btn-danger btn-profile" v-on:click="reconectError()">OK</button>
      </div>
    </div>
  </div>
</template>

<script>
import alertBox from './templates/invalidToken';
import axios from 'axios';
import {backend} from '@/constants.js';

export default {
  data() {
    return {
      url: backend + '/api/admin/users',
      pagination: [],
      users: null,
      searchedUser: '',
      active: 0,
      actualUserId: null,
      actualUserIndex: null,
      reason: '',
      info: '',
    }
  },

  mounted() {
    //verificarea userului pentru a se determina daca este un administrator valid
    axios.get(backend + '/api/verifyData', {
      params: {
        token: localStorage.getItem('token'),
      }
    }).then((res) => {
      localStorage.setItem('admin', res.data['isAdmin']);
      if (res.data['isAdmin'] === 0) this.$router.push('/');
      else this.getUsers();
    }).catch(() => {
      this.$router.push('/');
    })
    //
  },
  components: {
    'alert-box': alertBox,
  },
  methods: {
    confirm_warn: function () {
      // eslint-disable-next-line no-undef
      $('#modal-user').modal('hide');
      // eslint-disable-next-line no-undef
      $('#modal-confirm-warn').modal('show');
    },
    warnUser: function (userID, reason) {
      axios.post(backend + '/api/warnUser', {
        token: localStorage.getItem('token'),
        userID: userID,
        reason: reason,
      }).then(() => {
        // eslint-disable-next-line no-undef
        $('#modal-user').modal('hide');
        // eslint-disable-next-line no-undef
        $('#modal-confirm-warn').modal('hide');
        // eslint-disable-next-line no-undef
        this.actualUserId = null;
        this.actualUserIndex = null;
        this.reason = '';
        this.getUsers();
      }).catch((error) => {
        console.log(error)
      })
    },
    confirm_ban: function () {
      // eslint-disable-next-line no-undef
      $('#modal-user').modal('hide');
      // eslint-disable-next-line no-undef
      $('#modal-confirm-ban').modal('show');
    },
    actualUser: function (id, index) {
      this.actualUserId = id;
      this.actualUserIndex = index;
    },
    cancelProducer: function (producerID) {
      axios.post(backend + '/api/cancelProducer', {
        token: localStorage.getItem('token'),
        producer_id: producerID,
      }).then(() => {
        // eslint-disable-next-line no-undef
        $('#modal-user').modal('hide');
        this.getUsers()
      }).catch((e) => {
        console.log(e)
      })
    },
    giveProducer: function (producerID) {
      axios.post(backend + '/api/giveProducer', {
        token: localStorage.getItem('token'),
        producer_id: producerID,
      }).then(() => {
        // eslint-disable-next-line no-undef
        $('#modal-user').modal('hide');
        this.getUsers()
      }).catch((e) => {
        console.log(e)
      })
    },
    banUser: function (userID, reason) {
      axios.post(backend + '/api/banUser', {
        token: localStorage.getItem('token'),
        userID: userID,
        reason: reason,
      }).then(() => {
        // eslint-disable-next-line no-undef
        $('#modal-user').modal('hide');
        // eslint-disable-next-line no-undef
        $('#modal-confirm-ban').modal('hide');
        // eslint-disable-next-line no-undef
        $('#modal-ban-user').modal('show');
        this.getUsers();
        this.actualUserId = null;
        this.actualUserIndex = null;
        this.reason = '';
      }).catch((error) => {
        console.log(error)
      })
    },
    unbanUser: function (userID) {
      axios.post(backend + '/api/unbanUser', {
        token: localStorage.getItem('token'),
        userID: userID,
      }).then(() => {
        // eslint-disable-next-line no-undef
        $('#modal-user').modal('hide');
        this.getUsers();
      }).catch((error) => {
        console.log(error)
      })
    },
    aplications: function () {
      this.$router.push('/admin/applications');
    },
    reports: function () {
      this.$router.push('/admin/reports');
    },
    deleteUser: function (id) {
      if (this.users[this.actualUserIndex].banned === 1) {
        axios.post(backend + '/api/admin/delete', {
          id: id,
        }).then(() => {
          // eslint-disable-next-line no-undef
          $('#modal-user').modal('hide');
          this.getUsers();
        })
      } else {
        // eslint-disable-next-line no-undef
        $('#modal-user').modal('hide');
        // eslint-disable-next-line no-undef
        $('#modal-info-delete-user').modal('show');
      }
    },
    searchUser: function () { // functie pentru a cauta un anumit utilizator
      if (this.searchedUser != '') {
        this.getUsers();
        this.active = 1;
      }
    },
    usersButton: function () { // functie pentru resetarea cautarii si afisarea listei cu utilizatorii inregistrati
      this.active = 0;
      this.searchedUser = '';
      this.getUsers();
    },
    back: function () {
      this.$router.push('/');
    },
    getUsers() { // functie pentru preluarea userilor din backend
      let $this = this;
      axios.get(this.url, {params: {searchedUser: this.searchedUser}}).then(response => {
        this.users = response.data.data
        $this.makePagination(response.data)
        if (this.users.length == 0) {
          //alert("Utilizatorul cautat nu exista in baza de date");
          this.usersButton();
        }
      })
    },
    makePagination(data) { // realizarea link-urilor pentru paginare
      let pagination = {
        current_page: data.current_page,
        last_page: data.last_page,
        next_page: data.next_page_url,
        prev_page: data.prev_page_url
      };
      this.pagination = pagination;
    },
    fetchPaginateUsers(url) { // generarea urmatoarelor pagini pentru "next" sau "prev" si incarcarea userilor de pe o anumita pagina
      this.url = url;
      this.getUsers();
    }
  }
}
</script>

<style scoped>
.btn-modal {
  margin-bottom: 5px;
  width: 100%;
}

.input-search {
  width: 300px;
}

.search-btn {
  background-color: transparent;
  margin-left: 0px;
  width: 50px;
}

#confirm-text {
  color: white;
  font-size: 2rem;
  text-align: center;
  padding-top: 110px;
}

#confirm-box {
  position: fixed;
  display: none;
  width: 500px;
  height: 500px;
  background-color: rgb(77, 74, 73);
  z-index: 2;
  cursor: pointer;
  margin-left: 300px;
  margin-top: -140px;
}

.cancel-search {
  width: 50px;
}
</style>