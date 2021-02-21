<template>
  <div class="container">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a href="/"><img src="@/assets/Logo.png" alt="Logo" style="width: 50px;"></a>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a disabled style="font-weight: bold;" class="nav-link">Utilizatori</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/admin/reports">Raportari</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/admin/applications">Aplicatii</a>
          </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
          <form class="search-user d-flex justify-content-center">
            <input v-model="searchedUser" class="form-control mr-sm-2 input-search" type="search"
                   placeholder="Cautati un utilizator" aria-label="Search">
            <button style="border: 0; background: transparent; color:black;" v-if="!active || active !== 1"
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
          <td v-if="user.producer === true"><span class="material-icons">done_outline</span></td>
          <td v-else><span class="material-icons">clear</span></td>
          <td>
            <button data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Sterge utilizator" class="btn btn-danger btn-modal" type="button" v-on:click="deleteUser(user.id, index)"><span class="material-icons">delete_forever</span></button>
            <button data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Scoate gradul de producator" v-if="user.producer === 1" class="btn btn-danger btn-modal" type="button" v-on:click="cancelProducer(user.id)"><span class="material-icons">do_disturb</span></button>
            <button data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Adauga gradul de producator" v-if="user.producer === 0" class="btn btn-danger btn-modal" type="button" v-on:click="giveProducer(user.id)"><span class="material-icons">control_point</span></button>
            <button data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Baneaza utilizatorul" v-if="user.banned === 0" class="btn btn-danger btn-modal" v-on:click="confirm_ban(user.id)"><span class="material-icons">do_disturb_on</span></button>
            <button data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Debaneaza utilizatorul"  v-if="user.banned === 1" class="btn btn-danger btn-modal" v-on:click="unbanUser(user.id)"><span class="material-icons">do_disturb_off</span></button>
            <button data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Acorda avertisment" v-if="user.banned === 0" class="btn btn-warning btn-modal" v-on:click="confirm_warn(user.id)"><span class="material-icons">warning_amber</span></button>
          </td>
        </tr>
        </tbody>
      </table>
      <div class="pagination d-flex justify-content-center">
        <button data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Pagina anterioara" class="btn btn-outline-secondary pagination-button" v-on:click="fetchPaginateUsers(pagination.prev_page)" :disabled="!pagination.prev_page"><span class="material-icons">undo</span></button>
        <span>Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
        <button data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Pagina urmatoare" class="btn btn-outline-secondary pagination-button" v-on:click="fetchPaginateUsers(pagination.next_page)" :disabled="!pagination.next_page"><span class="material-icons">redo</span></button>
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

// eslint-disable-next-line no-undef
$(function(){
  // eslint-disable-next-line no-undef
  $('body').tooltip({ selector: '[data-toggle="tooltip"]' });
});

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
    // eslint-disable-next-line no-undef
    $(document).ready(function(){
      // eslint-disable-next-line no-undef
      $('[data-toggle="tooltip"]').tooltip();
      // eslint-disable-next-line no-undef
      $('[data-tooltip="tooltip"]').tooltip();
    });
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
    // eslint-disable-next-line no-undef
    $("[data-toggle=tooltip]").tooltip();
  },
  components: {
    'alert-box': alertBox,
  },
  methods: {
    confirm_warn: function (userID) {
      this.actualUserId = userID;
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
    confirm_ban: function (userID) {
      this.actualUserId = userID;
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
        this.getUsers();
        // eslint-disable-next-line no-undef
        $('#adminPanel').tooltip('hide')
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
    deleteUser: function (id, index) {
      if (this.users[index].banned === 1) {
        axios.post(backend + '/api/admin/delete', {
          id: id,
        }).then(() => {
          this.getUsers();
        })
      } else {
        // eslint-disable-next-line no-undef
        $('#modal-info-delete-user').modal('show');
      }
    },
    searchUser: function () { // functie pentru a cauta un anumit utilizator
      if (this.searchedUser !== '') {
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
        if (this.users.length === 0) {
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
  margin-left: 10px;
  background-color: transparent;
  color: black;
  border: none;
}

.btn-modal:active, .btn-modal:focus{
  outline: none!important;
  box-shadow: none;
}

.input-search {
  width: 300px;
}

.search-btn {
  background-color: transparent;
  width: 50px;
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
.pagination-button{
  background-color: transparent;
  color: black;
  font-size: 20px;
  border: none;
}
.pagination-button:active, .pagination-button:focus{
  outline: none!important;
  box-shadow: none;
}
</style>