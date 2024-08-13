<?php

class Common {
    /**
     * create link to url
     *
     * @param  array $data
     * @return string
     */
    function linkTo(array $data): string
    {
        if (isset($data) && is_array($data) && count($data) > 0) {
            if (!isset($data["page"]) || !file_exists(__BASE_DIR__."/pages/".$data["page"])) {
                return "#";
            }

            $urlArr = [];
            foreach ($data as $key => $value) {
                if ($key == "page") {
                    continue;
                }
                $urlArr[] = $key."=".$value;
            }
            $addParams = "";
            if (is_array($urlArr) && count($urlArr) > 0) {
                $addParams = "?".implode("&", $urlArr);
            }
            $baseUrl = $this->getBaseUrl();

            return $baseUrl."/pages/".$data["page"].$addParams;
        }//end if
               return "#";
    }//end linkTo()
}

?>