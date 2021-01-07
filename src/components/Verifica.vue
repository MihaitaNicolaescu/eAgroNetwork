<template>
  <div id="login">
    <div class="container d-flex align-items-center flex-column">
      <div v-if="valid === false && valid !== null" class="container">
        <p class="not-found">Link invalid.</p>
      </div>
    </div>
  </div>
</template>
<script>

import axios from 'axios';
import {backend} from '@/constants';

export default {
  data(){
    return {
      valid: null,
      email: decodeURIComponent(this.$route.params.email),
      code: this.$route.params.code,
    }
  },
  mounted() {
    this.verify();
  },
  methods: {
    verify: function(){
      axios.post(backend + '/api/verifyAccount', {
        email: this.email,
        code: this.code,
      }).then((respond)=>{
        if(respond.data['message'] === "INVALID") this.valid = false;
        if(respond.data['message'] === "success") this.$router.push('/login');
      }).catch((error)=>{
        console.log(error);
      })
    },
  }
}
</script>

<style scoped>

</style>