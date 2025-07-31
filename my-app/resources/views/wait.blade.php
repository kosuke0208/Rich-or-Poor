<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
    <link href="/css/standing.css" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hachi+Maru+Pop&family=Kaisei+Decol&display=swap" rel="stylesheet">
    
    <div class="board">
        <div class="bird">
            <img src="/img/cuteillust/cuteillust/bird.png" width="210" art="">
        </div>
        <div class="dolphin">
            <img src="/img/cuteillust/cuteillust/dolphin.png" width="210" art="">
        </div>
        <div class="text">
        <h1 class="hachi-maru-pop-regular">waiting...</h1>
        </div>
    <div class="spinner">
        <div class="bounce1"></div>
        <div class="bounce2"></div>
        <div class="bounce3"></div>
      </div>
    </div>


    <h2>大気中</h2>


        <p>プレイヤー名: {{ $player_name['name'] }}</p>
        <p>あなたの順番: {{ $player_name['id'] }}</p>
    <script>
    const player = @json($player_name);
    </script>    
    <script src="/js/wait.js"></script>
</body>
</html>
</body>
</html>