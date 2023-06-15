<?php
    header('Content-Type: text/html; charset=UTF-8');
    // Inicializa o cURL
    $api = curl_init();
    
    $cep = $_GET["cep"];
    // Define a URL alvo
    $url = "https://viacep.com.br/ws/". $cep ."/json/";
    
    // Configura as opções do cURL
    curl_setopt($api, CURLOPT_URL, $url);
    curl_setopt($api, CURLOPT_RETURNTRANSFER, true);

    // Executa a requisição e obtém o conteúdo retornado
    $response = curl_exec($api);
    $cepArray = json_decode($response,true);
    $erro = array_key_exists("erro", $cepArray); 
    
    if ($response === false || $erro) {
        $error = curl_error($api);
        $dados = array(
            'ok' => false
        );
    } else {
        $numDex = intval(substr($cep, 2, 3));
        $url = "https://pokeapi.co/api/v2/pokemon/".$numDex;
        curl_setopt($api, CURLOPT_URL, $url);
        curl_setopt($api, CURLOPT_RETURNTRANSFER, true);
        $pokeResponse = curl_exec($api);
        $pokeData = json_decode($pokeResponse,true);
        $dados = array(
            'ok' => true,
            'cep' => $cepArray['cep'],
            'logradouro' => $cepArray['logradouro'],
            'bairro'=> $cepArray['bairro'],
            'cidade'=> $cepArray['localidade'],
            'uf' => $cepArray['uf'],
            'pokemon' => $numDex == 0? "N/A": $pokeData['name'],
            'numero-pokemon' => intval($pokeData['id']),
            'foto-pokemon' => $pokeData['sprites']['other']['official-artwork']['front_default']?$pokeData['sprites']['other']['official-artwork']['front_default']:'' 
        );
        
    }    
    
    $json = json_encode($dados,JSON_UNESCAPED_UNICODE);
    echo $json;

?>
