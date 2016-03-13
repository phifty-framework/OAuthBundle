<?php
namespace OAuthPlugin\Controller;
use Phifty\Controller;

class RedirectController extends Controller
{

    public function indexAction( $provider, $url, $seconds = 0)
    {
        if ( $seconds ) {
            /* send delayed redirect header */
            header("refresh:  $seconds; url=" . $url );
            return $this->render('@OAuthPlugin/redirect.html',array(
                'provider' => $provider ,
                'url' => $url,
            ));
        } else {
            // redirect directly..
            header("Location: $url");
        }
    }

}


