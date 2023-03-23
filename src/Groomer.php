<?php

namespace Groomer;

use Groomer\Components\Link;
use Groomer\Components\Meta;
use Groomer\Components\NoScript;
use Groomer\Components\Style;

if (!defined('MANAGE_SESSION') || MANAGE_SESSION === true) :
    session_start();
endif;

/**
 * Prepares and prints the markup to the browser.
 *
 * Optimized to make custom web development easy.
 * @pageAuthor Isaac Machakata <isaac@caasi.co.zw>
 * @copyright Caasi
 * @link https://github.com/caasi-co-zw/Groomer
 * @version 1.3.10
 * @license GPL-3.0+ http://www.gnu.org/licenses/gpl-3.0.txt
 */
class Groomer
{
    /**
     * A list of pre-defined head tags types which are
     * * seo
     * * global
     * * fonts
     * * prefetch
     * * preload
     * * css
     * * js
     */
    const HEAD_TAGS_TYPES = [
        'seo' => 'seo',
        'global' => 'global',
        'fonts' => 'fonts',
        'prefetch' => 'prefetch',
        'preload' => 'preload',
        'css' => 'css',
        'js' => 'js',
    ];

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
     * Enable buffer to be compressed
     * @var bool
     */
    private $compressHtmlOutput = true;

    /**
     * Custom route regex presets
     * @var array
     */
    private $pageRouteRegexPresets = [];

    /**
     * These are printed even when SEO is disabled and may include css stylsheets urls
     */
    private $headTags = [];

    /**
     * Used to prevent duplicates head keys
     */
    private $headTagsKeys = [];
    /**
     * The page title
     * @var string
     */
    private $pageTitle;

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
    private $stylesheetsURI = [];

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
     * Custom version for your website and assets
     * @var string
     */
    private $version = '1.0';

    /**
     * URL to the manifest of the site
     * @var string
     */
    protected $manifest;

    /**
     * Manifest file with files to auto cache
     * @var string
     */
    protected $cacheManifest = null;

    /**
     * Localhost top domain level if any
     * @var string
     */
    protected $localTLD = 'localhost';
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
    protected $seoEnabled = true;

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
    protected $metaCallbacks = array();

    /**
     * Name of the website
     * @var string
     */
    public $siteName;

