<style media="screen">
  div.fatal{
    position: absolute;
    width: 100vw;
    top:0;
  }
  div.fatal > span{
    display: inline-block;
    position: absolute;
    width: 150px;
    text-align: center;
    background-color: rgb(199, 28, 33);
    font-weight: bold;
    color:#fff;
    animation:fatal 50s infinite;
    -moz-animation:fatal 80s infinite;
  }
  @-webkit-keyframes fatal {
    0%{
      left: 0%;
    }
    50%{
      right: 0;
    }
    100%{
      left: 100%;
    }
  }
  }
  @keyframes fatal {
    0%{
      left: 0%;
    }
    50%{
      right: 0;
    }
    100%{
      left: 100%;
    }
  }
</style>
<div class="fatal">
  <span>Fatal Error</span>
</div>
