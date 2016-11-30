<?php
namespace Dreamlex\Bundle\GoogleSpreadsheetBundle\Spreadsheet;

use Symfony\Component\Filesystem\Filesystem;

use Google_Client as G_Client;
use Google_Service_Drive as GS_Drive;
use Google_Service_Drive_Permission as GS_Drive_Permission;
use Google_Service_Sheets as GS_Sheets;
use Google_Service_Sheets_Spreadsheet as GS_Sheets_Ss;
use Google_Service_Sheets_SpreadsheetProperties as GS_Sheets_SsProperties;
use Google_Service_Sheets_ValueRange as GS_Sheets_VR;

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

        $this->client = new G_Client();
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
        $service = new GS_Sheets($this->getAuthorizedClient());

        if ($range) {
            $response = $service->spreadsheets_values->get($spreadsheetId, $range)->getValues();
        } else {
            $response = $service->spreadsheets->get($spreadsheetId);
        }

        return $response;
    }

    /**
     * @param string $title title is filename
     *
     * @return string
     */
    public function createTable($title)
    {
        $service = new GS_Sheets($this->getAuthorizedClient());

        $properties = new GS_Sheets_SsProperties();
        $properties->setTitle($title);

        $newSheet = new GS_Sheets_Ss();
        $newSheet->setProperties($properties);
        $newSheet = $service->spreadsheets->create($newSheet);

        return $newSheet->spreadsheetId;
    }

    /**
     * @param string $spreadsheetId spreadsheetId
     * @param string $email email
     * @param string $type type is user
     * @param string $role role is writer
     * @param boolean $sendNotificationEmail send mail is false
     */
    public function addPermissionTable($spreadsheetId, $email, $type = 'user', $role = 'writer', $sendNotificationEmail = false)
    {
        $drive = new GS_Drive($this->getAuthorizedClient());

        $newPermission = new GS_Drive_Permission();
        $newPermission->setEmailAddress($email);
        $newPermission->setType($type);
        $newPermission->setRole($role);

        $optParams = array('sendNotificationEmail' => $sendNotificationEmail);

        $drive->permissions->create($spreadsheetId, $newPermission, $optParams);
    }

    /**
     * @param string $spreadsheetId spreadsheetId
     * @param string $range         Range
     * @param array $values values
     *
     * @return string
     */
    public function addRowTable($spreadsheetId, $range, $values)
    {
        $service = new GS_Sheets($this->getAuthorizedClient());

        $body = new GS_Sheets_VR(array(
          'values' => $values
        ));
        $params = array(
          'valueInputOption' => 'USER_ENTERED'
        );

        $result = $service->spreadsheets_values->append($spreadsheetId, $range,
            $body, $params);

        return $result;
    }

    /**
     * @param string $spreadsheetId spreadsheetId
     * @param string $range         Range
     * @param array $values values
     *
     * @return string
     */
    public function updateRowTable($spreadsheetId, $range, $values)
    {
        $service = new GS_Sheets($this->getAuthorizedClient());

        $body = new GS_Sheets_VR(array(
          'values' => $values
        ));
        $params = array(
          'valueInputOption' => 'USER_ENTERED'
        );

        $result = $service->spreadsheets_values->update($spreadsheetId, $range,
            $body, $params);

        return $result;
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
