<?php


namespace Absoft\Line\Core\HTTP;

use Absoft\Line\Core\FaultHandling\Errors\FileNotFound;

class FileResponse extends Response {

    /**
     * @var string
     */
    public $type;
    public $location;
    public $download;

    public function __construct() {
        parent::__construct("file");
    }

    /**
     * @var string[][]
     */
    public static $extensions = [
        "images" => ["png", "jpg", "jpeg", "ico", "svg", "gif"],
        "video" => ["mp4", "mkv", "3gp", "mov"],
    ];

    /**
     * @param $file
     * file can be file absolute location to a file needs to be responded
     * or it can be file array containing file content and other file information
     * organized in the array as follows
     * [
     *      "extension" => "file extension",
     *      "content" => "file content",
     *      "size" => "file size in bites"
     * ]
     *
     * this method should be used to force download of file.
     * @throws FileNotFound
     */
    static function fileDownload($file){

        self::setHeader(true);

        if(is_array($file)){

            header('Content-Disposition: attachment; filename="'.$file["name"].'"');
            header('Content-Length: ' . $file["size"]);
            flush(); // Flush system output buffer
            print $file["content"];

        }else{

            if(file_exists($file)){

                header('Content-Disposition: attachment; filename="'.basename($file).'"');
                header('Content-Length: ' . filesize($file));
                flush(); // Flush system output buffer
                readfile($file);

            }else{
                throw new FileNotFound($file, __FILE__, __LINE__);
            }

        }

    }

    /**
     * @param $file
     * file can be file absolute location of a file to be responded
     * or it can be file array containing file content and other file information
     * organized in the array as follows
     * [
     *      "extension" => "file extension",
     *      "content" => "file content",
     *      "size" => "file size in bites"
     * ]
     *
     * this method should be used for file streaming purpose.
     * @throws FileNotFound
     */
    static function fileContent($file){

        self::setHeader(false);

        if(is_array($file)){

            if(in_array(strtolower($file["extension"]), self::$extensions["images"])){
                header("Content-type: image/".$file["extension"]);
            }else{
                header("Content-type: application/".$file["extension"]);
            }

            header('Content-Length: ' . $file["size"]);
            print $file["content"];

        }else{

            if(file_exists($file)){

                $file_name = basename($file);
                $arr = explode(".", $file_name);
                $extension = (sizeof($arr) > 1) ? $arr[sizeof($arr) - 1] : "";
                if(in_array(strtolower($extension), self::$extensions["images"])){
                    header("Content-type: image/".$extension);
                }else {
                    header("Content-type: application/".$extension);
                }

                header('Content-Length: ' . filesize($file));
                print file_get_contents($file);

            }else{
                throw new FileNotFound($file, __FILE__, __LINE__);
            }

        }

    }

    private static function setHeader(bool $download){

        header("Provider: Absoft");
        header("Access-Control-Allow-Origin: *");

        if($download){
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
        }

    }

    function prepare($location, $download = false){
        $this->location = $location;
        $this->download = $download;
    }

    /**
     * @throws FileNotFound
     */
    function respond(){

        if($this->download){
            self::fileDownload($this->location);
        }else{
            self::fileContent($this->location);
        }

    }

    function getResponse(){
        return $this;
    }

}