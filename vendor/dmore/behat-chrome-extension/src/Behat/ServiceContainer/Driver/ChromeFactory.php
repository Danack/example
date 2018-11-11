<?php
namespace DMore\ChromeExtension\Behat\ServiceContainer\Driver;

use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory;
use DMore\ChromeDriver\ChromeDriver;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;

final class ChromeFactory implements DriverFactory
{
    /**
     * {@inheritdoc}
     */
    public function getDriverName()
    {
        return 'chrome';
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder->children()
            ->scalarNode('api_url')->end()
            ->booleanNode('validate_certificate')->defaultTrue()->end()
            ->enumNode('download_behavior')
                ->values(['allow', 'default', 'deny'])->defaultValue('default')->end()
            ->scalarNode('download_path')->defaultValue('/tmp')->end()
            ->integerNode('socket_timeout')->defaultValue(5)->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function buildDriver(array $config)
    {
        $validateCert = isset($config['validate_certificate']) ? $config['validate_certificate'] : true;
        $socketTimeout = $config['socket_timeout'];
        $downloadBehavior = $config['download_behavior'];
        $downloadPath = $config['download_path'];
        return new Definition(ChromeDriver::class, [
            $config['api_url'],
            null,
            '%mink.base_url%',
            [
                'validateCertificate' => $validateCert,
                'socketTimeout' => $socketTimeout,
                'downloadBehavior' => $downloadBehavior,
                'downloadPath' => $downloadPath,
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsJavascript()
    {
        return true;
    }
}
