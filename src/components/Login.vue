<template>
    <div id="login">
        <div class="container">
        <form class="form-group">
           <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Email</span>
                </div>
                <input v-model="email" type="email" class="form-control" placeholder="" aria-label="emailField" aria-describedby="basic-addon1" name="emailField" id="emailField">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Password</span>
                </div>
                <input v-model="password" type="password" class="form-control" placeholder="" aria-label="passwordField" aria-describedby="basic-addon1" name="passwordField" id="passwordField">
            </div>
            <button type="button" class="btn btn-outline-secondary" v-on:click="checkForms">Login</button>
            <router-link :to="{path: 'register'}">    Don't have an account? Click here for sign up</router-link>    
        </form>
    </div>
    </div>
</template>
<script>

    import axios from 'axios';

    export default {
        data(){
            return {
                email: '',
                password: '',
                //eslint-disable-next-line
                reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/
            }
        },
        methods: {
            login: function(){
                axios.post('http://127.0.0.1:8000/api/login', {
                    email: this.email,
                    password: this.password,
                    headers:{
                        'Access-Control-Allow-Origin': '*',
                        'Content-Type': 'application/json',
                    }
                }).then((res) =>{
                    localStorage.setItem('token', res.data['token']);
                    localStorage.setItem('admin', res.data['admin']);
                    this.$router.push('/');
                }).catch((error)=>{
                    console.log(error);
                });
            },
            checkForms: function(){
                if(!this.email && !this.password)  alert('Please insert email and password');
                else if(!this.password) alert('Please insert password');
                else if(!this.email){
                    alert('Please insert email');
                }
                else if(!this.reg.test(this.email)) alert('Invalid email! Example for a valid email: example@domain.com');
                else this.login();
            },
        }
    }
</script>

<style scoped>
    .container{
        margin-top:100px;
        width: 500px;
    }
    .input-group-text{
        width: 92px;
    }
</style>tio