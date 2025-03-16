<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\Application;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;

class ApplicationController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'contentNegotiator' => [
                'class' => \yii\filters\ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ]);
    }

    /**
     * Creates a new application record.
     */
    public function actionCreate()
    {
        Yii::$app->response->format = Response::FORMAT_JSON; // Force JSON response

        // Fetch raw input
        $rawBody = Yii::$app->request->getRawBody();

        // Debugging log
        Yii::info("Raw Request Body: " . $rawBody, 'application');

        if (empty($rawBody)) {
            throw new BadRequestHttpException('Request body is empty or not in JSON format.');
        }

        // Decode JSON
        $data = json_decode($rawBody, true);

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON format.');
        }

        $model = new Application();

        if (!$model->load($data, '')) {
            throw new BadRequestHttpException('Failed to load data into the model.');
        }

        if (!$model->validate()) {
            return [
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $model->getErrors(),
            ];
        }

        if (!$model->save()) {
            return [
                'status' => 'error',
                'message' => 'Failed to save',
                'errors' => $model->getErrors(),
            ];
        }

        return [
            'status' => 'success',
            'message' => 'Application created successfully',
            'data' => $model,
        ];
    }


    /**
     * Updates an existing application record by ID.
     */
    public function actionUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON; // Ensure JSON response

        $model = Application::findOne($id);

        if (!$model) {
            throw new NotFoundHttpException('Application not found.');
        }

        // Fetch raw input
        $rawBody = Yii::$app->request->getRawBody();
        Yii::info("Raw Request Body: " . $rawBody, 'application');

        if (empty($rawBody)) {
            throw new BadRequestHttpException('Request body is empty or not in JSON format.');
        }

        // Decode JSON
        $data = json_decode($rawBody, true);

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON format.');
        }

        $model->attributes = $data;

        if (!$model->validate()) {
            return [
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $model->getErrors(),
            ];
        }

        if ($model->save(false)) {
            return [
                'status' => 'success',
                'message' => 'Application updated successfully',
                'data' => $model,
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Failed to save changes',
            'errors' => $model->getErrors(),
        ];
    }
}
