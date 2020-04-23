<?php
require __DIR__ . "/vendor/autoload.php";
$dotenv = Dotenv\Dotenv::create(__DIR__);

$dotenv->load();


function processCommand($RawCommand)
{
    $resp = [];
    $reverse = 'false';
    $command = explode(" ", $RawCommand);

    if (count($command) === 5) {
        $amount = $command[1];
        $from = $command[2];
        $to = $command[4];

        if ($from === 'BTC') {
            $reverse = 'true';
            $symbol = $to;
        }
        if ($to === 'BTC') {
            $symbol = $from;
        }

        $resp = ['status' => 'success','data'=> [
            'amount' => $amount,
            'symbol' => $symbol,
            'reverse' => $reverse,
            'from' => $from,
            'to' => $to
        ]];
    } else {
        $resp = ['status' => 'error','message' => 'Command is not well structred'];
    }
    return $resp;
}


function convertCurrency($message)
{
    $resp = [];
    $command = processCommand($message);
    try {
        if ($command['status'] === 'success') {
            $symbol = $command['data']['symbol'];
            $amount = $command['data']['amount'];
            $from = $command['data']['from'];
            $to = $command['data']['to'];

        
            $tickerUrl = getenv('CRYPTOCOMPARE_URL');
            // print_r($tickerUrl);
            // Initialize Guzzle client
            $client = new GuzzleHttp\Client();
            $globalPriceCall = $client->get($tickerUrl, [
      'query'   => [
        'fsym' => 'BTC',
        'tsyms' => $command['data']['symbol']
      ],
  ])->getBody()->getContents();
  
            $globalPrice = json_decode($globalPriceCall);
  
            if ($command['data']['reverse'] === 'false') {
                $resp = ['from' => $from , 'to' => $to,'amount'=>round(($amount / $globalPrice->$symbol), 8) ];
            } else {
                $resp = ['from' => $from , 'to' => $to,'amount'=> number_format(($amount * $globalPrice->$symbol), 2) ];
            }
        }
        return $resp;
    } catch (exception $e) {
        return "An error occured please try again later";
    }
}