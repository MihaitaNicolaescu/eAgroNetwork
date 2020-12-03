<template>
    <div class="container">
        <nav class="navbar navbar-light bg-light">
           <button class="btn btn-home" v-on:click="home"><span class="material-icons">home</span></button>              
        </nav>
        <h3 style="text-align: center;">Aplicatie pentru gradul de producator</h3>
        <div class="edit">
            <form class="form-group">
                <div class="d-flex align-items-center flex-column">
                    <div class="d-flex flex-row">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Nume de familie</span>
                        </div>
                        <input v-model="firstName" style="width: 200px" type="text" class="form-control" id="firstNameInput">
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Prenume</span>
                        </div>
                        <input v-model="lastName" style="width: 200px" type="text" class="form-control" id="lastNameInput">
                    </div>
                    </div>
                    <!--<div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Genul</span>
                        </div>
                        <select style="width: 100px" class="form-control" id="gender">
                            <option>Neutru</option>
                            <option>Barbat</option>
                            <option>Femeie</option>
                        </select>
                    </div>-->
                    <div class="d-flex flex-row adresa">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Judet</span>
                            </div>
                            <input v-model="judet" style="width: 200px;" type="text" class="form-control" id="judet">
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span style="margin-left: 75px" class="input-group-text">Localitate</span>
                            </div>
                            <input v-model="localitate" style="width: 200px;" type="text" class="form-control" id="localitate">
                        </div>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Adresa completa</span>
                        </div>
                        <input v-model="adresa" style="width: 488px;" type="text" class="form-control" id="adress">
                    </div>
                    <div class="d-flex flex-row">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Seria</span>
                            </div>
                            <input v-model="seria" style="width: 50px;" type="text" class="form-control" id="serie_ci" maxlength="2">
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span style="margin-left: 40px;" class="input-group-text">Numarul</span>
                            </div>
                            <input v-model="numar_serie" style="width: 100px;" type="numeric" class="form-control" id="serie_ci" minlength="6" maxlength="6">
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span style="margin-left: 40px;" class="input-group-text">CNP</span>
                            </div>
                            <input v-model="cnp" style="width: 200px" type="number" class="form-control" id="cnp" minlength="15" maxlength="15">
                        </div>
                    </div>
                    <div class="d-flex flex-row">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Nr. certificat</span>
                            </div>
                            <input v-model="nr_certificat" style="width: 170px;" type="number" class="form-control" id="serie_certificat">
                        </div>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span style="margin-left: 70px;" class="input-group-text">Primaria</span>
                            </div>
                            <input v-model="primaria" style="width: 200px;" type="text" class="form-control" id="primaria">
                        </div>
                    </div>
                    <div class="custom-file">
                        <input type="file" @change="selectCertificat" class="custom-file-input" id="certificat_producator" required>
                        <label class="custom-file-label" for="validatedCustomFile">Selectati copia certificatului de producator</label>
                        <div class="invalid-feedback">Invalid file</div>
                    </div>
                    <div class="custom-file">
                        <input type="file"  @change="selectCI" class="custom-file-input" id="certificat_producator" required>
                        <label class="custom-file-label" for="validatedCustomFile">Selectati copia C.I/B.I</label>
                        <div class="invalid-feedback">Invalid file</div>
                    </div>
                    <div class="input-group">
                        <textarea v-model="alte_precizari" placeholder="*Alte precizari" class="form-control textarea" id="altele" rows="3"></textarea>
                    </div>
                </div>
                <button class="btn btn-success" type="button" v-on:click="confirmForm()">Trimite aplicatia</button>
            </form>
        </div>
        <alert-box></alert-box>


        <div class="overlay" id="confirm">
             <form class="form-group">
            <div class="d-flex align-items-center flex-column">
                <div class="d-flex flex-row">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Nume de familie</span>
                    </div>
                    <input v-model="firstName" style="width: 200px" type="text" class="form-control" id="firstNameInput" readonly>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Prenume</span>
                    </div>
                    <input v-model="lastName" style="width: 200px" type="text" class="form-control" id="lastNameInput" readonly>
                </div>
                </div>
                <!--<div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Genul</span>
                    </div>
                    <select style="width: 100px" class="form-control" id="gender">
                        <option>Neutru</option>
                        <option>Barbat</option>
                        <option>Femeie</option>
                    </select>
                </div>-->
                <div class="d-flex flex-row adresa">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Judet</span>
                        </div>
                        <input v-model="judet" style="width: 200px;" type="text" class="form-control" id="judet" readonly>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span style="margin-left: 75px" class="input-group-text">Localitate</span>
                        </div>
                        <input v-model="localitate" style="width: 200px;" type="text" class="form-control" id="localitate" readonly>
                    </div>
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">Adresa completa</span>
                    </div>
                    <input v-model="adresa" style="width: 488px;" type="text" class="form-control" id="adress" readonly>
                </div>
                <div class="d-flex flex-row">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Seria</span>
                        </div>
                        <input v-model="seria" style="width: 50px;" type="text" class="form-control" id="serie_ci" maxlength="2" readonly>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span style="margin-left: 40px;" class="input-group-text">Numarul</span>
                        </div>
                        <input v-model="numar_serie" style="width: 100px;" type="numeric" class="form-control" id="serie_ci" minlength="6" maxlength="6" readonly>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span style="margin-left: 40px;" class="input-group-text">CNP</span>
                        </div>
                        <input v-model="cnp" style="width: 200px" type="number" class="form-control" id="cnp" minlength="15" maxlength="15" readonly>
                    </div>
                </div>
                <div class="d-flex flex-row">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Nr. certificat</span>
                        </div>
                        <input v-model="nr_certificat" style="width: 170px;" type="number" class="form-control" id="serie_certificat" readonly>
                    </div>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span style="margin-left: 70px;" class="input-group-text">Primaria</span>
                        </div>
                        <input v-model="primaria" style="width: 200px;" type="text" class="form-control" id="primaria" readonly>
                    </div>
                </div>
                <div class="input-group">
                    <textarea v-model="alte_precizari" placeholder="*Alte precizari" class="form-control textarea" id="altele" rows="3" readonly></textarea>
                </div>
            </div>
            <button style="margin-left: 43%; margin-top: 20px;" class="btn btn-danger" type="button" v-on:click="editForm()">Editeaza</button>
            <button style="margin-left: 5px; margin-top: 20px;" class="btn btn-success" type="button" v-on:click="sendForm()">Confirma trimiterea</button>
        </form>
        </div>
    </div>
