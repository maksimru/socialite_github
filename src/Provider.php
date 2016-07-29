<?php

namespace Akkyoh\SocialiteGithub;

use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\ProviderInterface;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider implements ProviderInterface
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'GITHUB';

    /**
     * {@inheritdoc}
     */
    protected $scopes = ['user'];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this -> buildAuthUrlFromBase(
            'https://github.com/login/oauth/authorize', $state
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://github.com/login/oauth/access_token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this -> getHttpClient() -> get(
            'https://api.github.com/user?access_token='.$token['access_token']
        );

        $response = json_decode($response -> getBody() -> getContents(), true);

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User()) -> setRaw($user) -> map([
            'id' => $user['id'], 'nickname' => $user['username'],
            'name' => $user['name'],
            'email' => $user['email'], 'avatar' => $user['avatar_url'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessToken($body)
    {
        return json_decode($body, true);
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        if ($this -> hasInvalidState())
            throw new InvalidStateException();

        $user = $this -> mapUserToObject($this -> getUserByToken(
            $token = $this -> getAccessTokenResponse($this -> getCode())
        ));

        return $user->setToken(array_get($token, 'access_token'));
    }
}
