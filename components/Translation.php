<?php
class Translation
{

    protected $key;
    protected $detectUrl;
    protected $translateUrl;

    /**
     * Translation constructor.
     * @param $type type of chosen translation service
     */

    private function __construct($type) {
        include_once('config/translation.php');
        if(array_key_exists($type, $trConfig)){
            if(self::checkKey($trConfig[$type]['key'])){
                $this->key = $trConfig[$type]['key'];
                $this->detectUrl = $trConfig[$type]['DETECT_URL'];
                $this->translateUrl = $trConfig[$type]['TRANSLATE_URL'];
            }else{
                throw new InvalidConfigException("Key is required");
            }
        }else{
            throw new InvalidConfigException("Key is required");
        }
    }

    /**
     * @param $key string with key for translation service
     * @return mixed $key string with key for translation service or false if $key is empty
     */
    static public function checkKey($key) {
        if(empty($key)){
            return false;
        }else{
            return $key;
        }
    }

    /**
     * @param $param string parameter from $_GET
     * @return string
     */
    static public function escapeGetParam($param) {
        $safeParam = strip_tags($param);
        $safeParam = htmlentities($param, ENT_QUOTES, "UTF-8");
        $safeParam = htmlspecialchars($param, ENT_QUOTES);

        return $safeParam;
    }

    /**
     * @param string $format text format
     * @return mixed
     */
    public function translate_text($format = "text") {
        $text = self::escapeGetParam($_GET['text']);
        $lang = self::escapeGetParam($_GET['lang']);

        $values = array(
            'key' => $this->key,
            'text' => $text,
            'lang' => $lang,
            'format' => $format == "text" ? "plain" : $format
        );

        $formData = http_build_query($values);

        $ch = curl_init($this->translateUrl);
        curl_setopt($ch, CURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        if($data['code'] == 200){
            return $data['text'];
        }else{
            return $data;
        }
    }
}