    public function __construct(array $config = [], $callback = null)
    {
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
            $this->pageCharset = get_bloginfo('charset');
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
    public function compressOutput($enabled)
    {
        $this->compressHtmlOutput = $enabled;
        return $this;
    }
    /**
     * Changes the title of the page.
     * @param string $pageTitle The new page title
     */
    public function setTitle(string $pageTitle)
    {
        $this->pageTitle = $pageTitle;

        // fix: opengraph & twitter titles as they were being left
        // with a separator only
        $this->addHeadTag(
            'og:title',
            new Meta([Meta::PROPERTY, 'og:title'], [Meta::CONTENT, $this->getTitle()]),
            self::HEAD_TAGS_TYPES['seo']
        );
        $this->addHeadTag(
            'twitter:title',
            new Meta([Meta::PROPERTY, 'twitter:title'], [Meta::CONTENT, $this->getTitle()]),
            self::HEAD_TAGS_TYPES['seo']
        );
        return $this;
    }

    /**
     * Changes the page page author name
     * @param string $pageAuthor Author name
     */
    public function setAuthor(string $pageAuthor)
    {
        $this->pageAuthor = $pageAuthor;
        return $this;
    }

    /**
     * Sets the twitter owner of the site
     * @param string $username Site username
     */
    public function setTwitterSite(string $username)
    {
        $this->twitterSite = $username;
        return $this;
    }

    /**
     * Sets a theme color for the web app
     * @param string $color A hex color
     */
    public function setThemeColor(string $color)
    {
        $this->pageThemeColor = $color;
        return $this;
    }

    /**
     * Sets the schema json for the page
     * @param string $schema The schema.org json
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
        return $this;
    }

    /**
     * Prepends the new seo for the page keywords
     * @param string|array $schema The seo keywords
     */
    public function addKeywords($seoKeywords)
    {
        if (is_array($seoKeywords)) :
            $this->seoKeywords = array_merge($this->seoKeywords, $seoKeywords);
        else :
            $this->seoKeywords[] = $seoKeywords;
        endif;
        return $this;
    }

    /**
     * Sets the new (overrides any existsing) seo keywords for the page
     * @param string|array $schema The seo keywords
     */
    public function setKeywords($seoKeywords)
    {
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
    public function setPostImage($img)
    {
        $this->pageThumbnail = $img;
        return $this;
    }

    /**
     * Sets the seo excerpt for the page
     * @param string $schema The seo excerpt
     */
    public function setExcerpt(string $excerpt)
    {
        $this->seoDescritpion = $excerpt;
        return $this;
    }

    public function getExcerpt()
    {
        return $this->seoDescritpion;
    }
    /**
     * Sets the seo excerpt for the page
     * @param string $schema The seo excerpt
     */
    public function setDetails(string $excerpt)
    {
        $this->seoDescritpion = $excerpt;
        return $this;
    }

    /**
     * Sets the seo excerpt for the page
     * @param string $schema The seo excerpt
     */
    public function setDescription(string $excerpt)
    {
        $this->seoDescritpion = $excerpt;
        return $this;
    }

    /**
     * Set the website pageLanguage
     * @var string
     */
    public function setLanguage(string $pageLanguage)
    {
        $this->pageLanguage = $pageLanguage;
        return $this;
    }

    /**
     * Enable or disable SEO
     * @param bool $seo Enables or disables SEO
     */
    public function setSeo(bool $seo)
    {
        $this->seoEnabled = $seo;
        return $this;
    }

    /**
     * Set the pageFavicon url
     * @param string $url Url of the pageFavicon
     */
    public function setFavicon(string $url)
    {
        $this->pageFavicon = $url;
        return $this;
    }

    /**
     * Adds a stylesheet to queque
     * @param array $sheets A list of the css sheet
     */
    public function addStyle(array $stylesheet)
    {
        $source = $stylesheet['src'] ?? $stylesheet['href'];
        $name = $stylesheet['name'] ?? $source;
        $async = (isset($stylesheet['async']) && $stylesheet['async']);
        $version = (!isset($stylesheet['control_version']) || !$stylesheet['control_version']) ?: '?ver=' . ($stylesheet['version'] ?? $this->version);
        $link = new Link(
            [Link::REL, Link::STYLESHEET],
            !$async ? null : [Link::TYPE, Link::CSS],
            [Link::HREF, $source . $version],
            !$async ? null : Link::PRELOAD_CSS,
        );

        // if async mentioned add a preload
        // prefix z is to make them appear last
        if ($async) {
            $this->addHeadTag(sprintf('zcss-preload-for-%s', $name), $link, self::HEAD_TAGS_TYPES['preload']);
            $link = new NoScript(['noscript', new Link(
                [Link::REL, Link::STYLESHEET],
                [Link::HREF, $source . $version],
            )]);
        }
        $this->addHeadTag(
            sprintf('z-css-for-%s', $name),
            $link,
            self::HEAD_TAGS_TYPES['css']
        );
        return $this;
    }

    /**
     * Adds a stylesheet to queque
     * @param array $sheets A list of the css sheet
     */
    public function addStyles(...$sheets)
    {
        if (is_array($sheets)) {
            // $this->stylesheetsURI = array_merge($this->stylesheetsURI, $sheets);
            foreach ($sheets as $sheet) {
                $this->addStyle($sheet);
            }
        }
        return $this;
    }

    /**
     * Adds stylesheetsURI to <style> tag
     * @param string $headCss CSS to be added in <headCss>
     */
    public function addInlineStyles(string $headCss)
    {
        if (!$this->headCss) {
            $this->headCss = $headCss;
        } else {
            $this->headCss .= $headCss;
        }
        $this->addHeadTag('zstyle', new Style(
            ['style', $this->headCss],
            Style::TYPE_CSS
        ), self::HEAD_TAGS_TYPES['css']);
        return $this;
    }

    /**
     * Adds a javascript file to queque
     * @param array $scripts A list of the scripts
     */
    public function addScripts(array ...$scripts)
    {
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
     * @param string $name
     * @param string $display
     * @param string $weight
     */
    public function addGoogleFont($name, $display = 'swap', $weight = '400')
    {
        $this->addHeadTag('gstatic-preconect', new Link([Link::REL, 'preconnect'], 'crossorigin', [Link::HREF, '//fonts.gstatic.com']), self::HEAD_TAGS_TYPES['prefetch']);
        $this->addHeadTag(sprintf('font-%s-preload', $name), new Link([Link::REL, 'preload'], ['as', 'css'], [Link::HREF, sprintf('//fonts.googleapis.com/css2?family=%s&display=%s', $name, $display)]), self::HEAD_TAGS_TYPES['preload']);
        $this->addHeadTag(sprintf('font-%s-prefetch', $name), new Link([Link::REL, 'stylesheet preload prefetch'], [Link::HREF, sprintf('//fonts.googleapis.com/css?family=%s:%s', $name, $weight)]), self::HEAD_TAGS_TYPES['global']);
        return $this;
    }

    /**
     * Change robots rules for page(s)
     * @param string $val New rule
     * @return Groomer
     */
    public function setRobots(string $val)
    {
        $this->robots = $val;
        return $this;
    }

    /**
     * Configure a custom domain name for your localhost site.
     * @param string $localTLD
     * @return Groomer
     */
    public function setDomainExtension($localTLD)
    {
        $this->localTLD = $localTLD;
        return $this;
    }

    /**
     * Sets the manifest url
     * @param string $url
     * @return Groomer
     */
    public function setManifest($url)
    {
        $this->manifest = $url;

        if ($this->isWordPress()) {
            $this->manifest = $this->wp_asset($url);
        }
        return $this;
    }

    /**
     * Returns true if installed on WordPress CMS
     * @return bool
     */
    public function isWordPress()
    {
        return class_exists('WP');
    }

    /**
     * Server name in this format caasi.co.zw
     * @return string
     */
    public function getServerName()
    {
        return $_SERVER['SERVER_NAME'];
    }

    /**
     * Returns the domain name
     * @return string
     */
    public function getAppName()
    {
        return explode('.', $this->getServerName())[0];
    }

    /**
     * Homepage url of the site in this format https://caasi.co.zw/
     * @return string
     */
    public function getWebsiteHome()
    {
        return (($this->onSecureConnection() ? 'https://' : 'http://') . $this->getServerName());
    }

    /**
     * Returns the TLD for the site
     */
    public function getCurrentTLD()
    {
        $site = explode('.', $this->getServerName());
        return $this->localTLD = end($site);
    }
    /**
     * Returns true if the page is on https
     * @return bool
     */
    public function onSecureConnection()
    {
        $ssl = $_SERVER['HTTPS'] ?? null;
        return $ssl === "on";
    }

    /**
     * Returns the current opened page url in this format https://caasi.co.zw/websites/
     * @return string
     */
    public function getCurrentPage()
    {
        return $this->getWebsiteHome() . $_SERVER['REQUEST_URI'];
    }

    /**
     * Returns a well formated page pageTitle
     * @return string
     * @var string
     */
    public function getTitle()
    {
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
    public function getDetails()
    {
        if ($this->isWordPress()) {
            return empty($this->seoDescritpion) ? get_the_excerpt() : $this->seoDescritpion;
        }
        return $this->seoDescritpion;
    }

    /**
     * Returns the document page author
     * @return string
     */
    public function getAuthor()
    {
        return $this->pageAuthor;
    }

    /**
     * Returns the url of the site pageFavicon
     * @return string
     */
    public function getFavicon()
    {
        return $this->pageFavicon;
    }
    public function getFaviconType()
    {
        return $this->getImageMime($this->getFavicon());
    }

    /**
     * Returns the theme color of the app
     * @return string
     */
    public function getThemeColor()
    {
        return $this->pageThemeColor ?? '#003883';
    }

    /**
     * Sets a new site name
     * @param string $sitename
     * @return Groomer
     */
    public function setSitename($sitename)
    {
        $this->siteName = $sitename;
        $this->addHeadTag(
            'og:site_name',
            new Meta(
                [Meta::PROPERTY, 'og:site_name'],
                [Meta::CONTENT, $this->siteName]
            ),
            self::HEAD_TAGS_TYPES['seo']
        );
        return $this;
    }
    /**
     * Returns the set site name
     * @return string|null
     */
    public function getSitename()
    {
        return $this->siteName;
    }

    /**
     * Sets the version controll number for your assets.
     * @param string|int $version
     * @return Groomer
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Returns the set version number
     * @return string|null
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $key 
     * @param string $value
     * @param string $source
     */
    final public function addHeadTag($key, $value, $source = null)
    {
        // set default source if not mentioned
        $source = $source ?? self::HEAD_TAGS_TYPES['global'];

        // if key already exists but in a different setting
        // then remove the old setting and make way for the new setting
        // eg. if name exists for seo only but the current user trying to
        // set it globally, remove for seo and set it globally
        if (array_key_exists($key, $this->headTagsKeys)) {
            $this->removeHeadTag($key, $source);
        }

        // store the key and its value
        $this->headTags[$source][$key] = $value;

        // record key setting
        $this->headTagsKeys[$key] = $source;
        return $this;
    }

    /**
     * Returns string of tags for the specified source
     * @param string|array $source
     * @return string
     */
    final public function getHeadTags(...$source)
    {
        $tags = '';
        $source = $source ?? [self::HEAD_TAGS_TYPES['global']];
        foreach ($source as $tag) {
            $tags .= implode($this->getTags($tag));
        }
        return $tags;
    }

    /**
     * @param string $key 
     * @param string $value
     * @param string $seo
     */
    final public function removeHeadTag($key, $source = null)
    {
        $source = $source ?? self::HEAD_TAGS_TYPES['global'];
        if (array_key_exists($key, $this->headTagsKeys)) {
            unset($this->headTags[$source][$key]);
        }
        return $this;
    }

    /**
     * Returns a list of all tags values
     * @return array
     */
    public function getTags($source = null)
    {
        $source = $source ?? self::HEAD_TAGS_TYPES['global'];
        return isset($this->headTags[$source]) ? array_values($this->headTags[$source]) : [];
    }

    /**
     * Returns true when the set local localTLD does not match the current localTLD
     * @return bool
     */
    public function isProductionServer()
    {
        $server_tld = explode('.', $this->getServerName());
        $server_tld = end($server_tld);
        return strtolower($server_tld) !== strtolower($this->localTLD);
    }

    /**
     * Returns true if running on a local server
     * @return bool
     */
    public function isTestingServer()
    {
        return !$this->isProductionServer();
    }

    /**
     * Enables or disables google translating the site
     */
    public function allowTranslating(bool $trans)
    {
        $this->translate = $trans;
        return $this;
    }

    /**
     * Returns $active if the url matches the request url
     * @param string $link The url to match to
     * @param string $active The text to return when url matches
     * @return string
     */
    public function active($link, string $active = 'active')
    {
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
    final public function getHead(string $pageTitle = null)
    {
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
        !$pageTitle ?: $this->setTitle($pageTitle);
        $documentLanguage = explode('-', $this->pageLanguage)[0];
        $cacheManifest = !$this->cacheManifest ? null : sprintf(' manifest="%s"', $this->cacheManifest);

        printf('<!DOCTYPE html><html lang="%s" dir="%s"%s><head>', $documentLanguage, $this->pageTextDirection, $cacheManifest);
        print($this->getHeadTags(self::HEAD_TAGS_TYPES['prefetch']));
        !$this->seoEnabled ?: print($this->getHeadTags(self::HEAD_TAGS_TYPES['seo']));
        print($this->getHeadTags(self::HEAD_TAGS_TYPES['global'], self::HEAD_TAGS_TYPES['preload'], self::HEAD_TAGS_TYPES['fonts'], self::HEAD_TAGS_TYPES['css'], self::HEAD_TAGS_TYPES['js']));
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
                    $headCss['src'] = get_template_directory_uri() . $headCss['src'];
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
        printf("<title>%s</title></head>", $this->getTitle());
        return $this;
    }

    /**
     * Returns the html + head section of the site.
     * @param string $pageTitle Dynamically change the page pageTitle
     * @param \callable $cb A callback function to be executed before the function stops
     */
    final public function getMeta($pageTitle = null)
    {
        $this->getHead($pageTitle);
        return $this;
    }

    /**
     * Opens the body tag adding any necessary classes or additional arguments.
     * @param string $class Body class
     * @param string $args Additional arguments to be added
     */
    final public function openBody($class = null, $args = null)
    {
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
    public function isCrawlingBot()
    {
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
    final public static function wp_asset(string $url)
    {
        return get_template_directory_uri() . $url;
    }

    /**
     * Returns the stylesheet for index or all stylesheets
     * @return array
     */
    protected function getStyles(int $index = null)
    {
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
    final protected function getScripts(int $index = null, bool $footer = true)
    {
        $type = $footer ? 'footer' : 'head';
        return ($index === null) ? $this->javascriptsURI[$type] : $this->javascriptsURI[$type][$index];
    }

    /**
     * Returns the robots rules
     * @return string
     */
    protected function getRobots()
    {
        return $this->robots;
    }

    /**
     * Returns true if translation is enabled
     * @return bool
     */
    protected function getTranslate()
    {
        return $this->translate;
    }

    /**
     * Returns the SEO seoKeywords set for the page.
     * @return string
     */
    protected function getKeywords()
    {
        if (is_array($this->seoKeywords)) {
            $this->seoKeywords = implode(',', $this->seoKeywords);
        }
        return $this->seoKeywords;
    }
    protected function getPostImage()
    {
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
        if ($this->pageThumbnail && $this->pageThumbnail[0] == '/') {
            return $this->getWebsiteHome() . $this->pageThumbnail;
        }
        return $this->getWebsiteHome() . '/' . $this->pageThumbnail;
    }

    protected function addCompressionRule(string $regex, string $replacement)
    {
        $this->outputBufferRegex['replace'][] =  $regex;
        $this->outputBufferRegex['with'][] =  $replacement;
        return $this;
    }

    /**
     * Compresses the html to be out-puted
     * @var string
     */
    final protected function _compressOutput($buffer)
    {
        $buffer = preg_replace($this->outputBufferRegex['replace'], $this->outputBufferRegex['with'], $buffer);
        return $buffer;
    }

    /**
     * Prevents the body tag from beng called when unecessary.
     *
     * This function must be called just before the header tag if it's in a function.
     */
    final protected function beforeHeader()
    {
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
    final protected function beforeMenu()
    {
        if (!$this->pageBodyOpened) {
            $this->openBody();
        }
        return $this;
    }

    /**
     * Prints the html markup (static by default) of the website footer.
     * @param array $scripts An array of footer scripts.
     */
    final protected function beforeFooter(array $scripts = [])
    {
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
    final protected function printScripts($script)
    {
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
                    get_template_directory_uri() . $javascriptsURI['src'],
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
    final protected function printStylesheets($headCss)
    {
        if (!$headCss) {
            return;
        }

        if (!$this->isWordPress()) :
            if (!is_array($headCss)) {
                printf("<link rel=\"stylesheet\" type=\"text/css\"  href=\"%s?ver=%s\" media=\"all\">", $headCss, $this->version);
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
                    $stylesheetsURI .= sprintf("rel=\"stylesheet\" type=\"text/css\"  href=\"%s", $headCss['href'], $this->version);
                endif;
                if ($version) :
                    $stylesheetsURI .= sprintf("?ver=%s\" ", isset($headCss['version']) ? $headCss['version'] : $this->version);
                else :
                    $stylesheetsURI .= sprintf("\" ");
                endif;
                if ($async) :
                    $stylesheetsURI .= 'as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
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
     * Adds a function to the systemTriggers
     * @return bool
     */
    private function addTrigger(string $name, callable $callback)
    {
        $name = strtolower($name);
        if (!array_key_exists($name, $this->systemTriggers)) {
            return false;
        }
        $this->systemTriggers[$name][] = $callback;
        return true;
    }
    private function __initializeDefaults()
    {
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

        // set global meta tags
        $tags = [
            'charset' => new Meta(['charset', strtoupper($this->pageCharset)]),
            'content-type' => new Meta([Meta::HTTP_EQUIV, 'Content-Type'], [Meta::CONTENT, 'text/html']),
            'x-ua-compatible' => new Meta([Meta::HTTP_EQUIV, 'X-UA-Compatible'], [Meta::CONTENT, 'IE=edge']),
            'viewport' => new Meta([Meta::NAME, 'viewport'], [Meta::CONTENT, 'width=device-width, initial-scale=1,shrink-to-fit=no']),
            'application-name' => new Meta([Meta::NAME, 'application-name'], [Meta::CONTENT, $this->getAppName()]),
            'mobile-web-app-capable' => new Meta([Meta::NAME, 'mobile-web-app-capable'], [Meta::CONTENT, 'yes']),
            'theme-color' => new Meta([Meta::NAME, 'theme-color'], [Meta::CONTENT, $this->getThemeColor()]),
            'format-detection' => new Meta([Meta::NAME, 'format-detection'], [Meta::CONTENT, "telephone=no"]),
            'apple-mobile-web-app-capable' => new Meta([Meta::NAME, 'apple-mobile-web-app-capable'], [Meta::CONTENT, "yes"]),
            'apple-mobile-web-app-status-bar-style' => new Meta([Meta::NAME, 'apple-mobile-web-app-status-bar-style'], [Meta::CONTENT, $this->getThemeColor()]),
            'msapplication-TileColor' => new Meta([Meta::NAME, 'msapplication-TileColor'], [Meta::CONTENT, $this->getThemeColor()]),
            'msapplication-TileImage' => new Meta([Meta::NAME, 'msapplication-TileImage'], [Meta::CONTENT, $this->getPostImage()]),
            'author' => new Meta([Meta::NAME, 'author'], [Meta::CONTENT, $this->getAuthor()]),
        ];
        $this->isWordPress() ?: $tags['generator'] = new Meta([Meta::NAME, 'generator'], [Meta::CONTENT, $this->systemName]);
        $tags['canonical'] = new Meta([Meta::NAME, 'canonical'], [Meta::HREF, $this->getCurrentPage()]);
        !$this->manifest ?: $tags['manifest'] = new Link([Link::MANIFEST], [Link::HREF, $this->manifest]);
        !$this->getFavicon() ?: $tags['shortcut-icon'] = new Link([Link::REL, 'shortcut icon'], [Link::TYPE, $this->getFaviconType()], [Link::HREF, $this->getFavicon()]);

        // add default seo tags
        $seo_tags = [
            'keywords' => new Meta([Meta::NAME, 'keywords'], [Meta::CONTENT, $this->getKeywords()]),
            'description' => new Meta([Meta::NAME, 'description'], [Meta::CONTENT, $this->getDetails()]),
            'og:url' => new Meta([Meta::PROPERTY, 'og:url'], [Meta::CONTENT, $this->getCurrentPage()]),
            'og:locale' => new Meta([Meta::PROPERTY, 'og:locale'], [Meta::CONTENT, str_replace("-", "_", $this->pageLanguage)]),
            'og:type' => new Meta([Meta::PROPERTY, 'og:type'], [Meta::CONTENT, $this->isHomePage() ? 'website' : 'article']),
            'og:title' => new Meta([Meta::PROPERTY, 'og:title'], [Meta::CONTENT, $this->getTitle()]),
            'og:description' => new Meta([Meta::PROPERTY, 'og:description'], [Meta::CONTENT, $this->getDetails()]),
            'og:image' => new Meta([Meta::PROPERTY, 'og:image'], [Meta::CONTENT, $this->getPostImage()]),
            'og:image_alt' => new Meta([Meta::PROPERTY, 'og:image_alt'], [Meta::CONTENT, $this->thumbnailDescription]),
            'og:site_name' => new Meta([Meta::PROPERTY, 'og:site_name'], [Meta::CONTENT, $this->siteName]),
            'twitter:image' => new Meta([Meta::PROPERTY, 'twitter:image'], [Meta::CONTENT, $this->getPostImage()]),
            'twitter:card' => new Meta([Meta::PROPERTY, 'twitter:card'], [Meta::CONTENT, $this->twitterCardType]),
            'twitter:title' => new Meta([Meta::PROPERTY, 'twitter:title'], [Meta::CONTENT, $this->getTitle()]),
            'twitter:description' => new Meta([Meta::PROPERTY, 'twitter:description'], [Meta::CONTENT, $this->getDetails()]),
        ];
        !$this->twitterSite ?: $seo_tags['twitter:site'] = new Meta([Meta::PROPERTY, 'twitter:site'], [Meta::CONTENT, '@' . $this->twitterSite]);

        // add tags to system
        foreach ($tags as $key => $value) {
            $this->addHeadTag($key, $value);
        }
        foreach ($seo_tags as $key => $value) {
            $this->addHeadTag($key, $value, self::HEAD_TAGS_TYPES['seo']);
        }
    }

    private function isHomePage()
    {
        return ($this->getCurrentPage() == $this->getWebsiteHome() . '/');
    }
    private function getImageMime($img)
    {
        $mime = getimagesize($img);
        return $mime['mime'];
    }
};
