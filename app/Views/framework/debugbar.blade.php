<style>
    div#app_debug_bar {
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

    #app_debug_bar #b-top {
        display: flex;
        flex-flow: row wrap;
        justify-content: space-between;
        padding: 2px 5px;
        font-size: 1.6em;
        background: #c1d0ce;
        line-height: initial;
    }

    #app_debug_bar #b-top button {
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

    #app_debug_bar #b-top button:focus {
        outline: none !important;
    }

    #app_debug_bar #b-top p {
        padding: 5px 11px;
        margin: -2px 0 0 -5px;
        background: teal;
        color: #fff;
        border-radius: 0;
        border: 1px solid teal;
        cursor: pointer;


    }

    #app_debug_bar #b-top span {
        padding: 5px 11px;
        margin-top: -2px;
        background: #04243e;
        color: #fff;
        border-radius: 0;
        border: 1px solid #04243e;
        cursor: pointer;
        flex: 1 1;
    }

    #app_debug_bar #b-bottom {
        display: grid;
        justify-content: space-around;
        padding: 2px 5px;
        font-size: 1.3em;
        min-height: 150px;
        overflow-y: auto;
        grid-template-columns: auto auto auto;
    }

    #app_debug_bar #b-bottom div {
        flex: 1 1 50px;
        align-items: center;
    }

    #app_debug_bar .b-show {
        display: none !important;
    }

    @media screen and (max-width:720px) {
        div#app_debug_bar {
            font-size: 12px;
        }

        div#app_debug_bar .b-hidden {
            display: none !important;
        }

        div#app_debug_bar .b-show {
            display: block !important;
        }


    }

    @media screen and (max-width:1000px) {

        #app_debug_bar #b-bottom {
            display: flex;
            flex-flow: row wrap;
            justify-content: center;
            font-size: 1.3em;
            height: 100%;
            min-height: 150px;
            overflow-y: scroll;
            padding-bottom: 15%;
        }

        #app_debug_bar #b-bottom div {
            flex: 1 1 auto;
            margin: 5px auto;
            display: grid !important;
        }
    }


</style>
<div id="app_debug_bar">
    <div id="b-top">
        <p>{{http_response_code()}}</p>
        <span>Time:<?php echo $data['time']; unset($data['time']); ?></span>
        <span class="b-hidden">Files:<?php echo $data['load-files']; ?></span>
        <span class="b-hidden">Memory:<?php echo $data['memory-usage']; ?></span>
        <button title="Show Panel" onclick="debugBarToggle(this)">&uarr;</button>
    </div>
    <div style="overflow-y: auto;overflow-x:hidden;height: 210px">
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
        <div style="text-align: center;border-top:2px solid #04243e;color:darkred">Requests</div>
        <div>
            <p style="margin: 10px;font-weight: bold;color: brown;">REQUEST []</p>
            @foreach($request->query->all() as $key => $value)
                <div style="padding: 6px">
                    <span style="padding: 2px;border:1px solid #04243e;color:#690303">
                    {{$key}}
                    </span>
                        <span style="color: tomato;padding: 2px;background: #ffffff;border: 1px solid #04243e;font-weight: bold;">
                        {{$value }}
                    </span>
                </div>
            @endforeach
        </div>
        <div>
            <p style="margin: 10px;font-weight: bold;color: brown;">QUERY []</p>
            @foreach($request->query->all() as $key => $value)
                <div style="padding: 6px">
                    <span style="padding: 2px;border:1px solid #04243e;color:#690303">
                    {{$key}}
                    </span>
                    <span style="color: tomato;padding: 2px;background: #ffffff;border: 1px solid #04243e;font-weight: bold;">
                        {{$value }}
                    </span>
                </div>
            @endforeach
        </div>
        <div>
            <p style="margin: 10px;font-weight: bold;color: brown;">ROUTE PARAMETERS []</p>
            @foreach($request->routeParams as $key => $value)
                <div style="padding: 6px">
                    <span style="padding: 2px;border:1px solid #04243e;color:#690303">
                    {{$key}}
                    </span>
                    <span style="color: tomato;padding: 2px;background: #ffffff;border: 1px solid #04243e;font-weight: bold;">
                        {{$value }}
                    </span>
                </div>
            @endforeach
        </div>
        <div>
            <p style="margin: 10px;font-weight: bold;color: brown;">INPUT []</p>
            @foreach($request->input->all() as $key => $value)
                <div style="padding: 6px">
                    <span style="padding: 2px;border:1px solid #04243e;color:#690303">
                    {{$key}}
                    </span>
                    <span style="color: tomato;padding: 2px;background: #ffffff;border: 1px solid #04243e;font-weight: bold;">
                        {{$value }}
                    </span>
                </div>
            @endforeach
        </div>
        <div style="text-align: center;border-top:2px solid #04243e;color:darkred">Sessions</div>
        <div style="display: grid;padding: 2em;">
            @foreach($_SESSION as $key => $value)
                <div style="padding: 6px">
                    <span style="padding: 2px;border:1px solid #04243e;color:#690303">
                    {{$key}}
                    </span>
                    <span style="color: tomato;padding: 2px;background: #ffffff;border: 1px solid #04243e;font-weight: bold;">
                        {{$value }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>
<script id="app-debug-bar-script">
    function debugBarToggle($this) {
        let debug_bar_element = document.getElementById('app_debug_bar');
        if (debug_bar_element.style.height !== '250px') {
            let debug_bar_height = 36;
            let debug_bar_up = setInterval(function() {
                if (debug_bar_height === 250) {
                    clearInterval(debug_bar_up);
                }
                debug_bar_element.style.height = debug_bar_height + 'px';
                debug_bar_height++;
            }, 7);
            $this.innerHTML = '&darr;';
        } else {
            let debug_bar_height = 250;
            let debug_bar_down = setInterval(function() {
                if (debug_bar_height === 36) {
                    clearInterval(debug_bar_down);
                }
                debug_bar_element.style.height = debug_bar_height + 'px';
                debug_bar_height--;
            }, 7);
            $this.innerHTML = '&uarr;';
        }
    }
</script>
