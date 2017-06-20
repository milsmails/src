<?php
namespace Fot\Bundle\ElvisConnectorBundle\Curl;

class Curl extends \Twig_Extension
{
    private $host;
    private $login;
    private $pwd;
    private $curl;
    private $nb = 0;

    public function getFunctions()
    {
        return array(
            'getElvisCategorie' => new \Twig_Function_Method($this, 'listCategories')
        );
    }

    public function getName()
    {
        return 'Curl';
    }

    public function __construct($host, $login, $pwd)
    {
        $this->host = $host;
        $this->login = $login;
        $this->pwd = $pwd;
        $this->connection();
    }

    public function searchAsset($key)
    {

        if (!curl_errno($this->curl)) {
            $curl = $this->curl;
            $url2 = "$this->host/services/search?q=$key&metadataToReturn=id,assetType,assetPath,name";
            curl_setopt($curl, CURLOPT_URL, str_replace (' ', '%20',$url2));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($curl);
            $result = json_decode($result);
            $cred = base64_encode("$this->login:$this->pwd");
            $result->cred = $cred;
            return $result;
        } else return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

    }

    public function listCategorieContent($path = false,$page = false,$perPage = false,$assetName = false,$filterType = false,$assetNameFilterType)
    {
        $pageFilter = "";
        if ($page && $perPage) $pageFilter = "&start=$page&num=$perPage";
        else $pageFilter ="&start=0&num=25";
        $filterType = $filterType ? " AND assetType:$filterType" : "";
        //filter name
		if($assetName)
		    switch ($assetNameFilterType) {
                case "4" :
                    $assetName = "filename:" . $assetName . "*&" ;
                    break;
                default :
                    $assetName = "filename:*" . $assetName . "*&" ;
            }
		//
        if (!$path || $path=="/" || $path == -2) $path = "/*";
        if (!curl_errno($this->curl)) {
            $curl = $this->curl;
            $url2 = $this->host."/services/search?q=".$assetName."ancestorPaths:$path$filterType&metadataToReturn=assetType,assetPath,name,assetFileModified,assetCreator,assetDomain$pageFilter";
            //return $url2;
            curl_setopt($curl, CURLOPT_URL, str_replace (' ', '%20',$url2));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($curl);
            $result = json_decode($result);
            $cred = base64_encode("$this->login:$this->pwd");
            $result->cred = $cred;
            //$result->url = $url2;
            return $result;
        } else return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

    }
    public function getAssets($q) {
        if (!curl_errno($this->curl)) {
            $curl = $this->curl;
            $url2 = $this->host."/services/search?q=$q";
            curl_setopt($curl, CURLOPT_URL, str_replace (' ', '%20',$url2));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            $result = json_decode($result);
            $cred = base64_encode("$this->login:$this->pwd");
            $result->cred = $cred;
            $result->url = $url2;
            return $result;
        } else return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

    }




   public function listCategories($path = false)
    {

        if ($path == false) $path = "";
        if (!curl_errno($this->curl)) {
            $curl = $this->curl;
            $url2 = $this->host . "/services/browse?path=$path";
            curl_setopt($curl, CURLOPT_URL, $url2);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($curl);
            $result = json_decode($result);
            $cred = base64_encode("$this->login:$this->pwd");
            return $result;
        } else return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

    }

    public function test($path = false,$page = false,$perPage = false)
    {

        if ($path == false) $path = "/";

        $pageFilter = "";
        if ($page && $perPage) $pageFilter = "&start=$page&num=$perPage";
        if ($path == false) $path = "/";
        if (!curl_errno($this->curl)) {
            $curl = $this->curl;
            $url2 = "$this->host/services/search?q=ancestorPaths:$path&metadataToReturn=assetType,assetPath,name,assetFileModified,assetCreator$pageFilter";
            curl_setopt($curl, CURLOPT_URL, $url2);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        }
        return  $url2 . "\n";

    }


    public function connection()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "username=$this->login&password=$this->pwd");
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_URL, "http://172.27.63.140:8080/services/login");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($curl,CURLOPT_COOKIESESSION, true);
        $cookies_file = __DIR__ . '/cookies.txt';
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookies_file);
        $result = curl_exec($curl);
        $this->curl = $curl;
    }


}


?>