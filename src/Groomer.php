<?php

namespace Caasi;

if (!defined('MANAGE_SESSION') || MANAGE_SESSION === true) :
    session_start();
endif;

if (!defined('WP_SHORTEN_ASSETS_URL')) {
    define('WP_SHORTEN_ASSETS_URL', false);
}

/**
 * Prepares and prints the markup to the browser.
 *
 * Optimized to make custom web development easy.
 * @pageAuthor Isaac <isaac@caasi.co.zw>
 * @copyright Caasi
 * @link https://github.com/caasi-co-zw/Groomer
 * @version 1.1
 * @license GPL-3.0+ http://www.gnu.org/licenses/gpl-3.0.txt
 */
class Groomer {
    /**
     * The system name
     * @var string
     */
    private $systemName;

    /**
     * Developer name
     * @var string
     */
    private $developerName;

    /**
     * Developer website
     * @var string
     */
    private $developerURL;

    /**
     * @var array
     */
    private $outputBufferRegex = [];

    /**
     * Custom route regex presets
     * @var array
     */
    private $pageRouteRegexPresets = [];

    /**
     * The page title
     * @var string
     */
    protected $pageTitle;

    /**
     * SEO Keywords for page
     * @var array
     */
    protected $seoKeywords = [];

    /**
     * The SEO description of the page
     * @var string
     */
    protected $seoDescritpion;

    /**
     * Author of the website
     * @var string
     */
    protected $pageAuthor;

    /**
     * Website pageFavicon url
     * @var string
     */
    protected $pageFavicon;

    /**
     * Favicon mime image type
     * @var string
     */
    protected $faviconType;

    /**
     * The SEO pageThumbnail image url
     * @var string
     */
    protected $pageThumbnail;

    /**
     * SEO pageThumbnail image alt text
     * @var string
     */
    protected $thumbnailDescription;

    /**
     * A list of all stylesheetsURI stylesheets and their properties
     * @var array
     */
    protected $stylesheetsURI = [];

    /**
     * Text direction of the site
     * @var string
     */
    protected $pageTextDirection = 'ltr';

    /**
     * The site pageLanguage
     * @var string
     */
    protected $pageLanguage = 'en-ZW';

    /**
     * How to display post on twitter
     * @var string
     */
    protected $twitterCardType = 'summary_large_image';

    /**
     * Who to credit post on twitter
     * @var string
     */
    protected $twitterSite;

    /**
     * Triggers for this generator
     * @var array
     */
    protected $systemTriggers = [];

    /**
     * An array of all default javascript files to be added to the header
     * @var array
     */
    protected $javascriptsURI = array(
        'head' => [],
        'footer' => []
    );

    /**
     * Facebook page ig
     * @var string
     */
    protected $facebookID;

    /**
     * Charset encoding for the site
     * @var string
     */
    protected $pageCharset = 'utf-8';

    /**
     * Page rules for robots
     */
    protected $robots      = "index";

    /**
     * Theme color of site
     * @var string
     */
    protected $pageThemeColor = "#a70c0c";

    /**
     * Custom page styling to be printed in the head
     * @var string
     */
    protected $headCss       = null;

    /**
     * Allow google translate
     * @var bool
     */
    protected $translate   = false;

    /**
     * Enable buffer to be compressed
     * @var bool
     */
    protected $compressHtmlOutput = true;

    /**
     * Custom version for your website and assets
     * @var string
     */
    protected $version;

    /**
     * URL to the manifest of the site
     * @var string
     */
    protected $manifest;

    /**
     * Localhost top domain level if any
     * @var string
     */
    protected $localTLD;
    protected $menu;
    protected $postImage;
    protected $errors;

    /**
     * A list of google fonts to be included
     * @var array
     */
    protected $fonts = array();

    /**
     * Google tag manager configuration
     * @var array
     */
    protected $gtag = array();

    /**
     * Enable or disable SEO
     * @var bool
     */
    protected $seo = true;

    /**
     * Custom schema.org json
     * @var string
     */
    protected $schema;

    /**
     * Prevents opening the body tag twice
     * @var bool
     */
    protected $pageBodyOpened = false;

    /**
     * Will return true if on mobile device
     */
    protected $isMobile = false;
    /**
     * Wordpress theme seoDescritpion
     * @var object
     */
    public $theme;

    /**
     * Name of the website
     * @var string
     */
    public $siteName;

