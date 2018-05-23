<?php
class InstagramScrapper {
    private $url;
    public function __construct($par_url) {
        $this->url = $par_url;
    }

    public function post_to_api() {
        $opts = array('http' =>
        array(
            'method'  => 'GET',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
        )
    );
    $context  = stream_context_create($opts);
    $result = file_get_contents($this->url, false, $context);
     
     return $result;
    }

    public function fetch_shortcodes() {
        libxml_use_internal_errors(true);

        $data = $this->post_to_api();
        $dom = new DOMDocument();
        $dom->loadHTML($data);

        $classname = "_mck9w _gvoze  _tn0ps";
    }
}
?>