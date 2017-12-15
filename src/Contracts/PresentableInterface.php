<?php

namespace Viewflex\Ligero\Contracts;

use Viewflex\Ligero\Exceptions\PresenterException;

interface PresentableInterface
{
    /**
     * Returns the model's presenter class.
     * 
     * @return string
     */
    public function getPresenter();

    /**
     * Sets  the model's presenter class.
     * 
     * @param string $presenter
     */
    public function setPresenter($presenter);

    /**
     * Returns a presenter instance with it's model instance,
     * and, if specified, optional config (and formatter).
     * 
     * @return mixed
     * @throws PresenterException
     */
    public function present();
    
}
