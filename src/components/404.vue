<template>
    <div class="container">
        <p class="not-found">Pagina nu a fost gasita!🐿️</p>
      <p class="go-home">Apasa <router-link :to="{path: '/'}">aici</router-link> pentru a te intoarce la pagina principala.</p>
    </div>
</template>
<style scoped>
    .go-home{
      font-size: 20px;
      font-family: "Courier New";
      text-align: center;
      font-weight: bold;
    }
    .not-found{
        font-size: 50px;
        font-family: "Courier New";
        text-align: center;
        margin-top: 200px;
    }
</style>