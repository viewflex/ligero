<?php

namespace Viewflex\Ligero\Base;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Viewflex\Ligero\Contracts\PresentableInterface;
use Viewflex\Ligero\Presenters\PresentableTrait;

class BaseModel extends EloquentModel implements PresentableInterface
{
    use PresentableTrait;

    protected $guarded = ['id'];
    
    public function dates()
    {
        return $this->dates;
    }
    
}
