<?php
$url = 'https://api.twelvedata.com/time_series?symbol=XAU/USD&interval=1h&apikey=f3db2fab3f82485d92e7b90eb0d87cc1&outputsize=5';
$data = json_decode(file_get_contents($url), true);
if(isset($data['values'])) {
    foreach($data['values'] as $v) {
        echo $v['datetime'] . " - Close: " . $v['close'] . "\n";
    }
} else {
    echo "Error or no values.\n";
    print_r($data);
}
