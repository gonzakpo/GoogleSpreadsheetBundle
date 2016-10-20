<?php
namespace Dreamlex\Bundle\GoogleSpreadsheetBundle\Tests\Functional;

use Dreamlex\Bundle\CoreBundle\Tests\WebTestCase;
use Dreamlex\Bundle\GoogleSpreadsheetBundle\Command\RemoveCredentialsCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class RemoveCredentialsCommandTest
 *
 * @package Dreamlex\Bundle\GoogleSpreadsheetBundle\Tests\Functional
 */
class RemoveCredentialsCommandTest extends WebTestCase
{
    /**  */
    public function testCommand()
    {
        static::bootKernel(['test_case' => 'Command']);

        $application = new Application(static::$kernel);
        $application->add(new RemoveCredentialsCommand());

        $command = $application->find('google-spreadsheet:remove-credentials');

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

    protected static function getKernelClass()
    {
        require_once __DIR__.'/app/AppKernel.php';

        return 'Dreamlex\Bundle\GoogleSpreadsheetBundle\Tests\Functional\app\AppKernel';
    }
}
