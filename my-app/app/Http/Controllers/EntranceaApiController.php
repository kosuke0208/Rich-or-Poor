<?php

namespace App\Http\Controllers;

use App\Models\CardInfo;
use App\Models\Playername;
use Illuminate\Http\Request;

class EntranceaApiController extends Controller
{
    public function apiHello(){
        return 'Hello World';
    }

    public function checkPlayer(){
        $a = Playername::all()->count();
        if($a == 4){$r['playerStatus']=true;}
        else{$r['playerStatus']=false;};
        return $r;

    }
    public function checkCard(){
        $table = CardInfo::inRandomOrder()->get();
        return $table;
    }
}
