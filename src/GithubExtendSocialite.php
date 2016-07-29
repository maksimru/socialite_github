<?php

namespace Akkyoh\SocialiteGithub;

use SocialiteProviders\Manager\SocialiteWasCalled;

class GithubExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite(
            'github', __NAMESPACE__.'\Provider'
        );
    }
}
