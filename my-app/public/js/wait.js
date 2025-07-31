async function fetchData(url) {
    const res = await fetch(url);    
    const json = await res.json();    
    return json;
}

function getPlayerID() {
    const playerElement = document.getElementById('player_name');
    if (playerElement) {
        return playerElement.getAttribute('data-id');
    }
    return null;
}

async function a() {
    let result;

    const playerID = getPlayerID();

    while (true) {
        result = await fetchData('/api/checkPlayer');
        if (result.playerStatus === true) {
            window.location.href = '/game/?playerid=' + player.id;
            break;
        }

        
        await new Promise(resolve => setTimeout(resolve, 5000));
    }
}

a();