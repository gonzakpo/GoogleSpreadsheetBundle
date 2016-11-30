<?php
namespace Dreamlex\Bundle\GoogleSpreadsheetBundle\Spreadsheet;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Class GoogleSpreadsheet
 *
 * @package Dreamlex\Bundle\GoogleSpreadsheetBundle\Spreadsheet
 */
class GoogleSpreadsheet
{
    /**
     * @var string
     */
    protected $kernelRootDir;

    /**
     * @var string
     */
    protected $appName;

    /**
     * @var string
     */
    protected $credentialsFilename;

    /**
     * @var \Google_Client
     */
    protected $client;

    protected $scopes = [
        /** View and manage the files in your Google Drive. */
        'drive' => 'https://www.googleapis.com/auth/drive',
        /** View the files in your Google Drive. */
        'drive_readonly' => 'https://www.googleapis.com/auth/drive.readonly',
        /** View and manage your spreadsheets in Google Drive. */
        'spreadsheets' => 'https://www.googleapis.com/auth/spreadsheets',
        /** View your Google Spreadsheets. */
        'spreadsheets_readonly' => 'https://www.googleapis.com/auth/spreadsheets.readonly',
    ];

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var boolean
     */
    protected $isAuthorized;

    /**
     * GoogleSpreadsheet constructor.
     *
     * @param string $kernelRootDir
     * @param string $appName
     * @param array  $scopes
     * @param string $authConfigPath
     *
     * @throws \InvalidArgumentException
     * @throws \Google_Exception
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public function __construct($kernelRootDir, $appName, $scopes = array('readonly'), $authConfigPath = null)
    {
        $setScopes = array();

        foreach ($scopes as $scope) {
            if (false === array_key_exists($scope, $this->scopes)) {
                throw new \InvalidArgumentException('Unknown scope');
            } else {
                array_push($setScopes, $this->scopes[$scope]);
            }
        }

        $this->appName = $appName;
        $this->kernelRootDir = $kernelRootDir;
        $this->credentialsFilename = $kernelRootDir.'/config/credentials/'.$this->appName.'.json';

        if ($authConfigPath === null) {
            $authConfigPath = $this->kernelRootDir.'/config/client_secret.json';
        }

        $this->fs = new Filesystem();

        $this->client = new \Google_Client();
        $this->client->setApplicationName($appName);
        $this->client->setScopes($setScopes);
        $this->client->setAuthConfigFile($authConfigPath);
        $this->client->setAccessType('offline');
        $this->isAuthorized = false;
    }

    /**
     * @param string $spreadsheetId Such as 13O_57K1FCSYVnI0oMESfqLx7_yPP3vNVuSjPuc75Fus
     * @param string $range         Range
     *
     * @return mixed
     */
    public function getTable($spreadsheetId, $range = null)
    {
        $service = new \Google_Service_Sheets($this->getAuthorizedClient());
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);

        return $response->getValues();
    }

    public function getTables($spreadsheetId)
    {
        $service = new \Google_Service_Sheets($this->getAuthorizedClient());
dump($service);die;
        $response = $service->spreadsheets->get($spreadsheetId);

        return $response;
    }

    public function createTable()
    {
        $service = new \Google_Service_Sheets($this->getAuthorizedClient());
        $drive = new \Google_Service_Drive($this->getAuthorizedClient());

        $ss = $service->spreadsheets->create(new \Google_Service_Sheets_Spreadsheet());
        $newPermission = new \Google_Service_Drive_Permission();
        $newPermission->setEmailAddress("gonzaloalonsod@gmail.com");
        $newPermission->setType('user');
        $newPermission->setRole('writer');
        $optParams = array('sendNotificationEmail' => false);
        $drive->permissions->create($ss->spreadsheetId,$newPermission,$optParams);

        return true;
    }

    /**
     * @return \Google_Client the authorized client object
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function getCredentialsFilename()
    {
        return $this->credentialsFilename;
    }

    /**
     * @param array $accessToken
     *
     * @return string
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public function saveCredentials(array $accessToken)
    {
        $this->fs = new Filesystem();

        $this->fs->dumpFile($this->credentialsFilename, json_encode($accessToken));

        return $this->credentialsFilename;
    }

    /**
     * @return bool
     */
    public function isCredentialsExisted()
    {
        $this->fs = new Filesystem();

        return $this->fs->exists($this->credentialsFilename);
    }

    /**
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public function removeCredentials()
    {
        $this->fs = new Filesystem();

        $this->fs->remove($this->credentialsFilename);
    }

    /**
     * Refresh the token if it's expired.
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    protected function refreshToken()
    {
        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            $this->fs->dumpFile($this->credentialsFilename, json_encode($this->client->getAccessToken()));
        }
    }

    /**
     * @return \Google_Client
     */
    protected function getAuthorizedClient()
    {
        if (false === $this->isAuthorized) {
            if (false === $this->isCredentialsExisted()) {
                throw new \BadMethodCallException('No credentials found');
            }

            $accessToken = json_decode(file_get_contents($this->credentialsFilename), true);
            $this->client->setAccessToken($accessToken);
            $this->refreshToken();
            $this->isAuthorized = true;
        }

        return $this->client;
    }
}
