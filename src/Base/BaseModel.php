<?php

namespace Viewflex\Ligero\Base;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Viewflex\Ligero\Contracts\PresentableInterface;
use Viewflex\Ligero\Presenters\PresentableTrait;

/**
 * The base Publisher Model class, used as the default.
 * Extend and customize Model and Presenter as needed.
 */
class BaseModel extends EloquentModel implements PresentableInterface
{
    use PresentableTrait;

    protected $guarded = ['id'];
    
    public function dates()
    {
        return $this->dates;
    }
    
}
