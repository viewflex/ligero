<?php

namespace Viewflex\Ligero\Utility;

use Viewflex\Forex\Forex;
use Viewflex\Ligero\Contracts\PublisherConfigInterface;
use Viewflex\Ligero\Exceptions\FormatterException;

/**
 * This class provides methods for basic formatting and conversions.
 * Used by presenters to perform common operations on model's data.
 **/
class Formatter
{
    /**
     * The configuration used by the publisher implementation.
     *
     * @var PublisherConfigInterface
     */
    protected $config;

    /**
     * @param PublisherConfigInterface $config
     */
    public function __construct(PublisherConfigInterface $config)
    {
        $this->config = $config;
    }

    /*
    |--------------------------------------------------------------------------
    | Currencies
    |--------------------------------------------------------------------------
    */

    /**
     * The primary-to-secondary currency exchange rate.
     *
     * @var null
     */
    protected $exchange_rate = null;

    /**
     * Formats money value as primary or secondary currency.
     * Specify decimals to override config precision.
     * 
     * @param $value
     * @param string $currency
     * @param int $decimals
     * @return string
     */
    public function formatCurrency($value, $currency = 'primary', $decimals = null)
    {
        $money = $this->config->getCurrencies()[$currency];
        $precision = $decimals === null ? $money['precision'] : $decimals;

        return (is_null($value) || $value == 0) ?
            $this->config->ls('ui.label_null_value') :
            $money['prefix']
            .number_format($value, $precision, $money['decimal'], $money['thousands'])
            .$money['suffix'];
    }

    /**
     * Returns a formatted string for given money value in primary currency.
     *
     * @param float $value
     * @return string
     */
    public function formatMoney($value)
    {
        return $this->formatCurrency($value, 'primary');
    }

    /**
     * Returns a formatted string for given money value in rounded primary currency.
     *
     * @param float $value
     * @return string
     */
    public function formatMoneyRound($value)
    {
        return $this->formatCurrency($value, 'primary', 0);
    }

    /**
     * Returns a formatted string for given money value in secondary currency.
     *
     * @param float $value
     * @return string
     */
    public function formatAltMoney($value)
    {
        return $this->formatCurrency($value, 'secondary');
    }

    /**
     * Returns a formatted string for given money value in rounded secondary currency.
     *
     * @param float $value
     * @return string
     */
    public function formatAltMoneyRound($value)
    {
        return $this->formatCurrency($value, 'secondary', 0);
    }

    /**
     * Converts a money value from primary to secondary currency.
     *
     * @param float $value
     * @return float
     */
    public function convertMoney($value)
    {
        return $value * $this->getExchangeRate();
    }

    /**
     * Gets and stores primary-to-secondary exchange rate.
     *
     * @return float|null
     * @throws FormatterException
     */
    public function getExchangeRate()
    {
        if ($this->exchange_rate === null) {

            $money = $this->config->getCurrencies();

            $this->exchange_rate = (new Forex)->getRate(
                $money['primary']['ISO_code'],
                $money['secondary']['ISO_code']
            );
        }

        return $this->exchange_rate;
    }

    /*
    |--------------------------------------------------------------------------
    | Length Units
    |--------------------------------------------------------------------------
    */

    /**
     * Length and weight conversion factors.
     *
     * @var array
     */
    protected $conversion_factors = [
        'in-cm'         =>  2.54,
        'ft-cm'         =>  30.5,
        'ft-m'          =>  0.305,
        'yd-m'          =>  0.914,
        'mi-km'         =>  1.61,
        'cm-in'         =>  0.394,
        'cm-ft'         =>  0.03278689,
        'm-ft'          =>  3.28,
        'm-yd'          =>  1.09,
        'km-mi'         =>  0.621,
        'oz-g'          =>  28.3,
        'oz-kg'         =>  0.0283,
        'lb-g'          =>  452.8,
        'lb-kg'         =>  0.454,
        'sh tn-Mg'      =>  0.907,
        'g-oz'          =>  0.0353,
        'g-lb'          =>  0.002208481,
        'kg-oz'         =>  35.2,
        'kg-lb'         =>  2.2,
        'Mg-sh tn'      =>  1.1,
    ];

    /**
     * Returns specified length or weight conversion factor.
     *
     * @param string $source
     * @param string $target
     * @return float
     * @throws FormatterException
     */
    public function conversionFactor($source, $target)
    {
        $conversion = ($source."-".$target);

        if (! array_key_exists($conversion, $this->conversion_factors))
            throw new FormatterException("The conversion factor \"".$conversion."\" does not exist.");
        else
            return $this->conversion_factors[$conversion];
    }

    /**
     * Format length value in primary or secondary units.
     * Specify decimals to override config precision.
     *
     * @param $value
     * @param string $unit
     * @param int $decimals
     * @return string
     */
    public function formatLengthUnits($value, $unit = 'primary', $decimals = null)
    {
        $ruler = $this->config->getRulerUnits()[$unit];
        $precision = $decimals === null ? $ruler['precision'] : $decimals;

        return (is_null($value) || $value == 0) ?
            $this->config->ls('ui.label_null_value') :
            number_format($value, $precision, '.', ',')
            .$ruler['suffix'];
    }

    /**
     * Returns a formatted string for a given length value using primary ruler unit.
     *
     * @param float $value
     * @return string
     */
    public function formatLength($value)
    {
        return $this->formatLengthUnits($value, 'primary');
    }

    /**
     * Returns a formatted string for a given length value using secondary ruler unit.
     *
     * @param float $value
     * @return string
     */
    public function formatAltLength($value)
    {
        return $this->formatLengthUnits($value, 'secondary');
    }

    /**
     * Converts a value from primary to secondary ruler unit.
     *
     * @param float $value
     * @return float
     */
    public function convertLength($value)
    {
        return $value * $this->conversionFactor(
            $this->config->getRulerUnits()['primary']['symbol'],
            $this->config->getRulerUnits()['secondary']['symbol']
        );
    }

    /**
     * Format weight value in primary or secondary units.
     * Specify decimals to override config precision.
     *
     * @param $value
     * @param string $unit
     * @param int $decimals
     * @return string
     */
    public function formatWeightUnits($value, $unit = 'primary', $decimals = null)
    {
        $scale = $this->config->getWeightUnits()[$unit];
        $precision = $decimals === null ? $scale['precision'] : $decimals;

        return (is_null($value) || $value == 0) ?
            $this->config->ls('label_null_value') :
            number_format($value, $precision, '.', ',')
            .$scale['suffix'];
    }

    /**
     * Returns a formatted string for a given weight value using primary weight unit.
     *
     * @param float $value
     * @return string
     */
    public function formatWeight($value)
    {
        return $this->formatWeightUnits($value, 'primary');
    }

    /**
     * Returns a formatted string for a given weight value using secondary weight unit.
     *
     * @param float $value
     * @return string
     */
    public function formatAltWeight($value)
    {
        return $this->formatWeightUnits($value, 'secondary');
    }

    /**
     * Converts a value from primary to secondary weight unit.
     *
     * @param float $value
     * @return float
     */
    public function convertWeight($value)
    {
        return $value * $this->conversionFactor(
            $this->config->getWeightUnits()['primary']['symbol'],
            $this->config->getWeightUnits()['secondary']['symbol']
        );
    }

}
