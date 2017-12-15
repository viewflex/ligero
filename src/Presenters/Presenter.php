<?php

namespace Viewflex\Ligero\Presenters;

use Viewflex\Ligero\Contracts\PresenterInterface;
use Viewflex\Ligero\Contracts\PublisherConfigInterface;
use Viewflex\Ligero\Exceptions\PresenterException;
use Viewflex\Ligero\Utility\Formatter as DefaultFormatter;

/**
 * Extend this class, and add methods for presenting item values.
 * Uses magic method __get() to grab attribute or method value.
 * Used via model's present() method (see PresentableTrait).
 */
abstract class Presenter implements PresenterInterface
{
    /**
     * The model instance.
     *
     * @var
     */
    protected $entity;

    /**
     * The optional publisher config object.
     *
     * @var null|PublisherConfigInterface
     */
    protected $config = null;

    /**
     * The optional formatter object.
     *
     * @var
     */
    protected $formatter = null;

    /**
     * Take injected entity (model), and optionally specified
     * config object, using custom formatter if configured.
     * In any case, we get a presenter ready to output the
     * base or custom presentations of model attributes.
     *
     * @throws PresenterException
     */
    public function __construct()
    {
        $num_args = func_num_args();

        switch ($num_args) {
            case 1: {
                $this->setEntity(func_get_arg(0));
                break;
            }

            case 2: {
                $this->setEntity(func_get_arg(0));
                $this->setConfig(func_get_arg(1));

                if (! $config_formatter = $this->config->getFormatter())
                    $this->setFormatter(new DefaultFormatter($this->config));
                else {
                    if (class_exists($config_formatter)) {
                        $this->setFormatter(new $config_formatter($this->getConfig()));
                    } else
                        throw new PresenterException('Invalid formatter class specified.');
                }

                break;
            }

            default: {
                throw new PresenterException('Wrong number of parameters.');
            }
        }
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param $entity
     * @return PresenterInterface
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @return PublisherConfigInterface
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * @param PublisherConfigInterface $config
     * @return PresenterInterface
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * @param $formatter
     * @return PresenterInterface
     */
    public function setFormatter($formatter)
    {
        $this->formatter = $formatter;
        return $this;
    }
    
    /**
     * @throws PresenterException
     */
    public function requireConfig()
    {
        if (is_null($this->getConfig()))
            throw new PresenterException('A valid config is required by this method.');
    }
    
    /**
     * @param $column
     * @throws PresenterException
     */
    public function requireColumn($column)
    {
        if (! $this->entity->offsetExists($column))
            throw new PresenterException("The column \"".$column."\" is required by this method.");
    }
    
    /**
     * @param $property
     * @return mixed
     * @throws PresenterException
     */
    public function __get($property)
    {
        if (method_exists($this, $property))
        {
            return $this->{$property}();
        }

        return $this->entity->{$property};
    }

}
