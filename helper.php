<?php
/**
 * @package      ITPrism Modules
 * @subpackage   ITPShare
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-3.0.en.html GNU/GPLv3
 */

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

// no direct access
defined('_JEXEC') or die;

class ItpShareHelper
{
    protected static $loaded = array();

    /**
     * A method that make a long url to short url
     *
     * @param string    $link
     * @param Registry $params
     *
     * @return string
     */
    public static function getShortUrl($link, $params)
    {
        JLoader::register('ItpShareModuleShortUrl', __DIR__ . DIRECTORY_SEPARATOR . 'shorturl.php');

        $options = array(
            'login'   => $params->get('shortener_login'),
            'api_key' => $params->get('shortener_api_key'),
            'service' => $params->get('shortener_service'),
        );

        $shortLink = '';

        try {
            $shortUrl  = new ItpShareModuleShortUrl($link, $options);
            $shortLink = $shortUrl->getUrl();

            // Get original link
            if (!$shortLink) {
                $shortLink = $link;
            }
        } catch (Exception $e) {
            JLog::add($e->getMessage());

            // Get original link
            if (!$shortLink) {
                $shortLink = $link;
            }
        }

        return $shortLink;
    }

    /**
     * Generate a code for the extra buttons.
     * Is also replace indicators {URL} and {TITLE} with that of the article.
     *
     * @param string    $title  Article Title
     * @param string    $url    Article URL
     * @param Registry $params Plugin parameters
     *
     * @return string
     */
    public static function getExtraButtons($params, $url, $title)
    {
        $html = '';
        // Extra buttons
        for ($i = 1; $i < 6; $i++) {
            $btnName     = 'ebuttons' . $i;
            $extraButton = $params->get($btnName, '');
            if ($extraButton !== '') {
                $extraButton = str_replace('{URL}', $url, $extraButton);
                $extraButton = str_replace('{TITLE}', $title, $extraButton);
                $html .= $extraButton;
            }
        }

        return $html;
    }

