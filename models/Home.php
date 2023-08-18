<?php
namespace app\models;

use Yii;
use DateTime;
use yii\db\ActiveRecord;

class Home extends ActiveRecord{

    public function rules(){
        return [
            [['name'], 'required', 'message' => 'Необходимо заполнить «Название».'],
            [['description'], 'trim'],
            [['date_start'], 'validateDates'],
            [['date_finish'], 'validateDates']
        ];
    }

    public function validateDates($attribute, $params){
        if ($this->$attribute == null || strlen($this->$attribute) <= 1) {
            $this->addError($attribute, 'Необходимо заполнить данное поле.');
        }
    }

    public function getFloors(){
        return $this->hasMany(Floor::class, ['home_id' => 'id'])->count();
    }

    public function getEntrances(){
        return $this->hasMany(Entrance::class, ['home_id' => 'id'])->count();
    }

    public function getFlats(){
        return $this->hasMany(Flat::class, ['home_id' => 'id'])->count();
    }

    public function getRooms(){
        return $this->hasMany(Room::class, ['home_id' => 'id'])->count();
    }

    private function get_date(){
        $months = ['Янв' => 'Jan', 'Фев' => 'Feb', 'Март' => 'Mar', 'Апр' => 'Apr','Май' => 'May', 'Июнь' => 'Jun', 'Июль' => 'Jul', 'Авг' => 'Aug','Сен' => 'Sep', 'Окт' => 'Oct', 'Ноя' => 'Nov', 'Дек' => 'Dec'];

        $start_date_transformed = strtr($this->date_start , $months);
        $finish_date_transformed = strtr($this->date_finish, $months);

        $new_date_start = (DateTime::createFromFormat('d M y', $start_date_transformed) !== false) ? DateTime::createFromFormat('d M y', $start_date_transformed)->getTimestamp() : false;
        $new_date_finish = (DateTime::createFromFormat('d M y', $finish_date_transformed) !== false) ? DateTime::createFromFormat('d M y', $finish_date_transformed)->getTimestamp() : false;

        $arr_dates = [$new_date_start, $new_date_finish];
        return $arr_dates;
    }

    public function add_home($id) {
        $dates = $this->get_date();
        $this->date_start = $dates[0];
        $this->date_finish = $dates[1];

        $current_time = time();
        $this->create = $current_time;
        $this->last_update = $current_time;

        $this->object_id = $id;
        $this->author_id =  Yii::$app->user->id;

        return $this->save();
    }

    public function edit_home($id) {
        $dates = $this->get_date();
        $this->date_start = $dates[0];
        $this->date_finish = $dates[1];

        $current_time = time();
        $this->create = $current_time;
        $this->last_update = $current_time;
        $this->object_id = $id;

        return $this->save();
    }

    public function afterDelete(){
        parent::afterDelete();
        Entrance::deleteAll(['home_id' => $this->id]);
    }
}