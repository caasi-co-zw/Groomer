<?php


include __DIR__ . '/../vendor/autoload.php';

class Webpage extends Groomer\Groomer
{
    public $name;
    /**
     * @var bool
     */
    public function __construct()
    {
        parent::__construct();
        $this->setSeo(true);
        $this->setSitename('Groomer Demo');
        $this->addStyles(
            [
                'src' => '/samples/assets/style.css',
                'name' => 'cs',
                'control_version' => true
            ],
        );
        $this->addGoogleFont('Poppins');
        $this->setVersion(1.01);
        $this->setDomainExtension('test');
        $this->name = $this->getSitename();
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
