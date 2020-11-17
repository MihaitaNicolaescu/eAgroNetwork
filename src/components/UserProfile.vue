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
        </div>
    </div>
</template>

<script>

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
        mounted() {
            axios.get('http://127.0.0.1:8000/api/fetchProfile', {
                params: {
                    id: this.$route.params.id
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
            }).catch((error) =>{
                console.log(error)
            })
        },
        methods: {
            home: function(){
                this.$router.push('/');
            },
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