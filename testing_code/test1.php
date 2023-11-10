<?php
// Define an array of URLs and associated POST data

// Define your bulk orders
$bulkOrders = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
$threadCount = 3; // Number of threads to process orders concurrently

// Split the bulk orders into batches for each thread
$batchSize = ceil(count($bulkOrders) / $threadCount);
$threadBatches = array_chunk($bulkOrders, $batchSize);
// print_r($threadBatches);die;
// Create and start threads for processing orders
$threads = [];
foreach ($threadBatches as $batch) {
   // $threads[] = new BulkOrderProcessor($batch);
}

$urls = [
    'https://robomart.com/index.php?route=api/request/method1' => [
        'key1' => 'value1',
        'key2' => 'value2',
    ],
    'https://robomart.com/index.php?route=api/request/method1' => [
        'key3' => 'value3',
        'key4' => 'value4',
    ],
    'https://robomart.com/index.php?route=api/request/method3' => [
        'key5' => 'value5',
        'key6' => 'value6',
    ],
];


// Define a single URL and POST data
$url = 'https://robomart.com/index.php?route=api/request/method1';

$postData[0] = [
    'key1' => 'value1',
    'key2' => 'value2',
];
$postData[1] = [
    'key3' => 'value3',
    'key4' => 'value4',
];
$postData[2] = [
    'key5' => 'value5',
    'key6' => 'value6',
];

// Number of requests to send

$requestCount = 3;


// Initialize an array to store cURL resource handles
$curlHandles = [];

// Initialize an array to store response data
$responses = [];

// Initialize a multi-cURL handle
$multiCurl = curl_multi_init();

// Create cURL resources and add them to the multi-cURL handle

//  for hit the unique request each time

// foreach ($urls as $url => $postData) {
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_multi_add_handle($multiCurl, $ch);
//     $curlHandles[] = $ch;
// }

// for hit the same request 
for ($i = 0; $i < $requestCount; $i++) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $threadBatches[$i]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiCurl, $ch);
    $curlHandles[] = $ch;
}



// Execute the multi-cURL requests
do {
    curl_multi_exec($multiCurl, $running);
} while ($running > 0);

// Retrieve and store the responses
foreach ($curlHandles as $i => $ch) {
    $responses[$i] = curl_multi_getcontent($ch);
    curl_multi_remove_handle($multiCurl, $ch);
    curl_close($ch);
}

// Close the multi-cURL handle
curl_multi_close($multiCurl);
print_r($responses);
// Handle and process the responses as needed
// foreach ($responses as $i => $response) {
//     echo "Response from request" . ($i + 1) . ": " . $response . PHP_EOL;
// }
?>
