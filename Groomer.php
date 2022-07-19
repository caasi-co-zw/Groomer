<?php
namespace Caasi\Groomer;

if(!class_exists('WP')):
    session_start();
endif;

/**
 * Server name in this format caasi.co.zw
 * @var String
 */
define('SERVER_NAME', $_SERVER['SERVER_NAME']);

/**
 * Returns the domain name
 * @var String
 */
if(!defined('APP_NAME')){
    define('APP_NAME', ucwords(explode('.', SERVER_NAME)[0]));
}

/**
 * Homepage url of the site in this format https://caasi.co.zw
 * @var String
 */
define('WEBSITE_HOME', ((isset($_SERVER['HTTPS']) && "on" === $_SERVER['HTTPS']) ? 'https://' : 'http://') . SERVER_NAME);

/**
 * Returns the current opened page url in this format https://caasi.co.zw/websites
 * @var String
 */
define('CURRENT_PAGE', WEBSITE_HOME . $_SERVER['REQUEST_URI']);

/**
 * Returns true if on a wordpress website.
 * @var Bool
 */
define('IS_WORDPRESS', class_exists('WP')&&function_exists('wp_enqueue_script')); 

/**
 * Returns set page title
 * @var String
 */
if(!defined("PG_TITLE")):
    define('PG_TITLE', "Caasi Groomer");
endif;

if(!defined('DATABASE_FILE')){
    define('DATABASE_FILE',__DIR__.'/database.php');
}

if (file_exists(DATABASE_FILE)) {
    include DATABASE_FILE;
}


/**
 * Prepares and prints the markup to the browser.
 * 
 * Has been optimized to be SEO friendly and brotli optimezd. Most configurations are saved in the config.php file.
 * @author Caasi <info@caasi.co.zw>
 * @copyright GNU
 * @link www.caasi.co.zw
 * @method setTitle
 * @method setAuthor
 * @method setKeywords
 * @method setSeo
 * @method setExcerpt
 */
class Groomer
{
    private $sys_name         = 'Caasi Groover';
    private $developer         = 'Caasi';
    private $developer_url         = 'www.caasi.co.zw';
    protected $db;
    protected $title = false;
    protected $keywords = array();
    protected $details;
    protected $appname;
    protected $author         = 'Caasi';
    protected $favicon        = '/img/logo.png';
    protected $favicon_type = 'image/png';
    protected $post_images;
    protected $post_images_alt;
    protected $default_styles = [];
    protected $text_dir ='ltr';
    protected $language ='en-ZW';
    protected $tw_card ='summary_large_image';
    protected $triggers = array();
    protected $schema;
    /**
     * Accepts an array with the following structure
     * @var array
     */
    protected $default_scripts = [];
    protected $facebook_id;
    /**
     * @var array
     */
    protected $mobiledetect;
    protected $footer_scripts;
    protected $charset = 'utf-8';
    protected $robots      = "index";
    protected $theme_color = "#a70c0c";
    protected $style       = null;
    protected $translate   = false;
    protected $compress_output = true;
    protected $version;
    protected $manifest;
    protected $fonts = array();
    protected $gtag = array();
    protected $seo = true;
    protected $tld;
    protected $body_open = false;

    public $theme;
    public $ismobile;
    public $isproduction;
    public $sitename;

