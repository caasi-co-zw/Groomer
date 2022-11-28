<?php

namespace Caasi;

if (!defined('MANAGE_SESSION') || MANAGE_SESSION === true) :
    session_start();
endif;

if (!defined('WP_SHORTEN_ASSETS_URL')) {
    @define('WP_SHORTEN_ASSETS_URL', false);
}

if (!defined('SERVER_NAME')) :
    /**
     * Server name in this format caasi.co.zw
     * @var string
     */
    define('SERVER_NAME', $_SERVER['SERVER_NAME']);
endif;

if (!defined('APP_NAME')) {
    /**
     * Returns the domain name
     * @var string
     */
    define('APP_NAME', ucwords(explode('.', SERVER_NAME)[0]));
}

/**
 * Homepage url of the site in this format https://caasi.co.zw/
 * @var string
 */
define('WEBSITE_HOME', ((isset($_SERVER['HTTPS']) && "on" === $_SERVER['HTTPS']) ? 'https://' : 'http://') . SERVER_NAME);

/**
 * Returns the current opened page url in this format https://caasi.co.zw/websites/
 * @var string
 */
define('CURRENT_PAGE', WEBSITE_HOME . $_SERVER['REQUEST_URI']);

/**
 * Returns true if on a wordpress website.
 * @var bool
 */
define('IS_WORDPRESS', class_exists('\WP') && function_exists('wp_enqueue_script'));

if (!defined("PG_TITLE")) :
    /**
     * Returns set page title
     * @var string
     */
    define('PG_TITLE', "Caasi Groomer");
endif;

if (!defined("INC_DATABASE")) :
    /**
     * Will include the database file when set to true
     * @var bool
     */
    define('INC_DATABASE', true);
endif;

if (!defined('DATABASE_FILE')) {
    /**
     * Returns database file path
     * @var string
     */
    define('DATABASE_FILE', __DIR__ . '/Database.php');
}

if (!defined('BOOTSTRAP_NAV_WALKER')) {
    /**
     * Returns the path to Bootstrap Navigation Walker
     * @var string
     */
    define('BOOTSTRAP_NAV_WALKER', __DIR__ . '/Wordpress/Bootstrap_Nav_Walker.php');
}

if (!defined('COMMMENTS_WALKER')) {
    /**
     * Returns the path to Custom Comments Walker
     * @var string
     */
    define('COMMMENTS_WALKER', __DIR__ . '/Wordpress/Comments_Walker.php');
}

if (file_exists(DATABASE_FILE) && INC_DATABASE) {
    include DATABASE_FILE;
}


