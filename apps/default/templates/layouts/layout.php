<!DOCTYPE html>
<html lang="en">
    <head>
        <?php View::printTitle(); ?>
        <?php View::printJs(); ?>
        <?php View::getCss(); ?>
        <?php View::getMeta(); ?>
    </head>
    <body>
        <?php View::printElement("header");?>
        <?php View::printMainView(); ?>
        <?php View::printElement("footer");?>
    </body>
</html>

