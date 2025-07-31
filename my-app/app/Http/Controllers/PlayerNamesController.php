<?php
 
namespace App\Http\Controllers;
 
use App\Models\CardInfo;
use App\Models\Playername;
use Illuminate\Http\Request;

 
 
class PlayerNamesController extends Controller
{
    // urlからidを取得
    public function show(Request $request)
    {
        $playerID = $request->query('playerid'); //自分のidを"url"から取得
        $playerName = null;
        //自分の手札のカードのIDを取得
        $CardId = CardInfo::where('playername_id',$playerID)
        ->orderBy('lv','asc')
        ->select('id','lv')
        ->get();
 
        //orderに順番を入れる
        $orders = Playername::take(4)->get();
        foreach($orders as $index => $order){
        $order->order = ($index % 4) + 1;
        $order->save();
        };
 
        //orderが1のカラムのturnに1を入れる
        if( Playername::where('Turn',1)->count() == 0){
            $turns1 = Playername::take(4)->orderBy('order')->first();//orderが1のカラムを呼ぶ
            $turns1->turn = 1;
            $turns1->save();

            //orderが2,3,4のカラムのturnに0を入れる
            $turns2 = Playername::take(4)
            ->where('order','>',$turns1->order)
            ->get();
            
            //orderが2,3,4のカラムを呼ぶ

            foreach($turns2 as $index => $turn){
                $turn->turn = 0;
                $turn->save();
            };            
        }
 


            
            $playerall = Playername::orderBy('order', 'asc')->get(); //dbの中身を全て取得（１~4順）
            $player = Playername::find($playerID); //自分の列を取得
            $playerorder = $player->order; //自分の順番を取得
            $playerName = $player->name; //自分の名前を取得


        // playernames テーブルから全ての名前を取得
        $playernames = Playername::all();
        $allids = $playerall->pluck('id'); // 全プレイヤーのidをリストとして取得
        $names = $playerall->pluck('name'); // 全プレイヤーの名前をリストとして取得
 
        $firstid = $allids[0];
        $secondid = $allids[1];
        $thirdid = $allids[2];
        $fourthid = $allids[3];
 
 
        $firstname = $names[0];
        $secondname = $names[1];
        $thirdname = $names[2];
        $fourthname = $names[3];
 
        if ($playerorder == 1) {
            $positionA = $playerName;
            $positionB = $secondname;
            $positionC = $thirdname;
            $positionD = $fourthname;
            $player1 = $playerID;
            $player2 = $secondid;
            $player3 = $thirdid;
            $player4 = $fourthid;
        } elseif ($playerorder == 2) {
            $positionA = $playerName;
            $positionB = $thirdname;
            $positionC = $fourthname;
            $positionD = $firstname;
            $player1 = $playerID;
            $player2 = $thirdid;
            $player3 = $fourthid;
            $player4 = $firstid;
 
        } elseif ($playerorder == 3) {
            $positionA = $playerName;
            $positionB = $fourthname;
            $positionC = $firstname;
            $positionD = $secondname;
            $player1 = $playerID;
            $player2 = $fourthid;
            $player3 = $firstid;
            $player4 = $secondid;
 
        } elseif ($playerorder == 4) {
            $positionA = $playerName;
            $positionB = $firstname;
            $positionC = $secondname;
            $positionD = $thirdname;
            $player1 = $playerID;
            $player2 = $firstid;
            $player3 = $secondid;
            $player4 = $thirdid;
 
        }
 
 
 
 
        return view('game', compact('positionA', 'positionB', 'positionC', 'positionD', 'playerName','CardId','playerID','player1','player2','player3','player4'));
    }
}
 
