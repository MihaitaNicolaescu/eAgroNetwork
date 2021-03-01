<template>
  <div class="container">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a href="/"><img src="@/assets/Logo.png" alt="Logo" style="width: 50px;"></a>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="/admin">Utilizatori</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" style="font-weight: bold">Raportari</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/admin/applications">Aplicatii</a>
          </li>
        </ul>
      </div>
    </nav>
    <div v-if="reports !== null && reports.length === 0">
      <h2>Momentan nu sunt raportari.</h2>
    </div>
    <div v-if="numberOfReports != null && numberOfReports">
      <h2>Momentan sunt {{numberOfReports}} sesizari in asteptare.</h2>
      <table style="margin-top: 20px;" class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th scope="col">#</th>
            <th scope="col">Tipul</th>
            <th scope="col">Motivul</th>
            <th scope="col">Data trimiterii</th>
            <th scope="col">Link</th>
            <th scope="col">Trimis de</th>
            <th scope="col">Actiune</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(report, index) in reports" :key="report.id">
            <td>{{index}}</td>
            <td v-if="report.report_type === 1">Profil</td>
            <td v-if="report.report_type === 2">Postare</td>
            <td style="width: 260px;"><p class="motiv" style="margin-bottom: -10px!important;">{{report.reason}}</p></td>
            <td>{{formatDate(report.updated_at)}}</td>
            <td v-if="report.report_type === 1"><a style="color: black; font-weight: bold;" :href="frontend+'/report/profile/'+report.link">{{report.reportedFName}} {{ report.reportedLName}}</a></td>
            <td v-if="report.report_type === 2"><a style="color: black; font-weight: bold;" :href="frontend+'/report/post/'+report.link">{{report.reportedFName}} {{ report.reportedLName}}</a></td>
            <td><a  style="color: black; text-decoration: none; font-weight: bold;" :href="frontend + '/profile/' + report.sender_id">{{report.senderFName}} {{report.senderLName}}</a></td>
            <td>
              <button data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Baneaza utilizatorul" style="margin-bottom: 10px;" type="button" class="btn btn-secondary btn-modal" v-on:click="confirm_ban(report.reported_id, report.id)"><span class="material-icons">do_disturb_on</span></button>
              <button data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Acorda avertisment" style="margin-bottom: 10px;" type="button" class="btn btn-secondary btn-modal" v-on:click="confirm_warn(report.reported_id, report.id)"><span class="material-icons">warning_amber</span></button>
              <button data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Inchide reclamatia fara a lua vreo masura" style="margin-bottom: 10px;" type="button" class="btn btn-secondary btn-modal" v-on:click="closeReport(report.id)"><span class="material-icons">beenhere</span></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="pagination d-flex justify-content-center">
      <button id="btn-prev" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Pagina anterioara" class="btn btn-outline-secondary pagination-button" v-on:click="fetchPaginateUsers(pagination.prev_page)" :disabled="!pagination.prev_page"><span class="material-icons">undo</span></button>
      <span>Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
      <button id="btn-next" data-toggle="tooltip" data-trigger="hover" data-placement="bottom" title="Pagina urmatoare" class="btn btn-outline-secondary pagination-button" v-on:click="fetchPaginateUsers(pagination.next_page)" :disabled="!pagination.next_page"><span class="material-icons">redo</span></button>
    </div>
    <!-- Modal confirm ban user -->
    <div class="modal fade" id="modal-confirm-ban" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
            <button type="button" class="btn btn-primary" v-on:click="banUser(actualUserId, reason, reportID)">Ban utilizator</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal confirm warn user -->
    <div class="modal fade" id="modal-confirm-warn" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
            <button type="button" class="btn btn-primary" v-on:click="warnUser(actualUserId, reason, reportID)">Aplica avertizarea</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

  import axios from "axios";
  import {backend} from "@/constants";
  import {frontend} from "@/constants";

  // eslint-disable-next-line no-undef
  $(function(){
    // eslint-disable-next-line no-undef
    $('body').tooltip({ selector: '[data-toggle="tooltip"]' });
  });

  export default{
    data(){
      return{
        url: backend + '/api/admin/reports',
        reports: null,
        actualUserId: null,
        reason: '',
        reportID: null,
        frontend: frontend,
        pagination: [],
        numberOfReports: null,
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
        if(res.data['isAdmin'] === 0) this.$router.push('/');
        else this.getReports();
      }).catch((error) => {
        console.log(error);
      })
    },
    methods:{
      formatDate: function (date) {
        let newDate = new Date(date);
        let months = ['Ianuarie', 'Februarie', 'Martie', 'Aprilie', 'Mai', 'Iunie', 'Iulie', 'August', 'Septembrie', 'Octombrie', 'Noiembrie', 'Decembrie'];
        return newDate.getDate() + ' ' + months[newDate.getMonth()] + ' ' + newDate.getFullYear();
      },
      warnUser: function(userID, reason, reportID){
        axios.post(backend +'/api/warnUser', {
          token: localStorage.getItem('token'),
          userID: userID,
          reason: reason,
        }).then(()=>{
          // eslint-disable-next-line no-undef
          $('#modal-user').modal('hide');
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
      closeReport: function(reportID){
        this.markSolved(reportID);
        this.getReports();
      },
      markSolved: function(reportID){
        axios.post(backend + '/api/markSolved',{
          token: localStorage.getItem('token'),
          reportID: reportID,
        }).then(()=>{
          this.getReports();
        }).catch((error)=>{
          console.log(error);
        })
      },
      getReports: function(){
        axios.get(this.url,{
          params:{
            token: localStorage.getItem('token'),
          }
        }).then((res)=>{
          this.reports = res.data.data;
          this.makePagination(res.data);
        }).catch((error)=>{
          console.log(error);
        })
      },
      makePagination(data) { // realizarea link-urilor pentru paginare
        let pagination = {
          current_page: data.current_page,
          last_page: data.last_page,
          next_page: data.next_page_url,
          prev_page: data.prev_page_url
        };
        this.numberOfReports = data.total;
        this.pagination = pagination;
      },
      fetchPaginateUsers(url) { // generarea urmatoarelor pagini pentru "next" sau "prev" si incarcarea userilor de pe o anumita pagina
        this.url = url;
        // eslint-disable-next-line no-undef
        $('#btn-next').tooltip('hide')
        // eslint-disable-next-line no-undef
        $('#btn-prev').tooltip('hide')
        this.getReports();
      },
      banUser: function(userID, reason, reportID){
        axios.post(backend +'/api/banUser', {
          token: localStorage.getItem('token'),
          userID: userID,
          reason: reason,
        }).then(()=>{
          // eslint-disable-next-line no-undef
          $('#modal-user').modal('hide');
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
      back: function(){
        this.$router.push('/admin');
      },
      confirm_ban: function(reportedID, reportID){
        this.actualUserId = reportedID;
        this.reportID = reportID;
        // eslint-disable-next-line no-undef
        $('#modal-confirm-ban').modal('show');
      },
      confirm_warn: function(reportedID, reportID){
        this.actualUserId = reportedID;
        this.reportID = reportID;
        // eslint-disable-next-line no-undef
        $('#modal-confirm-warn').modal('show');
      },
    },
  }
</script>
<style>
.btn-modal {
  margin-right: 10px;
  background-color: transparent;
  color: black;
  border: none;
  height: 30px;
}

.btn-modal:active, .btn-modal:focus{
  outline: none!important;
  box-shadow: none;
}
.btn-modal:hover{
  background-color: transparent;
  color: black;
}
.motiv {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 250px;
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
.pagination-button:hover{
  background-color: transparent;
  color: black;
}
</style>