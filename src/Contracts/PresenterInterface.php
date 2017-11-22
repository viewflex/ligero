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
     * Set the model instance to use.
     *
     * @param $entity
     */
    public function setEntity($entity);

    /**
     * @return PublisherConfigInterface
     */
    public function getConfig();

    /**
     * @param PublisherConfigInterface $config
     */
    public function setConfig($config);

    /**
     * @return mixed
     */
    public function getFormatter();

    /**
     * @param $formatter
     */
    public function setFormatter($formatter);

    /**
     * Make sure the presenter's config reference
     * has been set, for methods that require it.
     *
     * @throws PresenterException
     */
    public function requireConfig();

    /**
     * Make sure the attribute exists before trying to use it.
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
