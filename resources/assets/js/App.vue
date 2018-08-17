<template>
    <div>
      <div class="background-image">
      </div>
      <div id="app">
        <Chatbox />
      </div>
    </div>
</template>

<script>
import Chatbox from './components/Chatbox.vue'

export default {
  name: 'app',
  components: {
    Chatbox
  },
  mounted: function() {

    }
}
</script>

<style lang="scss">
@import '../node_modules/bootstrap/scss/bootstrap.scss';
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
}
.background-image {
    position: fixed;
    background-image: url(https://i.ytimg.com/vi/NDbLo_BmfP8/maxresdefault.jpg);
    background-size: cover;
    display: block;
    filter: blur(5px);
    height: 100%;
    left: 0px;
    position: fixed;
    right: 0px;
    z-index: 1;
}
</style>