    public function __construct(array $config = [], $callback = null) {
        // init default values
        $this->__initializeDefaults();

        if ($this->isWordPress()) :

            // exit if called directly and on wordpress
            defined('ABSPATH') || exit;

            // get theme seoDescritpion
            $this->theme = wp_get_theme();
            $this->siteName = get_bloginfo('name');

            // site info
            if (get_bloginfo('description')) {
                $this->seoDescritpion = get_bloginfo('description');
            }

            // meta data
            $this->pageTextDirection = is_rtl() ? 'rtl' : 'ltr';
            $this->pageCharset = get_bloginfo('pageCharset');
            $this->version = $this->theme->get('Version');
            $this->pageLanguage = get_bloginfo('pageLanguage');

            // device type
            $this->isMobile = wp_is_mobile();

            // fix site icon
            if (has_site_icon()) :
                $this->pageFavicon = get_site_icon_url();
            endif;

            // default seoKeywords
            $this->setKeywords(
                array(
                    $this->systemName,
                )
            );
        endif;

        // initialized system configurations
        foreach ($config as $key => $value) {
            switch ($key) {
                case 'title':
                    $this->pageTitle = $value;
                    break;
                case 'author':
                    $this->pageAuthor = ucwords($value);
                    break;
                case 'description':
                    $this->seoDescritpion = $value;
                    break;
                case 'keywords':
                    if (is_array($value)) {
                        $this->seoKeywords = '';
                        for ($j = 0; $j < count($value); $j++) {
                            $this->seoKeywords .= ($value[$j]);
                            if ($j + 1 !== count($value)) {
                                $this->seoKeywords .= ',';
                            }
                        }
                    } else {
                        $this->seoKeywords = ($value);
                    }
                    break;
                case 'fonts':
                    if (is_array($value)) {
                        for ($j = 0; $j < count($value); $j++) {
                            $this->fonts[] = $value[$j];
                        }
                    } else {
                        $this->fonts[] = $value;
                    }
                    break;
                case 'styles':
                    if ($value) {
                        if (is_array($value)) {
                            for ($j = 0; $j < count($value); $j++) {
                                $this->stylesheetsURI[] = $value[$j];
                            }
                        } else {
                            $this->stylesheetsURI = array_merge(array($value), $this->stylesheetsURI);
                        }
                    }
                    break;
                case 'js':
                case 'footer_js':
                    if (is_array($value)) {
                        for ($j = 0; $j < count($value); $j++) {
                            $this->javascriptsURI['footer'][] = $value[$j];
                        }
                    } else {
                        $this->javascriptsURI['footer'][] = $value;
                    }
                    break;
                case 'header_js':
                    if (is_array($value)) {
                        for ($j = 0; $j < count($value); $j++) {
                            $this->javascriptsURI['head'][] = $value[$j];
                        }
                    } else {
                        $this->javascriptsURI['head'][] = $value;
                    }
                    break;
                case 'favicon':
                    $this->pageFavicon = $value;
                    break;

                case 'menu':
                    $this->menu = $value;
                    break;
                case 'css':
                    $this->headCss = $value;
                    break;
                case 'robots':
                    $this->robots = $value;
                    break;
                case 'image':
                    $this->postImage = $value;
                    break;
                default:
                    # log to errors
                    $this->errors[] = $key . ' was not recognized.';
                    break;
            }
        }

        // send a system header
        header(sprintf("X-Powered-By: %s", $this->systemName));

        // compress html if enabled
        if ($this->compressHtmlOutput) :
            ob_start([$this, '_compressOutput']);
        endif;

        // callback function
        if ($callback && is_callable($callback)) :
            call_user_func($callback);
        endif;
    }

    /**
     * Enable or disable html compression on output.
     * @param bool $enabled
     */
    public function compressOutput($enabled) {
        $this->compressHtmlOutput = $enabled;
        return $this;
    }
    /**
     * Changes the title of the page.
     * @param string $pageTitle The new page title
     */
    public function setTitle(string $pageTitle) {
        $this->pageTitle = $pageTitle;
        return $this;
    }

    /**
     * Changes the page pageAuthor
     * @param string $pageAuthor Author name
     */
    public function setAuthor(string $pageAuthor) {
        $this->pageAuthor = $pageAuthor;
        return $this;
    }

    /**
     * Sets the twitter owner of the site
     * @param string $username Site username
     */
    public function setTwitterSite(string $username) {
        $this->twitterSite = $username;
        return $this;
    }

    /**
     * Sets a theme color for the web app
     * @param string $color A hex color
     */
    public function setThemeColor(string $color) {
        $this->pageThemeColor = $color;
        return $this;
    }

    /**
     * Sets the schema json for the page
     * @param string $schema The schema.org json
     */
    public function setSchema($schema) {
        $this->schema = $schema;
        return $this;
    }

