<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="/js/end.js"></script>
    <link href="/css/end.css" rel="stylesheet" type="text/css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Yusei+Magic&display=swap" rel="stylesheet">
</head>
<body>
  <div class="background">
    <div class="ribbon9">
      <h3 class="yusei-magic-regular">  &nbsp;&nbsp;&nbsp;&nbsp;ゲーム結果!&nbsp;&nbsp;&nbsp;&nbsp;  </h3>
    </div>

    <div class="ranking">
      <div class="text yusei-magic-regular">
        <h1 >大富豪</1>
        <h2>{{$name1}}</h2>
      </div>
      <div class="text yusei-magic-regular">
        <h1 class="">富豪</h1>
        <h2>{{$name2}}</h2>
      </div>
      <div class="text yusei-magic-regular">
        <h1 class="">貧民</h1>
        <h2>{{$name3}}</h2>
      </div>
      <div class="text yusei-magic-regular">
        <h1 class="">大貧民</h1>
        <h2>{{$name4}}</h2>
      </div>
    </div>
    <a href="{{ route('finish',['playerid' => request()->query('playerid')]) }}" class="btn-square">ゲームを終了する</a>
    
      <div class="confetti"><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span><span></span>
      </div>
          
</body>
</html>