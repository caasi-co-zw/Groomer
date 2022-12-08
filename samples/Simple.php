<?php

use Caasi\Groomer;

include __DIR__ . '/../vendor/autoload.php';

class Webpage extends Groomer {
    public $name;
    /**
     * @var bool
     */
    public function __construct() {
        $this->setSitename('Demo');
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
        parent::__construct();
        $this->setVersion(1.01);
        $this->setDomainExtension('com');
        $this->name = &$this->getSitename();
    }
    public function getHeader() {
        //parent::beforeGetHeader();
        return $this;
    }
    public function getMenu() {
        //parent::beforeGetMenu();
?>
    <?php return $this;
    }
    public function getSearch() {
    }
    public function getFooter(array $scripts = []) {
        //parent::beforeGetFooter($scripts);
    ?>
        </body>

        </html>
<?php
    }
}