    /**
     * Prepends the new seo seoKeywords for the page
     * @param string|array $schema The seo seoKeywords
     */
    public function addKeywords($seoKeywords) {
        if (is_array($seoKeywords)) :
            $this->seoKeywords = array_merge($this->seoKeywords, $seoKeywords);
        else :
            $this->seoKeywords[] = $seoKeywords;
        endif;
        return $this;
    }

    /**
     * Sets the new (overrides any existsing) seo seoKeywords for the page
     * @param string|array $schema The seo seoKeywords
     */
    public function setKeywords($seoKeywords) {
        if (is_array($seoKeywords)) :
            $this->seoKeywords = $seoKeywords;
        else :
            $this->seoKeywords = [$seoKeywords];
        endif;
        return $this;
    }

    /**
     * Sets the page pageThumbnail
     * @param string $schema The pageThumbnail url
     */
    public function setPostImage($img) {
        $this->pageThumbnail = $img;
        return $this;
    }

    /**
     * Sets the seo excerpt for the page
     * @param string $schema The seo excerpt
     */
    public function setExcerpt(string $excerpt) {
        $this->seoDescritpion = $excerpt;
        return $this;
    }

    /**
     * Sets the seo excerpt for the page
     * @param string $schema The seo excerpt
     */
    public function setDetails(string $excerpt) {
        $this->seoDescritpion = $excerpt;
        return $this;
    }

    /**
     * Sets the seo excerpt for the page
     * @param string $schema The seo excerpt
     */
    public function setDescription(string $excerpt) {
        $this->seoDescritpion = $excerpt;
        return $this;
    }

    /**
     * Set the website pageLanguage
     * @var string
     */
    public function setLanguage(string $pageLanguage) {
        $this->pageLanguage = $pageLanguage;
        return $this;
    }

    /**
     * Enable or disable SEO
     * @param bool $seo Enables or disables SEO
     */
    public function setSeo(bool $seo) {
        $this->seo = $seo;
        return $this;
    }

    /**
     * Set the pageFavicon url
     * @param string $url Url of the pageFavicon
     */
    public function setFavicon(string $url) {
        $this->pageFavicon = $url;
        return $this;
    }

    /**
     * Adds a stylesheet to queque
     * @param array $sheets A list of the headCss sheet
     */
    public function addStyle(array ...$sheets) {
        $this->stylesheetsURI = array_merge($this->stylesheetsURI, $sheets);
        return $this;
    }
    /**
     * Adds a stylesheet to queque
     * @param array|Css[] $sheets A list of the headCss sheet
     */
    public function addStyles(...$sheets) {
        if (is_array($sheets)) {
            $this->stylesheetsURI = array_merge($this->stylesheetsURI, $sheets);
        }
        return $this;
    }

    /**
     * Adds stylesheetsURI to <headCss> tag
     * @param string $headCss CSS to be added in <headCss>
     */
    public function addInlineStyles(string $headCss) {
        if (!$this->headCss) {
            $this->headCss = $headCss;
        } else {
            $this->headCss .= $headCss;
        }
        return $this;
    }

    /**
     * Adds a javascript file to queque
     * @param array $scripts A list of the scripts
     */
    public function addScripts(array ...$scripts) {
        $head  = array();
        $footer = array();

        foreach ($scripts as $script) {
            if (!isset($script['footer']) || $script['footer']) {
                $footer[] = $script;
            } else {
                $head[] = $script;
            }
        };

        $this->javascriptsURI['head'] = array_merge($this->javascriptsURI['head'], $head);
        $this->javascriptsURI['footer'] = array_merge($this->javascriptsURI['footer'], $footer);

        return $this;
    }

    /**
     * Add asyncronous google fonts to your website.
     * @param array $fonts A list of fonts, each enclosed in an array.
     */
    public function setFonts(array ...$fonts) {
        $this->fonts = array_merge($this->fonts, $fonts);
        return $this;
    }

    /**
     * Add asyncronous google fonts to your website.
     * @param array $fonts A list of fonts, each enclosed in an array.
     */
    public function addFonts(array ...$fonts) {
        $this->setFonts($fonts);
        return $this;
    }

    /**
     * Change robots rules for page(s)
     * @param string $val New rule
     */
    public function setRobots(string $val) {
        $this->robots = $val;
        return $this;
    }

    public function setDomainExtension($localTLD) {
        $this->localTLD = $localTLD;
    }

    /**
     * Returns true if installed on WordPress CMS
     * @return bool
     */
    public function isWordPress() {
        return class_exists('WP');
    }


