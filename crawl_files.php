<?php
http_response_code(200);
?>
<html>
<body style="margin:0;padding:0;width:100vw;height:100vh;background:black;color:white">
    <?php
    ob_end_flush();
    ob_implicit_flush();
    if(!function_exists('is_404')){
        function is_404($url) {
            $handle = curl_init($url);
            curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

            /* Get the HTML or whatever is linked in $url. */
            $response = curl_exec($handle);

            /* Check for 404 (file not found). */
            $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            curl_close($handle);

            /* If the document has loaded successfully without any redirection or error */
            if($httpCode >= 200 && $httpCode < 300)
                return false;
            else
                return true;
        }
    }
    $files = [];
    $output_disabled = true;
    foreach(json_decode(file_get_contents('mirrors.json'), true)['mirrors'] as $mirror=>$data){
        if(!is_404($mirror . $data['public'])){
            $public = json_decode(file_get_contents($mirror . $data['public']), true);
            if(is_array($public))
                $files = array_merge($files, $public);
        }
    }
    shuffle($files);
    foreach($files as $file){
        $hash = array_keys($file)[0];
        $fileid = $file[$hash][0];
        echo $hash . '<br>';
        if(!file_exists('cache/' . $hash . '.stuf')){
            echo '<div style="color:red">File not Cached<br>';
                include 'index.php';
            echo '</div><br>';
        }else echo '<div style="color: lime">File Already Cached</div><br>';
    }
    http_response_code(200);?>
</body>
</html>