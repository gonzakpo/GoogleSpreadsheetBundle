<?php

namespace Dreamlex\GoogleSpreadsheetBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DreamlexGetCredentialsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dreamlex:get-credentials')
            ->setDescription('Get credentials for google spreasheet');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $googleApi = $this->getContainer()->get('dreamlex_google_spreadsheet');
        $client = $googleApi->getClient();
        $googleApi->clientGetToken($client);
    }

}
