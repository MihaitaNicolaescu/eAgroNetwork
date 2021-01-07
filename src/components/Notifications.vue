<template>
  <div class="container container-view">
    <nav class="navbar navbar-light bg-light">
      <button class="btn btn-outline-secondary" v-on:click="back">Back</button>
    </nav>
    <div class="d-flex justify-content-center">
      <ul style="width: 100%" class="list-group">
        <li v-for="notification in notifications" :key="notification.id" class="list-group-item">
          <div class="row">
            <div class="col-sm-1">
              <img :src="require('@/assets/profiles/'+ notification.profile_image)" width="50" height="50" style="object-fit: cover">
            </div>
            <div class="col-sm-9">

              <p class="notification-message"><a class="link" :href="'/profile/' + notification.from">{{ notification.firstName }} {{ notification.lastName }}</a> {{ notification.message }}.</p>
            </div>
          </div>
        </li>

      </ul>


    </div>
  </div>
</template>

<script>
  import axios from 'axios';
  import {backend} from '@/constants';

  export default {
    data(){
      return{
        notifications: [],
      }
    },
    mounted(){
      this.verifyToken();
      this.getNotifications();
    },
    methods:{
      getNotifications: function(){
        axios.get(backend+'/api/getNotifications', {
          params:{
            token: localStorage.getItem('token'),
          }
        }).then((res) =>{
          this.notifications = res.data['notifications'];
          if(this.notifications.length > 0) this.hasNotifications = 1;
          else this.hasNotifications = 0;
        }).catch((error)=>{
          console.log(error);
        })
      },
      back: function(){
        this.$router.push('/');
      },
      verifyToken: function(){
        axios.get(backend+'/api/fetchUserData', {
          params: {
            token: localStorage.getItem('token'),
          }
        }).then(()=>{
        }).catch(() =>{
          this.$router.push('login');
        })
      }
    }
  }
</script>

<style>
  .link{
    text-decoration: none;
    color: black;
    font-weight: bold;
  }
  .notification-message{
    font-size: 20px;
    padding-top: 5px;

  }

</style>