<?php
ini_set('max_execution_time', 3600); // 1 Hour
function headers(){
    header('Content-Type: application/octet-stream');
    header('Connection: close');
    header('Pragma:cache');
    header('Cache-Control: public, max-age=2592000');
    header('User-Cache-Control: public, max-age=2592000');
    header('Content-Transfer-Encoding: binary');
    header('Content-Disposition: attachment; filename="' . $_GET['name'] . '"');
    ob_end_flush();
    ob_implicit_flush();
}
$sources = json_decode(file_get_contents('mirrors.json'), true)['mirrors'];
$file = $_GET['id'];
$cache_path = 'cache/' . $file . '.stuf';
if(file_exists($cache_path)){
    headers();
    // Output File
    echo file_get_contents($cache_path);
}else{
    $success = false;
    foreach($sources as $source=>$data){
        // Find Mirrors
        $curl = curl_init($source . $data['mirrors']);
        curl_setopt($curl,  CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if($httpCode >= 200 && $httpCode < 300){
            $mirrors = json_decode($result, true)['mirrors'];
            if(is_array($mirrors))
                array_unique(array_merge($sources, $mirrors), SORT_REGULAR);
            file_put_contents('mirrors.json', json_encode($sources));
        }
        curl_close($curl);
        // Get File
        $handle = curl_init($source . $data['download'] . $file);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if($httpCode >= 200 && $httpCode < 300) {
            $success = true;
            break;
        }
        curl_close($handle);
    }
    if($success){
        headers();
        // Output File
        echo $response;
        // Cache File
        file_put_contents($cache_path, $response);
    }else{
        echo '<h1>Unable to mirror file</h1>';
    }
}