    /**
     * @param Registry $params
     * @param string    $url
     * @param string    $title
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function getTwitter($params, $url, $title)
    {
        $html = '';
        if ($params->get('twitterButton')) {
            $title = htmlentities($title, ENT_QUOTES, 'UTF-8');

            // Get locale code
            if (!$params->get('dynamicLocale')) {
                $locale = $params->get('twitterLanguage', 'en');
            } else {
                $tag     = JFactory::getLanguage()->getTag();
                $locale  = str_replace('-', '_', $tag);
                $locales = self::getButtonsLocales($locale);
                $locale  = ArrayHelper::getValue($locales, 'twitter', 'en');
            }

            $html = '
             	<div class="itp-share-tw">
                	<a href="https://twitter.com/share" class="twitter-share-button" data-url="' . rawurldecode(html_entity_decode($url, ENT_COMPAT, 'UTF-8')) . '" data-text="' . $title . '" data-via="' . $params->get("twitterName") . '" data-lang="' . $locale . '" data-size="' . $params->get("twitterSize") . '" data-related="' . $params->get("twitterRecommend") . '" data-hashtags="' . $params->get("twitterHashtag") . '" data-count="' . $params->get("twitterCounter") . '">Tweet</a>';

            if ($params->get('load_twitter_library', 1)) {
                $html .= "<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>";
            }

            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param Registry $params
     * @param string $url
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function getGooglePlusOne($params, $url)
    {
        $html = '';
        if ($params->get('plusButton')) {
            // Get locale code
            if (!$params->get('dynamicLocale')) {
                $plusLocale = $params->get('plusLocale', 'en');
            } else {
                $tag     = JFactory::getLanguage()->getTag();
                $locale  = str_replace('-', '_', $tag);
                $locales = self::getButtonsLocales($locale);
                $plusLocale = ArrayHelper::getValue($locales, 'google', 'en');
            }

            $html .= '<div class="itp-share-gone">';

            $annotation = '';
            if ($params->get('plusAnnotation')) {
                $annotation = ' data-annotation="' . $params->get('plusAnnotation') . '"';
            }

            $html .= '<div class="g-plusone" data-size="' . $params->get('plusType') . '" ' . $annotation . ' data-href="' . $url . '"></div>';

            // Load the JavaScript asynchronous
            if ($params->get('loadGoogleJsLib') and !array_key_exists('google', self::$loaded)) {
                $html .= '
<script src="https://apis.google.com/js/platform.js" async defer>
  {lang: "'.$plusLocale.'"}
</script>';
                self::$loaded['google'] = true;
            }

            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param Registry $params
     * @param string $url
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function getFacebookLike($params, $url)
    {
        $html = '';
        if ($params->get('facebookLikeButton')) {
            // Get locale code
            if (!$params->get('dynamicLocale')) {
                $locale = $params->get('fbLocale', 'en_US');
            } else {
                $tag     = JFactory::getLanguage()->getTag();
                $locale  = str_replace('-', '_', $tag);
                $locales = self::getButtonsLocales($locale);
                $locale  = ArrayHelper::getValue($locales, 'facebook', 'en_US');
            }

            // Faces
            $faces = (!$params->get('facebookLikeFaces')) ? 'false' : 'true';

            // Layout Styles
            $layout = $params->get('facebookLikeType', 'button_count');

            // Generate code
            $html = '<div class="itp-share-fbl">';

            if ($params->get('facebookLoadJsLib', 1)) {
                $appId = '';
                if ($params->get('facebookLikeAppId')) {
                    $appId = '&amp;appId=' . $params->get('facebookLikeAppId');
                }

                $html .= '
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/' . $locale . '/sdk.js#xfbml=1&version=v2.7' . $appId . '";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));</script>';
            }

            $html .= '
            <div
            class="fb-like"
            data-href="' . rawurldecode(html_entity_decode($url, ENT_COMPAT, 'UTF-8')) . '"
            data-share="' . $params->get('facebookLikeShare', 0) . '"
            data-layout="' . $layout . '"
            data-width="' . $params->get('facebookLikeWidth', '450') . '"
            data-show-faces="' . $faces . '"
            data-colorscheme="' . $params->get('facebookLikeColor', 'light') . '"
            data-action="' . $params->get('facebookLikeAction', 'like') . '"';

            if ($params->get('facebookLikeFont')) {
                $html .= ' data-font="' . $params->get('facebookLikeFont') . '" ';
            }

            if ($params->get('facebookKidDirectedSite')) {
                $html .= ' data-kid-directed-site="true"';
            }

            $html .= '></div>';

            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param Registry $params
     * @param string    $url
     *
     * @return string
     */
    public static function getLinkedIn($params, $url)
    {
        $html = '';
        if ($params->get('linkedInButton')) {
            // Get locale code
            if (!$params->get('dynamicLocale')) {
                $locale = $params->get('linkedInLocale', 'en_US');
            } else {
                $tag     = JFactory::getLanguage()->getTag();
                $locale  = str_replace('-', '_', $tag);
            }

            $html = '<div class="itp-share-lin">';

            if ($params->get('load_linkedin_library', 1)) {
                $html .= '<script src="//platform.linkedin.com/in.js">lang: '.$locale.'</script>';
            }

            $html .= '<script type="IN/Share" data-url="' . rawurldecode(html_entity_decode($url, ENT_COMPAT, 'UTF-8')) . '" data-counter="' . $params->get('linkedInType', 'right') . '"></script>
            </div>
            ';
        }

        return $html;
    }

