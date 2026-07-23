<?php
$url = 'https://api.twelvedata.com/price?symbol=XAU/USD&apikey=f3db2fab3f82485d92e7b90eb0d87cc1';
echo file_get_contents($url);
