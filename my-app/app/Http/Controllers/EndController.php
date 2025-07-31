<?php

namespace App\Http\Controllers;
use App\Models\Rank;
use App\Models\CardInfo;
use App\Models\Playername;
use Illuminate\Http\Request;

class EndController extends Controller
{
    public function end(Request $request){
        $rank = Rank::orderBy('id', 'asc')
        ->select('id','playername_id')
        ->get();

        $names = [];
        $excludedIds = []; // 除外するIDを格納
        foreach($rank as $index => $r){
            $names[$index + 1] = Playername::where('id', $r->playername_id)
            ->value('name');
            $excludedIds[] = $r->playername_id; // 除外リストに追加
        }
        // $name1, $name2, $name3 のように変数を個別に作りたい場合
        // デフォルト値を設定（もし $names に値が入らなかった場合）
        $name1 = $names[1];
        $name2 = $names[2];
        $name3 = $names[3];
        $name4 = Playername::whereNotIn('id',$excludedIds)
        ->pluck('name')
        ->first();

        return view('end', compact('name1','name2','name3','name4'));
    }
    public function finish(Request $request){
        $playerID = $request->query('playerid'); //自分のidを"url"から取得
        $playerfinish = Playername::where('id',$playerID)
        ->first();
        $playerfinish->finished= 1;
        $playerfinish->save();
        $finish_count = Playername::where('finished',1)
        ->count();
        if($finish_count == 4){
            rank::query()->delete();
            Playername::where('finished',1)->delete();
            $nullids = CardInfo::all();
            foreach ($nullids as $nullid) {
            $nullid->playername_id = null;
            $nullid->save();
            }
            foreach ($nullids as $nullid) {
               $nullid->field = 0;
                $nullid->save();
            }
        }
        return view('name');
    }
}