    /**
     * Server name in this format caasi.co.zw
     * @return string
     */
    public function getServerName() {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Returns the domain name
     * @return string
     */
    public function getAppName() {
        return explode('.', $this->getServerName())[0];
    }

    /**
     * Homepage url of the site in this format https://caasi.co.zw/
     * @return string
     */
    public function getWebsiteHome() {
        return (($this->onSecureConnection() ? 'https://' : 'http://') . $this->getServerName());
    }

    /**
     * Returns the TLD for the site
     */
    public function getCurrentTLD() {
        $site = explode('.', $this->getServerName());
        return $this->localTLD = end($site);
    }
    /**
     * Returns true if the page is on https
     * @return bool
     */
    public function onSecureConnection() {
        $ssl = $_SERVER['HTTPS'] ?? null;
        return $ssl === "on";
    }

    /**
     * Returns the current opened page url in this format https://caasi.co.zw/websites/
     * @return string
     */
    public function getCurrentPage() {
        return $this->getWebsiteHome() . $_SERVER['REQUEST_URI'];
    }

    /**
     * Returns a well formated page pageTitle
     * @return string
     * @var string
     */
    public function getTitle() {
        if (!$this->isWordPress()) {
            return $this->pageTitle . " " . html_entity_decode("&ndash;") . " " . $this->siteName;
        }
        return sprintf("%s %s %s", $this->pageTitle, html_entity_decode('&ndash;'),  $this->siteName);
    }

    /**
     * Returns the SEO site description
     * @return string
     * @var string
     */
    public function getDetails() {
        if ($this->isWordPress()) {
            return empty($this->seoDescritpion) ? get_the_excerpt() : $this->seoDescritpion;
        }
        return $this->seoDescritpion;
    }

    /**
     * Returns the document pageAuthor
     * @return string
     */
    public function getAuthor() {
        return $this->pageAuthor;
    }

    /**
     * Returns the url of the site pageFavicon
     * @return string
     */
    public function getFavicon() {
        return $this->pageFavicon;
    }

    /**
     * Returns the theme color of the app
     * @return string
     */
    public function getThemeColor() {
        return $this->pageThemeColor ?? '#003883';
    }

    /**
     * Returns true when the set local localTLD does not match the current localTLD
     * @return bool
     */
    public function isProductionServer() {
        $server_tld = explode('.', $this->getServerName());
        $server_tld = end($server_tld);
        return strtolower($server_tld) !== strtolower($this->localTLD);
    }

    /**
     * Returns true if running on a local server
     * @return bool
     */
    public function isTestingServer() {
        return !$this->isProductionServer();
    }

    /**
     * Enables or disables google translating the site
     */
    public function allowTranslating(bool $trans) {
        $this->translate = $trans;
        return $this;
    }

    /**
     * Returns $active if the url matches the request url
     * @param string $link The url to match to
     * @param string $active The text to return when url matches
     * @return string
     */
    public function active($link, string $active = 'active') {
        if ($this->isWordPress()) {
            if (is_array($link) && in_array($_SERVER['REQUEST_URI'], $link)) {
                return $active;
            }
            if (!is_array($link) && $link === $_SERVER['REQUEST_URI']) {
                return $active;
            }
        } else {
            if (is_array($link) && in_array($_SERVER['PHP_SELF'], $link)) {
                return $active;
            }
            if (!is_array($link) && $link . '.php' === $_SERVER['PHP_SELF']) {
                return $active;
            }
        }
        return '';
    }

    /**
     * Returns the html + head section of the site.
     * @param string $pageTitle Dynamically change the page title
     * @param callable $cb A callback function to be executed before the function stops
     */
    public function getHead(string $pageTitle = null, callable $cb = null) {
        if ($this->isWordPress()) {
            if (!$this->pageTitle) {
                $this->pageTitle = get_the_title();
            }
            $this->setKeywords(
                array(
                    $this->getTitle(),
                    $this->getDetails(),
                )
            );
        }
        if ($pageTitle) {
            $this->pageTitle = $pageTitle;
        } ?>
        <!DOCTYPE html>
        <html lang="<?= $this->pageLanguage ?>" dir="<?= $this->pageTextDirection ?>">

        <head>
            <meta pageCharset="<?= $this->pageCharset ?>" http-equiv="Content-Type" content="text/html">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="application-name" content="<?= $this->getAppName(); ?>">
            <meta name="mobile-web-app-capable" content="yes">
            <meta name="theme-color" content="<?= $this->getThemeColor(); ?>">
            <meta name="format-detection" content="telephone=no">
            <meta name="apple-mobile-web-app-capable" content="yes">
            <meta name="apple-mobile-web-app-status-bar-headCss" content="<?= $this->getThemeColor(); ?>">
            <meta name="msapplication-TileColor" content="<?= $this->getThemeColor(); ?>">
            <meta name="msapplication-TileImage" content="<?= $this->getPostImage(); ?>">
            <meta name="pageAuthor" content="<?= $this->getAuthor(); ?>">
            <?php if (!$this->isWordPress()) : ?>
                <meta name="generator" content="<?= $this->systemName ?>">
            <?php endif; ?>
            <meta name="canonical" href="<?= $this->getCurrentPage(); ?>">
            <?php if ($this->seo) : ?>
                <meta name="seoKeywords" content="<?= $this->getKeywords(); ?>">
                <meta name="description" content="<?= $this->getDetails(); ?>">
                <meta property="og:url" content="<?= $this->getCurrentPage(); ?>">
                <meta property="og:locale" content="<?= str_replace("-", "_", $this->pageLanguage) ?>">
                <?php if ($this->getCurrentPage() == $this->getWebsiteHome() . '/') : ?>
                    <meta property="og:type" content="website">
                <?php else : ?>
                    <meta property="og:type" content="article">
                <?php endif; ?>
                <meta property="og:title" content="<?= $this->getTitle(); ?>">
                <meta property="og:description" content="<?= $this->getDetails(); ?>">
                <meta property="og:image" content="<?= $this->getPostImage(); ?>">
                <meta property="og:image_alt" content="<?= $this->thumbnailDescription ?>">
                <meta property="og:siteName" content="<?= $this->siteName; ?>">
                <meta property="twitter:card" content="<?= $this->twitterCardType ?>">
                <meta property="twitter:title" content="<?= $this->getTitle(); ?>">
                <meta property="twitter:description" content="<?= $this->getDetails(); ?>">
                <?php if ($this->twitterSite) : ?>
                    <meta property="twitter:site" content="@<?= $this->twitterSite ?>">
                <?php endif; ?>
                <meta property="twitter:image" content="<?= $this->getPostImage() ?>">
            <?php endif;
            if ($this->facebookID) : ?>
                <meta property="fb:app_id" content="<?= $this->facebookID ?>">
            <?php endif;
            if ($this->fonts) : foreach ($this->fonts as $font) :
                    $this->printFonts($font);
                endforeach;
            endif; ?>
            <link rel="shortcut icon" href="<?= $this->getFavicon(); ?>" type="<?= $this->faviconType ?>">
            <?php

            if ($this->manifest) :
                printf("<link rel=\"manifest\" href=\"%s\">", $this->manifest);
            endif;
            if (!$this->isWordPress()) :
                foreach ($this->getStyles() as $headCss) {
                    $this->printStylesheets($headCss);
                }
                for ($i = 0; $i < count($this->getScripts(null, false)); $i++) {
                    $this->printScripts($this->getScripts($i, false));
                }
            else :
                add_action('wp_enqueue_scripts', function () {
                    $styles = $this->getStyles();
                    foreach ($styles as $headCss) :
                        $name = $headCss['name'] ?? uniqid('csg');
                        $headCss['src'] = !WP_SHORTEN_ASSETS_URL ? get_template_directory_uri() . $headCss['src'] : $headCss['src'];
                        wp_register_style(sprintf("csg-%s", $name), $headCss['src'], $headCss['deps'] ?? [], $headCss['version'] ?? $this->version, $headCss['media'] ?? 'all');
                        wp_enqueue_style(sprintf("csg-%s", $name));
                    endforeach;
                });
                add_action('wp_enqueue_scripts', function () {
                    $this->printScripts($this->javascriptsURI['head']);
                });
                add_action('wp_enqueue_scripts', function () {
                    $this->printScripts($this->javascriptsURI['footer']);
                });
                wp_head();
            endif;
            // @define('PG_TITLE', $this->getTitle());
            printf("<title>%s</title>", $this->getTitle());
            if ($this->headCss) :
                printf("<headCss type=\"text/stylesheetsURI\">%s</headCss>", $this->headCss);
            endif; ?>
        </head>
<?php
        if ($cb && is_callable($cb)) :
            call_user_func($cb);
        endif;
        return $this;
    }

    /**
     * Returns the html + head section of the site.
     * @param string $pageTitle Dynamically change the page pageTitle
     * @param Callable $cb A callback function to be executed before the function stops
     */
    public function getMeta($pageTitle = null, $cb = null) {
        $this->getHead($pageTitle, $cb);
        return $this;
    }

    /**
     * Opens the body tag adding any necessary classes or additional arguments.
     * @param string $class Body class
     * @param string $args Additional arguments to be added
     */
    public function openBody($class = null, $args = null) {
        if ($this->pageBodyOpened) {
            return $this;
        }
        echo "<body";
        if ($this->isWordPress()) :
            echo ' ';
            body_class($class);
        elseif ($class) :
            printf(" class=\"%s\"", $class);
        endif;
        if ($args) :
            echo $args;
        endif;
        echo ">";
        $this->pageBodyOpened = true;

        return $this;
    }

    /**
     * Returns true if it's a bot that crawling!
     * @link https://stackoverflow.com/questions/677419/how-to-detect-search-engine-bots-with-php
     * @return bool
     */
    public function isCrawlingBot() {
        if (preg_match('/abacho|accona|AddThis|AdsBot|ahoy|AhrefsBot|AISearchBot|alexa|altavista|anthill|appie|applebot|arale|araneo|AraybOt|ariadne|arks|aspseek|ATN_Worldwide|Atomz|baiduspider|baidu|bbot|bingbot|bing|Bjaaland|BlackWidow|BotLink|bot|boxseabot|bspider|calif|CCBot|ChinaClaw|christcrawler|CMC\/0\.01|combine|confuzzledbot|contaxe|CoolBot|cosmos|crawler|crawlpaper|crawl|curl|cusco|cyberspyder|cydralspider|dataprovider|digger|DIIbot|DotBot|downloadexpress|DragonBot|DuckDuckBot|dwcp|EasouSpider|ebiness|ecollector|elfinbot|esculapio|ESI|esther|eStyle|Ezooms|facebookexternalhit|facebook|facebot|fastcrawler|FatBot|FDSE|FELIX IDE|fetch|fido|find|Firefly|fouineur|Freecrawl|froogle|gammaSpider|gazz|gcreep|geona|Getterrobo-Plus|get|girafabot|golem|googlebot|\-google|grabber|GrabNet|griffon|Gromit|gulliver|gulper|hambot|havIndex|hotwired|htdig|HTTrack|ia_archiver|iajabot|IDBot|Informant|InfoSeek|InfoSpiders|INGRID\/0\.1|inktomi|inspectorwww|Internet Cruiser Robot|irobot|Iron33|JBot|jcrawler|Jeeves|jobo|KDD\-Explorer|KIT\-Fireball|ko_yappo_robot|label\-grabber|larbin|legs|libwww-perl|linkedin|Linkidator|linkwalker|Lockon|logo_gif_crawler|Lycos|m2e|majesticsEO|marvin|mattie|mediafox|mediapartners|MerzScope|MindCrawler|MJ12bot|mod_pagespeed|moget|Motor|msnbot|muncher|muninn|MuscatFerret|MwdSearch|NationalDirectory|naverbot|NEC\-MeshExplorer|NetcraftSurveyAgent|NetScoop|NetSeer|newscan\-online|nil|none|Nutch|ObjectsSearch|Occam|openstat.ru\/Bot|packrat|pageboy|ParaSite|patric|pegasus|perlcrawler|phpdig|piltdownman|Pimptrain|pingdom|pinterest|pjspider|PlumtreeWebAccessor|PortalBSpider|psbot|rambler|Raven|RHCS|RixBot|roadrunner|Robbie|robi|RoboCrawl|robofox|Scooter|Scrubby|Search\-AU|searchprocess|search|SemrushBot|Senrigan|seznambot|Shagseeker|sharp\-info\-agent|sift|SimBot|Site Valet|SiteSucker|skymob|SLCrawler\/2\.0|slurp|snooper|solbot|speedy|spider_monkey|SpiderBot\/1\.0|spiderline|spider|suke|tach_bw|TechBOT|TechnoratiSnoop|templeton|teoma|titin|topiclink|twitterbot|twitter|UdmSearch|Ukonline|UnwindFetchor|URL_Spider_SQL|urlck|urlresolver|Valkyrie libwww\-perl|verticrawl|Victoria|void\-bot|Voyager|VWbot_K|wapspider|WebBandit\/1\.0|webcatcher|WebCopier|WebFindBot|WebLeacher|WebMechanic|WebMoose|webquest|webreaper|webspider|webs|WebWalker|WebZip|wget|whowhere|winona|wlm|WOLP|woriobot|WWWC|XGET|xing|yahoo|YandexBot|YandexMobileBot|yandex|yeti|Zeus/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true; // 'Above given bots detected'
        }

        return false;
    }

    /**
     * Generates a wordpress assets link url
     *
     * Creates a theme friendly url to your theme assets
     * @param string $url The short asset url eg. /img/file.png
     * @return string
     */
    public static function wp_asset(string $url) {
        return get_template_directory_uri() . $url;
    }

    /**
     * Returns the stylesheet for index or all stylesheets
     * @return array
     */
    protected function getStyles(int $index = null) {
        if (null === $index) {
            return $this->stylesheetsURI;
        } else {
            return $this->stylesheetsURI[$index] ?? null;
        }
    }

    /**
     * Returns javascriptsURI properties for index or all if no index
     * @param int|null $index [optional] returns a script from that index
     * @param bool $footer [optional] returns a script from that index
     * @return array
     */
    protected function getScripts(int $index = null, bool $footer = true) {
        $type = $footer ? 'footer' : 'head';

        if (null === $index) {
            return $this->javascriptsURI[$type];
        } else {
            return $this->javascriptsURI[$type][$index];
        }
    }

    /**
     * Returns the robots rules
     * @return string
     */
    protected function getRobots() {
        return $this->robots;
    }

    /**
     * Returns true if translation is enabled
     * @return bool
     */
    protected function getTranslate() {
        return $this->translate;
    }

    /**
     * Returns the SEO seoKeywords set for the page.
     * @return string
     */
    protected function getKeywords() {
        $kw = '';
        $count = 1;
        if (is_array($this->seoKeywords)) {
            foreach ($this->seoKeywords as $word) {
                if (empty($word)) {
                    continue;
                }
                if ($count > 1) {
                    $kw .= ',';
                }
                $kw .= $word;
                $count++;
            }
            return $kw;
        }
        return $this->seoKeywords;
    }
    protected function getPostImage() {
        if ($this->isWordPress()) {
            if (has_post_thumbnail()) {
                $this->thumbnailDescription = the_post_thumbnail_caption();
                return the_post_thumbnail_url();
            }
            if (has_site_icon()) {
                $this->thumbnailDescription = get_site_icon_url();
                return get_site_icon_url();
            }
            return $this->pageThumbnail;
        }
        return $this->getWebsiteHome() . '/' . $this->pageThumbnail;
    }

    protected function addCompressionRule(string $regex, string $replacement) {
        $this->outputBufferRegex['replace'][] =  $regex;
        $this->outputBufferRegex['with'][] =  $replacement;
        return $this;
    }

    /**
     * Compresses the html to be out-puted
     * @var string
     */
    protected final function _compressOutput($buffer) {
        $buffer = preg_replace($this->outputBufferRegex['replace'], $this->outputBufferRegex['with'], $buffer);
        return $buffer;
    }

    /**
     * Prevents the body tag from beng called when unecessary.
     *
     * This function must be called just before the header tag if it's in a function.
     */
    protected function beforeHeader() {
        if (!$this->pageBodyOpened) {
            $this->openBody();
        }
        return $this;
    }

    /**
     * Prevents the body tag from beng called when unecessary.
     *
     * Must be the first thing in a getMenu() function
     */
    protected function beforeMenu() {
        if (!$this->pageBodyOpened) {
            $this->openBody();
        }
        return $this;
    }

    /**
     * Prints the html markup (static by default) of the website footer.
     * @param array $scripts An array of footer scripts.
     */
    protected function beforeFooter(array $scripts = []) {
        $this->javascriptsURI['footer'] = array_merge($scripts, $this->javascriptsURI['footer']);
        if (!$this->isWordPress()) {
            foreach ($this->getScripts() as $script) {
                if (!$script) continue;
                $this->printScripts($script);
            }
        } else {
            wp_footer();
        }
    }

    /**
     * Enqueues a script for printing
     * @param array|string $script The javascript files property
     */
    protected function printScripts($script) {
        if (!$script || empty($script)) {
            return $this;
        }

        if (!$this->isWordPress()) :
            if (!is_array($script)) {
                echo "<script src=\"{$script}?ver={$this->version}\" type=\"text/javascript\" defer=\"defer\"></script>";
            } else {
                $scr =  "<script src=\"{$script['src']}";
                if ((isset($script['control_version']) && $script['control_version']) || !isset($script['control_version'])) {
                    $scr .= sprintf("?ver=%s", $script['version'] ?? $this->version);
                }
                $scr .= "\" type=\"text/javascript\"";
                if ((isset($script['async']) && $script['async']) || !isset($script['async'])) {
                    $scr .= " defer=\"defer\"";
                }
                $scr .= "></script>";
                echo $scr;
            }
        else :
            $jquery = false;
            $script = is_array($script) ? $script : [$script];
            foreach ($script as $javascriptsURI) :
                $name = $javascriptsURI['name'] ?? uniqid('csg');
                $version = $this->version;

                if (isset($javascriptsURI['control_version'])) {
                    $version = isset($javascriptsURI['version']) ? $javascriptsURI['version'] : $this->version;
                }
                if (!$jquery && strpos($javascriptsURI['src'], "jquery")) {
                    wp_deregister_script('jquery');
                    $jquery = true;
                }
                wp_register_script(
                    sprintf("csg-%s", $name),
                    !WP_SHORTEN_ASSETS_URL ? get_template_directory_uri() . $javascriptsURI['src'] : $javascriptsURI['src'],
                    $javascriptsURI['deps'] ?? [],
                    $version,
                    $javascriptsURI['footer'] ?? true
                );
                wp_enqueue_script(sprintf("csg-%s", $name));
            endforeach;
        endif;
        return $this;
    }

    /**
     * Enqueues a headCss for printing
     * @param string $headCss The stylesheet file property
     */
    protected function printStylesheets($headCss) {
        if (!$headCss) {
            return;
        }

        if (!$this->isWordPress()) :
            if (!is_array($headCss)) {
                printf("<link rel=\"stylesheet\" type=\"text/stylesheetsURI\"  href=\"%s?ver=%s\" media=\"all\">", $headCss, $this->version);
            } else {
                if (!isset($headCss['href'])) {
                    $headCss['href'] = $headCss['src'];
                }
                $async = $headCss['async'] ?? false;
                $media = $headCss['media'] ?? 'all';
                $version = $headCss['control_version'] ?? false;
                $stylesheetsURI = '<link ';
                if ($async) :
                    $stylesheetsURI .= sprintf("rel=\"preload\" href=\"%s", $headCss['href']);
                else :
                    $stylesheetsURI .= sprintf("rel=\"stylesheet\" type=\"text/stylesheetsURI\"  href=\"%s", $headCss['href'], $this->version);
                endif;
                if ($version) :
                    $stylesheetsURI .= sprintf("?ver=%s\" ", isset($headCss['version']) ? $headCss['version'] : $this->version);
                else :
                    $stylesheetsURI .= sprintf("\" ");
                endif;
                if ($async) :
                    $stylesheetsURI .= 'as="headCss" onload="this.onload=null;this.rel=\'stylesheet\'">';
                    $stylesheetsURI .= sprintf("<noscript><link rel=\"stylesheet\" href=\"%s", $headCss['href']);
                    if ($version) :
                        $stylesheetsURI .= sprintf("?ver=%s", $this->version);
                    endif;
                    $stylesheetsURI .= sprintf("\"></noscript>");
                else :
                    $stylesheetsURI .= sprintf("media=\"%s\">", $media, $headCss['href'], $this->version);
                endif;
                echo $stylesheetsURI;
            }
        endif;
        return $this;
    }

    /**
     * Links a font asynchronously from google servers
     * @param string $font The name of the font
     */
    protected function printFonts($font) {
        if (is_array($font)) {
            $font_name = $font['name'];
            $html = sprintf("<link rel=\"preconnect\" href=\"%s\" crossorigin>", $font['preconnect'] ?? '//fonts.gstatic.com');
            $html .= sprintf("<link rel=\"preload\" as=\"headCss\" href=\"//fonts.googleapis.com/css2?family=%s&display=%s\">", $font_name, $font['display'] ?? 'swap');
            $html .= sprintf("<link rel=\"stylesheet preload prefetch\" href=\"//fonts.googleapis.com/stylesheetsURI?family=%s:%s\">", $font_name, $font['weight'] ?? '400');
            print($html);
        }
        return $this;
    }

    /**
     * Adds a function to the systemTriggers
     * @return bool
     */
    private function addTrigger(string $name, callable $callback) {
        $name = strtolower($name);
        if (!array_key_exists($name, $this->systemTriggers)) {
            return false;
        }
        $this->systemTriggers[$name][] = $callback;
        return true;
    }
    private function __initializeDefaults() {
        // set default values

        $this->outputBufferRegex = array(
            'replace' => array(
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
                "/\n/",
            ),
            'with' => array(
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
                ' ',
            ),
        );

        $this->pageRouteRegexPresets = array(
            '{all}' => '.*',
            '{alpha}' => '([a-zA-Z])+',
            '{any}' => '[^/]+',
            '{num}' => '\d+|-\d+',
        );

        $this->thumbnailDescription = 'Our logo';
        $this->systemName = 'Caasi Groomer';
        $this->developerName = $this->pageAuthor = 'Caasi';
        $this->developerURL = 'https://caasi.co.zw/';
    }
};
