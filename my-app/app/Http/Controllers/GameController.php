<?php

namespace App\Http\Controllers;

use App\Models\CardInfo;
use App\Models\playername;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function game(Request $request){
        //orderに順番を入れる
        $orders = playername::take(4)->get();
        foreach($orders as $index => $order){
            $order->order = ($index % 4) + 1;
            $order->save();
        }

        //自分の手札
       $request->id;

        $hand = CardInfo::where($request->id,'==',)
        ->orderBy('lv','asc')
        ->get();

        //全員の手札の枚数
        $hand_count = CardInfo::;

        //全員のプレイヤーネーム
        //自分が下に、2番目が左に来るように
        $myId = 5; // 自分のIDを設定（動的に設定する場合はログインIDなどを使用）

        $name = playername::orderByRaw("CASE WHEN id >= ? THEN 0 ELSE 1 END, id ASC", [$myId])
        ->get();
    }
}