</template>

<script>
    import {backend} from '../constants.js';
    import axios from 'axios';
    import alertBox from './templates/invalidToken'; 
    export default{
        data(){
            return{
                id: null,
                firstName: '',
                lastName: '',
                judet: '',
                localitate: '',
                adresa: '',
                seria: '',
                numar_serie: '',
                cnp: '',
                nr_certificat: '',
                primaria: '',
                alte_precizari: '',
                copie_ci: null,
                copie_certificat: null,

            }
        }, 
        components:{
            'alert-box': alertBox,
        },
        mounted(){
            this.verifyToken();
            this.getUserData();
        },
        methods:{
            onUpload: function(){
                const fd = new FormData();
                fd.append('copie_ci', this.copie_ci, 'copie_ci'+this.id);
                fd.append('copie_certificat', this.copie_certificat, 'copie_certificat'+this.id);
                fd.append('token', localStorage.getItem('token'));
                axios.post('http://127.0.0.1:8000/api/updateUserProfilePhoto', fd, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(() => {
                    alert('Imaginea a fost actualizata cu succes!');
                    this.userPhoto = 'profile_image_'+this.id+'.jpg';
                })
            },
            selectCI(event){
                this.copie_ci = null;
                this.copie_ci = event.target.files[0]
            },
            selectCertificat(event){
                this.copie_certificat = null;
                this.copie_certificat = event.target.files[0]
            },
            verifyToken: function(){
                axios.get(backend + '/api/fetchUserData', {
                    params: {
                        token: localStorage.getItem('token'),
                    }
                }).catch(() =>{
                    document.getElementById('overlay-alert').style.display ="block";    
                })
            },
            home: function(){
                this.$router.push('/');
            },
            getUserData: function(){
                axios.get(backend+'/api/fetchUserData', {
                    params: {
                        token: localStorage.getItem('token'),
                    }
                }).then((response)=>{
                    this.id = response.data['id'];
                    this.firstName = response.data['firstName'];
                    this.lastName = response.data['lastName'];
                }).catch(() =>{
                    this.$router.push('login');    
                })
            },
            confirmForm: function(){
                document.getElementById('confirm').style.display ="block";
                document.getElementById('edit').style.display ="none";
            },
            editForm: function(){
                document.getElementById('edit').style.display ="block";
                document.getElementById('confirm').style.display ="none";
            }
            
        }
    }
</script>
<style scoped>
    .form-control{
        flex: none;
        display:flex;
    }
    .input-group{
        width: auto;
        margin-top: 10px;

    }
    .textarea{
        resize: none;
        width: 636px;
        font-weight: normal;
    }
    .custom-file{
        width: 636px;
        margin-top: 10px;
    }
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        /* display: none; <- Crashes Chrome on hover */
        -webkit-appearance: none;
        margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
    }

    input[type=number] {
        -moz-appearance:textfield; /* Firefox */
    }
    .overlay{
        position: fixed;
        display: none;
        width: 100%;
        height: 100%;
        cursor: pointer;
        margin-top: -605px;
        margin-left: -405px;
    }
</style>