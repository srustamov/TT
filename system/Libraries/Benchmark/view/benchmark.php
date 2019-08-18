<style>
        div#bench-container::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 3px rgba(0,0,0,0.3);
            background-color: #F5F5F5;
        }

        div#bench-container::-webkit-scrollbar {
            width: 3px;
            background-color: #F5F5F5;
        }

        div#bench-container::-webkit-scrollbar-thumb {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
            background-color: #333;
        }
    div#bench-container * {
        margin: 0;
        padding: 0;
        text-decoration: none;
        box-sizing: border-box;
        font-size: 14px;
        font-family: monospace;

    }

    div#bench-container {
        max-height: 400px;
        margin: 0 auto;
        overflow: auto;
        z-index: 99999999999999;
        background-color: #1f1d1d;
        color: white;
        position: fixed;
        float: right;
        bottom: 0;
        right: 40px;
        padding: 0 0 5px 0;
        box-sizing: border-box;
        font-size: 14px;
        max-width: 900px;
         border-radius: 8px 0 0 0;
         border: 1px solid #c7cbd0;
        /*min-width: calc(100% - 40px);*/
    }

    div#bench-container table tr td{
        padding: 5px;
    }
    div#bench-container table{
        margin-top: 10px;
    }

    button.bench_button {
        background-color: black;
        color: white;
        width: 40px;
        min-height: 40px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        align-content: center;
        position: fixed;
        bottom: 0;
        right: 0;
        font-size: 20px;
        font-weight: bold;
        text-align: center;
        cursor: pointer;
        border-radius: 8px 0 0;
        border:1px solid #fff;
        z-index: 2;
        outline:none;
    }

    p.http_status {
        background-color: #2b542c;
        color: white;
        padding: 10px 5px;
    }

    p.http_status span:first-child{
        display: inline-block;
        text-align: left;
        width: 49%;
    }

    span.bench_app_name {
        text-align: right;
        color: white;
        font-weight: bold;
        display: inline-block;
        width: 49%;
    }

    div#bench-autohide-loadtime{
        display: inline-flex;
        justify-content: center;
        align-items: center;
        border-radius: 8px 0 0;
        border:none;
        box-sizing: border-box;
        max-width: 80px;
        height: 40px;
        opacity: 1;
        padding: 0 3px;
        box-shadow: 1px rgb(57, 47, 59) !important;
        background-color:#2d8546;
        position: fixed;
        text-align: center;
        color:#fff;
        font-weight:bold;
        bottom:0;
        right: 0;
        z-index: 1;
        animation: loadtime 10s 1;
        -moz-animation: loadtime 10s 1;
        -webkit-animation:loadtime 10s 1;
        transition: 0.7s;
        outline:none;

    }

    @-webkit-keyframes loadtime{
      10%{
        right:40px;
      }
      100%{
        right:0;
      }
    }
    @keyframes loadtime{
      10%{
        right:40px;
      }
      100%{
        right:0;
      }
    }
</style>
<div id="bench-autohide-loadtime">
    <?php echo substr($data['Load time'], 0, -6) ?>
</div>
<div id="bench-container" style="display: none">
    <p class="http_status">
        <span><?php echo http_response_code() ?></span>
        <span class="bench_app_name"><?php echo setting('APP_NAME', 'TT') ?></span>
    </p>
    <p>
        <span style="color:green"> <span>root@</span><?php echo strtolower(setting('APP_NAME', 'TT')) ?></span>
        :~<span style="color:red">#</span> benchmark
    </p>
    <table border="1">
      <?php foreach ($data as $name => $value): ?>
            <tr>
                <td><i style="color:rgb(190, 49, 3)"><?php echo $name ?></i></td>
                <td><i style="color:green"><?php echo $value ?></i></td>
            </tr>
      <?php endforeach; ?>
    </table>
</div>
<button onclick="benchToggle(this)" class="bench_button">B</button>
<script>
    setTimeout(function(){
      document.getElementById('bench-autohide-loadtime').style.display = 'none';
    },10000);

    function benchToggle($this)
    {
        let bench = document.getElementById("bench-container");

        if (bench.style.display !== "none")
        {
            $this.style.height  = "40px";
            $this.innerHTML     = "B";
            bench.style.display = "none";
        }
        else
        {
            $this.innerHTML     = "X";
            bench.style.display = "inline-block";
            $this.style.height  = bench.offsetHeight + "px";
        }
    }
</script>
