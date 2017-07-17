<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 2017-07-17
 * Time: 11:30
 */

namespace app\app\exception;


use think\App;
use think\Config;
use think\exception\Handle;
use think\exception\HttpException;
use think\Response;

class AppExceptionHandler extends Handle
{
    public function render(\Exception $e)
    {
        if ($e instanceof HttpException) {
            return $this->renderHttpException($e);
        } else {
            return $this->convertExceptionToResponse($e);
        }
    }

    protected function renderHttpException(HttpException $e)
    {

        $status   = $e->getStatusCode();
        $template = Config::get('http_exception_template');
        if (!App::$debug && !empty($template[$status])) {

            return Response::create($template[$status], 'view', $status)->header("Access-Control-Allow-Origin","*")
                ->header("Access-Control-Allow-Methods","GET, POST,OPTIONS")
                ->header("Access-Control-Allow-Headers","Origin, X-Requested-With, Content-Type, Accept, BY-SESSION-ID,BY-APP-ID ")->assign(['e' => $e]);
        } else {
            return $this->convertExceptionToResponse($e);
        }
    }

    protected function convertExceptionToResponse(\Exception $exception)
    {
        // 收集异常数据
        if (App::$debug) {
            // 调试模式，获取详细的错误信息
            $data = [
                'name'    => get_class($exception),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'message' => $this->getMessage($exception),
                'trace'   => $exception->getTrace(),
                'code'    => $this->getCode($exception),
                'source'  => $this->getSourceCode($exception),
                'datas'   => $this->getExtendData($exception),
                'tables'  => [
                    'GET Data'              => $_GET,
                    'POST Data'             => $_POST,
                    'Files'                 => $_FILES,
                    'Cookies'               => $_COOKIE,
                    'Session'               => isset($_SESSION) ? $_SESSION : [],
                    'Server/Request Data'   => $_SERVER,
                    'Environment Variables' => $_ENV
                ],
            ];
        } else {
            // 部署模式仅显示 Code 和 Message
            $data = [
                'code'    => $this->getCode($exception),
                'message' => $this->getMessage($exception),
            ];

            if (!Config::get('show_error_msg')) {
                // 不显示详细错误信息
                $data['message'] = Config::get('error_message');
            }
        }

        //保留一层
        while (ob_get_level() > 1) {
            ob_end_clean();
        }

        $data['echo'] = ob_get_clean();

        ob_start();
        extract($data);
        include Config::get('exception_tmpl');
        // 获取并清空缓存
        $content  = ob_get_clean();
        $response = new Response($content, 'html');
        $response->header("Access-Control-Allow-Origin","*")
            ->header("Access-Control-Allow-Methods","GET, POST,OPTIONS")
            ->header("Access-Control-Allow-Headers","Origin, X-Requested-With, Content-Type, Accept, BY-SESSION-ID,BY-APP-ID ");

        $response->code(200);
        return $response;
    }
}