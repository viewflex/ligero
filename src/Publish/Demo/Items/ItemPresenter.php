<?php

namespace Viewflex\Ligero\Publish\Demo\Items;

use Viewflex\Ligero\Base\BasePresenter;
use Viewflex\Ligero\Exceptions\PresenterException;

class ItemPresenter extends BasePresenter
{
    /**
     * Returns an array of dynamic fields for current item.
     *
     * @return array
     * @throws PresenterException
     */
    public function dynamicFields()
    {
        $data = [];
        $this->requireConfig();

        $data['price'] = $this->price();
        $data['price_round'] = $this->priceRound();

        if ($this->config->unitConversions()) {
            $data['alt_price'] = $this->altPrice();
            $data['alt_price_round'] = $this->altPriceRound();
        }

        return $data;
    }

}
