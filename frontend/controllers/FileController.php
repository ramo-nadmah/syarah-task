<?php

namespace frontend\controllers;

use Google\Exception;
use Google_Client;
use Google_Service_Drive;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Class ListController
 * @package frontend\controllers
 *
 * In this class i have use SOLID principles to write function and methods, then i have chained the methods as
 * it will make the code readable, as you are reading a story :)
 *
 * Also, i tried to make everything configurable, as you can change the config location, the base host url and more,
 * and i have used PHP 8 features and type hinting, nullable operator, so we have more reliable code base.
 *
 */
class FileController extends Controller
{
    private ?Google_Client $client = null;
    private const CONFIG_PATH = '/config/google-api.json';
    private const BASE_URI = "http://localhost:20080";

    private function initGoogleClient(): self
    {
        if (!isset($this->client)) {
            $this->client = new Google_Client();
        }
        return $this;
    }

    /**
     * @throws Exception
     */
    private function setAuth(): self
    {
        $this->client->setAuthConfig(\Yii::getAlias('@common') . self::CONFIG_PATH);
        return $this;
    }

    private function setScope(): self
    {
        $this->client->addScope(Google_Service_Drive::DRIVE_FILE);
        return $this;
    }

    private function setAccessToken(): Google_Service_Drive
    {
        $this->client->setAccessToken($_SESSION['access_token']);
        return new Google_Service_Drive($this->client);
    }

    private function setRedirectUri(): self
    {
        $this->client->setRedirectUri(self::BASE_URI . Url::to(['file/google-callback']));
        return $this;
    }

    public function actionIndex()
    {
        if (!isset($_SESSION['access_token'])) {
            return $this->redirect(['file/google-callback']);
        }

        $client = $this->initGoogleClient()->setAuth()->setScope()->setAccessToken();
        $files = $client->files->listFiles([
            'fields' => 'nextPageToken, files(id, name,thumbnailLink, webContentLink, modifiedTime, size, owners)'
        ])->getFiles();

        $files = new ArrayDataProvider(['allModels' => $files]);
        return $this->render('index', ['dataProvider' => $files]);
    }

    /**
     * @throws Exception
     */
    public function actionGoogleCallback()
    {
        $client = $this->initGoogleClient()->setAuth()->setRedirectUri()->setScope();

        if (!isset($_GET['code'])) {
            $auth_url = $client->client->createAuthUrl();
            $this->redirect($auth_url);
        } else {
            $client->client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $client->client->getAccessToken();
            $this->redirect(['index']);
        }
    }
}