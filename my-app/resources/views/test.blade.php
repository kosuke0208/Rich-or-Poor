<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script defer src="/js/game.js"></script>
    <link href="/css/game.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="board">
        <div id="field">
            <div class="tefuda card" data-level="1" data-mark="sperd">
                <img src="/img/torannpu-illust3.png" width="90" art="">
            </div>
        </div>
        <div class="card-holder">
            <div class="player1">
                @foreach ($CardId as $cardid){
                <div class="tefuda card" data-level="12" data-mark="sperd">
                    <img src="/img/torannpu-illust{{ $cardid['id'] }}.png" width="90" art="">
                </div>
                }
                @endforeach
            </div>
        </div>
        <div class="player2">
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
        </div>

        <div class="player3">
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
        </div>

        <div class="player4">
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">
            </div>
            <div class="tefuda">
                <img src="/img/torannpu-illust54.png" width="75" art="">

            </div>
        </div>
        <div class="button">
            <button class="btn" id="put">カードを出す</button>
            <button class="btn2" id="pass">パス</button>
        </div>
        <div class="box1">
            <p>名前：{{ $positionA }}<br>枚数</p>
        </div>
        <div class="box2">
            <p>名前:{{ $positionB }}<br>枚数</p>
        </div>
        <div class="box3">
            <p>名前:{{ $positionC }}<br>枚数</p>
        </div>
        <div class="box4">
            <p>名前:{{ $positionD }}<br>枚数</p>
        </div>
    </div>
    </div>
</body>

</html>