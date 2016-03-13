<?php
namespace OAuthPlugin\Controller;
use Phifty\Controller;
use OAuthPlugin\OAuthPlugin;


class AuthenticationErrorController extends Controller
{

    public $parent;

    public $provider;

    public function indexAction($exception = null)
    {
        $plugin = OAuthPlugin::getInstance();
        $templateFile = $plugin->config('Templates.Error') ?: 'message.html';

        // use error_log();
        return $this->render($templateFile, [
            'error' => true,
            'message' => __('無法連結 %1', $this->provider->getName() ),
            'debug_message' => $exception ? $exception->getMessage() : null,
            'exception' => $exception,
        ]);
    }
}





