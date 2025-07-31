<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}"><!-- CSRFトークンを追加 -->
    <title>Document</title>
    <script defer src="/js/game.js"></script>
    <link href="/css/game.css" rel="stylesheet" type="text/css">
</head>

<body>

    <div class="board">
        <div id="field">
        
        </div>

        <div class="card-holder" data-player="{{ $player1}}">
            <div class="player1 player_{{ $player1 }}">
                @foreach ($CardId as $cardid)
                <div class="tefuda card disabled" data-card-id="{{ $cardid['id']}}" data-level="{{ $cardid['lv'] }}">
                    <img src="/img/torannpu-illust{{ $cardid['id'] }}.png" width="90" art="">
                </div>
                @endforeach
            </div>
        </div>
        <div class="player2 tefuda player_{{ $player2 }}">
            <!-- <img src="/img/torannpu-illust54.png" width="55" art="">    55 -->
            <img src="/img/torannpu-illust54.png" width="10" art="">
            <img src="/img/torannpu-illust54.png" width="10" art="">
            <img src="/img/torannpu-illust54.png" width="10" art="">
            <img src="/img/torannpu-illust54.png" width="10" art="">
            <img src="/img/torannpu-illust54.png" width="10" art="">
            <img src="/img/torannpu-illust54.png" width="10" art="">
            <img src="/img/torannpu-illust54.png" width="10" art="">
            <img src="/img/torannpu-illust54.png" width="10" art="">
            <img src="/img/torannpu-illust54.png" width="10" art="">
            <img src="/img/torannpu-illust54.png" width="10" art="">
            <img src="/img/torannpu-illust54.png" width="10" art="">
            <img src="/img/torannpu-illust54.png" width="10" art="">
        </div>

        <div class="player3 tefuda player_{{ $player3 }}">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
        </div>

        <div class="player4 tefuda player_{{ $player4 }}">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
            <img src="/img/torannpu-illust54.png" width="75" art="">
        </div>
        <div class="button">
            <button class="btn" id="put">カードを出す</button>
            <button class="btn2" id="pass">パス</button>
            <button class="btn3" id="give">渡す</button>

        </div>
        <input type="hidden" id="playerid" value="{{ $playerID }}">
        <div class="namebox box1 playerbox_{{$player1}}">
            <p>名前：{{ $positionA }}<br><div id="card-count{{$player1}}"></div></p>
        </div>
        <div class="namebox box2 playerbox_{{$player2}}">
            <p>名前：{{ $positionB }}<br><div id="card-count{{$player2}}"></div></p>
        </div>
        <div class="namebox box3 playerbox_{{$player3}}">
            <p>名前：{{ $positionC }}<br><div id="card-count{{$player3}}"></div></p>
        </div>
        <div class="namebox box4 playerbox_{{$player4}}">
            <p>名前：{{ $positionD }}<br><div id="card-count{{$player4}}"></div></p>
        </div>

    </div>
    </div>
</body>

</html>