const putButton = document.getElementById('put');
const passButton = document.getElementById('pass');
const giveButton = document.getElementById('give');//7渡し
const field = document.getElementById('field');
const seventIds = [7, 20, 33, 46];//７のid


let allCards = document.querySelectorAll('.card-holder .card');


// ターンの管理 (true = 自分のターン, false = 相手のターン)
let isMyTurn = true;

let canPlay = true; // 1枚出した後にもうカードを出せなくするフラグ
let cardPlayed = false; // カードを出せたかどうかのフラグ

const onMyCardClicked = function (e) {
    if (canPlay && isMyTurn && !e.currentTarget.classList.contains('disabled')) {  // カードを出せる状態でのみ選択可能
        if (e.currentTarget.classList.contains('selected')) {
            // 'selected'クラスを外す
            e.currentTarget.classList.remove('selected');
        } else {
            // 'selected'クラスを追加
            e.currentTarget.classList.add('selected');
        }
    }
}


// カードの選択・解除を管理
allCards.forEach( card => card.addEventListener('click', onMyCardClicked));



putButton.addEventListener('click', async function () {
    // if (!isMyTurn || !canPlay || cardPlayed) return; // 自分のターンでなく、またカードを出せない状態では何もしない

    let selectedCards = document.querySelectorAll('.selected');

    // 一枚でもカードが選択された場合
    if (selectedCards.length > 0) {

        // ①場に出ているカードの枚数と、手札から出す枚数が一致
        let fieldCardCount = document.querySelectorAll('#field .card').length;
        if (selectedCards.length !== fieldCardCount && fieldCardCount != 0) {
            alert(`場に出ているカードの枚数(${fieldCardCount})と、選択したカードの枚数(${selectedCards.length})が一致していません。`);
            return;
        }


        //②手札から出すカードのレベルが全て同じ
        let firstCard = selectedCards[0];

        for (let i = 0; i < selectedCards.length; i++) {
            if (parseInt(selectedCards[i].dataset.level) != parseInt(firstCard.dataset.level)) {
                alert(`カードのレベルが同じではありません`);
                return;
            }
        }
        //③場のカードのレベルより手札のカードのレベルが上ならOK
        let field_cards = document.querySelectorAll('#field .card');
        if (fieldCardCount != 0 && parseInt(firstCard.dataset.level) <= parseInt(field_cards[0].dataset.level)) {
            alert('手札のカードは場のカードよりレベルが低い');
            return;
        }

        let putCardIds = [];

        let cards = document.createElement('div');
        cards.className = 'cards';
        selectedCards.forEach(function (card) {
            // 場にある最新のカードを取得           

            const fieldCard = document.querySelectorAll('#field .card');
            cards.appendChild(card);


            // カードを場に出す 
            card.classList.remove('selected'); // 選択解除
            // カードのIDを取得
            const cardId = card.getAttribute('data-card-id');

            putCardIds.push(cardId);//取得したカードIDをputCardIds配列に追加

            // カードを無効にするためにdisabledクラスを追加
            // card.classList.add('disabled');
            // card.removeEventListener('click', handleCardClick); // クリックイベントを解除

            cardPlayed = true;  // フラグをtrueに設定


        });
        // 1枚出したらもうカードを出せなくする
        canPlay = true;



        if (cardPlayed == true) {
            field.append(cards);

            // すべてのカードを無効にする
            // allCards.forEach(function (card) {
            //     card.classList.add('disabled'); // disabledクラスを追加
            //     card.removeEventListener('click', handleCardClick); // クリックイベントを解除
            // });

            // カードを出したら「put」ボタンを非表示にする
            putButton.style.display = 'none'; // ボタンを非表示
            passButton.style.display = 'none';


            //turn(put)実行
            const cardHolder = document.querySelector('.card-holder');
            const myid = cardHolder.getAttribute('data-player');//自分のid
            const selecteditems = selectedCards.length;//選択枚数(&put_card_id[]='+putCardIdsが何個必要かの数字？）
            let url = '/api/turn?action=put&playerid=';
            url = url + myid;

            const seventIds = [7, 20, 33, 46];//７のid



            // if (putCardIds.some(id => seventIds.includes(Number(id)))) {
            //     alert("７渡し！");
            //     giveButton.style.display = 'inline-block';//渡すボタン表示
            //     putButton.style.display = 'none'; // 出すボタンを非表示
            //     passButton.style.display = 'none';//パスボタンを非表示

            // } else {//７以外の時
                for (i = 0; i < selecteditems; i++) {
                    url = url + '&put_card_id[]=' + putCardIds[i];
                }
                console.log('putCardIDs', putCardIds);
                console.log(url);
                await fetch(url); //turnを実行     
                fetchControllerData();

                //alert('カードを出しました。これでターンが終了します。');
                endTurn()
            // }





        }
    }
});

