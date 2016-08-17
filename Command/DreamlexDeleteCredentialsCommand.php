<?php

namespace Dreamlex\GoogleSpreadsheetBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DreamlexDeleteCredentialsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('dreamlex:delete-credentials')
            ->setDescription('Delete credentials(you must use it when changing scope)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = $this->getContainer()->get('dreamlex_google_spreadsheet')->removeCredential();

        $output->writeln($result);
    }

}