    /**
     * @param Registry $params
     * @param string    $url
     * @param string    $title
     *
     * @return string
     */
    public static function getReddit($params, $url, $title)
    {
        $html = '';
        if ($params->get('redditButton')) {
            $url   = rawurldecode(html_entity_decode($url, ENT_COMPAT, 'UTF-8'));
            $title = htmlentities($title, ENT_QUOTES, 'UTF-8');
            
            $alt   = JText::_('MOD_ITPSHARE_SUBMIT_REDDIT');

            $html .= '<div class="itp-share-reddit">';
            $redditType = $params->get('redditType');

            $jsButtons = range(1, 9);

            if (in_array($redditType, $jsButtons)) {
                $html .= '<script>
  reddit_url = "' . $url . '";
  reddit_title = "' . $title . '";
  reddit_bgcolor = "' . $params->get('redditBgColor') . '";
  reddit_bordercolor = "' . $params->get('redditBorderColor') . '";
  reddit_newwindow = "' . $params->get('redditNewTab') . '";
</script>';
            }
            switch ($redditType) {
                case 1:
                    $html .= '<script src="//www.reddit.com/static/button/button1.js"></script>';
                    break;
                case 2:
                    $html .= '<script src="//www.reddit.com/static/button/button2.js"></script>';
                    break;
                case 3:
                    $html .= '<script src="//www.reddit.com/static/button/button3.js"></script>';
                    break;
                case 4:
                    $html .= '<script src="//www.reddit.com/buttonlite.js?i=0"></script>';
                    break;
                case 5:
                    $html .= '<script src="//www.reddit.com/buttonlite.js?i=1"></script>';
                    break;
                case 6:
                    $html .= '<script src="//www.reddit.com/buttonlite.js?i=2"></script>';
                    break;
                case 7:
                    $html .= '<script src="//www.reddit.com/buttonlite.js?i=3"></script>';
                    break;
                case 8:
                    $html .= '<script src="//www.reddit.com/buttonlite.js?i=4"></script>';
                    break;
                case 9:
                    $html .= '<script src="//www.reddit.com/buttonlite.js?i=5"></script>';
                    break;
                case 10:
                    $html .= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=' . $url . '\'; return false"> <img src="//www.reddit.com/static/spreddit6.gif" alt="' . $alt . '" border="0" /> </a>';
                    break;
                case 11:
                    $html .= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=' . $url . '\'; return false"> <img src="//www.reddit.com/static/spreddit1.gif" alt="' . $alt . '" border="0" /> </a>';
                    break;
                case 12:
                    $html .= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=' . $url . '\'; return false"> <img src="//www.reddit.com/static/spreddit2.gif" alt="' . $alt . '" border="0" /> </a>';
                    break;
                case 13:
                    $html .= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=' . $url . '\'; return false"> <img src="//www.reddit.com/static/spreddit3.gif" alt="' . $alt . '" border="0" /> </a>';
                    break;
                case 14:
                    $html .= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=' . $url . '\'; return false"> <img src="//www.reddit.com/static/spreddit4.gif" alt="' . $alt . '" border="0" /> </a>';
                    break;
                case 15:
                    $html .= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=' . $url . '\'; return false"> <img src="//www.reddit.com/static/spreddit5.gif" alt="' . $alt . '" border="0" /> </a>';
                    break;
                case 16:
                    $html .= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=' . $url . '\'; return false"> <img src="//www.reddit.com/static/spreddit8.gif" alt="' . $alt . '" border="0" /> </a>';
                    break;
                case 17:
                    $html .= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=' . $url . '\'; return false"> <img src="//www.reddit.com/static/spreddit9.gif" alt="' . $alt . '" border="0" /> </a>';
                    break;
                case 18:
                    $html .= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=' . $url . '\'; return false"> <img src="//www.reddit.com/static/spreddit10.gif" alt="' . $alt . '" border="0" /> </a>';
                    break;
                case 19:
                    $html .= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=' . $url . '\'; return false"> <img src="//www.reddit.com/static/spreddit11.gif" alt="' . $alt . '" border="0" /> </a>';
                    break;
                case 20:
                    $html .= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=' . $url . '\'; return false"> <img src="//www.reddit.com/static/spreddit12.gif" alt="' . $alt . '" border="0" /> </a>';
                    break;
                case 21:
                    $html .= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=' . $url . '\'; return false"> <img src="//www.reddit.com/static/spreddit13.gif" alt="' . $alt . '" border="0" /> </a>';
                    break;
                case 22:
                    $html .= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=' . $url . '\'; return false"> <img src="//www.reddit.com/static/spreddit14.gif" alt="' . $alt . '" border="0" /> </a>';
                    break;

                default:
                    $html .= '<a href="http://www.reddit.com/submit" onclick="window.location = \'http://www.reddit.com/submit?url=' . $url . '\'; return false"> <img src="//www.reddit.com/static/spreddit7.gif" alt="' . $alt . '" border="0" /> </a>';
                    break;
            }

            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param Registry $params
     *
     * @return string
     */
    public static function getTumblr($params)
    {
        $html = '';
        if ($params->get('tumblrButton')) {
            $html .= '<div class="itp-share-tbr">';

            if ($params->get('loadTumblrJsLib')) {
                $html .= '<script src="//platform.tumblr.com/v1/share.js"></script>';
            }

            $thumlrTitle = JText::_('MOD_ITPSHARE_SHARE_THUMBLR');

            switch ($params->get('tumblrType')) {
                case 1:
                    $html .= '<a href="http://www.tumblr.com/share" title="' . $thumlrTitle . '" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:61px; height:20px; background:url(\'//platform.tumblr.com/v1/share_2.png\') top left no-repeat transparent;">' . $thumlrTitle . '</a>';
                    break;
                case 2:
                    $html .= '<a href="http://www.tumblr.com/share" title="' . $thumlrTitle . '" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:129px; height:20px; background:url(\'//platform.tumblr.com/v1/share_3.png\') top left no-repeat transparent;">' . $thumlrTitle . '</a>';
                    break;
                case 3:
                    $html .= '<a href="http://www.tumblr.com/share" title="' . $thumlrTitle . '" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:20px; height:20px; background:url(\'//platform.tumblr.com/v1/share_4.png\') top left no-repeat transparent;">' . $thumlrTitle . '</a>';
                    break;
                case 4:
                    $html .= '<a href="http://www.tumblr.com/share" title="' . $thumlrTitle . '" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:81px; height:20px; background:url(\'//platform.tumblr.com/v1/share_1T.png\') top left no-repeat transparent;">' . $thumlrTitle . '</a>';
                    break;
                case 5:
                    $html .= '<a href="http://www.tumblr.com/share" title="' . $thumlrTitle . '" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:61px; height:20px; background:url(\'//platform.tumblr.com/v1/share_2T.png\') top left no-repeat transparent;">' . $thumlrTitle . '</a>';
                    break;
                case 6:
                    $html .= '<a href="http://www.tumblr.com/share" title="' . $thumlrTitle . '" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:129px; height:20px; background:url(\'//platform.tumblr.com/v1/share_3T.png\') top left no-repeat transparent;">' . $thumlrTitle . '</a>';
                    break;
                case 7:
                    $html .= '<a href="http://www.tumblr.com/share" title="' . $thumlrTitle . '" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:20px; height:20px; background:url(\'//platform.tumblr.com/v1/share_4T.png\') top left no-repeat transparent;">' . $thumlrTitle . '</a>';
                    break;
                default:
                    $html .= '<a href="http://www.tumblr.com/share" title="' . $thumlrTitle . '" style="display:inline-block; text-indent:-9999px; overflow:hidden; width:81px; height:20px; background:url(\'//platform.tumblr.com/v1/share_1.png\') top left no-repeat transparent;">' . $thumlrTitle . '</a>';
                    break;
            }

            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param Registry $params
     * @param string    $url
     * @param string    $title
     *
     * @return string
     */
    public static function getPinterest($params, $url, $title)
    {
        $html = '';
        if ($params->get('pinterestButton')) {
            $bubblePosition = $params->get('pinterestType', 'beside');

            $divClass = (strcmp('above', $bubblePosition) === 0) ? 'itp-share-pinterest-above' : 'itp-share-pinterest';

            $html .= '<div class="' . $divClass . '">';

            if (strcmp('one', $params->get('pinterestImages', 'one')) === 0) {
                $button = 'buttonPin';
            } else {
                $button = 'buttonBookmark';
            }

            $large = '';
            $largeSize = 20;
            if ((bool)$params->get('pinterestLarge')) {
                $large = ' data-pin-tall="true" ';
                $largeSize = 28;
            }

            $url = '?url=' . rawurldecode(html_entity_decode($url, ENT_COMPAT, 'UTF-8'));
            $description = '&amp;description=' . rawurlencode($title);
            $pin = ' data-pin-count="' . $params->get('pinterestType', 'beside') . '" ';

            switch ($params->get('pinterestColor', 'gray')) {
                case 'red':
                    $dataColor = ' data-pin-color="red" ';
                    $color = '//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_red_'.$largeSize.'.png';
                    break;
                case 'white':
                    $dataColor = ' data-pin-color="white" ';
                    $color = '//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_white_'.$largeSize.'.png';
                    break;
                default: //gray
                    $dataColor = '';
                    $color = '//assets.pinterest.com/images/pidgets/pinit_fg_en_rect_gray_'.$largeSize.'.png';
                    break;
            }

            $html .= '<a href="//pinterest.com/pin/create/button/' .$url . $description .'" data-pin-do="'.$button.'" '.$pin. $dataColor .$large.'><img src="'.$color.'" /></a>';

            // Load the JS library
            if ($params->get('loadPinterestJsLib') and !array_key_exists('pinterest', self::$loaded)) {
                $html .= '<script async defer src="//assets.pinterest.com/js/pinit.js"></script>';
                self::$loaded['pinterest'] = true;
            }

            $html .= '</div>';
        }

        return $html;
    }

    /**
     * @param Registry $params
     *
     * @return string
     */
    public static function getStumbpleUpon($params)
    {
        $html = '';
        if ($params->get('stumbleButton')) {
            $html = "
            <div class=\"itp-share-su\">
            <su:badge layout='" . $params->get('stumbleType', 1) . "'></su:badge>
            </div>
            
            <script>
          (function() {
            var li = document.createElement('script'); li.type = 'text/javascript'; li.async = true;
            li.src = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//platform.stumbleupon.com/1/widgets.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(li, s);
          })();
        </script>
            ";
        }

        return $html;
    }

    /**
     * @param Registry $params
     * @param string    $url
     * @param string    $title
     *
     * @return string
     */
    public static function getBuffer($params, $url, $title)
    {
        $html = '';
        if ($params->get('bufferButton')) {
            $title = htmlentities($title, ENT_QUOTES, 'UTF-8');

            $html = '
            <div class="itp-share-buffer">
            <a href="http://bufferapp.com/add" class="buffer-add-button" data-text="' . $title . '" data-url="' . rawurldecode(html_entity_decode($url, ENT_COMPAT, 'UTF-8')) . '" data-count="' . $params->get('bufferType') . '" data-via="' . $params->get('bufferTwitterName') . '">Buffer</a><script src="//static.bufferapp.com/js/button.js"></script>
            </div>
            ';
        }

        return $html;
    }


    public static function getButtonsLocales($locale)
    {
        // Default locales
        $result = array(
            'twitter'  => 'en',
            'facebook' => 'en_US',
            'google'   => 'en'
        );

        // The locales map
        $locales = array(
            'en_US' => array(
                'twitter'  => 'en',
                'facebook' => 'en_US',
                'google'   => 'en'
            ),
            'en_GB' => array(
                'twitter'  => 'en',
                'facebook' => 'en_GB',
                'google'   => 'en_GB'
            ),
            'th_TH' => array(
                'twitter'  => 'th',
                'facebook' => 'th_TH',
                'google'   => 'th'
            ),
            'ms_MY' => array(
                'twitter'  => 'msa',
                'facebook' => 'ms_MY',
                'google'   => 'ms'
            ),
            'tr_TR' => array(
                'twitter'  => 'tr',
                'facebook' => 'tr_TR',
                'google'   => 'tr'
            ),
            'hi_IN' => array(
                'twitter'  => 'hi',
                'facebook' => 'hi_IN',
                'google'   => 'hi'
            ),
            'tl_PH' => array(
                'twitter'  => 'fil',
                'facebook' => 'tl_PH',
                'google'   => 'fil'
            ),
            'zh_CN' => array(
                'twitter'  => 'zh-cn',
                'facebook' => 'zh_CN',
                'google'   => 'zh'
            ),
            'ko_KR' => array(
                'twitter'  => 'ko',
                'facebook' => 'ko_KR',
                'google'   => 'ko'
            ),
            'it_IT' => array(
                'twitter'  => 'it',
                'facebook' => 'it_IT',
                'google'   => 'it'
            ),
            'da_DK' => array(
                'twitter'  => 'da',
                'facebook' => 'da_DK',
                'google'   => 'da'
            ),
            'fr_FR' => array(
                'twitter'  => 'fr',
                'facebook' => 'fr_FR',
                'google'   => 'fr'
            ),
            'pl_PL' => array(
                'twitter'  => 'pl',
                'facebook' => 'pl_PL',
                'google'   => 'pl'
            ),
            'nl_NL' => array(
                'twitter'  => 'nl',
                'facebook' => 'nl_NL',
                'google'   => 'nl'
            ),
            'id_ID' => array(
                'twitter'  => 'in',
                'facebook' => 'nl_NL',
                'google'   => 'in'
            ),
            'hu_HU' => array(
                'twitter'  => 'hu',
                'facebook' => 'hu_HU',
                'google'   => 'hu'
            ),
            'fi_FI' => array(
                'twitter'  => 'fi',
                'facebook' => 'fi_FI',
                'google'   => 'fi'
            ),
            'es_ES' => array(
                'twitter'  => 'es',
                'facebook' => 'es_ES',
                'google'   => 'es'
            ),
            'ja_JP' => array(
                'twitter'  => 'ja',
                'facebook' => 'ja_JP',
                'google'   => 'ja'
            ),
            'nn_NO' => array(
                'twitter'  => 'no',
                'facebook' => 'nn_NO',
                'google'   => 'no'
            ),
            'ru_RU' => array(
                'twitter'  => 'ru',
                'facebook' => 'ru_RU',
                'google'   => 'ru'
            ),
            'pt_PT' => array(
                'twitter'  => 'pt',
                'facebook' => 'pt_PT',
                'google'   => 'pt'
            ),
            'pt_BR' => array(
                'twitter'  => 'pt',
                'facebook' => 'pt_BR',
                'google'   => 'pt'
            ),
            'sv_SE' => array(
                'twitter'  => 'sv',
                'facebook' => 'sv_SE',
                'google'   => 'sv'
            ),
            'zh_HK' => array(
                'twitter'  => 'zh-tw',
                'facebook' => 'zh_HK',
                'google'   => 'zh_HK'
            ),
            'zh_TW' => array(
                'twitter'  => 'zh-tw',
                'facebook' => 'zh_TW',
                'google'   => 'zh_TW'
            ),
            'de_DE' => array(
                'twitter'  => 'de',
                'facebook' => 'de_DE',
                'google'   => 'de'
            ),
            'bg_BG' => array(
                'twitter'  => 'en',
                'facebook' => 'bg_BG',
                'google'   => 'bg'
            ),
        );

        if (array_key_exists($locale, $locales)) {
            $result = $locales[$locale];
        }

        return $result;
    }

    /**
     * @param Registry $params
     * @param string    $url
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function getGoogleShare($params, $url)
    {

        $html = '';
        if ($params->get('gsButton')) {
            // Get locale code
            if (!$params->get('dynamicLocale')) {
                $gshareLocale = $params->get('gsLocale', 'en');
            } else {
                $tag     = JFactory::getLanguage()->getTag();
                $locale  = str_replace('-', '_', $tag);
                $locales = self::getButtonsLocales($locale);
                $gshareLocale  = ArrayHelper::getValue($locales, 'google', 'en');
            }

            $html .= '<div class="itp-share-gshare">';

            $annotation = '';
            if ($params->get('gsAnnotation')) {
                $annotation = ' data-annotation="' . $params->get('gsAnnotation') . '"';
            }

            $size = '';
            if ($params->get('gsType') !== 'vertical-bubble') {
                $size = ' data-height="' .$params->get('gsType') . '"';
            }

            $html .= '<div class="g-plus" data-action="share" ' . $annotation . $size . ' data-href="' . $url . '"></div>';

            // Load the JavaScript asynchronous.
            if ($params->get('loadGoogleJsLib') and !array_key_exists('google', self::$loaded)) {
                $html .= '<script type="text/javascript">';
                if ($gshareLocale) {
                    $html .= ' window.___gcfg = {lang: "' . $gshareLocale . '"}; ';
                }
                $html .= '
  (function() {
    var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
    po.src = \'https://apis.google.com/js/platform.js\';
    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
  })();
                </script>';
            }

            $html .= '</div>';
        }

        return $html;
    }
}
