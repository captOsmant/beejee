<?php
namespace Classes\Utils;
class GDUtil{

    public static function convertToBlob($dataURL){
        $dt = explode(",",$dataURL);
        $type = $dt[0];
        $type = explode(";",$type);
        $type = explode(":",$type[0]);
        $type = $type[1];
        $dt = $dt[1];
        $data = base64_decode($dt);
        return ["type" => $type, "data"=>$data, "size"=>getimagesize($dataURL)];
    }

    const MAX_WIDTH = 320;
    const MAX_HEIGHT = 240;


    public static function minimize($data){
        $image = imagecreatefromstring($data['data']);
        $type = $data['type'];
       
        $wh = $data['size'][0] / $data['size'][1];
        $w = $data['size'][0];
        $h = $data['size'][1];
        
        
        if($wh > self::MAX_WIDTH/self::MAX_HEIGHT){
            $W = 320;
            $H = round($W / $wh);
        }else{
            $H = 240;
            $W = round($H*$wh);
        }

        
        $dest = imagecreatetruecolor($W, $H);
        imagecopyresized($dest, $image, 0, 0, 0, 0, $W, $H, $w, $h);

        
        ob_start();
        switch($type){
            case "image/jpeg":
                imagejpeg($dest);
                break;
            case "image/png":
                imagepng($dest);
                break;
            case "image/gif":
                imagegif($dest);
                break;
        }
        
        $data = ob_get_contents();
        ob_clean();

        return "data:".$type.";base64,".base64_encode($data);

        


    }
}