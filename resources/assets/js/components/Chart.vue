<template>
    <div class="container">
        {{msg}}
        <br/>
        <svg version="1.2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="graph" aria-labelledby="title" role="img">
            <path d=" M 10 25
            L 10 75
            L 60 75
            L 10 25"
            stroke="red" stroke-width="2" fill="none" />
        </svg>
    </div>
</template>

<script>
export default {
  name: 'Chart',
  props: {
    msg: String,
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>
.chart .ohlc-series .bar.down-day {
    stroke: red;
}
</style>
