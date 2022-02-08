<?php
/* @var $dataProvider ArrayDataProvider */

/** @var yii\web\View $this */

use Google\Service\Drive\DriveFile;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'label' => 'Name',
            'value' => function ($model) {
                /* @var $model DriveFile */
                return $model->getName();
            }
        ],
        [
            'label' => 'Preview Image',
            'value' => function ($model) {
                /* @var $model DriveFile */
                return $model->getThumbnailLink();
            }
        ],
        [
            'label' => 'Link',
            'value' => function ($model) {
                /* @var $model DriveFile */
                return $model->getWebContentLink();
            },
            'format' => 'url'
        ],
        [
            'label' => 'Updated At',
            'value' => function ($model) {
                /* @var $model DriveFile */
                return $model->getModifiedTime();
            },
            'format' => 'date'
        ],
        [
            'label' => 'Size in MB',
            'value' => function ($model) {
                /* @var $model DriveFile */
                return (int)($model->getSize()) / 100000 . ' MB';
            }
        ],
        [
            'label' => 'Owner',
            'value' => function ($model) {
                /* @var $model DriveFile */
                return $model->getOwners()[0]->displayName ?? "";
            }
        ]
    ]
]);
