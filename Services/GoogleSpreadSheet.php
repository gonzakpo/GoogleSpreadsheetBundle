<?php

namespace Dreamlex\GoogleSpreadsheetBundle\Services;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class GoogleApiController
 * @package Dreamlex\GoogleSpreadsheetBundle\Services
 */
class GoogleSpreadSheet
{
    protected $scope;
    protected $appname;
    protected $credentialPath = __DIR__.'/../Resources/credentials/client.json';
    protected $authConfigPath = __DIR__.'/../../../../app/config/client_secret.json';

    /**
     * GoogleSpreadSheet constructor.
     * @param string $scope
     * @param string $authConfigPath
     * @param string $appName
     */
    public function __construct($scope = 'readonly', $appName = 'DreamlexSpreadhseetBundle')
    {
        $this->scope = $scope;
        $this->appname = $appName;
    }

    /**
     * @param string $spreadsheetId
     * @param string $range
     * @return mixed
     */
    public function getTable($spreadsheetId, $range = null, $authConfigPath = null) // tableid such as 13O_57K1FCSYVnI0oMESfqLx7_yPP3vNVuSjPuc75Fus
    {
        $client = $this->getClient($authConfigPath);
        $client = $this->clientGetToken($client);
        $service = new \Google_Service_Sheets($client);
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);

        return $response->getValues();
    }
    //===========================
    /**
     * Returns an authorized API client.
     * @return \Google_Client the authorized client object
     */
    public function getClient($authConfigPath = null)
    {
        if ($authConfigPath === null) {
            $authConfigPath = __DIR__.'/../../../../../../app/config/client_secret.json';
        }
        $client = new \Google_Client();
        $client->setApplicationName($this->appname);
        $client->setScopes($this->getScope($this->scope));
        $client->setAuthConfigFile($authConfigPath);
        $client->setAccessType('offline');

        return $client;
    }



    //===========================

    /**
     * @param string $scope
     * @return mixed
     */
    public function getScope($scope)
    {
        $scopes = [
            'readonly' => 'https://www.googleapis.com/auth/spreadsheets.readonly',
            'full' => 'https://www.googleapis.com/auth/spreadsheets',
        ];

        return $scopes[$scope];
    }

    public function removeCredential()
    {
        $credentialsPath = $this->credentialPath;
        if (file_exists($credentialsPath)) {
            unlink($credentialsPath);
            $text = 'Credential file is deleted';
        } else {
            $text = 'Credential file is not exist';
        }

        return $text;
    }

    public function clientGetToken(\Google_Client $client)
    {
        $fs = new Filesystem();
        // Load previously authorized credentials from a file.
        $credentialsPath = $this->credentialPath;
        if (file_exists($credentialsPath)) {
            $accessToken = file_get_contents($credentialsPath);
        } else {
            // Request authorization from the user.
            $authUrl = $client->createAuthUrl();
            printf("Open the following link in your browser:\n%s\n", $authUrl);
            print 'Enter verification code: ';
            $authCode = trim(fgets(STDIN));

            // Exchange authorization code for an access token.
            $accessToken = $client->authenticate($authCode);

            // Store the credentials to disk.
            if (!$fs->exists(dirname($credentialsPath))) {
                try {
                    $fs->mkdir(dirname($credentialsPath), 0700);
                } catch (IOExceptionInterface  $e) {
                    echo "An error occurred while creating credentials directory at ".$e->getPath();
                }
            }
            file_put_contents($credentialsPath, $accessToken);
            printf("Credentials saved to %s\n", $credentialsPath);
        }
        $client->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($client->isAccessTokenExpired()) {
            $client->refreshToken($client->getRefreshToken());
            file_put_contents($credentialsPath, $client->getAccessToken());
        }
        return $client;
    }
}
