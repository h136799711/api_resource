<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-05-03
 * Time: 15:14
 */

namespace app\src\sunsun\aq806\action;


use app\src\base\action\BaseAction;
use app\src\sunsun\aq806\logic\VAq806TempOneDayHisLogic;

class VAq806TempHisAction extends BaseAction
{
    /**
     * 获取设备过去24小时的平均温度值
     * @param $did
     * @return array
     */
    public function oneDay($did){
        $logic = (new VAq806TempOneDayHisLogic());

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
        $logic = (new VAq806TempOneDayHisLogic());
        $map = ['did'=>$did];
        $ymd = date('Ymd',time()-$days*24*3600);
        $map['his_date'] = ['egt',$ymd];
        $result = $logic->queryNoPaging($map,"his_date desc");

        return $this->result($result);
    }
}