passButton.addEventListener('click', async function () {

    putButton.style.display = 'none'; // ボタンを非表示
    passButton.style.display = 'none';
    try {
        const cardHolder = document.querySelector('.card-holder');
        const myid = cardHolder.getAttribute('data-player');//自分のidをmyidにいれた
        // turn(pass)を実行
        await fetch(`/api/turn?action=pass&player_id=` + myid, {
        });
        //成功した場合
        //alert('ターンをパスしました。次のターンに移ります。');
        endTurn();
        fetchControllerData();
    } catch (error) {
        console.error('API呼び出し中にエラーが発生しました:', error);
        alert('ターンのパス中にエラーが発生しました。もう一度試してください。');
    }
});



// ターンを終了させる関数
function endTurn() {
    // フラグのリセット
    isMyTurn = !isMyTurn;  // 自分のターンか相手のターンかを切り替える
    canPlay = true;         // 1ターンごとに再度カードを出せるようにする
    cardPlayed = false;     // カードが出ていない状態に戻す

    // カード選択の状態をリセット
    allCards.forEach(function (card) {
        card.classList.remove('selected');
        card.classList.add('disabled');
        //card.addEventListener('click', handleCardClick); // クリックイベントを再追加
    });

}

// ボタンを再表示
function button() {
    putButton.style.display = 'inline-block';
    passButton.style.display = 'inline-block';
    giveButton.style.display = 'none';//giveは消しておく

    // カードを選択可能にする
    // disabledクラスを消す
    document.querySelectorAll('.card-holder .card')?.forEach(function (card) {
        card.classList.remove('disabled');
    });

    //alert('ターンが終了しました。');
}

// カードクリック時の選択・解除を管理する関数

// function handleCardClick() {
//     if (this.classList.contains('selected')) {
//         this.classList.remove('selected');
//     } else {
//         this.classList.add('selected');
//     }
// }

// function change_card(hand_Counts,playername_id){
//     const player_2_cards = document.querySelector('.hand_Counts' + playername_id) //'.player'+pm
//     player_2_cards.querySelectorAll('img').forEach( e => e.remove() )

//     // 最新のカードの枚数分、画像を再度作成
//     // responseの中からプレーヤーAのカードの枚数の数字を取り出す
//     // 仮で枚数の数字はplayer_1_countとする
//     for( let i = 0; i < hand_Counts; i++){
//         // 新しい<img>を作る
//         const card_image = document.createElement('img')
//         // 画像のパスをセット
//         card_image.src = '/img/torannpu-illust54.png'
//         // 新しく作った<img>をプレーヤー1のカードをまとめている<div>に追加する
//         player_2_cards.append(card_image)
//     }
// } 

// let hand_Counts = json.allData.hand_Counts[0].playername_id; 
// change_card(hand_Counts,2)
// console.log(secondCount); 

// let thirdCount = json.allData.hand_Counts[0].playername_id;
// change_card(thirdCount,3)
// console.log(thirdCount);

// let fourthCount = json.allData.hand_Counts[0].playername_id;
// change_card(fourthCount,4)
// console.log(fourthCount);



//ここから下apiの処理


