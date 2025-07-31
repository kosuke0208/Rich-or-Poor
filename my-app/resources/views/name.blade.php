<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>大富豪 - ゲームスタート画面</title>
  <link rel="stylesheet" href="/css/title.css">

  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kaisei+Decol&display=swap" rel="stylesheet">
</head>
<body>
    <div class="background">
  <div class="container">
    <div class="title">
      <h1 class="kaisei-decol-regular">大富豪</h1>
      <form method="post" action="{{ url('/add') }}"> 
        @csrf
        {{ csrf_field() }}
        <label for="playerName"></label>
        <input type="text" id="player_name" name="name">
        <br><br>
    </div>
    <h1 class="text">名前を入力してください</h1>
    <h1 class="text2">(10文字以内)</h1>
    <div class="start-button">
     <input type="submit" value="ゲームスタート">
    </div>
    </form>
  </div>
</body>
</html>
