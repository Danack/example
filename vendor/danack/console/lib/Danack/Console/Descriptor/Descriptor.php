<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Danack\Console\Descriptor;

use Danack\Console\Application;
use Danack\Console\Command\AbstractCommand;
use Danack\Console\Input\InputArgument;
use Danack\Console\Input\InputDefinition;
use Danack\Console\Input\InputOption;
use Danack\Console\Output\OutputInterface;

/**
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
abstract class Descriptor implements DescriptorInterface
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * {@inheritdoc}
     */
    public function describe(OutputInterface $output, $object, array $options = array())
    {
        $this->output = $output;

        switch (true) {
            case $object instanceof InputArgument:
                $this->describeInputArgument($object, $options);
                break;
            case $object instanceof InputOption:
                $this->describeInputOption($object, $options);
                break;
            case $object instanceof InputDefinition:
                $this->describeInputDefinition($object, $options);
                break;
            case $object instanceof AbstractCommand:
                $this->describeCommand($object, $options);
                break;
            case $object instanceof Application:
                $this->describeApplication($object, $options);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Object of type "%s" is not describable.', get_class($object)));
        }
    }

    /**
     * Writes content to output.
     *
     * @param string  $content
     * @param bool    $decorated
     */
    protected function write($content, $decorated = false)
    {
        $this->output->write($content, false, $decorated ? OutputInterface::OUTPUT_NORMAL : OutputInterface::OUTPUT_RAW);
    }

    /**
     * Describes an InputArgument instance.
     *
     * @param InputArgument $argument
     * @param array         $options
     *
     * @return string|mixed
     */
    abstract protected function describeInputArgument(InputArgument $argument, array $options = array());

    /**
     * Describes an InputOption instance.
     *
     * @param InputOption $option
     * @param array       $options
     *
     * @return string|mixed
     */
    abstract protected function describeInputOption(InputOption $option, array $options = array());

    /**
     * Describes an InputDefinition instance.
     *
     * @param InputDefinition $definition
     * @param array           $options
     *
     * @return string|mixed
     */
    abstract protected function describeInputDefinition(InputDefinition $definition, array $options = array());

    /**
     * Describes a Command instance.
     *
     * @param AbstractCommand $command
     * @param array   $options
     *
     * @return string|mixed
     */
    abstract protected function describeCommand(AbstractCommand $command, array $options = array());

    /**
     * Describes an Application instance.
     *
     * @param Application $application
     * @param array       $options
     *
     * @return string|mixed
     */
    abstract protected function describeApplication(Application $application, array $options = array());
}
