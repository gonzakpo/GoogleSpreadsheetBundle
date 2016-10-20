<?php
namespace Dreamlex\Bundle\GoogleSpreadsheetBundle\Tests\Functional;

use Dreamlex\Bundle\CoreBundle\Tests\WebTestCase;

/**
 * Class GoogleSpreadsheetTest
 *
 * @package Dreamlex\Bundle\GoogleSpreadsheetBundle\Tests\Functional
 */
class GoogleSpreadsheetTest extends WebTestCase
{
    /**  */
    public function testCommand()
    {
        static::bootKernel(['test_case' => 'Spreadsheet']);

        $tableData = static::$kernel->getContainer()->get('dreamlex_google_spreadsheet')->getTable('1QnVOj-3oCDp-NN97JcybzIbrswKrwKBxtiKdGLCUs_E', 'List1!A1:A');
        self::assertSame([0 => [0 => 'it works']], $tableData);
    }

    /**  */
    public static function setUpBeforeClass()
    {
        parent::deleteTmpDir('Spreadsheet');
    }

    /**  */
    public static function tearDownAfterClass()
    {
        parent::deleteTmpDir('Spreadsheet');
    }

    protected static function getKernelClass()
    {
        require_once __DIR__.'/app/AppKernel.php';

        return 'Dreamlex\Bundle\GoogleSpreadsheetBundle\Tests\Functional\app\AppKernel';
    }
}
