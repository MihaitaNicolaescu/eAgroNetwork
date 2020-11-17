<template>
    <div class="container">
        <nav class="navbar navbar-light bg-light">
           <button style="" class="btn btn-outline-secondary" v-on:click="home">Home</button>
        </nav> 
        <div class="d-flex align-items-center flex-column" style="height: 100px; margin-top: 50px;">
            <div class="row" id="profile">
                <div class="col">
                    <img alt="profile image" class="profile-image" :src="require('@/assets/profiles/' + userPhoto)">
                </div>
                <div class="col" id="profile-info">
                        <p class="info">First name: {{ firstName }}</p>
                        <p class="info">Last name: {{ lastName }}</p>
                        <p class="info">Email: {{ email }}</p>
                        <p class="info">Birthday: {{ birthday }}</p>
                </div>
            </div>
            <button class="btn btn-outline-danger btn-profile" v-on:click="on()">Edit profile</button>
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
                            <input @change="onFileSelected" type="file" class="custom-file-input" id="inputGroupFile01">
                            <label class="custom-file-label" for="inputGroupFile01">Choose file</label>
                        </div>
                    </div>
                    <button class="btn btn-success btn-profile" v-on:click="save()">Save data</button>
                    <button class="btn btn-danger btn-profile" v-on:click="cancel()">Cancel</button>
                </div>
            </div>
        </div>
        <alert-box></alert-box>
    </div>
</template>

<script>
import alertBox from './templates/invalidToken';

import axios from 'axios';
    export default{
        data(){
            return{
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

                selectedFile: null
            }
        },
        components:{
            'alert-box': alertBox,
        },
        mounted() {
            axios.get('http://127.0.0.1:8000/api/fetchUserData', {
                params: {
                    token: localStorage.getItem('token'),
                }
            }).then((response)=>{
                this.firstName = response.data['firstName'],
                this.lastName = response.data['lastName'],
                this.email = response.data['email'],
                this.birthday = response.data['birthday'],
                this.id = response.data['id'],
                this.userPhoto = response.data['profile_image']
                if(this.userPhoto == null)
                    this.userPhoto = 'default.jpg'
            }).catch(() =>{
                document.getElementById('overlay-alert').style.display ="block";    
            })
        },
        methods: {
            on: function(){
                this.tempFName = this.firstName;
                this.tempLName = this.lastName;
                this.tempEmail = this.email;
                this.tempBirthday = this.birthday;
                document.getElementById('overlay').style.display ="block";
            },
            off: function(){
                document.getElementById('overlay').style.display ="none";
            },
            home: function(){
                this.$router.push('/');
            },
            onFileSelected(event){
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
                }).then(response => {
                    console.log(response)
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
    .btn-profile{
        margin-top: 10px;
        width: 425px;
        font-size: 20px;
    }
    #profile-info{
        margin-bottom: 50px;
        padding-top: 50px;
    }
    #profile{
        font-size: 30px;
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
        width: 300px;
        height: 300px;
    }
</style>