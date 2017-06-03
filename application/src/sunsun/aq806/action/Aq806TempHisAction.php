<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-05-03
 * Time: 15:14
 */

namespace app\src\sunsun\aq806\action;


use app\src\base\action\BaseAction;
use app\src\base\helper\ResultHelper;
use app\src\sunsun\aq806\logic\Aq806PhHisLogic;
use app\src\sunsun\aq806\logic\Aq806TempHisLogic;
use app\src\sunsun\aq806\logic\VAq806PhDayHisLogic;
use app\src\sunsun\aq806\logic\VAq806PhOneDayHisLogic;

class Aq806TempHisAction extends BaseAction
{
    /**
     * 增加一条温度值记录
     * @param $did
     * @param $content
     * @param $item
     * @return array
     * @internal param $data
     * @internal param $did
     */
    public function add($did,$content,$item){
        $logic = (new Aq806TempHisLogic());

        $entity = [];
        //实时温度数据
        if(array_key_exists("t",$content)){
            $entity['temp'] = intval($content['t']);
        }else{
            return ResultHelper::error('缺少t值');
        }

        if($entity['temp'] <= 0){
            return ResultHelper::error('t值错误');
        }
        $entity['did'] = $did;
        $entity['create_time'] = $item['create_time'];
        $entity['ymd'] = date('Ymd',$item['create_time']);
        $entity['ymdh'] = date('YmdH',$item['create_time']);

        $result = $logic->add($entity);

        return $this->result($result);
    }
}