    /**
     * Returns a well formated page title
     */
    public function getTitle()
    {
        if($this->title){
            return $this->title . " " . html_entity_decode("&ndash;") . " " . $this->appname;
        }
        return wp_title('&raquo;',false);
    }
    protected function getDetails()
    {
        if(!empty($this->details)){
            return $this->details;
        }
        if(IS_WORDPRESS){
            if(!empty(get_the_excerpt())){
                return get_the_excerpt();
            }
            return get_bloginfo('description');
        }
        return $this->getTitle();
    }
    protected function getAuthor()
    {
        return $this->author;
    }
    public function getFavicon()
    {
        return $this->favicon;
    }
    protected function getThemeColor()
    {
        return $this->theme_color ?? '#003883';
    }
    protected function getStyles(int $index = null)
    {
        if (null === $index) {
            return $this->default_styles;
        } else {
            return $this->default_styles[$index]??null;
        }
    }
    protected function getScripts(int $index = null)
    {
        if (null === $index) {
            return $this->default_scripts;
        } else {
            return $this->default_scripts[$index];
        }
    }
    protected function getRobots()
    {
        return $this->robots;
    }
    protected function getTranslate()
    {
        return $this->translate;
    }
    protected function getKeywords()
    {
        $kw = '';
        $count = 1;
        if(is_array($this->keywords)){
            foreach($this->keywords as $word){
                if(empty($word)){
                    continue;
                }
                if($count > 1){
                    $kw .= ',';
                }
                $kw .= $word;
                $count++;
            }
            return $kw;
        }
        return $this->keywords;
    }
    protected function getPostImage(){
        if(IS_WORDPRESS){
            if(has_post_thumbnail()){
                $this->post_images_alt = the_post_thumbnail_caption();
                return the_post_thumbnail_url();
            }
            if(has_site_icon()){
                $this->post_images_alt = get_site_icon_url();
                return get_site_icon_url();
            }
            return $this->post_images;
        }
        return WEBSITE_HOME.'/'.$this->post_images;
    }
    protected function sanitizer($buffer)
    {
        $search = array(
            '/\>[^\S ]+/s',
            '/[^\S ]+\</s',
            '/(\s)+/s',
            '/<!--(.|\s)*?-->/',
            '/^([\t ])+/m',
            '/([\t ])+$/m',
            '/\)[\r\n\t ]?{[\r\n\t ]+/s',
            '/}[\r\n\t ]+/s',
            '/([\t ])+/s',
            '/\>[\r\n\t ]+\</s',
        );
        $replace = array(
            '>',
            '<',
            '\\1',
            '',
            '',
            '',
            '){',
            '{',
            ' ',
            '><',
        );
        $buffer = preg_replace($search, $replace, $buffer);
        return $buffer;
    }
    public function __construct(array $config, $callback=null)
    {
        $this->post_images_alt = 'Our logo';
        if(IS_WORDPRESS):
            defined( 'ABSPATH' ) || exit;
            $this->theme = wp_get_theme();
            $this->appname = get_bloginfo('name');
            $this->text_dir = is_rtl()?'rtl':'ltr';
            $this->charset = get_bloginfo('charset');
            $this->version = $this->theme->get('Version');
            $this->language = get_bloginfo('language');
            $this->ismobile = wp_is_mobile();
            if(has_site_icon()):
                $this->favicon = get_site_icon_url();
            endif;
            $this->setKeywords(
                array(
                    $this->sys_name,
                )
            );
        endif;
        $this->sitename = &$this->appname;
        $this->isproduction = $this->isProductionServer();
        $keys      = array_keys($config);
        for ($i = 0; $i < count($keys); $i++) {
            switch ($keys[$i]) {
                case 'title':
                    $this->title = $config['title'];
                    break;
                case 'author':
                    $this->author = ucwords($config['author']);
                    break;
                case 'details':
                    $this->details = $config['details'];
                    break;
                case 'keywords':
                    if (is_array($config['keywords'])) {
                        $this->keywords = '';
                        for ($j = 0; $j < count($config['keywords']); $j++) {
                            $this->keywords .= ($config['keywords'][$j]);
                            if ($j + 1 !== count($config['keywords'])) {
                                $this->keywords .= ',';
                            }
                        }
                    } else {
                        $this->keywords = ($config['keywords']);
                    }
                    break;
                case 'fonts':
                    if (is_array($config['fonts'])) {
                        for ($j = 0; $j < count($config['fonts']); $j++) {
                            $this->fonts[] = $config['fonts'][$j];
                        }
                    } else {
                        $this->fonts[] = $config['fonts'];
                    }
                    break;
                case 'css':
                    if ($config['css']) {
                        if (is_array($config['css'])) {
                            for ($j = 0; $j < count($config['css']); $j++) {
                                $this->default_styles[] = $config['css'][$j];
                            }
                        } else {
                            $this->default_styles = array_merge(array($config['css']), $this->default_styles);
                        }
                    }
                    break;
                case 'js':
                case 'footer_js':
                    if (is_array($config['js'])) {
                        for ($j = 0; $j < count($config['js']); $j++) {
                            $this->footer_scripts[] = $config['js'][$j];
                        }
                    } else {
                        $this->footer_scripts[] = $config['js'];
                    }
                    break;
                case 'header_js':
                    if (is_array($config['header_js'])) {
                        for ($j = 0; $j < count($config['header_js']); $j++) {
                            $this->default_scripts[] = $config['header_js'][$j];
                        }
                    } else {
                        $this->default_scripts[] = $config['header_js'];
                    }
                    break;
                case 'favicon':
                    $this->favicon = $config['favicon'];
                    break;

                case 'menu':
                    $this->menu = $config['menu'];
                    break;
                case 'style':
                    $this->style = $config['style'];
                    break;
                case 'robots':
                    $this->robots = $config['robots'];
                    break;
                case 'post_image':
                    $this->post_image = $config['post_image'];
                    break;
                default:
                    # log to errors
                    $this->errors[] = $keys[$i] . ' was not recognized.';
                    break;
            }
        }
        header('X-Powered-By: Caasi');
        if($this->compress_output):
            ob_start([$this, "sanitizer"]);
        endif;
        if($callback && is_callable($callback)):
            call_user_func($callback);
        endif;
        return $this;
    }
    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }
    public function setSchema($schema)
    {
        $this->schema = $schema;
        return $this;
    }
    public function setDatabase(&$db)
    {
        $this->db = $db;
        return $this;
    }
    public function setAuthor(string $author)
    {
        $this->author = $author;
        return $this;
    }
    public function setThemeColor(string $color)
    {
        $this->theme_color = $color;
        return $this;
    }
    public function setKeywords($keywords)
    {
        if(is_array($keywords)):
            $this->keywords = array_merge($this->keywords,$keywords);
        else:
            $this->keywords[] = $keywords;
        endif;
        return $this;
    }
    public function setPostImage($img)
    {
        $this->post_images = $img;
        return $this;
    }
    public function setExcerpt(string $excerpt)
    {
        $this->details = $excerpt;
        return $this;
    }
    public function setDetails(string &$excerpt)
    {
        $this->details = $excerpt;
        return $this;
    }
    public function setLanguage(string $language)
    {
        $this->language = $language;
        return $this;
    }
    public function setSeo(bool $seo)
    {
        $this->seo = $seo;
        return $this;
    }
    public function addStyles($sheets)
    {
        if(IS_WORDPRESS){
            $this->default_styles[] = $sheets;
        } else {
            if (is_array($sheets)) {
                for ($i = 0; $i < count($sheets); $i++) {
                    $this->default_styles[] = $sheets[$i];
                }
            } else {
                $this->default_styles[] = $sheets;
            }
        }
        return $this;
    }
    public function setStyles($sheets){
        $this->addStyles($sheets);
        return $this;
    }
    public function addScripts($scripts)
    {
        if (is_array($scripts)) {
            for ($i = 0; $i < count($scripts); $i++) {
                $this->footer_scripts[] = $scripts[$i];
            }
        } else {
            $this->footer_scripts[] = $scripts;
        }
        return $this;
    }
    public function setScripts($scripts){
        $this->addScripts($scripts);
        return $this;
    }
    public function isProductionServer()
    {
        $curr_tld = explode('.', SERVER_NAME);
        $curr_tld = end($curr_tld);
        return $curr_tld == $this->tld;
    }
    public function isTestingServer()
    {
        return !$this->isProductionServer();
    }
    public function allowTranslating(bool $trans)
    {
        $this->translate = $trans;
    }
    public function allowRobots(string $val)
    {
        $this->robots = $val;
    }
    public function wp_asset($url)
    {
        return get_template_directory_uri().$url;
    }

    public function active($link)
    {
        if(IS_WORDPRESS){
            if (is_array($link) && in_array($_SERVER['REQUEST_URI'], $link)) {
                return 'active disabled';
            }
            if (!is_array($link) && $link === $_SERVER['REQUEST_URI']) {
                return 'active disabled';
            }
        } else {
            if (is_array($link) && in_array($_SERVER['PHP_SELF'], $link)) {
                return 'active disabled';
            }
            if (!is_array($link) && $link . '.php' === $_SERVER['PHP_SELF']) {
                return 'active disabled';
            }
        }
        return '';
    }
    protected function printScripts($script)
    {
        if(!$script){
            return;
        }

        if(!IS_WORDPRESS):
            if (!is_array($script)) {
                echo "<script src=\"{$script}?ver={$this->version}\" type=\"text/javascript\" defer=\"defer\"></script>";
            } else {
                $scr =  "<script src=\"{$script['src']}";
                if ((isset($script['control_version']) && $script['control_version']) || !isset($script['control_version'])) {
                    $scr .= "?ver={$this->version}";
                }
                $scr .= "\" type=\"text/javascript\"";
                if ((isset($script['async']) && $script['async']) || !isset($script['async'])) {
                    $scr .= " defer=\"defer\"";
                }
                $scr .= "></script>";
                echo $scr;
            }
        else:
            $jquery = false;
            $script = is_array($script)?$script:[$script];
            foreach($script as $js):
                $name = $js['name']??uniqid('csg');
                if(!$jquery && strpos($js['src'],"jquery")){
                    wp_deregister_script('jquery');
                    $jquery = true;
                }
                wp_register_script(sprintf("csg-%s",$name), get_template_directory_uri() . $js['src'], $js['deps']??[], $js['version']??$this->version, $js['footer']??true);
                wp_enqueue_script(sprintf("csg-%s",$name));
            endforeach;
        endif;
        return $this;
    }
    protected function printStylesheets($style)
    {
        if(!$style){
            return;
        }

        if(!IS_WORDPRESS):
            if (!is_array($style)) {
                printf("<link rel=\"stylesheet\" type=\"text/css\"  href=\"%s?ver=%s\" media=\"all\">",$style,$this->version);
            } else {
                if(!isset($style['href'])){
                    $style['href']=$style['src'];
                }
                $async = $style['async']??false;
                $version = $style['control_version']??false;
                $css = '<link ';
                if($async):
                    $css .= sprintf("rel=\"preload\" href=\"%s",$style['href']);
                else:
                    $css .= sprintf("rel=\"stylesheet\" type=\"text/css\"  href=\"%s",$style['href'],$this->version);
                endif;
                if($version):
                    $css .= sprintf("?ver=%s\" ",$this->version);
                else:
                    $css .= sprintf("\" ");
                endif;
                if($async):
                    $css .= 'as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
                    $css .= sprintf("<noscript><link rel=\"stylesheet\" href=\"%s",$style['href']);
                    if($version):
                        $css .= sprintf("?ver=%s",$this->version);
                    endif;
                    $css .= sprintf("\"></noscript>");
                else:
                    $css .= sprintf("media=\"all\">",$style['href'],$this->version);
                endif;
                echo $css;
            }
        else:
        endif;
        return $this;
    }
    protected function printFonts($font)
    {
        if(is_array($font)){
            $font_name = $font['name'];
            $html = sprintf("<link rel=\"preconnect\" href=\"%s\" crossorigin>",$font['preconnect']??'//fonts.gstatic.com');
            $html .= sprintf("<link rel=\"preload\" as=\"style\" href=\"//fonts.googleapis.com/css2?family=%s&display=%s\">",$font_name,$font['display']??'swap');
            $html .= sprintf("<link rel=\"stylesheet preload prefetch\" href=\"//fonts.googleapis.com/css?family=%s:%s\">",$font_name,$font['weight']??'400');
            print($html);
        }
        return $this;
    }
    public function getHead($title = null,$cb=null)
    {
        if ($title) {
            $this->title = $title;
        }
        if(IS_WORDPRESS){ 
            if(empty($this->title)){
                $this->title = get_the_title();
            }
            $this->setKeywords(
                array(
                    $this->getTitle(),
                    $this->getDetails(),
                )
            );
        }?>
        <!DOCTYPE html>
        <html lang="<?=$this->language?>" dir="<?=$this->text_dir?>">
        <head>
            <meta charset="<?=$this->charset?>" http-equiv="Content-Type" content="text/html">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="application-name" content="<?= APP_NAME; ?>">
            <meta name="mobile-web-app-capable" content="yes">
            <meta name="theme-color" content="<?= $this->getThemeColor(); ?>">
            <meta name="format-detection" content="telephone=no">
            <meta name="apple-mobile-web-app-capable" content="yes">
            <meta name="apple-mobile-web-app-status-bar-style" content="<?= $this->getThemeColor(); ?>">
            <meta name="msapplication-TileColor" content="<?= $this->getThemeColor(); ?>">
            <meta name="msapplication-TileImage" content="<?= $this->getPostImage(); ?>">
            <meta name="author" content="<?= $this->getAuthor(); ?>">
            <?php if(!IS_WORDPRESS):?>
                <meta name="generator" content="<?= $this->sys_name ?>">
            <?php endif;?>
            <meta name="canonical" href="<?= CURRENT_PAGE; ?>">
            <?php if ($this->seo) : ?>
                <meta name="keywords" content="<?= $this->getKeywords(); ?>">
                <meta name="description" content="<?= $this->getDetails(); ?>">
                <meta property="og:url" content="<?= CURRENT_PAGE; ?>">
                <meta property="og:locale" content="<?=str_replace("-","_",$this->language)?>">
                <?php if(CURRENT_PAGE==WEBSITE_HOME.'/'):?>
                <meta property="og:type" content="website">
                <?php else:?>
                <meta property="og:type" content="article">
                <?php endif;?>
                <meta property="og:title" content="<?= $this->getTitle(); ?>">
                <meta property="og:description" content="<?= $this->getDetails(); ?>">
                <meta property="og:image" content="<?= $this->getPostImage(); ?>">
                <meta property="og:image_alt" content="<?=$this->post_images_alt?>">
                <meta property="og:site_name" content="<?= $this->appname; ?>">
                <meta property="twitter:card" content="<?=$this->tw_card?>">
                <meta property="twitter:title" content="<?= $this->getTitle(); ?>">
                <meta property="twitter:description" content="<?= $this->getDetails(); ?>">
                <meta property="twitter:site" content="@caasi_zw">
                <meta property="twitter:image" content="<?= $this->getPostImage() ?>">
            <?php endif; 
            if ($this->facebook_id) : ?>
                <meta property="fb:app_id" content="<?= $this->facebook_id ?>">
            <?php endif;
            if ($this->fonts) : foreach($this->fonts as $font):
                $this->printFonts($font);
            endforeach; endif; ?>
            <link rel="shortcut icon" href="<?= $this->getFavicon(); ?>" type="<?= $this->favicon_type ?>">
            <?php 
            
            if ($this->manifest) :
                printf("<link rel=\"manifest\" href=\"%s\">", $this->manifest);
            endif;
            if(!IS_WORDPRESS):
                foreach ($this->getStyles() as $style) {
                    $this->printStylesheets($style);
                }
                for ($i = 0; $i < count($this->getScripts()); $i++) {
                    $this->printScripts($this->getScripts($i));
                }
            else:
                add_action('wp_enqueue_scripts', function(){
                    $styles = $this->getStyles();
                    foreach($styles as $style):
                        $name = $style['name']??uniqid('csg');
                        wp_register_style(sprintf("csg-%s",$name), get_template_directory_uri() . $style['src'], $style['deps']??[], $style['version']??$this->version, $style['media']??'all');
                        wp_enqueue_style(sprintf("csg-%s",$name));
                    endforeach;
                });
                add_action('wp_enqueue_scripts', function(){
                    $this->printScripts($this->default_scripts);
                });
                add_action('wp_enqueue_scripts', function(){
                    $ft_js = &$this->footer_scripts;
                    $this->printScripts($ft_js);
                });
                wp_head();
            endif;
            if($this->title):
                @define('PG_TITLE', $this->getTitle());
                printf("<title>%s</title>", $this->getTitle());
            endif;
            if($this->schema):
                print($this->getSchema());
            endif;
            if ($this->style):
                printf("<style type=\"text/css\">%s</style>", $this->style);
            endif; ?>
        </head><?php
        if($cb && is_callable($cb)):
            call_user_func($cb);
        endif;
        return $this;
    }
    public function getMeta($title=null,$cb=null)
    {
        $this->getHead($title,$cb);
        return $this;
    }
    public function getSchema(){
        return sprintf("<script type=\"application/ld+json\" id=\"csg-schema-seo\">%s</script>",$this->schema);
    }
    function openBody($args=null,$class=null){
        if($this->body_open){
            return $this;
        }
        echo "<body ";
        if(IS_WORDPRESS): 
            body_class($class); 
        elseif($class): 
            echo $class; 
        endif;
        if($args): 
            echo $args; 
        endif;
        echo "/>";
        $this->body_open = true;
        return $this;
    }
    /**
     * Returns true if it's a bot that crawling!
     * @link https://stackoverflow.com/questions/677419/how-to-detect-search-engine-bots-with-php
     * @var bool
     */
    function isCrawlingBot()
    {
        if (preg_match('/abacho|accona|AddThis|AdsBot|ahoy|AhrefsBot|AISearchBot|alexa|altavista|anthill|appie|applebot|arale|araneo|AraybOt|ariadne|arks|aspseek|ATN_Worldwide|Atomz|baiduspider|baidu|bbot|bingbot|bing|Bjaaland|BlackWidow|BotLink|bot|boxseabot|bspider|calif|CCBot|ChinaClaw|christcrawler|CMC\/0\.01|combine|confuzzledbot|contaxe|CoolBot|cosmos|crawler|crawlpaper|crawl|curl|cusco|cyberspyder|cydralspider|dataprovider|digger|DIIbot|DotBot|downloadexpress|DragonBot|DuckDuckBot|dwcp|EasouSpider|ebiness|ecollector|elfinbot|esculapio|ESI|esther|eStyle|Ezooms|facebookexternalhit|facebook|facebot|fastcrawler|FatBot|FDSE|FELIX IDE|fetch|fido|find|Firefly|fouineur|Freecrawl|froogle|gammaSpider|gazz|gcreep|geona|Getterrobo-Plus|get|girafabot|golem|googlebot|\-google|grabber|GrabNet|griffon|Gromit|gulliver|gulper|hambot|havIndex|hotwired|htdig|HTTrack|ia_archiver|iajabot|IDBot|Informant|InfoSeek|InfoSpiders|INGRID\/0\.1|inktomi|inspectorwww|Internet Cruiser Robot|irobot|Iron33|JBot|jcrawler|Jeeves|jobo|KDD\-Explorer|KIT\-Fireball|ko_yappo_robot|label\-grabber|larbin|legs|libwww-perl|linkedin|Linkidator|linkwalker|Lockon|logo_gif_crawler|Lycos|m2e|majesticsEO|marvin|mattie|mediafox|mediapartners|MerzScope|MindCrawler|MJ12bot|mod_pagespeed|moget|Motor|msnbot|muncher|muninn|MuscatFerret|MwdSearch|NationalDirectory|naverbot|NEC\-MeshExplorer|NetcraftSurveyAgent|NetScoop|NetSeer|newscan\-online|nil|none|Nutch|ObjectsSearch|Occam|openstat.ru\/Bot|packrat|pageboy|ParaSite|patric|pegasus|perlcrawler|phpdig|piltdownman|Pimptrain|pingdom|pinterest|pjspider|PlumtreeWebAccessor|PortalBSpider|psbot|rambler|Raven|RHCS|RixBot|roadrunner|Robbie|robi|RoboCrawl|robofox|Scooter|Scrubby|Search\-AU|searchprocess|search|SemrushBot|Senrigan|seznambot|Shagseeker|sharp\-info\-agent|sift|SimBot|Site Valet|SiteSucker|skymob|SLCrawler\/2\.0|slurp|snooper|solbot|speedy|spider_monkey|SpiderBot\/1\.0|spiderline|spider|suke|tach_bw|TechBOT|TechnoratiSnoop|templeton|teoma|titin|topiclink|twitterbot|twitter|UdmSearch|Ukonline|UnwindFetchor|URL_Spider_SQL|urlck|urlresolver|Valkyrie libwww\-perl|verticrawl|Victoria|void\-bot|Voyager|VWbot_K|wapspider|WebBandit\/1\.0|webcatcher|WebCopier|WebFindBot|WebLeacher|WebMechanic|WebMoose|webquest|webreaper|webspider|webs|WebWalker|WebZip|wget|whowhere|winona|wlm|WOLP|woriobot|WWWC|XGET|xing|yahoo|YandexBot|YandexMobileBot|yandex|yeti|Zeus/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true; // 'Above given bots detected'
        }

        return false;
    }
    protected function beforeGetHeader()
    {
        if(!$this->body_open){
            $this->openBody();
        }
        return $this;
    }
    protected function beforeGetMenu()
    {
        if(!$this->body_open){
            $this->openBody();
        }
        return $this;
    }

    /**
     * Prints the html markup (static by default) of the website footer.
     */
    protected function beforeGetFooter(array $scripts = [])
    {
        $this->footer_scripts = array_merge($this->footer_scripts,$scripts);
        if(!IS_WORDPRESS){
            foreach ($this->footer_scripts as $script) {
                if (!$script) continue;
                $this->printScripts($script);
            }
        } else {
            wp_footer();
        }
    }
};
