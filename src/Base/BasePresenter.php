<?php

namespace Viewflex\Ligero\Base;

use Viewflex\Ligero\Exceptions\PresenterException;
use Viewflex\Ligero\Presenters\Presenter;

/**
 * The default model presenter class, used if none specified.
 *
 * Using in publisher scope, $this is injected on first use
 * of model's present() method, to provide access to all model
 * properties, methods, and (optionally) publisher config.
 *
 * Since a presenter can also be used outside the publisher
 * scope, use requireConfig() at the top of any custom
 * methods to throw exception if no config exists.
 *
 * Outside of publisher scope, a presenter can be used in any
 * context with access to the model (ie: views, composer).
 *
 * This example function formats an attribute with an HTML tag:
 *
 * public function title()
 * {
 *     return '<h1>'.$this->entity->title.'</h1>';
 * }
 *
 * The entity here is the model class instance from which
 * we will get the attribute or method value using the
 * magic __get() method of the base Presenter class.
 * 
 * The requireColumn() method can be used to make sure the
 * column exists on a model before trying to access it.
 * 
 */
class BasePresenter extends Presenter
{
    /*
    |--------------------------------------------------------------------------
    | Dynamic Fields
    |--------------------------------------------------------------------------
    */

    /**
     * Returns an array of dynamic fields for current item.
     * Extend this presenter and override to customize.
     * This is just an example employing a formatter
     * for currencies & measures, with conversions.
     *
     * @return array
     * @throws PresenterException
     */
    public function dynamicFields()
    {
        $data = [];

        // Make sure we have initialized the presenter
        // with a config and formatter, needed here.
        $this->requireConfig();

        // An example of how presenter methods can be employed
        // to generate custom formatting and unit conversions.
        if (false) {

            $data['length'] = $this->length();
            $data['width'] = $this->width();
            $data['depth'] = $this->depth();
            $data['dimensions'] = $this->dimensions();
            $data['weight'] = $this->weight();
            $data['price'] = $this->price();
            $data['price_round'] = $this->priceRound();

            if ($this->config->getUnitConversions()) {
                $data['alt_length'] = $this->altLength();
                $data['alt_width'] = $this->altWidth();
                $data['alt_depth'] = $this->altDepth();
                $data['alt_dimensions'] = $this->altDimensions();
                $data['alt_weight'] = $this->altWeight();
                $data['alt_price'] = $this->altPrice();
                $data['alt_price_round'] = $this->altPriceRound();
            }

        }

        return $data;
    }

    /*
    |--------------------------------------------------------------------------
    | Currencies
    |--------------------------------------------------------------------------
    */

    /**
     * @return string
     */
    public function price()
    {
        $this->requireColumn('price');
        return $this->formatter->formatMoney($this->entity->price);
    }

    /**
     * @return string
     */
    public function priceRound()
    {
        $this->requireColumn('price');
        return $this->formatter->formatMoneyRound($this->entity->price);
    }

    /**
     * @return string
     */
    public function altPrice()
    {
        $this->requireColumn('price');
        return $this->formatter->formatAltMoney($this->formatter->convertMoney($this->entity->price));
    }

    /**
     * @return string
     */
    public function altPriceRound()
    {
        $this->requireColumn('price');
        return $this->formatter->formatAltMoneyRound($this->formatter->convertMoney($this->entity->price));
    }

    /*
    |--------------------------------------------------------------------------
    | Length and Weight Units
    |--------------------------------------------------------------------------
    */

    /**
     * @return string
     */
    public function length()
    {
        $this->requireColumn('length');
        return $this->formatter->formatLength($this->entity->length);
    }

    /**
     * @return string
     */
    public function width()
    {
        $this->requireColumn('width');
        return $this->formatter->formatLength($this->entity->width);
    }

    /**
     * @return string
     */
    public function depth()
    {
        $this->requireColumn('depth');
        return $this->formatter->formatLength($this->entity->depth);
    }

    /**
     * @return string
     */
    public function dimensions()
    {
        $dimensions = '';

        if ($this->entity->length) {
            $dimensions = $this->formatter->formatLength($this->entity->length);

            if ($this->entity->width) {
                $dimensions .= ' x ' . $this->formatter->formatLength($this->entity->width);

                if ($this->entity->depth)
                    $dimensions .= ' x ' . $this->formatter->formatLength($this->entity->depth);
            }
        }

        return $dimensions;
    }

    /**
     * @return string
     */
    public function weight()
    {
        $this->requireColumn('weight');
        return $this->formatter->formatWeight($this->entity->weight);
    }

    /**
     * @return string
     */
    public function altLength()
    {
        $this->requireColumn('length');
        return $this->formatter->formatAltLength($this->formatter->convertLength($this->entity->length));
    }

    /**
     * @return string
     */
    public function altWidth()
    {
        $this->requireColumn('width');
        return $this->formatter->formatAltLength($this->formatter->convertLength($this->entity->width));
    }

    /**
     * @return string
     */
    public function altDepth()
    {
        $this->requireColumn('depth');
        return $this->formatter->formatAltLength($this->formatter->convertLength($this->entity->depth));
    }

    /**
     * @return string
     */
    public function altDimensions()
    {
        $dimensions = '';

        if ($this->entity->length) {
            $dimensions = $this->formatter->formatAltLength($this->formatter->convertLength($this->entity->length));

            if ($this->entity->width) {
                $dimensions .= ' x ' . $this->formatter->formatAltLength($this->formatter->convertLength($this->entity->width));

                if ($this->entity->depth)
                    $dimensions .= ' x ' . $this->formatter->formatAltLength($this->formatter->convertLength($this->entity->depth));
            }
        }

        return $dimensions;
    }

    /**
     * @return string
     */
    public function altWeight()
    {
        $this->requireColumn('weight');
        return $this->formatter->formatAltWeight($this->formatter->convertWeight($this->entity->weight));
    }

}
