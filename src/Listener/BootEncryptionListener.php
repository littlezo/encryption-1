<?php

declare(strict_types=1);
/**
 * This file is part of friendsofhyperf/encryption.
 *
 * @link     https://github.com/friendsofhyperf/encryption
 * @document https://github.com/friendsofhyperf/encryption/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace Friendsofhyperf\Encryption\Listener;

use Friendsofhyperf\Encryption\KeyParser;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Opis\Closure\SerializableClosure;
use Psr\Container\ContainerInterface;

class BootEncryptionListener implements ListenerInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var KeyParser
     */
    private $parser;

    public function __construct(ContainerInterface $container)
    {
        $this->config = $container->get(ConfigInterface::class);
        $this->parser = $container->get(KeyParser::class);
    }

    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event)
    {
        $this->registerOpisSecurityKey();
    }

    /**
     * Configure Opis Closure signing for security.
     */
    protected function registerOpisSecurityKey(): void
    {
        $config = $this->config->get('encryption', []);

        if (! class_exists(SerializableClosure::class) || empty($config['key'])) {
            return;
        }

        SerializableClosure::setSecretKey($this->parser->parseKey($config));
    }
}
