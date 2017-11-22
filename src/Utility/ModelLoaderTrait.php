<?php

namespace Viewflex\Ligero\Utility;

/**
 * This trait loads the model and sets table name as specified in publisher's config.
 * If model name is empty in config, the default specified here is used instead.
 */
trait ModelLoaderTrait
{
    /**
     * Returns the publisher's configured model name or package default hard-coded here.
     *
     * @return string
     */
    public function modelName()
    {
        return $this->config->getModelName() ? : 'Viewflex\Ligero\Base\BaseModel';
    }

    /**
     * Used in publisher api to verify that configured (or default) model class exists.
     *
     * @return bool
     */
    public function modelExists()
    {
        return class_exists($this->modelName());
    }

    /**
     * Used in publisher queries to initialize model and (optionally) set it's table name.
     * If table name is not configured, default is lowercase plural of model class name.
     */
    public function loadModel()
    {
        $model_name = $this->modelName();
        $this->model = new $model_name;
        if ($this->config->getTableName())
            $this->model->setTable($this->config->getTableName());
    }

}
