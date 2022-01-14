<html>
<body style="margin:0;padding:0;width:100vw;height:100vh;background:black;color:white">
    <?php
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
    ob_end_flush();
    ob_implicit_flush();
    $files = [];
    foreach(json_decode(file_get_contents(__DIR__ . '/mirrors.json'), true) as $mirror=>$data){
        if(!isset($data['public']))
            continue;
        $public = json_decode(@file_get_contents($mirror . $data['public']), true);
        if(is_array($public))
            $files = array_merge($files, $public);
    }
    shuffle($files);
    foreach($files as $file){
        $id = array_keys($file)[0];
        $hash = $file[$id]['hash'];
        echo $hash . '<br>';
        $file_found = false;
        foreach(scandir(__DIR__ . '/cache/') as $file){
            if(str_starts_with($file, $hash)){
                $file_found = true;
                $file = __DIR__ . '/cache/' . $file;
                break;
            }
        }
        if(!$file_found || !file_exists($file)){
            echo '<div style="color:red">File not Cached<br>';
            $_GET['hash'] = $hash;
            include 'mirror.php';
            echo '</div><br>';
        }else echo '<div style="color: lime">File Already Cached</div><br>';
    }?>
</body>
</html>