<?php

namespace App\Http\Controllers;

use App\Models\CardInfo;
use App\Models\Playername;
use App\Models\Rank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameApiController extends Controller
{

    //全部取得する処理
    public function all(Request $request){
        //誰のターンか(完成)
        $playerTurn = Playername::where('Turn',1)//playernamesのTurnが1の人を呼ぶ
        ->select('id')//ほしい情報がid
        ->get();

        //手札の枚数(全員)
        $hand_Counts = CardInfo::select('playername_id', DB::raw('count(if(field = 0,1,null)) as hand_Counts'))//group byを使えるようにするための準備
        ->groupBy('playername_id')//group byでplayeridごとにする
        ->get();//playerごとの枚数が知りたい
        
        //最新の自分の手札(完成、ただし順番はID順になっているからそこはあとで)
        $playerID = $request->query('playerid'); //自分のidを"url"から取得
        $hands = CardInfo::where('field',0)//fieldが0のカードが手札にある
        ->where('playername_id',$playerID)//自分のidを入れる
        ->select('id','lv')//ほしい情報がidとlv
        ->orderBy('lv','asc')//lv順で送る
        ->get();
        
        //場のカード
        $field1 = CardInfo::where('field', 1)
        ->orderBy('lv', 'desc')
        ->first();
        //場の最高レベルのカードが複数あるか見る
        if($field1){
            $max_field_count = CardInfo::where('field',1)
            ->where('lv',$field1->lv)
            ->count();

            $field2 = CardInfo::where('field', 1)
            ->where('lv', $field1->lv)
            ->select('id', 'lv')
            ->get();
            
            $not_field = CardInfo::where('lv', '<=', $field1->lv)
            ->select('id')
            ->get();
        }
        //場が流れたかどうか
        //fieldが1がないandどこかに-1がある
        $shed_1 = CardInfo::where('field',1)->get();
        $shed_2 = CardInfo::where('field',-1)->get();
        $shed = false;
        if (!$shed_1->count() && $shed_2->count()) {
            $shed = true;
        }        
        //ゲームが終了したかどうか(rankに三つデータが登録されたかどうか)
        $finish = false;
        $finish_1 = rank::get();
        if($finish_1->count() === 3){
            $finish = true;
        }

        //場にカードがなく自分の手札もないとき
        if(!$field1 && !$hands) {
            $all = array(
                'finish' =>$finish,//終わったかどうか
                'shed' =>$shed,//場が流れたかどうか
                'playerTurn' => $playerTurn,//いま、誰のターンか
                'hand_Counts' => $hand_Counts,//いま、全員の手札は何枚か
                'hands' => [],//いま、自分の手札は何か
                'max_card_count' => [],//いま、場の最新のカードは何枚あるか
                'field_cards' => [],//いま、場のlvはいくつか
                'not_field_cards' => []//いま、場に置けないカードは何か
            );
        }elseif(!$field1) {//field1(今,場にカードがないとき)にからの配列を送る
            $all = array(
                'finish' =>$finish,//終わったかどうか
                'shed' =>$shed,//場が流れたかどうか
                'playerTurn' => $playerTurn,//いま、誰のターンか
                'hand_Counts' => $hand_Counts,//いま、全員の手札は何枚か
                'hands' => $hands,//いま、自分の手札は何か
                'max_card_count' => [],//いま、場の最新のカードは何枚あるか
                'field_cards' => [],//いま、場のlvはいくつか
                'not_field_cards' => []//いま、場に置けないカードは何か
            );
        }elseif(!$hands) {//自分の手札がないとき
            $all = array(
                'finish' =>$finish,//終わったかどうか
                'shed' =>$shed,//場が流れたかどうか
                'playerTurn' => $playerTurn,//いま、誰のターンか
                'hand_Counts' => $hand_Counts,//いま、全員の手札は何枚か
                'hands' => [],//いま、自分の手札は何か
                'max_card_count' => $max_field_count,//いま、場の最新のカードは何枚あるか
                'field_cards' => $field2,//いま、場のlvはいくつか
                'not_field_cards' => $not_field//いま、場に置けないカードは何か
            );
        }else{
            $all = array(
                'finish' =>$finish,//終わったかどうか
                'shed' =>$shed,//場が流れたかどうか
                'playerTurn' => $playerTurn,//いま、誰のターンか
                'hand_Counts' => $hand_Counts,//いま、全員の手札は何枚か
                'hands' => $hands,//いま、自分の手札は何か
                'max_card_count' => $max_field_count,//いま、場の最新のカードは何枚あるか
                'field_cards' => $field2,//いま、場のlvはいくつか
                'not_field_cards' => $not_field//いま、場に置けないカードは何か
            );
        }    
        return $all;
    }

    //handsのみ取得する処理
    public function hands(Request $request){
        //最新の自分の手札(完成、ただし順番はID順になっているからそこはあとで)
        $playerID = $request->query('playerid'); //自分のidを"url"から取得
        $hands = CardInfo::where('field',0)//fieldが0のカードが手札にある
        ->where('playername_id',$playerID)//自分のidを入れる
        ->select('id','lv')//ほしい情報がidとlv
        ->orderBy('lv','asc')//lv順で送る
        ->get();

        return $hands;
    }
    //出す、パスの処理

    //パスか出すを押したときにDBにturnの情報を登録する
    //(turnの方は完成,ただしgetで検証しているのでpostでやると動かないので後日聞く)
    //動かすときはactionにボタンのIDを入れて動かす
    public function turn(Request $request){
        $action = $request->input('action');//actionにgameのボタンのIDをいれ動くようにする　
        $playerID = $request->query('playerid'); //自分のidを"url"から取得
        
        // 自分
        $my = Playername::find($playerID);

        $player_id = Playername::pluck('id');//player_idをすべて持ってくる
        $player_have = CardInfo::where('field',0)//手札のカードすべて持ってくる
        ->pluck('playername_id');
        //残っているプレイヤーから手札のないプレイヤーを抽出
        $player0 = $player_id->diff($player_have);
        $turn = array();
        
        if($action === 'put'){//出すのボタンが押されたときの動作


            //出したカードをDBのfieldに入れる
            //urlパラメータで配列を渡すときは?put_card_id[]=数字
            //urlパラメータで数字を渡すときは?put_card_id=数字
            //このコードは配列をDBに入れる
            $put_cards = $request->input('put_card_id');//$put_cardに出したカードのidが何枚分か入っている
            

            //出たカードのfield欄を1にする
            CardInfo::whereIn('id',$put_cards)->update(['field'=>1]);

            $put_card_count = count($put_cards);//出されたカードが何枚か

            $player_id = Playername::pluck('id');//player_idをすべて持ってくる
            $player_have = CardInfo::where('field',0)//手札のカードすべて持ってくる
            ->pluck('playername_id');
            //残っているプレイヤーから手札のないプレイヤーを抽出
            $player0 = $player_id->diff($player_have);

            //8切りするカード
            $eight_cards = [8,21,34,47];
            // 5スキップするカード
            $five_cards = [5, 18, 31, 44];
            // 10捨てするカード
            $ten_cards = [10, 23, 36, 49];
            // 7渡しするカード
            $seven_cards = [7, 20, 33, 46];
            // 2カード
            $two_cards = [2, 15, 28, 41];

            //判定
            if(count(array_intersect($put_cards, $two_cards)) > 0) {

                // 場のカードを流す(fieldに-1をいれる)
                CardInfo::where('field',1)->update(['field'=>-1]);

                // 上がっているか判定
                if( CardInfo::where('field',0)->where('playername_id',$playerID)->count() < 1 ){
                    // 上がってたら

                    // 次の人のターンにする
                    $nextOrderQuery = Playername::leftJoin('ranks', function($join){
                        $join->on('playernames.id','=','ranks.playername_id');
                    })->whereNull('ranks.playername_id')->orderBy('order','asc');

                    $nextPlayer = $nextOrderQuery->where('order','>',$my->order)->first();
                    if(!$nextPlayer){
                        $nextPlayer = $nextOrderQuery->first();
                    }
                    // 次の人のターンを1に
                    $nextPlayer->turn = 1;
                    $nextPlayer->save();
                    
                    // 自分のターンを0に
                    $my->turn = 0;

                    // 順位を入れる
                    Rank::insert(['playername_id' => $playerID]);
                    
                    
                }else{
                    // 上がってなかったら何もしない
                }

            }else if(count(array_intersect($put_cards, $eight_cards)) > 0) {
                //8切りで場が流れるようのコード
                $field3 = CardInfo::where('field',1)//fieldが1のカードが場にある
                ->update(['field'=>-1]);//流れたカードはfieldを-1にする

                //スキップを4回して自分のターンに返ってくるように
                for($i = 0;$i < 4; $i++){
        
                $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                ->select('order')//その人のorderをとる
                ->first();
                $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                ->first();
        
                $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
               ->first();
        
                $turn0->turn = 0;//turn1の人のturnを１から０に
                $turn0->save();
                $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                $turn2->save();
                }

                //8切りで上がったとき用
                //手札が0の人にturnが回って来た時に回すように
                if($turn2 && $player0->contains($turn2->id)){
                    $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                    ->select('order')//その人のorderをとる
                    ->first();
                    $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                    ->first();        
                    $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                    ->first();
                    $turn0->skip = 1;//パスの値に1を入れる
                    $turn0->turn = 0;//turn1の人のturnを１から０に
                    $turn0->save();
                    $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                    $turn2->save();
                    //手札が0の人に2回連続でturnが回って来た時に回すように
                    if($turn2 && $player0->contains($turn2->id)){
                        $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                        ->select('order')//その人のorderをとる
                        ->first();
                        $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                        ->first();                    
                        $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                        ->first();
                        $turn0->skip = 1;//パスの値に1を入れる                
                        $turn0->turn = 0;//turn1の人のturnを１から０に
                        $turn0->save();
                        $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                        $turn2->save();
                    }
                }
                //順位を入れるため(上がる人はカードを出して上がるためここに入れる)
                $hand_coats = CardInfo::where('field',0)//fieldが0のカードが手札にある
                ->select('playername_id',
                DB::raw('count(playername_id) as hand_coats'))//group byを使えるようにするための準備
                ->groupBy('playername_id')//group byでplayeridごとにする
                ->get();//playerごとの枚数が知りたい
                $Check_card = $hand_coats->filter(function($check0){
                    return $check0->hand_coats > 0;
                })
                ->count();//手札が1枚以上ある人をカウント

                //順位はIDが低い順
                //1位の人の記録
                $player1 = null;
                $player2 = null;
                $player3 = null;

                $player_have = CardInfo::where('field',0)//手札のカードすべて持ってくる
                ->pluck('playername_id');
                //残っているプレイヤーから手札のないプレイヤーを抽出
                $player0 = $player_id->diff($player_have);
                if($Check_card === 3){//残っている人が3人のとき
                    $player1 = $player0->first();
                    $player11 = Playername::where('id',$player1)
                    ->select('id')//ほしい情報がid
                    ->first();
                    if($player11 && !rank::where('playername_id', $player11->id)->exists()) {
                        $rank1 = new rank();
                        $rank1->playername_id = $player11->id;
                        $rank1->save();
                    }
                }elseif($Check_card === 2){//残っている人が2人のとき
                    //2位の人の記録
                    $diff_player1 = rank::orderBy('id','asc')->first();
                    $player2 = $player0->diff([$diff_player1->playername_id])->first();
                    $player22 = Playername::where('id',$player2)
                    ->select('id')//ほしい情報がid
                    ->first();
                    if($player22 && !rank::where('playername_id', $player22->id)->exists()) {
                        $rank2 = new rank();
                        $rank2->playername_id = $player22->id;
                        $rank2->save();
                    }
                }elseif($Check_card === 1){//残っている人が1人のとき
                    //3位の人の記録
                    $diff_player1 = rank::orderBy('id','asc')->first();
                    $diff_player2 = rank::orderBy('id','desc')->first();                
                    $player3 = $player0->diff([$diff_player1->playername_id, $diff_player2->playername_id])->first();
                    $player33 = Playername::where('id', $player3)
                    ->select('id')
                    ->first();
                    
                    if ($player33 && !rank::where('playername_id', $player33->id)->exists()) {
                        $rank3 = new rank();
                        $rank3->playername_id = $player33->id;
                        $rank3->save();
                    }
                }
                $turn = [];
                return $turn;
            }elseif(count(array_intersect($put_cards, $five_cards)) > 0){//5スキップするコード

                //2回して1回目にターンが回った人を飛ばす
                for($i = 0;$i < 0 + $put_card_count; $i++){
                    if($i == 0){
                        $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                        ->select('order')//その人のorderをとる
                        ->first();
                        $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                        ->first();
            
                        
                        $skip1 = Playername::orderBy('id','asc')->first();
                        $skip2 = Playername::where('id','>=',$skip1->id)
                        ->update(['skip'=> 0]);//出すボタンが一回押されたらパスの連続判定が止まるように
            
                        $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                        ->first();
                        $turn0->turn = 0;//turn1の人のturnを１から０に
                        $turn0->save();
                        $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                        $turn2->save();
                        //手札が0の人にturnが回って来た時に回すように
                        if($turn2 && $player0->contains($turn2->id)){

                            $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                            ->select('order')//その人のorderをとる
                            ->first();
                            $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                            ->first();        
                            $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                            ->first();
                            $turn0->skip = 1;//パスの値に1を入れる
                            $turn0->turn = 0;//turn1の人のturnを１から０に
                            $turn0->save();
                            $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                            $turn2->save();
                            //手札が0の人に2回連続でturnが回って来た時に回すように
                            if($turn2 && $player0->contains($turn2->id)){
                                $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                                ->select('order')//その人のorderをとる
                                ->first();
                                $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                                ->first();                    
                                $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                                ->first();
                                $turn0->skip = 1;//パスの値に1を入れる                
                                $turn0->turn = 0;//turn1の人のturnを１から０に
                                $turn0->save();
                                $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                                $turn2->save();
                            }
                        }
                    }
                    $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                    ->select('order')//その人のorderをとる
                    ->first();
                    $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                    ->first();
        
                    
                    $skip1 = Playername::orderBy('id','asc')->first();
        
                    $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                    ->first();
                    $turn0->skip = 1;//パスの値に1を入れる
                    $turn0->save();
                    $turn0->turn = 0;//turn1の人のturnを１から０に
                    $turn0->save();
                    $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                    $turn2->save();
                    //スキップが一周して自分の番を超えてしまったとき用
                    $shed1 = Playername::where('skip',1)//パスを3人が連続でしたかの判定
                    ->count();
                    if($shed1 = 4){
                        $shed4 = Playername::where('skip',1)//すべてのスキップを0にして判定をリセットする
                        ->update(['skip'=>0]);
                    }
                    //手札が0の人にturnが回って来た時に回すように
                    if($turn2 && $player0->contains($turn2->id)){
                    
                        $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                        ->select('order')//その人のorderをとる
                        ->first();
                        $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                        ->first();        
                        $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                        ->first();
                        $turn0->skip = 1;//パスの値に1を入れる
                        $turn0->turn = 0;//turn1の人のturnを１から０に
                        $turn0->save();
                        $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                        $turn2->save();

                        //スキップが一周して自分の番を超えてしまったとき用
                        $shed1 = Playername::where('skip',1)//パスを3人が連続でしたかの判定
                        ->count();
                        if($shed1 = 4){
                            $shed4 = Playername::where('skip',1)//すべてのスキップを0にして判定をリセットする
                            ->update(['skip'=>0]);
                        }
                        //手札が0の人に2回連続でturnが回って来た時に回すように
                        if($turn2 && $player0->contains($turn2->id)){
                            $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                            ->select('order')//その人のorderをとる
                            ->first();
                            $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                            ->first();                    
                            $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                            ->first();
                            $turn0->skip = 1;//パスの値に1を入れる                
                            $turn0->turn = 0;//turn1の人のturnを１から０に
                            $turn0->save();
                            $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                            $turn2->save();
                            //スキップが一周して自分の番を超えてしまったとき用
                            $shed1 = Playername::where('skip',1)//パスを3人が連続でしたかの判定
                            ->count();
                            if($shed1 = 4){
                                $shed4 = Playername::where('skip',1)//すべてのスキップを0にして判定をリセットする
                                ->update(['skip'=>0]);
                            }
                        }
                    }    
                    //場を流す(完成)
                    $shed1 = Playername::where('skip',3)//パスを3人が連続でしたかの判定
                    ->count();
                    if($shed1 == 3){//パスが3回連続で押されたときとそうじゃないときの動き
                        $field3 = CardInfo::where('field',1)//fieldが1のカードが場にある
                        ->update(['field'=>-1]);//流れたカードはfieldを-1にする
                    }
                }

                //順位を入れるため(上がる人はカードを出して上がるためここに入れる)
                $hand_coats = CardInfo::where('field',0)//fieldが0のカードが手札にある
                ->select('playername_id',
                DB::raw('count(playername_id) as hand_coats'))//group byを使えるようにするための準備
                ->groupBy('playername_id')//group byでplayeridごとにする
                ->get();//playerごとの枚数が知りたい
                $Check_card = $hand_coats->filter(function($check0){
                    return $check0->hand_coats > 0;
                })
                ->count();//手札が1枚以上ある人をカウント

                //順位はIDが低い順
                //1位の人の記録
                $player1 = null;
                $player2 = null;
                $player3 = null;

                $player_have = CardInfo::where('field',0)//手札のカードすべて持ってくる
                ->pluck('playername_id');
                //残っているプレイヤーから手札のないプレイヤーを抽出
                $player0 = $player_id->diff($player_have);
                if($Check_card === 3){//残っている人が3人のとき
                    $player1 = $player0->first();
                    $player11 = Playername::where('id',$player1)
                    ->select('id')//ほしい情報がid
                    ->first();
                    if($player11 && !rank::where('playername_id', $player11->id)->exists()) {
                        $rank1 = new rank();
                        $rank1->playername_id = $player11->id;
                        $rank1->save();
                    }
                }elseif($Check_card === 2){//残っている人が2人のとき
                    //2位の人の記録
                    $diff_player1 = rank::orderBy('id','asc')->first();
                    $player2 = $player0->diff([$diff_player1->playername_id])->first();
                    $player22 = Playername::where('id',$player2)
                    ->select('id')//ほしい情報がid
                    ->first();
                    if($player22 && !rank::where('playername_id', $player22->id)->exists()) {
                        $rank2 = new rank();
                        $rank2->playername_id = $player22->id;
                        $rank2->save();
                    }
                }elseif($Check_card === 1){//残っている人が1人のとき
                    //3位の人の記録
                    $diff_player1 = rank::orderBy('id','asc')->first();
                    $diff_player2 = rank::orderBy('id','desc')->first();                
                    $player3 = $player0->diff([$diff_player1->playername_id, $diff_player2->playername_id])->first();
                    $player33 = Playername::where('id', $player3)
                    ->select('id')
                    ->first();
                    
                    if ($player33 && !rank::where('playername_id', $player33->id)->exists()) {
                        $rank3 = new rank();
                        $rank3->playername_id = $player33->id;
                        $rank3->save();
                    }
                }
                $turn = [];
                return $turn;
            }elseif(count(array_intersect($put_cards, $ten_cards)) > 0){//10捨てをするコード

                //捨てるカードをDBのfieldに入れる
                //urlパラメータで配列を渡すときは?discard_card_id[]=数字
                //urlパラメータで数字を渡すときは?discard_card_id=数字
                //このコードは配列をDBに入れる
                $discard_cards = $request->discard_card_id;//$discard_cardに捨てるカードが何枚か入っている
                if(!empty($discard_cards) && count($discard_cards)>$put_card_count){//多い場合エラーを返す
                    $turn = array(
                        'status' => 'error',
                        'message' => 'できねえよバーーーーーーカ'
                    , 400);
                }
                if (!empty($discard_cards)) {
                    $card_updated = CardInfo::whereIn('id',$discard_cards)//ここはあってる
                    ->update(['field'=>-1]);
                }


                $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                ->select('order')//その人のorderをとる
                ->first();
                $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                ->first();
                
                $skip1 = Playername::orderBy('id','asc')->first();
                $skip2 = Playername::where('id','>=',$skip1->id)
                ->update(['skip'=> 0]);//出すボタンが一回押されたらパスの連続判定が止まるように
    
                $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                ->first();
                $turn0->turn = 0;//turn1の人のturnを１から０に
                $turn0->save();
                $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                $turn2->save();

                //手札が0の人にturnが回って来た時に回すように
                if($turn2 && $player0->contains($turn2->id)){
                    $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                    ->select('order')//その人のorderをとる
                    ->first();
                    $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                    ->first();        
                    $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                    ->first();
                    $turn0->skip = 1;//パスの値に1を入れる
                    $turn0->turn = 0;//turn1の人のturnを１から０に
                    $turn0->save();
                    $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                    $turn2->save();
                    //手札が0の人に2回連続でturnが回って来た時に回すように
                    if($turn2 && $player0->contains($turn2->id)){
                        $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                        ->select('order')//その人のorderをとる
                        ->first();
                        $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                        ->first();                    
                        $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                        ->first();
                        $turn0->skip = 1;//パスの値に1を入れる                
                        $turn0->turn = 0;//turn1の人のturnを１から０に
                        $turn0->save();
                        $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                        $turn2->save();
                    }
                }
                //順位を入れるため(上がる人はカードを出して上がるためここに入れる)
                $hand_coats = CardInfo::where('field',0)//fieldが0のカードが手札にある
                ->select('playername_id',
                DB::raw('count(playername_id) as hand_coats'))//group byを使えるようにするための準備
                ->groupBy('playername_id')//group byでplayeridごとにする
                ->get();//playerごとの枚数が知りたい
                $Check_card = $hand_coats->filter(function($check0){
                    return $check0->hand_coats > 0;
                })
                ->count();//手札が1枚以上ある人をカウント

                //順位はIDが低い順
                //1位の人の記録
                $player1 = null;
                $player2 = null;
                $player3 = null;

                $player_have = CardInfo::where('field',0)//手札のカードすべて持ってくる
                ->pluck('playername_id');
                //残っているプレイヤーから手札のないプレイヤーを抽出
                $player0 = $player_id->diff($player_have);
                if($Check_card === 3){//残っている人が3人のとき
                    $player1 = $player0->first();
                    $player11 = Playername::where('id',$player1)
                    ->select('id')//ほしい情報がid
                    ->first();
                    if($player11 && !rank::where('playername_id', $player11->id)->exists()) {
                        $rank1 = new rank();
                        $rank1->playername_id = $player11->id;
                        $rank1->save();
                    }
                }elseif($Check_card === 2){//残っている人が2人のとき
                    //2位の人の記録
                    $diff_player1 = rank::orderBy('id','asc')->first();
                    $player2 = $player0->diff([$diff_player1->playername_id])->first();
                    $player22 = Playername::where('id',$player2)
                    ->select('id')//ほしい情報がid
                    ->first();
                    if($player22 && !rank::where('playername_id', $player22->id)->exists()) {
                        $rank2 = new rank();
                        $rank2->playername_id = $player22->id;
                        $rank2->save();
                    }
                }elseif($Check_card === 1){//残っている人が1人のとき
                    //3位の人の記録
                    $diff_player1 = rank::orderBy('id','asc')->first();
                    $diff_player2 = rank::orderBy('id','desc')->first();                
                    $player3 = $player0->diff([$diff_player1->playername_id, $diff_player2->playername_id])->first();
                    $player33 = Playername::where('id', $player3)
                    ->select('id')
                    ->first();
                    
                    if ($player33 && !rank::where('playername_id', $player33->id)->exists()) {
                        $rank3 = new rank();
                        $rank3->playername_id = $player33->id;
                        $rank3->save();
                    }
                }      
                $turn = [];
                return $turn;          
            }elseif(count(array_intersect($put_cards, $seven_cards)) > 0){//7渡しをするコード

                //渡すカードをDBのplayername_id渡す相手のidを入れる
                //urlパラメータで配列を渡すときは?discard_card_id[]=数字
                //urlパラメータで数字を渡すときは?discard_card_id=数字
                //このコードは配列をDBに入れる

                $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                ->select('order')//その人のorderをとる
                ->first();
                $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                ->first();
                $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                ->first();


                $deliver_player_id0 = Playername::where('order',$turn2->order % 4 + 1)
                    ->first();
                $deliver_cards = $request->deliver_card_id;//$deliver_cardsに渡すカードが何枚か入っている
                $deliver_player_id = $turn2->id;
                if($turn2 && $player0->contains($turn2->id)){//次の人の手札が0の時その次の人に渡す
                    $deliver_player_id = $deliver_player_id0->id;
                }
                if($deliver_player_id0 && $player0->contains($deliver_player_id0->id)){//次の人の手札が0の時その次の人に渡す
                    $deliver_player_id1 = Playername::where('order',$turn2->order % 4 + 1)
                    ->first();
                    $deliver_player_id = $deliver_player_id1->id;
                }
                $deliver_player = $deliver_player_id;//$deliver_player_idに渡す相手が入る
                if(!empty($deliver_cards)){
                    $card_updated = CardInfo::whereIn('id',$deliver_cards)
                    ->update(['playername_id'=>$deliver_player_id]);
                    $deliver_cards_count =count($deliver_cards);
                }

                $skip1 = Playername::orderBy('id','asc')->first();
                $skip2 = Playername::where('id','>=',$skip1->id)
                ->update(['skip'=> 0]);//出すボタンが一回押されたらパスの連続判定が止まるように
    
                $turn0->turn = 0;//turn1の人のturnを１から０に
                $turn0->save();
                $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                $turn2->save();

                //手札が0の人にturnが回って来た時に回すように
                if($turn2 && $player0->contains($turn2->id)){
                    $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                    ->select('order')//その人のorderをとる
                    ->first();
                    $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                    ->first();        
                    $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                    ->first();
                    $turn0->skip = 1;//パスの値に1を入れる
                    $turn0->turn = 0;//turn1の人のturnを１から０に
                    $turn0->save();
                    $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                    $turn2->save();
                    //手札が0の人に2回連続でturnが回って来た時に回すように
                    if($turn2 && $player0->contains($turn2->id)){
                        $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                        ->select('order')//その人のorderをとる
                        ->first();
                        $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                        ->first();                    
                        $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                        ->first();
                        $turn0->skip = 1;//パスの値に1を入れる                
                        $turn0->turn = 0;//turn1の人のturnを１から０に
                        $turn0->save();
                        $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                        $turn2->save();
                    }
                }
                //順位を入れるため(上がる人はカードを出して上がるためここに入れる)
                $hand_coats = CardInfo::where('field',0)//fieldが0のカードが手札にある
                ->select('playername_id',
                DB::raw('count(playername_id) as hand_coats'))//group byを使えるようにするための準備
                ->groupBy('playername_id')//group byでplayeridごとにする
                ->get();//playerごとの枚数が知りたい
                $Check_card = $hand_coats->filter(function($check0){
                    return $check0->hand_coats > 0;
                })
                ->count();//手札が1枚以上ある人をカウント

                //順位はIDが低い順
                //1位の人の記録
                $player1 = null;
                $player2 = null;
                $player3 = null;

                $player_have = CardInfo::where('field',0)//手札のカードすべて持ってくる
                ->pluck('playername_id');
                //残っているプレイヤーから手札のないプレイヤーを抽出
                $player0 = $player_id->diff($player_have);
                if($Check_card === 3){//残っている人が3人のとき
                    $player1 = $player0->first();
                    $player11 = Playername::where('id',$player1)
                    ->select('id')//ほしい情報がid
                    ->first();
                    if($player11 && !rank::where('playername_id', $player11->id)->exists()) {
                        $rank1 = new rank();
                        $rank1->playername_id = $player11->id;
                        $rank1->save();
                    }
                }elseif($Check_card === 2){//残っている人が2人のとき
                    //2位の人の記録
                    $diff_player1 = rank::orderBy('id','asc')->first();
                    $player2 = $player0->diff([$diff_player1->playername_id])->first();
                    $player22 = Playername::where('id',$player2)
                    ->select('id')//ほしい情報がid
                    ->first();
                    if($player22 && !rank::where('playername_id', $player22->id)->exists()) {
                        $rank2 = new rank();
                        $rank2->playername_id = $player22->id;
                        $rank2->save();
                    }
                }elseif($Check_card === 1){//残っている人が1人のとき
                    //3位の人の記録
                    $diff_player1 = rank::orderBy('id','asc')->first();
                    $diff_player2 = rank::orderBy('id','desc')->first();                
                    $player3 = $player0->diff([$diff_player1->playername_id, $diff_player2->playername_id])->first();
                    $player33 = Playername::where('id', $player3)
                    ->select('id')
                    ->first();
                    
                    if ($player33 && !rank::where('playername_id', $player33->id)->exists()) {
                        $rank3 = new rank();
                        $rank3->playername_id = $player33->id;
                        $rank3->save();
                    }
                }
                $turn = [];
                return $turn;
            }else{            
            
                $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                ->select('order')//その人のorderをとる
                ->first();
                $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                ->first();
                
                $skip1 = Playername::orderBy('id','asc')->first();
                $skip2 = Playername::where('id','>=',$skip1->id)
                ->update(['skip'=> 0]);//出すボタンが一回押されたらパスの連続判定が止まるように
    
                $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                ->first();
                $turn0->turn = 0;//turn1の人のturnを１から０に
                $turn0->save();
                $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                $turn2->save();

                //手札が0の人にturnが回って来た時に回すように
                if($turn2 && $player0->contains($turn2->id)){
                    $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                    ->select('order')//その人のorderをとる
                    ->first();
                    $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                    ->first();        
                    $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                    ->first();
                    $turn0->skip = 1;//パスの値に1を入れる
                    $turn0->turn = 0;//turn1の人のturnを１から０に
                    $turn0->save();
                    $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                    $turn2->save();
                    //手札が0の人に2回連続でturnが回って来た時に回すように
                    if($turn2 && $player0->contains($turn2->id)){
                        $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                        ->select('order')//その人のorderをとる
                        ->first();
                        $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                        ->first();                    
                        $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                        ->first();
                        $turn0->skip = 1;//パスの値に1を入れる                
                        $turn0->turn = 0;//turn1の人のturnを１から０に
                        $turn0->save();
                        $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                        $turn2->save();
                    }
                }
                //順位を入れるため(上がる人はカードを出して上がるためここに入れる)
                $hand_coats = CardInfo::where('field',0)//fieldが0のカードが手札にある
                ->select('playername_id',
                DB::raw('count(playername_id) as hand_coats'))//group byを使えるようにするための準備
                ->groupBy('playername_id')//group byでplayeridごとにする
                ->get();//playerごとの枚数が知りたい
                $Check_card = $hand_coats->filter(function($check0){
                    return $check0->hand_coats > 0;
                })
                ->count();//手札が1枚以上ある人をカウント

                //順位はIDが低い順
                //1位の人の記録
                $player1 = null;
                $player2 = null;
                $player3 = null;
                
                $player_have = CardInfo::where('field',0)//手札のカードすべて持ってくる
                ->pluck('playername_id');
                //残っているプレイヤーから手札のないプレイヤーを抽出
                $player0 = $player_id->diff($player_have);
                if($Check_card === 3){//残っている人が3人のとき
                    $player1 = $player0->first();
                    $player11 = Playername::where('id',$player1)
                    ->select('id')//ほしい情報がid
                    ->first();
                    if($player11 && !rank::where('playername_id', $player11->id)->exists()) {
                        $rank1 = new rank();
                        $rank1->playername_id = $player11->id;
                        $rank1->save();
                    }
                }elseif($Check_card === 2){//残っている人が2人のとき
                    //2位の人の記録
                    $diff_player1 = rank::orderBy('id','asc')->first();
                    $player2 = $player0->diff([$diff_player1->playername_id])->first();
                    $player22 = Playername::where('id',$player2)
                    ->select('id')//ほしい情報がid
                    ->first();
                    if($player22 && !rank::where('playername_id', $player22->id)->exists()) {
                        $rank2 = new rank();
                        $rank2->playername_id = $player22->id;
                        $rank2->save();
                    }
                }elseif($Check_card === 1){//残っている人が1人のとき
                    //3位の人の記録
                    $diff_player1 = rank::orderBy('id','asc')->first();
                    $diff_player2 = rank::orderBy('id','desc')->first();                
                    $player3 = $player0->diff([$diff_player1->playername_id, $diff_player2->playername_id])->first();
                    $player33 = Playername::where('id', $player3)
                    ->select('id')
                    ->first();
                    
                    if ($player33 && !rank::where('playername_id', $player33->id)->exists()) {
                        $rank3 = new rank();
                        $rank3->playername_id = $player33->id;
                        $rank3->save();
                    }
                }
                $turn = [];
                return $turn;
            }
            
            //8切りするコード
            //8のカードを見る
            if($put_card = 8||$put_card = 21||$put_card = 34||$put_card = 47){
                //スキップを4回して自分のターンに返ってくるように
                for($i = 0;$i < 4; $i++){
        
                $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                ->select('order')//その人のorderをとる
                ->first();
                $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                ->first();
        
                $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
               ->first();
        
                $turn0->skip = 1;//パスの値に1を入れる
                $turn0->save();
                $turn0->turn = 0;//turn1の人のturnを１から０に
                $turn0->save();
                $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                $turn2->save();
                }
                //8切りで場が流れるようのコード
                $field3 = CardInfo::where('field',1)//fieldが1のカードが場にある
                ->update(['field'=>-1]);//流れたカードはfieldを-1にする

            }else{
                $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                ->select('order')//その人のorderをとる
                ->first();
                $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                ->first();
    
                
                $skip1 = Playername::orderBy('id','asc')->first();
                $skip2 = Playername::where('id','>=',$skip1->id)
                ->update(['skip'=> 0]);//出すボタンが一回押されたらパスの連続判定が止まるように
    
                $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                ->first();
                $turn0->turn = 0;//turn1の人のturnを１から０に
                $turn0->save();
                $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                $turn2->save();

                //手札が0の人にturnが回って来た時に回すように
                if($turn2 && $player0->contains($turn2->id)){
                    $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                    ->select('order')//その人のorderをとる
                    ->first();
                    $turn2 = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                    ->first();
        
                    
                    $skip1 = Playername::orderBy('id','asc')->first();
                    $skip2 = Playername::where('id','>=',$skip1->id)
                    ->update(['skip'=> 0]);//出すボタンが一回押されたらパスの連続判定が止まるように
        
                    $turn0 = Playername::where('Turn',1)//現在turnが１の人を知る
                    ->first();
                    $turn0->turn = 0;//turn1の人のturnを１から０に
                    $turn0->save();
                    $turn2->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                    $turn2->save();
                }
            }
        }elseif($action ==='pass'){//パスのボタンが押されたときの動作
            //デバック中passは正確にできた

            //現在turnが１の人を知る
            $turnNow = Playername::where('Turn',1)->first();

            $turnNow->skip = 1;//パスの値に1を入れる            
            $turnNow->turn = 0;//turn1の人のturnを１から０に
            $turnNow->save();

            // カードを出せる人がいない場合
            $postable = Playername::leftJoin('ranks', function($join){
                $join->on('playernames.id','=','ranks.playername_id');
            })->select('playernames.id','skip','ranks.playername_id')->whereNull('ranks.playername_id')->where('skip',0)->get();

            $lastCard = CardInfo::where('field',1)->orderBy('updated_at','desc')->orderBy('lv','desc')->first();

            if(count($postable) == 0){


                // カードを流す
                CardInfo::where('field',1)->update(['field' => -1]);
                // 全員のスキップをリセット
                Playername::where('skip',1)->update(['skip' => 0]);

                // 次のターンの人をセット
                $nextTurnPlayer = $this->getNextPlayerId($lastCard->playername_id);
                $nextTurnPlayer->turn = 1;
                $nextTurnPlayer->save();

                return [];
            }

            if(count($postable)==1 && $postable[0]->id == $lastCard->playername_id){
                // 最後にカードを捨てた人以外は、カードが出せない状態なら
                // 場を流し、最後にカードを捨てた人のターンにする
                $targetPlayer = Playername::find($postable[0]->id);
                $targetPlayer->turn = 1;
                $targetPlayer->save();

                // カードを流す
                CardInfo::where('field',1)->update(['field' => -1]);
                // 全員のスキップをリセット
                Playername::where('skip',1)->update(['skip' => 0]);
                return [];
            }

            // スキップしていない人と上がってない人の中から次のターンの人を探す
            $nextTurnPlayer = $this->getNextPlayerId($turnNow->id);

            $nextTurnPlayer->Turn = 1;
            $nextTurnPlayer->save();

            return [];


            // TODO まだスキップしてない中での次の人
            $turnNext = Playername::where('order',$turnNow->order % 4 + 1)->first();

            $turnNext->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
            $turnNext->save();

            //手札が0の人にturnが回って来た時に回すように
            if($turnNext && $turnNow->contains($turnNext->id)){
                $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                ->select('order')//その人のorderをとる
                ->first();
                $turnNext = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                ->first();                    
                $turnNow = Playername::where('Turn',1)//現在turnが１の人を知る
                ->first();
                $turnNow->skip = 1;//パスの値に1を入れる
                $turnNow->turn = 0;//turn1の人のturnを１から０に
                $turnNow->save();
                $turnNext->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                $turnNext->save();
                //手札が0の人に2回連続でturnが回って来た時に回すように
                if($turnNext && $turnNow->contains($turnNext->id)){
                    $turn1 = Playername::where('Turn',1)//現在turnが１の人を知る
                    ->select('order')//その人のorderをとる
                    ->first();
                    $turnNext = Playername::where('order',$turn1->order % 4 + 1)//turn1の人の次の人を知る
                    ->first();                    
                    $turnNow = Playername::where('Turn',1)//現在turnが１の人を知る
                    ->first();
                    $turnNow->skip = 1;//パスの値に1を入れる                
                    $turnNow->turn = 0;//turn1の人のturnを１から０に
                    $turnNow->save();
                    $turnNext->turn = 1;//turn2の人が次の人なのでその人のturnを０から１に
                    $turnNext->save();
                }
            }

            //場を流す(完成)
            $shed1 = Playername::where('skip',1)//パスを3人が連続でしたかの判定
            ->count();
            if($shed1 == 3){//パスが3回連続で押されたときとそうじゃないときの動き
                $field3 = CardInfo::where('field',1)//fieldが1のカードが場にある
                ->update(['field'=>-1]);//流れたカードはfieldを-1にする
            }
            $turn = [];
            return $turn;
        }
    }

    private function getNextPlayerId( $playerId ):Playername{
        $nextTurnPlayerId = $playerId;
        do{
            $nextPlayer = Playername::where('id','>',$nextTurnPlayerId)->orderBy('order','asc')->first();
            if(!$nextPlayer){
                $nextPlayer = Playername::where('id','<',$nextTurnPlayerId)->orderBy('order','asc')->first();
            }
            if( $nextPlayer->skip != 1 && Rank::where('playername_id',$nextPlayer->id)->count() == 0){
                break;
            }
            $nextTurnPlayerId = $nextPlayer->id;
        } while($nextTurnPlayerId != $playerId);

        return $nextPlayer;
    }
}
