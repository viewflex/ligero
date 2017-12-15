<?php

namespace Viewflex\Ligero\Contracts;

use Viewflex\Ligero\Exceptions\PresenterException;

interface PresenterInterface
{
    /**
     * Get the model instance being used.
     *
     * @return mixed
     */
    public function getEntity();

    /**
     * Fluently set the model instance to use.
     *
     * @param $entity
     * @return PresenterInterface
     */
    public function setEntity($entity);

    /**
     * Get the optional config object being used.
     * 
     * @return null|PublisherConfigInterface
     */
    public function getConfig();

    /**
     * Fluently set the optional config object to use.
     * 
     * @param PublisherConfigInterface $config
     * @return PresenterInterface
     */
    public function setConfig($config);

    /**
     * Get the formatter class being used.
     * 
     * @return mixed
     */
    public function getFormatter();

    /**
     * Fluently set the formatter class to use.
     * 
     * @param $formatter
     * @return PresenterInterface
     */
    public function setFormatter($formatter);

    /**
     * Make sure config object is set, for methods that require it.
     *
     * @throws PresenterException
     */
    public function requireConfig();

    /**
     * Make sure an attribute exists before trying to use it.
     *
     * @param $column
     * @throws PresenterException
     */
    public function requireColumn($column);

    /**
     * Magic method calls a presentation method if
     * it exists, otherwise gets object attribute.
     * Throws error on missing method/attribute.
     *
     * @param $property
     * @return mixed
     * @throws PresenterException
     */
    public function __get($property);

}
