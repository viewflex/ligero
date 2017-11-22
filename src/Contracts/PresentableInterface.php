<?php

namespace Viewflex\Ligero\Contracts;

use Viewflex\Ligero\Exceptions\PresenterException;

interface PresentableInterface
{
    /**
     * @return string
     */
    public function getPresenter();

    /**
     * @param string $presenter
     */
    public function setPresenter($presenter);

    /**
     * @return mixed
     * @throws PresenterException
     */
    public function present();
    
}