async function fetchData(url) {
    const res = await fetch(url);
    const json = await res.json();
    return json;
}
//5秒おきに実行されるapi
var json;
function fetchControllerDataUpdate() {
    let playerID = document.getElementById("playerid").value;
    if (json.allData.playerTurn.length == 0 || json.allData.playerTurn[0].id == playerID) {
        return;
    }
    fetchControllerData();

}
async function fetchControllerData() {
    console.log("fetchControllerData() called"); // 関数が実行されたかチェック

    try {
        let playerID = document.getElementById("playerid").value;
        var allResponse = await fetch('/api/all?playerid=' + playerID);
        var allData = await allResponse.json();
        var handsResponse = await fetch('/api/hands?playerid=' + playerID);
        var handsData = await handsResponse.json();

        // JSONデータが正しく取得されたか確認
        if (!allData || !handsData) {
            console.error('Error: Missing data');
            return;
        }

        json = {
            allData: allData,
            hands: handsData,
            playerID: playerID
        };
        console.log(allData)
        // $finish の値をチェックして、false → true に変わったらリダイレクト
        if (allData.finish == true) {
            window.location.href = '/end?playerid=' + playerID;
            return;
        }

        console.log('Combined JSON:', json);;


        // playerTurn, hand_Counts, max_card_count などの個別表示

        console.log('finish:', json.allData.finish);                     //$all['finish']
        console.log('Player Turn:', json.allData.playerTurn);           // $all['playerTurn']
        console.log('Hand Counts:', json.allData.hand_Counts);          // $all['hand_Counts']
        console.log('Max Card Count:', json.allData.max_card_count);    // $all['max_card_count']
        console.log('Field Cards:', json.allData.field_cards);          // $all['field_cards']

        //各プレイヤーの手札枚数を表示処理
        for (let i = 0; i < 4; i++) {
            var handCounts = allData.hand_Counts[i].hand_Counts;//０～４の枚数
            var handCounts_id = allData.hand_Counts[i].playername_id;//０～４のid
            var cc = handCounts + '枚';
            var elm = document.getElementById('card-count' + handCounts_id);
            elm.innerHTML = cc;
        };







        // hands のループ処理（配列かどうか確認）
        if (Array.isArray(json.hands)) {
            json.hands.forEach(hand => {
                console.log('Hand ID:', hand.id);
            });
        } else {
            console.log('No hands data available.');
        }

        // not_playable_cards のループ処理（配列かどうか確認）
        if (Array.isArray(json.allData.not_playable_cards)) {
            json.allData.not_playable_cards.forEach(card => {
                console.log('Not Playable Card ID:', card.id);
            });
        } else {
            console.log('No not_playable_cards data available.');
        }
        console.log('Combined JSON:', json);

        //自分の手札を書き換える、手札枚数分繰り返す
        const hand_times = json.allData.hands.length;//自分の手札の枚数（hand_times）
        let handCount = 0; // 手札の枚数を追跡するグローバル変数

        boxhighlight();

        // json.hands の個数を取得
        let handsCount = 14;
        if (Array.isArray(json.hands)) {
            handsCount = json.hands.length;
        }
        // .player1 内の画像の個数を取得
        let player1ImgCount = document.querySelectorAll('.player1 img')?.length || 0;

        // 2つの数値を比較
        if (handsCount !== player1ImgCount) {
            for (let i = 0; i < hand_times; i++) {
                change_hand();
            }

            function change_hand() {
                const handContainer = document.querySelector('.player1'); // 手札を含む親要素を入れる

                // 現在の手札をすべて削除
                handContainer.innerHTML = '';
                // handCountを1増やす
                handCount++;
                // 新しい手札を生成
                for (let i = 0; i < handCount; i++) {
                    const newCard = createNewCard(i);
                    handContainer.appendChild(newCard);
                }
            }
        } else {
            console.log(`数は同じです。 hands: ${handsCount}, player1の画像数: ${player1ImgCount}`);
        }

        function createNewCard(cardNumber) {
            const newCard = document.createElement('div');
            // newCard.className = 'tefuda card';
            newCard.classList.add('tefuda','card','disabled')

            // カードIDとレベルを設定
            const cardId = json.hands[cardNumber].id;
            const cardLevel = json.hands[cardNumber].lv;

            newCard.setAttribute('data-card-id', cardId);
            newCard.setAttribute('data-level', cardLevel);

            // カードの選択・解除を管理
            newCard.addEventListener('click', onMyCardClicked);

            const img = document.createElement('img');
            img.src = `/img/torannpu-illust${cardId}.png`;
            img.width = 90;
            img.alt = '';

            newCard.appendChild(img);
            return newCard;
        }





        // 自分以外のプレイヤーの手札枚数を書き換える
        // 4回繰り返す
        console.log('json:240', json);
        for (let i = 0; i < 4; i++) {
            let hand_Counts = json.allData.hand_Counts[i].hand_Counts;
            let playername_id = json.allData.hand_Counts[i].playername_id;
            if (playername_id != playerID) {
                change_card(hand_Counts, playername_id)
            }

            console.log(hand_Counts);
        }

        // console.log('json',json);
        // for( let i = 0; i < 4; i++){
        let playerid = json.playerID;
        let Turn = json.allData.playerTurn[0].id;
        
        isMyTurn = playerID == Turn

        if (playerID == Turn) {
            button()
        }

        const fieldCards = document.querySelectorAll('#field .card')
        if (fieldCards.length > 0) {
            fieldCards.forEach(e => e.remove())
        }

        let cards = document.createElement('div');
        cards.className = 'cards';
        for (let i = 0; i < json.allData.field_cards.length; i++) {
            let cardId = json.allData.field_cards[i].id;//json
            let cardLevel = json.allData.field_cards[i].lv;

            const fieldCard = document.createElement('div');
            fieldCard.className = 'card';

            fieldCard.setAttribute('data-card-id', cardId);
            fieldCard.setAttribute('data-level', cardLevel);

            const img = document.createElement('img');
            img.src = `/img/torannpu-illust${cardId}.png`;
            img.width = 90;
            fieldCard.appendChild(img); // 画像をカードに追加

            cards.appendChild(fieldCard); // 画像をカードに追加

            // 場にカードを追加
            const field = document.getElementById('field');
            field.appendChild(cards);
        }
    }




    catch (error) {
        console.error('Error fetching data:', error);
    }
}

