<?php
    /**
    *
    * MetaGeter
    * > get any metatag from site Director or Content HTML
    * @param meta 			<metatage name Like <title|description|keywords|article:title|article:auther| etc .... >
    * @param WS_URL    		 site URL
    * @param WSContentHTML   HTML Site Content
    *
    * @return mixed
    */
    function MetaGeter($meta = 'title', $WS_URL = null, $WSContentHTML = null)
    {
        
        // start handle HTML Content
        if ( $WS_URL === null &&  $WSContentHTML === null)
            return 'where is site content my Hero ?! ';
        
        if ( $WSContentHTML === null && null != $WS_URL)
            $WSContentHTML = getContentFromURL($WS_URL);
        // ./ end HTML
        
        // start Xpath
            $document = new \DOMDocument('1.0', 'UTF-8');
            $internalErrors = libxml_use_internal_errors(true);   // set error level
            $document->loadHTML($WSContentHTML);
            $xpath       = new \DOMXPath($document);
            
            // get data
            switch( $meta )
            {
                case 'title' :
                    $Matched = $document->getElementsByTagName('title');
                break;
                
                default:
                   if ( strpos($meta, 'og:') !== false ){
                        $Matched = $xpath->query("//meta[@property='".$meta."']/@content");
                   }else{
                        $Matched = $xpath->query("//meta[@name='".$meta."']/@content");
                   }
                break;
            }

            if ( is_object($Matched->item(0)) )
                return utf8_decode($Matched->item(0)->nodeValue);
            else
                return null;
    }
    
    /**
    * getContentFromURL
    * > get site content via CURL 
    * @param URL
    * @param referer
    * @return mixed
    */
    function getContentFromURL ($url, $referer = '')
    {
        if ( ! empty($url) )
        {
            $parse = parse_url($url);
            $ch    = curl_init();

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            if(!empty($referer))
                curl_setopt ($ch, CURLOPT_REFERER, $referer);
                
            $data     = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($httpCode != 200)
                return false;

            curl_close($ch);
            return  ($data);
        }
    } 
?>