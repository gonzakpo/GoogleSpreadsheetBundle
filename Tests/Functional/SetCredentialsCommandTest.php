<?php
namespace Dreamlex\Bundle\GoogleSpreadsheetBundle\Tests\Functional;

use Dreamlex\Bundle\CoreBundle\Tests\WebTestCase;
use Dreamlex\Bundle\GoogleSpreadsheetBundle\Command\SetCredentialsCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class SetCredentialsCommandTest
 *
 * @package Dreamlex\Bundle\GoogleSpreadsheetBundle\Tests\Functional
 */
class SetCredentialsCommandTest extends WebTestCase
{
    /**  */
    public function testCommand()
    {
        static::bootKernel(['test_case' => 'Command']);

        $application = new Application(static::$kernel);
        $application->add(new SetCredentialsCommand());

        $command = $application->find('google-spreadsheet:set-credentials');

        /** @var QuestionHelper $helper */
        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream("Test credential\n"));

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);
    }

    /**  */
    public static function setUpBeforeClass()
    {
        parent::deleteTmpDir('Command');
    }

    /**  */
    public static function tearDownAfterClass()
    {
        parent::deleteTmpDir('Command');
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fwrite($stream, $input);
        rewind($stream);

        return $stream;
    }

    protected static function getKernelClass()
    {
        require_once __DIR__.'/app/AppKernel.php';

        return 'Dreamlex\Bundle\GoogleSpreadsheetBundle\Tests\Functional\app\AppKernel';
    }
}