function change_card(hand_Counts, playername_id) {
    const player_2_cards = document.querySelector('.player_' + playername_id) //'.player'+pm
    player_2_cards.querySelectorAll('img').forEach(e => e.remove())

    // 最新のカードの枚数分、画像を再度作成
    // responseの中からプレーヤーAのカードの枚数の数字を取り出す
    // 仮で枚数の数字はplayer_1_countとする
    for (let i = 0; i < hand_Counts; i++) {
        // 新しい<img>を作る
        const card_image = document.createElement('img')
        // 画像のパスをセット
        card_image.src = '/img/torannpu-illust54.png'
        // 新しく作った<img>をプレーヤー1のカードをまとめている<div>に追加する
        player_2_cards.append(card_image)
    }
}

function boxhighlight() {
    try {
        let playerID = document.getElementById("playerid").value;//プレイヤーIDの取得
        let turn = json.allData.playerTurn[0].id;//誰のターンか
        let boxes = document.querySelectorAll('.namebox');//querySelectorAllを使いnameboxの要素がある全boxを取得する
        //同時にplayerIDを指定する
        boxes.forEach((box) => {
            if (box.classList.contains('playerbox_' + turn)) {
                //条件が同じboxに対してcssの.highlightを適用
                box.classList.add("highlight");
            } else {
                //条件が違うboxに対して.highlightを消す
                box.classList.remove("highlight");
            }
        });


    } catch (error) {
        console.error('error:', error);
    }
}

async function startPeriodicFetch() {

    // 初回の呼び出し
    await fetchControllerData();

    // 5秒おきに呼び出し
    setInterval(fetchControllerDataUpdate, 5000);
}

// ページ読み込み時に実行
document.addEventListener('DOMContentLoaded', startPeriodicFetch);

//ここから