/**
 * Prepares and prints the markup to the browser.
 *
 * Optimized to make custom web development easy.
 * @author Isaac <isaac@caasi.co.zw>
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
    private $system_name         = 'Caasi Groomer';

    /**
     * Developer name
     * @var string
     */
    private $developer         = 'Caasi';

    /**
     * Developer website
     * @var string
     */
    private $developer_url         = 'www.caasi.co.zw';

    /**
     * REGEX Patter to be searched
     */
    private $ob_regex = array(
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

    /**
     * Custom route regex presets
     * @var array
     */
    private $route_presets = array(
        '{all}' => '.*',
        '{alpha}' => '([a-zA-Z])+',
        '{any}' => '[^/]+',
        '{num}' => '\d+|-\d+',
    );

    /**
     * The page title
     * @var string
     */
    protected $title;

    /**
     * SEO Keywords for page
     * @var array
     */
    protected $keywords = [];

    /**
     * The SEO description of the page
     * @var string
     */
    protected $details;

    /**
     * Author of the website
     * @var string
     */
    protected $author         = 'Caasi';

    /**
     * Website favicon url
     * @var string
     */
    protected $favicon        = '/favicon.ico';

    /**
     * Favicon mime image type
     * @var string
     */
    protected $favicon_type = 'image/icon';

    /**
     * The SEO thumbnail image url
     * @var string
     */
    protected $thumbnail;

    /**
     * SEO thumbnail image alt text
     * @var string
     */
    protected $thumbnail_alt;

    /**
     * A list of all css stylesheets and their properties
     * @var array
     */
    protected $css = [];

    /**
     * Text direction of the site
     * @var string
     */
    protected $text_dir = 'ltr';

    /**
     * The site language
     * @var string
     */
    protected $language = 'en-ZW';

    /**
     * How to display post on twitter
     * @var string
     */
    protected $tw_card = 'summary_large_image';

    /**
     * Who to credit post on twitter
     * @var string
     */
    protected $tw_site;

    /**
     * Triggers for this generator
     * @var array
     */
    protected $triggers = [];

    /**
     * An array of all default javascript files to be added to the header
     * @var array
     */
    protected $js = array(
        'head' => [],
        'footer' => []
    );

    /**
     * Facebook page ig
     * @var string
     */
    protected $facebook_id;

    /**
     * Charset encoding for the site
     * @var string
     */
    protected $charset = 'utf-8';

    /**
     * Page rules for robots
     */
    protected $robots      = "index";

    /**
     * Theme color of site
     * @var string
     */
    protected $theme_color = "#a70c0c";

    /**
     * Custom page styling to be printed in the head
     * @var string
     */
    protected $style       = null;

    /**
     * Allow google translate
     * @var bool
     */
    protected $translate   = false;

    /**
     * Enable buffer to be compressed
     * @var bool
     */
    protected $compress_output = true;

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
    protected $tld;

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
    protected $body_open = false;

    /**
     * Home page url
     * @var string
     */
    protected $home_url;

    /**
     * Current page url
     * @var string
     */
    protected $current_url;

    /**
     * Wordpress theme details
     * @var object
     */
    public $theme;

    /**
     * Name of the website
     * @var string
     */
    public $sitename;

    public function __construct(array $config, $callback = null) {
        // set default alt text
        $this->thumbnail_alt = 'Our logo';
        $this->home_url = ((isset($_SERVER['HTTPS']) && "on" === $_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'];
        $this->current_url = $this->home_url . $_SERVER['REQUEST_URI'];

        if (IS_WORDPRESS) :

            // exit if called directly and on wordpress
            defined('ABSPATH') || exit;

            // get theme details
            $this->theme = wp_get_theme();
            $this->sitename = get_bloginfo('name');

            // site info
            if (get_bloginfo('description')) {
                $this->details = get_bloginfo('description');
            }

            // meta data
            $this->text_dir = is_rtl() ? 'rtl' : 'ltr';
            $this->charset = get_bloginfo('charset');
            $this->version = $this->theme->get('Version');
            $this->language = get_bloginfo('language');

            // device type
            $this->ismobile = wp_is_mobile();

            // fix site icon
            if (has_site_icon()) :
                $this->favicon = get_site_icon_url();
            endif;

            // default keywords
            $this->setKeywords(
                array(
                    $this->system_name,
                )
            );
        endif;

        $this->isproduction = $this->isProductionServer();
        if (!$this->tld) {
            $this->tld = end(explode('.', SERVER_NAME));
        }

        // initialized system configurations
        foreach ($config as $key => $value) {
            switch ($key) {
                case 'title':
                    $this->title = $value;
                    break;
                case 'author':
                    $this->author = ucwords($value);
                    break;
                case 'details':
                    $this->details = $value;
                    break;
                case 'keywords':
                    if (is_array($value)) {
                        $this->keywords = '';
                        for ($j = 0; $j < count($value); $j++) {
                            $this->keywords .= ($value[$j]);
                            if ($j + 1 !== count($value)) {
                                $this->keywords .= ',';
                            }
                        }
                    } else {
                        $this->keywords = ($value);
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
                case 'css':
                    if ($value) {
                        if (is_array($value)) {
                            for ($j = 0; $j < count($value); $j++) {
                                $this->css[] = $value[$j];
                            }
                        } else {
                            $this->css = array_merge(array($value), $this->css);
                        }
                    }
                    break;
                case 'js':
                case 'footer_js':
                    if (is_array($value)) {
                        for ($j = 0; $j < count($value); $j++) {
                            $this->js['footer'][] = $value[$j];
                        }
                    } else {
                        $this->js['footer'][] = $value;
                    }
                    break;
                case 'header_js':
                    if (is_array($value)) {
                        for ($j = 0; $j < count($value); $j++) {
                            $this->js['head'][] = $value[$j];
                        }
                    } else {
                        $this->js['head'][] = $value;
                    }
                    break;
                case 'favicon':
                    $this->favicon = $value;
                    break;

                case 'menu':
                    $this->menu = $value;
                    break;
                case 'style':
                    $this->style = $value;
                    break;
                case 'robots':
                    $this->robots = $value;
                    break;
                case 'post_image':
                    $this->post_image = $value;
                    break;
                default:
                    # log to errors
                    $this->errors[] = $key . ' was not recognized.';
                    break;
            }
        }

        // send a system header
        header(sprintf("X-Powered-By: %s", $this->system_name));

        // compress html if enabled
        if ($this->compress_output) :
            ob_start([$this, 'compressOutput']);
        endif;

        // callback function
        if ($callback && is_callable($callback)) :
            call_user_func($callback);
        endif;
    }

    /**
     * Create your own regex presets for cleaner code.
     */
    public function addRouteVariable(string $route, string $pattern) {
        $this->route_presets[$route] =  $pattern;
        return $this;
    }

    /**
     * Can be used to route your urls on a custom website
     * @param array|string $method The request method to route for eg. GET
     * @param string $pattern The url pattern to route for.
     * @return bool
     */
    public function route($method, $pattern, $callback) {
        // convert to uppercase
        if (is_array($method)) {
            array_walk($method, function (&$value) {
                strtoupper($value);
            });
        } else {
            $method = array(strtoupper($method));
        }

        $params = array();
        $pattern = "~^$pattern$~";

        foreach ($this->route_presets as $key => $value) {
            $pattern = str_replace($key, $value, $pattern);
        }

        // if request method routed
        if ($method[0] == 'ALL' || in_array($_SERVER['REQUEST_METHOD'], $method)) {

            if (preg_match_all($pattern, $_SERVER['REQUEST_URI'])) {

                // prepare parameters
                $url = explode('/', str_replace(['~', '^', '$'], '', $pattern));
                $uri = explode('/', $_SERVER['REQUEST_URI']);

                foreach ($uri as $link) {
                    if (!in_array($link, $url)) {
                        $params[] = $link;
                    }
                }

                if (count($params) == 1) {
                    $params = $params[0];
                }

                // route matched
                if (isset($callback)) {
                    $callback($params);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Changes the title of the page.
     * @param string $title The new title
     */
    public function setTitle(string $title) {
        $this->title = $title;
        return $this;
    }

    /**
     * Changes the page author
     * @param string $author Author name
     */
    public function setAuthor(string $author) {
        $this->author = $author;
        return $this;
    }

    /**
     * Sets the twitter owner of the site
     * @param string $username Site username
     */
    public function setTwitterSite(string $username) {
        $this->tw_site = $username;
        return $this;
    }

    /**
     * Sets a theme color for the web app
     * @param string $color A hex color
     */
    public function setThemeColor(string $color) {
        $this->theme_color = $color;
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
     * Prepends the new seo keywords for the page
     * @param string|array $schema The seo keywords
     */
    public function addKeywords($keywords) {
        if (is_array($keywords)) :
            $this->keywords = array_merge($this->keywords, $keywords);
        else :
            $this->keywords[] = $keywords;
        endif;
        return $this;
    }

    /**
     * Sets the new (overrides any existsing) seo keywords for the page
     * @param string|array $schema The seo keywords
     */
    public function setKeywords($keywords) {
        if (is_array($keywords)) :
            $this->keywords = $keywords;
        else :
            $this->keywords = [$keywords];
        endif;
        return $this;
    }

    /**
     * Sets the page thumbnail
     * @param string $schema The thumbnail url
     */
    public function setPostImage($img) {
        $this->thumbnail = $img;
        return $this;
    }

    /**
     * Sets the seo excerpt for the page
     * @param string $schema The seo excerpt
     */
    public function setExcerpt(string $excerpt) {
        $this->details = $excerpt;
        return $this;
    }

    /**
     * Sets the seo excerpt for the page
     * @param string $schema The seo excerpt
     */
    public function setDetails(string $excerpt) {
        $this->details = $excerpt;
        return $this;
    }

    /**
     * Sets the seo excerpt for the page
     * @param string $schema The seo excerpt
     */
    public function setDescription(string $excerpt) {
        $this->details = $excerpt;
        return $this;
    }

    /**
     * Set the website language
     * @var string
     */
    public function setLanguage(string $language) {
        $this->language = $language;
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
     * Set the favicon url
     * @param string $url Url of the favicon
     */
    public function setFavicon(string $url) {
        $this->favicon = $url;
        return $this;
    }

    /**
     * Adds a stylesheet to queque
     * @param array $sheets A list of the style sheet
     */
    public function addStyles(array ...$sheets) {
        $this->css = array_merge($this->css, $sheets);
        return $this;
    }

    /**
     * Adds css to <style> tag
     * @param string $style CSS to be added in <style>
     */
    public function setStyles(string $style) {
        if (!$this->style) {
            $this->style = $style;
        } else {
            $this->style .= $style;
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

        $this->js['head'] = array_merge($this->js['head'], $head);
        $this->js['footer'] = array_merge($this->js['footer'], $footer);

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

    /**
     * Returns a well formated page title
     * @return string
     * @var string
     */
    public function getTitle() {
        if (!IS_WORDPRESS) {
            return $this->title . " " . html_entity_decode("&ndash;") . " " . $this->sitename;
        } else {
            return sprintf("%s %s %s", $this->title, html_entity_decode('&ndash;'),  $this->sitename);
        }
        return '';
    }

    /**
     * Returns the SEO site description
     * @return string
     * @var string
     */
    public function getDetails() {
        if (IS_WORDPRESS) {
            return empty($this->details) ? get_the_excerpt() : $this->details;
        }
        return $this->details;
    }

    /**
     * Returns the document author
     * @return string
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * Returns the url of the site favicon
     * @return string
     */
    public function getFavicon() {
        return $this->favicon;
    }

    /**
     * Returns the theme color of the app
     * @return string
     */
    public function getThemeColor() {
        return $this->theme_color ?? '#003883';
    }

    /**
     * Returns true when the set local tld does not match the current tld
     * @return bool
     */
    public function isProductionServer() {
        $server_tld = explode('.', SERVER_NAME);
        $server_tld = end($server_tld);
        return strtolower($server_tld) !== strtolower($this->tld);
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
        if (IS_WORDPRESS) {
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
     * @param string $title Dynamically change the page title
     * @param callback $cb A callback function to be executed before the function stops
     */
    public function getHead(string $title = null, callable $cb = null) {
        if (IS_WORDPRESS) {
            if (!$this->title) {
                $this->title = get_the_title();
            }
            $this->setKeywords(
                array(
                    $this->getTitle(),
                    $this->getDetails(),
                )
            );
        }
        if ($title) {
            $this->title = $title;
        } ?>
        <!DOCTYPE html>
        <html lang="<?= $this->language ?>" dir="<?= $this->text_dir ?>">

        <head>
            <meta charset="<?= $this->charset ?>" http-equiv="Content-Type" content="text/html">
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
            <?php if (!IS_WORDPRESS) : ?>
                <meta name="generator" content="<?= $this->system_name ?>">
            <?php endif; ?>
            <meta name="canonical" href="<?= CURRENT_PAGE; ?>">
            <?php if ($this->seo) : ?>
                <meta name="keywords" content="<?= $this->getKeywords(); ?>">
                <meta name="description" content="<?= $this->getDetails(); ?>">
                <meta property="og:url" content="<?= CURRENT_PAGE; ?>">
                <meta property="og:locale" content="<?= str_replace("-", "_", $this->language) ?>">
                <?php if (CURRENT_PAGE == WEBSITE_HOME . '/') : ?>
                    <meta property="og:type" content="website">
                <?php else : ?>
                    <meta property="og:type" content="article">
                <?php endif; ?>
                <meta property="og:title" content="<?= $this->getTitle(); ?>">
                <meta property="og:description" content="<?= $this->getDetails(); ?>">
                <meta property="og:image" content="<?= $this->getPostImage(); ?>">
                <meta property="og:image_alt" content="<?= $this->thumbnail_alt ?>">
                <meta property="og:site_name" content="<?= $this->sitename; ?>">
                <meta property="twitter:card" content="<?= $this->tw_card ?>">
                <meta property="twitter:title" content="<?= $this->getTitle(); ?>">
                <meta property="twitter:description" content="<?= $this->getDetails(); ?>">
                <?php if ($this->tw_site) : ?>
                    <meta property="twitter:site" content="@<?= $this->tw_site ?>">
                <?php endif; ?>
                <meta property="twitter:image" content="<?= $this->getPostImage() ?>">
            <?php endif;
            if ($this->facebook_id) : ?>
                <meta property="fb:app_id" content="<?= $this->facebook_id ?>">
            <?php endif;
            if ($this->fonts) : foreach ($this->fonts as $font) :
                    $this->printFonts($font);
                endforeach;
            endif; ?>
            <link rel="shortcut icon" href="<?= $this->getFavicon(); ?>" type="<?= $this->favicon_type ?>">
            <?php

            if ($this->manifest) :
                printf("<link rel=\"manifest\" href=\"%s\">", $this->manifest);
            endif;
            if (!IS_WORDPRESS) :
                foreach ($this->getStyles() as $style) {
                    $this->printStylesheets($style);
                }
                for ($i = 0; $i < count($this->getScripts(null, false)); $i++) {
                    $this->printScripts($this->getScripts($i, false));
                }
            else :
                add_action('wp_enqueue_scripts', function () {
                    $styles = $this->getStyles();
                    foreach ($styles as $style) :
                        $name = $style['name'] ?? uniqid('csg');
                        wp_register_style(sprintf("csg-%s", $name), get_template_directory_uri() . $style['src'], $style['deps'] ?? [], $style['version'] ?? $this->version, $style['media'] ?? 'all');
                        wp_enqueue_style(sprintf("csg-%s", $name));
                    endforeach;
                });
                add_action('wp_enqueue_scripts', function () {
                    $this->printScripts($this->js['head']);
                });
                add_action('wp_enqueue_scripts', function () {
                    $this->printScripts($this->js['footer']);
                });
                wp_head();
            endif;
            // @define('PG_TITLE', $this->getTitle());
            printf("<title>%s</title>", $this->getTitle());
            if ($this->style) :
                printf("<style type=\"text/css\">%s</style>", $this->style);
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
     * @param string $title Dynamically change the page title
     * @param callback $cb A callback function to be executed before the function stops
     */
    public function getMeta($title = null, $cb = null) {
        $this->getHead($title, $cb);
        return $this;
    }

    /**
     * Opens the body tag adding any necessary classes or additional arguments.
     * @param string $class Body class
     * @param string $args Additional arguments to be added
     */
    public function openBody($class = null, $args = null) {
        if ($this->body_open) {
            return $this;
        }
        echo "<body";
        if (IS_WORDPRESS) :
            echo ' ';
            body_class($class);
        elseif ($class) :
            printf(" class=\"%s\"", $class);
        endif;
        if ($args) :
            echo $args;
        endif;
        echo ">";
        $this->body_open = true;

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
            return $this->css;
        } else {
            return $this->css[$index] ?? null;
        }
    }

    /**
     * Returns js properties for index or all if no index
     * @param int|null $index [optional] returns a script from that index
     * @param bool $footer [optional] returns a script from that index
     * @return array
     */
    protected function getScripts(int $index = null, bool $footer = true) {
        $type = $footer ? 'footer' : 'head';

        if (null === $index) {
            return $this->js[$type];
        } else {
            return $this->js[$type][$index];
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
     * Returns the SEO keywords set for the page.
     * @return string
     */
    protected function getKeywords() {
        $kw = '';
        $count = 1;
        if (is_array($this->keywords)) {
            foreach ($this->keywords as $word) {
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
        return $this->keywords;
    }
    protected function getPostImage() {
        if (IS_WORDPRESS) {
            if (has_post_thumbnail()) {
                $this->thumbnail_alt = the_post_thumbnail_caption();
                return the_post_thumbnail_url();
            }
            if (has_site_icon()) {
                $this->thumbnail_alt = get_site_icon_url();
                return get_site_icon_url();
            }
            return $this->thumbnail;
        }
        return WEBSITE_HOME . '/' . $this->thumbnail;
    }

    protected function addCompressionRule(string $regex, string $replacement) {
        $this->ob_regex['replace'][] =  $regex;
        $this->ob_regex['with'][] =  $replacement;
        return $this;
    }

    /**
     * Compresses the html to be out-puted
     * @var string
     */
    protected final function compressOutput($buffer) {
        $buffer = preg_replace($this->ob_regex['replace'], $this->ob_regex['with'], $buffer);
        return $buffer;
    }

    /**
     * Prevents the body tag from beng called when unecessary.
     *
     * This function must be called just before the header tag if it's in a function.
     */
    protected function beforeHeader() {
        if (!$this->body_open) {
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
        if (!$this->body_open) {
            $this->openBody();
        }
        return $this;
    }

    /**
     * Prints the html markup (static by default) of the website footer.
     * @param array $scripts An array of footer scripts.
     */
    protected function beforeFooter(array $scripts = []) {
        $this->js['footer'] = array_merge($scripts, $this->js['footer']);
        if (!IS_WORDPRESS) {
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

        if (!IS_WORDPRESS) :
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
            foreach ($script as $js) :
                $name = $js['name'] ?? uniqid('csg');
                $version = $this->version;

                if (isset($js['control_version'])) {
                    $version = isset($js['version']) ? $js['version'] : $this->version;
                }
                if (!$jquery && strpos($js['src'], "jquery")) {
                    wp_deregister_script('jquery');
                    $jquery = true;
                }
                wp_register_script(
                    sprintf("csg-%s", $name),
                    get_template_directory_uri() . $js['src'],
                    $js['deps'] ?? [],
                    $version,
                    $js['footer'] ?? true
                );
                wp_enqueue_script(sprintf("csg-%s", $name));
            endforeach;
        endif;
        return $this;
    }

    /**
     * Enqueues a style for printing
     * @param string $style The stylesheet file property
     */
    protected function printStylesheets($style) {
        if (!$style) {
            return;
        }

        if (!IS_WORDPRESS) :
            if (!is_array($style)) {
                printf("<link rel=\"stylesheet\" type=\"text/css\"  href=\"%s?ver=%s\" media=\"all\">", $style, $this->version);
            } else {
                if (!isset($style['href'])) {
                    $style['href'] = $style['src'];
                }
                $async = $style['async'] ?? false;
                $media = $style['media'] ?? 'all';
                $version = $style['control_version'] ?? false;
                $css = '<link ';
                if ($async) :
                    $css .= sprintf("rel=\"preload\" href=\"%s", $style['href']);
                else :
                    $css .= sprintf("rel=\"stylesheet\" type=\"text/css\"  href=\"%s", $style['href'], $this->version);
                endif;
                if ($version) :
                    $css .= sprintf("?ver=%s\" ", isset($style['version']) ? $style['version'] : $this->version);
                else :
                    $css .= sprintf("\" ");
                endif;
                if ($async) :
                    $css .= 'as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
                    $css .= sprintf("<noscript><link rel=\"stylesheet\" href=\"%s", $style['href']);
                    if ($version) :
                        $css .= sprintf("?ver=%s", $this->version);
                    endif;
                    $css .= sprintf("\"></noscript>");
                else :
                    $css .= sprintf("media=\"%s\">", $media, $style['href'], $this->version);
                endif;
                echo $css;
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
            $html .= sprintf("<link rel=\"preload\" as=\"style\" href=\"//fonts.googleapis.com/css2?family=%s&display=%s\">", $font_name, $font['display'] ?? 'swap');
            $html .= sprintf("<link rel=\"stylesheet preload prefetch\" href=\"//fonts.googleapis.com/css?family=%s:%s\">", $font_name, $font['weight'] ?? '400');
            print($html);
        }
        return $this;
    }

    /**
     * Adds a function to the triggers
     * @return bool
     */
    private function addTrigger(string $name, callable $callback) {
        $name = strtolower($name);
        if (!array_key_exists($name, $this->triggers)) {
            return false;
        }
        $this->triggers[$name][] = $callback;
        return true;
    }
};
