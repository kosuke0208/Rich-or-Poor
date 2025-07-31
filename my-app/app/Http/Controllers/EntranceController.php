<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caed;
use App\Models\CardInfo;
use App\Models\Playername;
use Faker\Guesser\Name;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Support\Facades\DB;

class EntranceController extends Controller
{
    //
    public function test(Request $request)
    {
        $action = $request->input('action');//actionにgameのボタンのIDをいれ動くようにする

        if($action === 'put'){//出すのボタンが押されたときの動作
            $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
            ->select('order')//その人のorderをとる
            ->get();
            $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
            ->get();
            $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
            $turn2->save();
            $turn1->turn = 0;//turn1の人のturnを0から1に
            $turn1->save();
        }elseif($action ==='pass'){//パスのボタンが押されたときの動作
            $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
            ->select('order')//その人のorderをとる
            ->get();
            $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
            ->get();
            $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
            $turn2->save();
            $turn1->turn = 0;//turn1の人のturnを0から1に
            $turn1->save();
        }
    }


    //
    public function param()
    {
        $num = request('parameter') + 5;
        return view('param', compact('num'));
    }


    public function create()
    {
        return view('name');
    }

    public function add(Request $request)
    {
        // 新しいプレイヤーを作成
        $playername = new Playername();
        $playername->name = $request->name;
        $playername->save();

        //idとnameを渡す
        $player_name = array(
        'id' => $playername->id,
        'name' => $playername->name,
    );

        // ランダムにカードを13枚選んでプレイヤーに割り当てる
        $playername->id;
        $cardinfos = CardInfo::whereNull('playername_id')
        ->inRandomOrder()
        ->take(13)
        ->get();
        foreach($cardinfos as $cardinfo){
            $cardinfo->playername_id = $playername->id;
            $cardinfo->save();
        }

         // 待機画面にviewを渡す
        return view('/wait',compact('player_name'));
    }

    public function title()
    {
        return view('title');
    }
    public function name(Request $request)
    {
        return redirect('/name');
    }



    
    public function delete(Request $request)
    {
        Playername::query()->delete();
        return redirect('');
    }

    public function nullid(Request $request)
    {
        $nullids = CardInfo::all();
        foreach ($nullids as $nullid) {
            $nullid->playername_id = null;
            $nullid->field = 0;
            $nullid->save();
        }
        return redirect('');
    }

    public function Turnnull(Request $request)
    {
        $Turnnulls = Playername::all();
        foreach ($Turnnulls as $Turnnull) {
            $Turnnull->Turn = null;
            $Turnnull->save();
        }
        return redirect('');
    }
}
