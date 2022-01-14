<?php
// https://starfiles.co/mirror/mirror?id=3c5e90&name=Wallpaper.jpeg
ini_set('max_execution_time', 3600); // 1 Hour
if(!function_exists('headers')){
    function headers($name){
        header('Content-Type: application/octet-stream');
        header('Connection: close');
        header('Pragma:cache');
        header('Cache-Control: public, max-age=2592000');
        header('User-Cache-Control: public, max-age=2592000');
        header('Content-Transfer-Encoding: binary');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        ob_end_flush();
        ob_implicit_flush();
    }
}
$sources = json_decode(__DIR__ . '/mirrors.json', true);

$file_found = false;
foreach(scandir(__DIR__ . '/cache/') as $file){
    if(str_starts_with($file, $_GET['hash'])){
        $file_found = true;
        $cache_path = __DIR__ . '/cache/' . $file;
        break;
    }
}

// Check if File Exists
if($file_found && file_exists($cache_path)){
    $name = explode('.stuf', explode('_', $cache_path)[1])[0];
    headers($name);
    die(file_get_contents($cache_path));
}

foreach($sources as $source=>$data){
    // Find Mirrors
    if(isset($data['mirrors'])){
        $curl = curl_init($source . $data['mirrors']);
        curl_setopt($curl,  CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if($httpCode >= 200 && $httpCode < 300){
            $mirrors = json_decode($result, true)['mirrors'];
            if(is_array($mirrors))
                array_unique(array_merge($sources, $mirrors), SORT_REGULAR);
            file_put_contents(__DIR__ . '/mirrors.json', json_encode($sources));
        }
        curl_close($curl);
    }

    // Get File
    $curl = curl_init($source . $data['download'] . $_GET['hash']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
    curl_close($curl);
    if($httpCode >= 200 && $httpCode < 300) {
        $success = true;

        // Get File Name
        foreach(get_headers($source . $data['download'] . $_GET['hash']) as $header){
            if(str_starts_with($header, 'Content-Disposition:')){
                function getFilenameFromDisposition($value){
                    $value = trim($value);
                    if(strpos($value, ';') === false)
                        return null;
                    list($type, $attr_parts) = explode(';', $value, 2);
                    $attr_parts = explode(';', $attr_parts);
                    $attributes = array();
                    foreach($attr_parts as $part){
                        if(strpos($part, '=') === false)
                            continue;
                        list($key, $value) = explode('=', $part, 2);
                        $attributes[trim($key)] = trim($value);
                    }
                    $attrNames = ['filename*' => true, 'filename' => false];
                    $filename = null;
                    $isUtf8 = false;
                    foreach($attrNames as $attrName => $utf8){
                        if(!empty($attributes[$attrName])){
                            $filename = trim($attributes[$attrName]);
                            $isUtf8 = $utf8;
                            break;
                        }
                    }
                    if($filename === null)
                        return null;
                    if($isUtf8 && strpos($filename, "utf-8''") === 0 && $filename = substr($filename, strlen("utf-8''")))
                        return rawurldecode($filename);
                    if(substr($filename, 0, 1) === '"' && substr($filename, -1, 1) === '"')
                        $filename = substr($filename, 1, -1);
                    return $filename;
                }
                $name = getFilenameFromDisposition($header);
            }
        }
        if(!isset($name) || empty($name)){
            $name = explode('/', $url);
            $name = end($name);
        }
        break;
    }
}
if(isset($success)){
    if(!isset($outputed))
        headers($name);
    $outputed = true;
    echo $response;
    file_put_contents( __DIR__ . '/cache/' . $_GET['hash'] . '_' . $name . '.stuf', $response);
}else
    echo '<h1>Unable to mirror file</h1>';