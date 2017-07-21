<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2016-12-06
 * Time: 9:58
 */
namespace  app\domain;
use app\src\banners\action\BannersQueryAction;
use app\src\i18n\helper\LangHelper;

/**
 * Class BannersDomain
 * 轮播广告类接口
 * @author hebidu <email:346551990@qq.com>
 * @package app\domain
 */
class BannersDomain extends BaseDomain{

    /**
     * 查询接口
     * @internal param String $position 广告图片所在位置
     */
    public function query(){

        $this->checkVersion("100");

        $position = $this->_post('position','',LangHelper::lackParameter('position'));

        $result = (new BannersQueryAction())->query($position);

        $this->returnResult($result);
    }

}