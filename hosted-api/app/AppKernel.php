<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Barbon\HostedApi\SecurityBundle\BarbonHostedApiSecurityBundle(),
            new Barbon\HostedApi\AppBundle\BarbonHostedApiAppBundle(),
            new Barbon\HostedApi\Agent\ReferenceBundle\BarbonHostedApiAgentReferenceBundle(),
            new Barbon\HostedApi\Agent\DebugBundle\BarbonHostedApiAgentDebugBundle(),
            new Barbon\HostedApi\Landlord\AuthenticationBundle\BarbonHostedApiLandlordAuthenticationBundle(),
            new Barbon\HostedApi\Landlord\ReferenceBundle\BarbonHostedApiLandlordReferenceBundle(),
            new Barbon\HostedApi\Landlord\DebugBundle\BarbonHostedApiLandlordDebugBundle(),
            new Barbon\HostedApi\Landlord\DashboardBundle\BarbonHostedApiLandlordDashboardBundle(),
            new Barbon\HostedApi\Landlord\ProfileBundle\BarbonHostedApiLandlordProfileBundle(),
            new Fp\JsFormValidatorBundle\FpJsFormValidatorBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
