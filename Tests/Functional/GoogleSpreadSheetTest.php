<?php
/**
 * Created by PhpStorm.
 * User: dreamlex
 * Date: 17.08.16
 * Time: 13:04
 */

namespace Dreamlex\GoogleSpreadsheetBundle\Tests\Services;


use Dreamlex\GoogleSpreadsheetBundle\Services\GoogleSpreadSheet;
use Dreamlex\GoogleSpreadsheetBundle\Tests\Functional\FunctionalWebTestCase;

class GoogleSpreadSheetTest extends FunctionalWebTestCase
{
    /** @var  GoogleSpreadSheet $apiclass */
    protected $apiclass;

    public function setUp()
    {
        $this->apiclass = new GoogleSpreadSheet();
    }

    public function testGetScopes()
    {
        $scope = $this->apiclass->getScope('readonly');
        $scopeString = 'https://www.googleapis.com/auth/spreadsheets.readonly';
        self::assertEquals($scopeString, $scope);
        $scope = $this->apiclass->getScope('full');
        $scopeString = 'https://www.googleapis.com/auth/spreadsheets';
        self::assertEquals($scopeString, $scope);
    }

    public function testGetClient()
    {
         $apiClient= $this->apiclass->getClient(__DIR__.'/app/config/test_client.json');
        self::assertEquals('DreamlexSpreadhseetBundle',$apiClient->getApplicationName());
    }

    public function testRemoveCredential()
    {
        $result = $this->apiclass->removeCredential();

        self::assertEquals('Credential file is not exist',$result);
    }


}
