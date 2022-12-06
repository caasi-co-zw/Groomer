<?php
include __DIR__ . "/samples/Simple.php";

$web = new Webpage();
$web
    ->setTitle('Groomer Site')
    ->setDescription('Create your website easily.')
    ->getHead()
    ->openBody()
    ->getMenu()
    ->getHeader(); ?>
<main>
    <div class="demo">
        <h1>Welcome to Groomer</h1>
        <p>Your site is ready to be customized. Start by editing the Simple.php file in the Examples folder.<br>For more advanced features, change Simple.php to Advanced.php on line 2 in this index file.</p>
    </div>
</main>
<?php $web->getFooter()?>