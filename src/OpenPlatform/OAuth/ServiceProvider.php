<?php

namespace EasyTiktok\OpenPlatform\OAuth;

use Overtrue\Socialite\SocialiteManager as Socialite;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}.
     */
    public function register(Container $app)
    {
        $app['oauth'] = function ($app) {
            $douyin = [
                'douyin' => [
                    'client_id' => $app['config']['app_id'],
                    'client_secret' => $app['config']['secret'],
                    'redirect' => $this->prepareCallbackUrl($app),
                ],
            ];

            $socialite = (new Socialite($douyin))->create('douyin');

            $scopes = (array)$app['config']->get('oauth.scopes', ['user_info']);

            if (!empty($scopes)) {
                $socialite->scopes($scopes);
            }

            return $socialite;
        };
    }

    /**
     * Prepare the OAuth callback url for wechat.
     *
     * @param Container $app
     *
     * @return string
     */
    private function prepareCallbackUrl($app)
    {
        $callback = $app['config']->get('oauth.callback');
        if (0 === stripos($callback, 'http')) {
            return $callback;
        }
        $baseUrl = $app['request']->getSchemeAndHttpHost();

        return $baseUrl . '/' . ltrim($callback, '/');
    }
}
