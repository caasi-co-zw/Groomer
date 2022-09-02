<?php
include __DIR__ . "/manuload.php";

$web = new Webpage();
$web
    ->setTitle('Groomer Site')
    ->setDescription('Create your website easily.')
    ->getHead()
    ->openBody()
    ->getMenu()
    ->getHeader() ?>
<main>
    <div class="demo">
        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td scope="row"></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td scope="row"></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</main>
<?php $web->getFooter()?>