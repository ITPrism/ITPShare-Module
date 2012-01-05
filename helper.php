<?php
/**
 * @package      ITPrism Modules
 * @subpackage   ITPShare
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * ITPShare is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class ItpShareHelper{
    
    /**
     * Generate a code for the extra buttons
     */
    public static function getExtraButtons($params, $url, $title) {
        
        $html  = "";
        // Extra buttons
        for($i=1; $i < 6;$i++) {
            $btnName = "ebuttons" . $i;
            $extraButton = $params->get($btnName, "");
            if(!empty($extraButton)) {
                $extraButton = str_replace("{URL}", $url,$extraButton);
                $extraButton = str_replace("{TITLE}", $title,$extraButton);
                $html  .= $extraButton;
            }
        }
        
        return $html;
    }
    
    public static function getTwitter($params, $url, $title){
        
        $html = "";
        if($params->get("twitterButton")) {
            $html = '
            <div class="itp-share-mod-tw">
            <a href="http://twitter.com/share" class="twitter-share-button" data-url="' . $url . '" data-text="' . $title . '" data-count="' . $params->get("twitterCounter") . '" data-via="' . $params->get("twitterName") . '" data-lang="' . $params->get("twitterLanguage") . '">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
            </div>
            ';
        }
        
        return $html;
    }
    
    public static function getGooglePlusOne($params, $url, $title){
        $type = "";
        $language = "";
        if($params->get("plusType")) {
            $type = 'size="' . $params->get("plusType") . '"';
        }
        
        if($params->get("plusLocale")) {
            $language = " {lang: '" . $params->get("plusLocale") . "'}";
        }
            
        $html = "";
        if($params->get("plusButton")) {
            $html = '
            <div class="itp-share-mod-gone">
            <!-- Place this tag in your head or just before your close body tag -->
            <script type="text/javascript" src="http://apis.google.com/js/plusone.js">' . $language . '</script>
            <!-- Place this tag where you want the +1 button to render -->
            <g:plusone ' . $type . ' href="' . $url . '"></g:plusone>
            </div>
            ';
        }
        
        return $html;
    }
    
    public static function getFacebookLike($params, $url, $title){
        
        if($params->get("fbDynamicLocale", 0)) {
            $fbLocale = JFactory::getLanguage();
            $fbLocale = $fbLocale->getTag();
            $fbLocale = str_replace("-","_",$fbLocale);
        } else {
            $fbLocale = $params->get("fbLocale", "en_US");
        }
        
        $html = "";
        if($params->get("facebookLikeButton")) {
            
            $faces = (!$params->get("facebookLikeFaces")) ? "false" : "true";
            
            $layout = $params->get("facebookLikeType","button_count");
            if(strcmp("box_count", $layout)==0){
                $height = "80";
            } else {
                $height = "25";
            }
            
            if(!$params->get("facebookLikeRenderer")){ // iframe
                $html = '
                <div class="itp-share-mod-fbl">
                <iframe src="http://www.facebook.com/plugins/like.php?';
                
                if($params->get("facebookLikeAppId")) {
                    $html .= 'app_id=' . $params->get("facebookLikeAppId"). '&amp;';
                }
                
                $html .= '
                href=' . rawurlencode($url) . '&amp;
                send=' . $params->get("facebookLikeSend",0). '&amp;
                locale=' . $fbLocale . '&amp;
                layout=' . $layout . '&amp;
                show_faces=' . $faces . '&amp;
                width=' . $params->get("facebookLikeWidth","450") . '&amp;
                action=' . $params->get("facebookLikeAction",'like') . '&amp;
                colorscheme=' . $params->get("facebookLikeColor",'light') . '&amp;
                height='.$height;
                if($params->get("facebookLikeFont")){
                    $html .= "&amp;font=" . $params->get("facebookLikeFont");
                }
                $html .= '
                " scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:' . $params->get("facebookLikeWidth", "450") . 'px; height:' . $height . 'px;" allowTransparency="true"></iframe>
                </div>
                ';
            } else {//XFBML
                $html = '<div class="itp-share-mod-fbl">';
                
                if($params->get("facebookRootDiv",1)) {
                    $html .= '<div id="fb-root"></div>';
                }
                
               if($params->get("facebookLoadJsLib", 1)) {
                    $html .= '<script src="http://connect.facebook.net/' . $fbLocale . '/all.js#';
                    if($params->get("facebookLikeAppId")){
                        $html .= 'appId=' . $params->get("facebookLikeAppId"). '&amp;'; 
                    }
                    $html .= 'xfbml=1"></script>';
                }
                
                $html .= '
                <fb:like href="' . $url . '" 
                layout="' . $layout . '" 
                show_faces="' . $faces . '" 
                width="' . $params->get("facebookLikeWidth","450") . '" 
                colorscheme="' . $params->get("facebookLikeColor","light") . '"
                send="' . $params->get("facebookLikeSend",0). '" 
                action="' . $params->get("facebookLikeAction",'like') . '" ';
                
                if($params->get("facebookLikeFont")){
                    $html .= 'font="' . $params->get("facebookLikeFont") . '"';
                }
                $html .= '></fb:like>
                </div>
                ';
            }
        }
        
        return $html;
    }
    
    public static function getDigg($params, $url, $title){
        $title = html_entity_decode($title,ENT_QUOTES, "UTF-8");
        
        $html = "";
        if($params->get("diggButton")) {
            
            $html = '
            <div class="itp-share-mod-digg">
            <script type="text/javascript">
(function() {
var s = document.createElement(\'SCRIPT\'), s1 = document.getElementsByTagName(\'SCRIPT\')[0];
s.type = \'text/javascript\';
s.async = true;
s.src = \'http://widgets.digg.com/buttons.js\';
s1.parentNode.insertBefore(s, s1);
})();
</script>
<a 
class="DiggThisButton '.$params->get("diggType","DiggCompact") . '"
href="http://digg.com/submit?url=' . rawurlencode($url) . '&amp;title=' . rawurlencode($title) . '">
</a>
            </div>
            ';
        }
        
        return $html;
    }
    
    public static function getStumbpleUpon($params, $url, $title){
        
        $html = "";
        if($params->get("stumbleButton")) {
            
            $html = '
            <div class="itp-share-mod-su">
            <script src="http://www.stumbleupon.com/hostedbadge.php?s=' . $params->get("stumbleType",1). '&r=' . rawurlencode($url) . '"></script>
            </div>
            ';
        }
        
        return $html;
    }
    
    public static function getLinkedIn($params, $url, $title){
        
        $html = "";
        if($params->get("linkedInButton")) {
            
            $html = '
            <div class="itp-share-mod-lin">
            <script type="text/javascript" src="http://platform.linkedin.com/in.js"></script><script type="in/share" data-url="' . $url . '" data-counter="' . $params->get("linkedInType",'right'). '"></script>
            </div>
            ';
        }
        
        return $html;
    }
    
    public static function getBuzz($params, $url, $title){
        
        $html = "";
        if($params->get("buzzButton")) {
            
            $html = '
            <div class="itp-share-mod-buzz">
            <a title="Post to Google Buzz" class="google-buzz-button" 
            href="http://www.google.com/buzz/post" 
            data-button-style="' . $params->get("buzzType","small-count"). '" 
            data-url="' . $url . '"
            data-locale="' . $params->get("buzzLocale", "en") . '"></a>
<script type="text/javascript" src="http://www.google.com/buzz/api/button.js"></script>
            </div>
            ';
        }
        
        return $html;
    }

    public static function getReTweetMeMe($params, $url, $title){
        
        $html = "";
        if($params->get("retweetmeButton")) {
            
            $html = '
            <div class="itp-share-mod-retweetme">
            <script type="text/javascript">
tweetmeme_url = "' . $url . '";
tweetmeme_style = "' . $params->get("retweetmeType") . '";
tweetmeme_source = "' . $params->get("twitterName") . '";
</script>
<script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>
            </div>';
        }
        
        return $html;
    }
    
    
    public static function getYahooBuzz($params, $url, $title){
        
        $html = "";
        if($params->get("yahooBuzzButton")) {
            
            $html = '
            <div class="itp-share-mod-yahoobuzz">
            <script type="text/javascript" src="http://d.yimg.com/ds/badge2.js" badgetype="'.$params->get("yahooBuzzType").'">' . $url . '</script>
            </div>';
        }
        
        return $html;
    }
    
    public static function getFacebookShareMe($params, $url, $title){
            
            $html = "";
            if($params->get("facebookShareMeButton")) {
                
                $html = '
                <div class="itp-share-mod-fbsh">
                <script>var fbShare = {
    url: "' . $url . '",
    title: "' . $title . '",
    size: "' . $params->get("facebookShareMeType","large"). '",
    badge_text: "' . $params->get("facebookShareMeBadgeText","C0C0C0"). '",
    badge_color: "' . $params->get("facebookShareMeBadge","CC00FF"). '",
    google_analytics: "false"
    }</script>
    <script src="http://widgets.fbshare.me/files/fbshare.js"></script>
                </div>
                ';
            }
            
            return $html;
        }
}