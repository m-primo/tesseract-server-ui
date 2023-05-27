<?php
if(!$_POST) exit;

require_once __DIR__.'/config/config.php';

set_time_limit(0);

function process($data) {
    $ch = curl_init();
    $url = CFG['url'].'/tesseract';

    if(isset($data['debug']) && $data['debug']) {
        var_dump($data);
    }

    $file_extension = explode('.', $data['file']['name']);
    $file_extension = end($file_extension);

    $postdata = [
        'file' => new CURLFile($data['file']['tmp_name'], $data['file']['type'], 'file-'.uniqid().'.'.$file_extension),
        'options' => json_encode(['languages' => $data['languages']]),
    ];
    
    if(isset($data['debug']) && $data['debug']) {
        var_dump($postdata);
    }

    // $boundary = uniqid();

    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postdata,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: multipart/form-data'
        ),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => CFG['timeout'],
        CURLOPT_CONNECTTIMEOUT => CFG['timeout']
    );
    
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);

    if(file_exists($data['file']['tmp_name'])) {
        unlink($data['file']['tmp_name']);
    }

    if (curl_errno($ch)) {
        throw new Exception("Error: " . curl_error($ch));
    }

    curl_close($ch);

    return $response;
}

header('Content-Type: application/json; charset=UTF-8');

try {
    http_response_code(200);
    echo process([
        'file' => $_FILES['file'],
        'languages' => json_decode($_POST['languages'], true),
        'debug' => isset($_POST['debug']) ? $_POST['debug'] : false,
    ]);
} catch (\Throwable $th) {
    http_response_code(500);
    echo json_encode(['error' => $th->getMessage()]);
}
?>