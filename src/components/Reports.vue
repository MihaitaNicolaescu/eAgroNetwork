<template>
  <div class="container">
    <nav class="navbar navbar-light bg-light">
      <button class="btn btn-outline-secondary" v-on:click="back">Back</button>
    </nav>
    <div v-if="reports.length === 0 && reports != null">
      <h2>Momentan nu sunt raportari.</h2>
    </div>
    <div v-if="reports.length && reports != null">
      <h2>Momentan sunt {{reports.length}} sesizari in asteptare.</h2>
      <table style="margin-top: 20px;" class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th scope="col">#</th>
            <th scope="col">Tipul</th>
            <th scope="col">Motivul</th>
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
            <td>{{report.reason}}</td>
            <td><a :href="report.link">{{report.link}}</a></td>
            <td><button class="btn btn-secondary"><a  style="color: white; text-decoration: none;" :href="frontend + '/profile/' + report.sender_id">Vizualizare profil</a></button></td>
            <td><button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#modalReport" v-on:click="actualReport(report.reported_id, report.id)">Raspunde</button></td>
          </tr>
        </tbody>
      </table>
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
    <!-- Modal -->
    <div class="modal fade bd-example-modal-sm" id="modalReport" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalReportTitle"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div style="display: grid;" class="modal-body">
            <button style="margin-bottom: 10px;" type="button" class="btn btn-secondary" v-on:click="confirm_ban">Interdictie</button>
            <button style="margin-bottom: 10px;" type="button" class="btn btn-secondary" v-on:click="confirm_warn">Atentionare</button>
            <button style="margin-bottom: 10px;" type="button" class="btn btn-secondary" v-on:click="closeReport(reportID)">Nimic gresit</button>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

  export default{
    data(){
      return{
        reports: [],
        actualUserId: null,
        reason: '',
        reportID: null,
        frontend: frontend,
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
        // eslint-disable-next-line no-undef
        $('#modalReport').modal('hide');
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
        axios.get(backend +'/api/getReports',{
        }).then((res)=>{
          this.reports = res.data['reports'];
        }).catch((error)=>{
          console.log(error);
        })
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
      confirm_ban: function(){
        // eslint-disable-next-line no-undef
        $('#modalReport').modal('hide');
        // eslint-disable-next-line no-undef
        $('#modal-confirm-ban').modal('show');
      },
      confirm_warn: function(){
        // eslint-disable-next-line no-undef
        $('#modalReport').modal('hide');
        // eslint-disable-next-line no-undef
        $('#modal-confirm-warn').modal('show');
      },
      actualReport: function(id, reportID){
        this.actualUserId = id;
        this.reportID = reportID;
      },
    },
  }
</script>