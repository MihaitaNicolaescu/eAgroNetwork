<template>
    <div id="overlay-alert">
        <div class="container">
            <div class="d-flex align-items-center flex-column">
                <p id="alert-text">A aparut o eroare la incarcarea informatiilor, este necesara reconecarea in aplicatie.</p>
                <button class="btn btn-danger btn-profile" v-on:click="reconectError()">OK</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    methods:{
        reconectError: function(){
            this.$router.push('login');
            localStorage.clear();
        }
    }
}
</script>
<style scoped>
    #alert-text{
        color:white;
        font-size: 2rem;
        text-align: center;
        padding-top: 110px;
    }
     #overlay-alert{
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
    .btn-profile{
        margin-top: 10px;
        width: 425px;
        font-size: 20px;
    }
</style>