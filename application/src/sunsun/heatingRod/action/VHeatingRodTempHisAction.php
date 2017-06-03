<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-04-12
 * Time: 13:09
 */

namespace app\src\sunsun\heatingRod\action;


use app\src\base\action\BaseAction;
use app\src\sunsun\heatingRod\logic\VHeatingRodOneDayTempHisLogic;
use app\src\sunsun\heatingRod\logic\VHeatingRodTempHisLogic;

class VHeatingRodTempHisAction extends BaseAction
{
    /**
     * 获取设备过去24小时的平均水温
     * @param $did
     * @return array
     */
    public function oneDay($did){
        $logic = (new VHeatingRodOneDayTempHisLogic());

        $result = $logic->queryNoPaging(['did'=>$did],'his_date desc');

        return $this->result($result);
    }
    /**
     * 获取设备过去n天的平均水温
     * @param $did
     * @param int $days
     * @return array
     */
    public function lastNDay($did,$days=7){
        $logic = (new VHeatingRodTempHisLogic());
        $map = ['did'=>$did];
        $ymd = date('Ymd',time()-$days*24*3600);
        $map['his_date'] = ['egt',$ymd];
        $result = $logic->queryNoPaging($map,"his_date desc");

        return $this->result($result);

    }
}