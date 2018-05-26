<?php
class InstagramScrapper {
    private $shortcode_list;
    /* generate an array of shortcodes */
    public function __construct() {
        $this->shortcode_list = array("BjCPMmGF3xh", "BihtPRnFTZl", 
        "BheksGclKnK", "BhL2uXKHE4M", "BgZA-1vja95", "Bdux68VHt_Q", "BZoLp1KlE2h");
    }

    public function generate_random() {
        return array_rand($this->shortcode_list, 5);
    }

    public function get_json_data() {
        $chosen_shortcodes = $this->generate_random();
        $curl = curl_init();
        for ($i = 0; $i < 5; $i++) {
            curl_setopt_array($curl, 
            array(
                CURLOPT_URL =>  "https://www.instagram.com/p/".$this->shortcode_list[$chosen_shortcodes[$i]]."/?__a=1",
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => true
                ) 
            );
            $result = curl_exec($curl);
            $result = json_decode($result, true);
            $data = array(
                "picture" => $result["graphql"]["shortcode_media"]["display_url"],
                "likes" => $result["graphql"]["shortcode_media"]["edge_media_preview_like"]["count"],
                "caption" => $result["graphql"]["shortcode_media"]["edge_media_to_caption"]["edges"][0]["node"]["text"],
                "shortcode" => $this->shortcode_list[$chosen_shortcodes[$i]]
            );
            $data_array[] = $data;
        }
     curl_close($curl);
     
     return $data_array;
    }
}
?>