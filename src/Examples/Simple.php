<?php
include __DIR__ . '/../manuload.php';

class Webpage extends Caasi\Groomer
{
    public $name;
    /**
     * @var bool
     */
    public function __construct($config = [], $cb = null)
    {
        $this->sitename = 'Demo';
        $this->addStyles(
            [
                'src' => '/css/bootstrap.css',
                'name' => 'tbs'
            ],
            [
                'src' => '/css/style.min.css',
                'name' => 'cs'
            ],
        );
        $this->addScripts(
            array(
                'src' => "/js/jquery-3.6.0.min.js",
                'control_version' => false,
                'async' => false,
                'name' => 'jqy',
                'footer' => false
            )
        );
        $this->version = 1.01;
        $this->tld = 'com';
        parent::__construct($config, $cb);
        $this->name = &$this->sitename;
        return $this;
    }
    public function getHeader()
    {
        //parent::beforeGetHeader();
        return $this;
    }
    public function getMenu()
    {
        //parent::beforeGetMenu();
?>

    <?php return $this;
    }
    public function getSearch()
    {
    }
    public function getFooter(array $scripts = [])
    {
        //parent::beforeGetFooter($scripts);
    ?>
        </body>

        </html>
<?php
    }
}
