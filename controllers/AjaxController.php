<?php
namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use app\models\Entrance;
use app\models\Floor;
use app\models\FileLink;
use app\models\Flat;
use app\models\Home;
use app\models\Objects;
use app\models\Room;


class AjaxController extends Controller{
    public function actionGenerator(){
        $model = Yii::$app->request->post('model');
        $id = Yii::$app->request->post('id');

        $html = $this->renderAjax('view_list/'.$model , compact('id'));
        return json_encode(['html' => $html,]);
    }

    public function actionDeleteFile(){
        $file_id = Yii::$app->request->post('id');
        $fileLink = FileLink::findOne(['file_id' => $file_id]);

        if ($fileLink) {
            $file = $fileLink->file;
            $fileLink->delete();
            $filePath = $fileLink->path;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            if ($file) {
                $file->delete();
            } else {}
        } else {}
    }

    public function actionDelete(){
        $delete_arr = Yii::$app->request->post('arr');

        foreach ($delete_arr as $record){
            switch ($record['model']){
                case 'object':
                    $object = Objects::findOne($record['id']);
                    $object->delete();
                    break;
                case 'home':
                    $home = Home::findOne($record['id']);
                    $home->delete();
                    break;
                case 'entrance':
                    $entrance = Entrance::findOne($record['id']);
                    $entrance->delete();
                    break;
                case 'floor':
                    $floor = Floor::findOne($record['id']);
                    $floor->delete();
                    break;
                case 'flat':
                    $flat = Flat::findOne($record['id']);
                    $flat->delete();
                    break;
                case 'room':
                    $room = Room::findOne($record['id']);
                    $room->delete();
                    break;
            }
        }
    }

    public function actionUpdateStatus() {
        $object_id = Yii::$app->request->post('object_id');
        $status_id = Yii::$app->request->post('status_id');
        $object = Objects::findOne($object_id);

        if ($object) {
            $object->status_id = $status_id;
            $object->save();
            return 'Success';
        }
        return 'Error';
    }

    public function actionInterpreter(){
        $json = Yii::$app->request->post('data_json');
        return json_encode(['data' => $json,]);
    }

    public function actionSaveJson(){
        $jsonString = Yii::$app->request->post('json');
        return ['message' => $jsonString];
    }

}