<?php

namespace Database\Seeders;

use App\Models\CardInfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CardinfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($card = 1; $card < 5; $card++){
            for($i = 1; $i < 14; $i++){
                $cardinfo = new CardInfo();
                //
                if($card == 1){$cardinfo->name = "スペード".$i;}
                elseif($card == 2){$cardinfo->name = "クラブ".$i;}
                elseif($card == 3){$cardinfo->name = "ダイヤ".$i;}
                else{$cardinfo->name = "ハート".$i;}

                if($i < 3){$cardinfo->lv = $i + 13;}
                else{$cardinfo->lv = $i;}

                $cardinfo->playername_id = null;
                $cardinfo->field = 0;
                $cardinfo->cardcolor = $card;
                //
                $cardinfo->save();
            }
            }

    }
}

