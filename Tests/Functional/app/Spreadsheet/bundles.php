<?php

return [
    new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
    new \Symfony\Bundle\TwigBundle\TwigBundle(),
    new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
    new \Dreamlex\Bundle\CoreBundle\Tests\Functional\Bundle\AppBundle\AppBundle(),
    new \Dreamlex\Bundle\GoogleSpreadsheetBundle\DreamlexGoogleSpreadsheetBundle(),
];
