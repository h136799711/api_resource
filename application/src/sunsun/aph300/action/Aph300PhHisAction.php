<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-05-03
 * Time: 15:14
 */

namespace app\src\sunsun\aph300\action;


use app\src\base\action\BaseAction;
use app\src\base\helper\ResultHelper;
use app\src\sunsun\aph300\logic\Aph300PhHisLogic;
use app\src\sunsun\aph300\logic\VAph300PhDayHisLogic;
use app\src\sunsun\aph300\logic\VAph300PhOneDayHisLogic;

class Aph300PhHisAction extends BaseAction
{
    /**
     * 增加一条ph值记录
     * @param $did
     * @param $content
     * @param $item
     * @return array
     * @internal param $data
     * @internal param $did
     */
    public function add($did,$content,$item){
        $logic = (new Aph300PhHisLogic());
        $entity = [];
        //实时温度数据
        if(array_key_exists("ph",$content)){
            $entity['ph'] = intval($content['ph']);
        }else{
            return ResultHelper::error('缺少ph值');
        }

        if($entity['ph'] <= 0){
            return ResultHelper::error('ph值错误');
        }
        $entity['did'] = $did;
        $entity['create_time'] = $item['create_time'];
        $entity['ymd'] = date('Ymd',$item['create_time']);
        $entity['ymdh'] = date('YmdH',$item['create_time']);

        $result = $logic->add($entity);

        return $this->result($result);
    }
}