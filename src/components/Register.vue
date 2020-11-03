<template>
    <div class="container">
    <h2>Register an account</h2>
        <form class="form-group">
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">First name</span>
                </div>
                <input v-model="firstName" type="text" class="form-control" placeholder="First name" aria-label="nameFRegisterField" aria-describedby="basic-addon1" name="nameFRegisterField" id="nameFRegisterField">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Last name</span>
                </div>
                <input v-model="lastName" type="text" class="form-control" placeholder="Last name" aria-label="nameLRegisterField" aria-describedby="basic-addon1" name="nameLRegisterField" id="nameLRegisterField">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Email</span>
                </div>
                <input v-model="email" type="email" class="form-control" placeholder="Email" aria-label="emailField" aria-describedby="basic-addon1" name="emailField" id="emailField">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Password</span>
                </div>
                <input v-model="password" type="password" class="form-control" placeholder="Password" aria-label="passwordField" aria-describedby="basic-addon1" name="passwordField" id="passwordField">
            </div>
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Check password</span>
                </div>
                <input v-model="checkPassword" type="password" class="form-control" placeholder="Passwrod" aria-label="checkPasswordField" aria-describedby="basic-addon1" name="checkPasswordField" id="checkPasswordField">
            </div>
             <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">Birthday</span>
                </div>
                <input v-model="birthday" type="date" class="form-control" value="2020-01-01" aria-label="birthdayField" aria-describedby="basic-addon1" name="birthdayField" id="birthdayField">
            </div>
            
        </form>
        <button class="btn btn-outline-success" v-on:click="verifyFields">Register</button>
        <router-link :to="{path: 'login'}">    Have already an account? Click here to sign in</router-link>
    </div>
</template>

<script>


    import axios from 'axios';

    export default{
        data(){
            return{
                firstName: '',
                lastName: '',
                email: '',
                password: '',
                birthday: '',
                checkPassword: '',
                //eslint-disable-next-line
                reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/
            }
        },
        methods: {
            registerUser: function(){
                axios.post('http://127.0.0.1:8000/api/addUser', {
                    firstName: this.firstName,
                    lastName: this.lastName,
                    email: this.email,
                    password: this.password,
                    birthday: this.birthday,
                    headers: {
                        'Access-Control-Allow-Origin': '*',
                        'Content-Type': 'application/json',
                    }
                }).then((res) => {
                     if(res.data['message'] === 'success'){
                         this.$router.push('login');
                     }
                    })
                    .catch((err) => {
                        if(err){
                            alert('Email already taken!');
                        }
                 });
            },
            verifyFields: function(){
                if(!this.firstName && !this.lastName && !this.email && !this.password && !this.checkPassword && !this.birthday) alert('All fields are required!');
                else if(this.password !== this.checkPassword) alert('Passwords do not match!');
                else if(!this.reg.test(this.email)) alert('Invalid email! Example for a valid email: example@domain.com');
                else this.registerUser();

            }
        }
    }
</script>

<style scoped>
    .container{
        width: 500px;
        margin-top:100px;
    }

    .input-group-text{
        width: 140px !important;
    }
</style>

