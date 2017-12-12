<?php

namespace Viewflex\Ligero\Presenters;

use Viewflex\Ligero\Contracts\PresenterInterface;
use Viewflex\Ligero\Exceptions\PresenterException;

/**
 * This code exists for use in all model classes as necessary,
 * creates or uses an existing singleton Presenter object to
 * invoke any method or property of the model. The entity
 * gets set to current item on each usage of present().
 * The Presenter class name set below is the default.
 */
trait PresentableTrait
{
    /**
     * The Presenter class for generating formatted content from this model.
     *
     * @var string
     */
    protected $presenter = 'Viewflex\Ligero\Base\BasePresenter';

    /**
     * @return string
     */
    public function getPresenter()
    {
        return $this->presenter;
    }

    /**
     * @param string $presenter
     */
    public function setPresenter($presenter)
    {
        $this->presenter = $presenter;
    }

    /**
     * The presenter singleton for repeated usage.
     * 
     * @var PresenterInterface
     */
    protected static $presenter_instance;
    
    /**
     * Returns a presenter instance with it's model instance,
     * and, if specified, optional config (and formatter).
     * 
     * @return PresenterInterface
     * @throws PresenterException
     */
    public function present()
    {
        if ((! $this->presenter) || (! class_exists($this->presenter)))
            throw new PresenterException($this->presenter.
                ' not found. Please set $presenter to a valid Presenter class.');

        // Create the presenter, optionally with config, or if already existing, set it's model reference.
        if (! isset(static::$presenter_instance))
            static::$presenter_instance = func_num_args() ? new $this->presenter($this, func_get_arg(0)) : new $this->presenter($this);
        else
            static::$presenter_instance->setEntity($this);
        
        return static::$presenter_instance;
    }

}
