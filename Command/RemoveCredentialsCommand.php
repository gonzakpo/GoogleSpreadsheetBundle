<?php
namespace Dreamlex\Bundle\GoogleSpreadsheetBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RemoveCredentialsCommand
 *
 * @package Dreamlex\Bundle\GoogleSpreadsheetBundle\Command
 */
class RemoveCredentialsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('google-spreadsheet:remove-credentials')
            ->setDescription('Remove credentials (you must use it when changing scope)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $googleSpreadsheet = $this->getContainer()->get('dreamlex_google_spreadsheet');
        $googleSpreadsheet->removeCredentials();

        $output->writeln(sprintf('<info>Credentials %s removed.</info>', $googleSpreadsheet->getCredentialsFilename()));
    }
}
