<style>
    div#app-benchmark-panel {
        height: 36px;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #efefef;
        box-shadow: -2px -2px 11px 2px #222;
        padding: 0;
        margin: 0;
        box-sizing: border-box;
        font-size: 15px;
        font-weight: 500;
        font-family: monospace;
        z-index: 9999999999999999;
        line-height: initial;

    }

    #app-benchmark-panel #b-top {
        display: flex;
        flex-flow: row wrap;
        justify-content: space-between;
        padding: 2px 5px;
        font-size: 1.6em;
        background: #c1d0ce;
        line-height: initial;
    }

    #app-benchmark-panel #b-top button {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: -2px;
        margin-right: -5px;
        background: #000000;
        color: #fff;
        border-radius: 0;
        border: 1px solid #000000;
        cursor: pointer;
        width: 50px;
        font-size: 1.4em;
        outline: none !important;
    }

    #app-benchmark-panel #b-top button:focus {
        outline: none !important;
    }

    #app-benchmark-panel #b-top p {
        padding: 5px 11px;
        margin: 0;
        margin-top: -2px;
        margin-left: -5px;
        background: teal;
        color: #fff;
        border-radius: 0;
        border: 1px solid teal;
        cursor: pointer;


    }

    #app-benchmark-panel #b-top span {
        padding: 5px 11px;
        margin-top: -2px;
        background: #04243e;
        color: #fff;
        border-radius: 0;
        border: 1px solid #04243e;
        cursor: pointer;

        flex: 1 1;

    }

    #app-benchmark-panel #b-bottom {
        display: flex;
        flex-flow: column wrap;
        justify-content: space-around;
        padding: 2px 5px;
        font-size: 1.3em;
        height: 85%;
        min-height: 150px;
        overflow-y: auto;
    }

    #app-benchmark-panel #b-bottom div {
        flex: 1 1 50px;
        align-items: center;
    }

    #app-benchmark-panel .b-show {
        display: none !important;
    }

    @media screen and (max-width:720px) {
        div#app-benchmark-panel {
            font-size: 12px;
        }

        div#app-benchmark-panel .b-hidden {
            display: none !important;
        }

        div#app-benchmark-panel .b-show {
            display: block !important;
        }


    }

    @media screen and (max-width:1000px) {

        #app-benchmark-panel #b-bottom {
            display: flex;
            flex-flow: row wrap;
            justify-content: center;
            padding: 2px 5px;
            font-size: 1.3em;
            height: 100%;
            min-height: 150px;
            overflow-y: scroll;
            padding-bottom: 15%;
        }

        #app-benchmark-panel #b-bottom div {
            flex: 1 1 auto;
            margin: 5px auto;
            display: grid !important;
        }
    }
</style>
<div id="app-benchmark-panel">
    <div id="b-top">
        <p>{{http_response_code()}}</p>
        <span>Time:<?php echo $data['time']; unset($data['time']); ?></span>
        <span class="b-hidden">Files:<?php echo $data['load-files']; ?></span>
        <span class="b-hidden">Memory:<?php echo $data['memory-usage']; ?></span>
        <button title="Show Panel" onclick="btoggle(this)">&uarr;</button>
    </div>
    <div id="b-bottom">
        <div class="b-show">
            <span style="padding: 2px;background:#04243e;border:1px solid #04243e;color:#fff">
                {{ 'FILES' }}
            </span>
            <span style="color: tomato;padding: 2px;background: #ffffff;border: 1px solid #04243e;font-weight: bold;">
                {{ $data['load-files'] }}
            </span>
        </div>
        <div class="b-show">
            <span style="padding: 2px;background:#04243e;border:1px solid #04243e;color:#fff">
                {{ 'MEMORY' }}
            </span>
            <span style="color: tomato;padding: 2px;background: #ffffff;border: 1px solid #04243e;font-weight: bold;">
                {{ $data['memory-usage'] }}
            </span>
        </div>
        <?php unset($data['load-files']); unset($data['memory-usage']); ?>
        @foreach ($data as $name => $value)
            <div>
                <span style="padding: 2px;background:#04243e;border:1px solid #04243e;color:#fff">
                    {{strtoupper($name) }}
                </span>
                <span style="color: tomato;padding: 2px;background: #ffffff;border: 1px solid #04243e;font-weight: bold;">
                    {{$value }}
                </span>
            </div>
        @endforeach
    </div>
</div>
<script id="app-benchmark-panel-script">
    function btoggle($this) {
        var be = document.getElementById('app-benchmark-panel');
        if (be.style.height !== '200px') {
            var bheight = 36;
            var bup = setInterval(function() {
                if (bheight === 200) {
                    clearInterval(bup);
                }
                be.style.height = bheight + 'px';
                bheight++;
            }, 7);
            $this.innerHTML = '&darr;'
        } else {
            var bheight = 200;
            var bdown = setInterval(function() {
                if (bheight === 36) {
                    clearInterval(bdown);
                }
                be.style.height = bheight + 'px';
                bheight--;
            }, 7);
            $this.innerHTML = '&uarr;';
        }
    }
</script>