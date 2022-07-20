<?php

namespace Caasi\Groomer;

include __DIR__ . '/Groomer.php';
include __DIR__ . '/NavWalkerBootstrap.php';

class Webpage extends Groomer
{
    public $name;
    /**
     * @var bool
     */
    public function __construct($config = [], $cb = null)
    {
        $this->default_styles = array(
            [
                'src' => '/css/fas/css/all.css',
                'name' => 'fas'
            ],
            [
                'src' => '/css/bootstrap.css',
                'name' => 'tbs'
            ],
            [
                'src' => '/css/mdb/mdb.min.css',
                'name' => 'mdb'
            ],
            [
                'src' => '/css/style.min.css',
                'name' => 'cs'
            ],
        );
        $this->default_scripts = array(
            [
                'src' => "/js/jquery-3.6.0.min.js",
                'control_version' => false,
                'async' => false,
                'name' => 'jqy',
                'footer' => false
            ],
        );
        $this->footer_scripts = array(
            [
                'src' => "/js/bootstrap.bundle.min.js",
                'control_version' => false,
                'async' => true,
                'name' => 'boots',
                'footer' => true
            ],
            [
                'src' => "/js/mdb/mdb.min.js",
                'control_version' => false,
                'async' => true,
                'name' => 'mdb',
                'footer' => true
            ],
            [
                'src' => "/js/index.min.js",
                'control_version' => false,
                'async' => false,
                'name' => 'app'
            ],
        );
        $this->version = 1.01;
        $this->post_images = $this->favicon = $this->wp_asset('/img/favicon.png');
        $this->tld = 'zw';
        parent::__construct($config, $cb);
        $this->name = &$this->sitename;
        return $this;
    }
    public function getHeader()
    {
        parent::beforeGetHeader(); ?>
        <header class="border-bottom white">
            <div class="container text-center py-2">
                <img src="<?=site_icon_url()?>" alt="Site Icon" class="p-2 mt-3" width="150">
                <h1 class="h2 mb-0"><?=$this->sitename?></h1>
                <p class="my-0"><?=bloginfo('description')?></p>
                <ul class="list-inline">
                    <li class="list-inline-item px-1">
                        <a href="//fb.me/ndaramahigh" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-facebook-square" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li class="list-inline-item px-1">
                        <a href="//wa.me/" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-whatsapp" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li class="list-inline-item px-1">
                        <a href="//youtube.com/" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-youtube" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li class="list-inline-item px-1">
                        <a href="//instagram.com/" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-instagram" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li class="list-inline-item px-1">
                        <a href="//twitter.com/" target="_blank" rel="noopener noreferrer">
                            <i class="fab fa-twitter" aria-hidden="true"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </header><?php 
        return $this;
    }
    public function getMenu()
    {
        parent::beforeGetMenu();
        $nav_class =  is_super_admin() ? '' : ' sticky-top';?>
        <nav class="navbar navbar-expand-xl navbar-light white z-depth-0 py-lg-3 border-bottom<?= $nav_class ?>">
            <div class="container-fluid justify-content-center px-lg-4">
                <button class="navbar-toggler d-xl-none py-2 border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collar" aria-controls="collar" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fa fa-chevron-down d-print-none" aria-hidden="true"></i> <span class="d-none d-print-block">MENU</span>
                </button>
                <?php
                wp_nav_menu(
                    array(
                        'theme_location'  => 'primary',
                        'container_class' => 'collapse navbar-collapse justify-content-center',
                        'container_id'    => 'collar',
                        'menu_class'      => 'navbar-nav text-center',
                        'fallback_cb'     => '',
                        'menu_id'         => 'primary-menu',
                        'depth'           => 2,
                        'walker'          => new \WP_Bootstrap_Navwalker(),
                    )
                );?>
            </div>
        </nav>
    <?php return $this;
    }
    public function getSearch()
    { ?>
        <form action="<?= $this->search::SEARCH_URL ?>" method="get" class="row justify-content-center mb-3">
            <div class="col-auto">
                <div class="input-group <?= !$_SERVER['SCRIPT_NAME'] == $this->search::SEARCH_URL . 'index.php' ? ' mb-4' : '' ?> rounded-pill align-items-center white shadow">
                    <input type="text" class="form-control border-0 rounded-pill rounded-end-0 ps-2 ms-0" placeholder="Search here..." aria-label="Search here..." autocomplete="off" aria-describedby="search-btn-body" name="query" <?= $this->search->hasSearch() ? sprintf(" value=\"%s\"", $this->search->getSearchQuery()) : '' ?> autofocus="true" required>
                    <button class="btn btn-sm shadow-none ms-1 me-2 pe-2" type="submit">Search</button>
                </div>
            </div>
        </form>
    <?php }
    public function getFooter(array $scripts = [])
    {
        parent::beforeGetFooter($scripts);?>
        <footer class="page-footer text-center text-md-left white lighten-2 text-black border-top border-dark border-2">
            <div class="container-fluid">
                <div class="container-fluid mx-auto mb-4 pt-3">
                    <div class="row mx-auto justify-content-around">
                        <div class="col-10 col-lg-6 d-flex flex-center mb-lg-3 mb-2">
                            <?= get_search_form()?>
                        </div>
                        <div class="col-12"></div>
                        <div class="col-xl-3 col-lg-3 pt-1 pb-1 text-lg-start">
                            <h1 class="text-uppercase mb-3 fw-bold h5 text-theme-primary">ABOUT US</h1>
                            <p>
                                <?=get_bloginfo('description'); ?>
                            </p>
                        </div>
                        <hr class="w-100 clearfix d-lg-none">
                        <div class="col-xl-3 col-lg-3 col-md-6 mt-1 mb-1 text-lg-start">
                            <!--Search-->
                            <h1 class="text-uppercase mb-3 text-theme-primary fw-bold h5">LOCATE US</h1>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-home pr-1"></i> Target Kopje, Masvingo</li>
                                <li class="mb-2"><i class="fas fa-envelope pr-1"></i> admin@ndaramahigh.co.zw</li>
                                <li class="mb-2"><i class="fas fa-phone pr-1"></i> + 263 392 252 984</li>
                            </ul>

                        </div>
                        <hr class="w-100 clearfix d-md-none">
                        <div class="col-xl-3 col-lg-3 col-md-6 mt-1 mb-1 text-lg-start">
                            <h1 class="text-uppercase mb-3 fw-bold h5 text-theme-primary">Archives</h2>

                            <ul class="footer-posts list-unstyled text-body">
                                <?php wp_get_archives( 'type=monthly&limit=4&after=</span>&before=<span+class="mb-3">' );?>
                            </ul>

                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-copyright py-3 text-center bg-dark">
                <div class="container-fluid">
                    Copyright of <?=$this->sitename?>. Made With <i class="fa-solid fa-heart" aria-hidden="true"></i> <span class="d-none">Love</span> by <a href="//caasi.co.zw" rel="noopener noreferrer" target="_blank" class="text-dark"> Caasi</a>
                </div>
            </div>
        </footer>
        </body>

        </html>
        <?php
    }
}
