<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-05-03
 * Time: 15:14
 */

namespace app\src\sunsun\aph300\action;


use app\src\base\action\BaseAction;
use app\src\sunsun\aph300\logic\VAph300PhDayHisLogic;
use app\src\sunsun\aph300\logic\VAph300PhOneDayHisLogic;

class VAph300PhHisAction extends BaseAction
{
    /**
     * 获取设备过去24小时的平均ph值
     * @param $did
     * @return array
     */
    public function oneDay($did){
        $logic = (new VAph300PhOneDayHisLogic());

        $result = $logic->queryNoPaging(['did'=>$did],'his_date desc');

        return $this->result($result);
    }
    /**
     * 获取设备过去n天的平均ph值
     * @param $did
     * @param int $days
     * @return array
     */
    public function lastNDay($did,$days=7){
        $logic = (new VAph300PhDayHisLogic());
        $map = ['did'=>$did];
        $ymd = date('Ymd',time()-$days*24*3600);
        $map['his_date'] = ['egt',$ymd];
        $result = $logic->queryNoPaging($map,"his_date desc");

        return $this->result($result);

    }
}