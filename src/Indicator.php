<?php

namespace Khalin\Fields;

use Laravel\Nova\Fields\Field;

class Indicator extends Field
{

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'indicator-field';
    /**
     * The callback to be used to hide the field.
     *
     * @var \Closure
     */
    public $hideCallback;
    /**
     * Indicates if the element should be shown on the creation view.
     *
     * @var bool
     */
    public $showOnCreation = false;
    /**
     * Indicates if the element should be shown on the update view.
     *
     * @var bool
     */
    public $showOnUpdate = false;
    /**
     * The callback to be used to display labels based on other value.
     *
     * @var \Closure
     */
    public $valuesCallback;

    /**
     * Specify the colours that should be displayed.
     *
     * @param array $colors
     *
     * @return $this
     */
    public function colors(array $colors)
    {
        return $this->withMeta(['colors' => $colors]);
    }

    /**
     * Specify the labels that should be displayed.
     *
     * @param array $labels
     *
     * @return $this
     */
    public function labels(array $labels)
    {
        return $this->withMeta(['labels' => $labels, 'withoutLabels' => false]);
    }

    /**
     * @inheritDoc
     */
    public function resolveForDisplay($resource, $attribute = null)
    {
        parent::resolveForDisplay($resource, $attribute);

        if (!is_null($this->valuesCallback)) {
            $this->withMeta(['displayValue' => call_user_func($this->valuesCallback, $this->value, $resource)]);
        }

        if (!is_null($this->hideCallback)) {
            if (is_callable($this->hideCallback)) {
                $shouldHide = call_user_func($this->hideCallback, $this->value, $resource);
            } elseif (is_array($this->hideCallback)) {
                $shouldHide = in_array($this->value, $this->hideCallback, false);
            } else {
                $shouldHide = $this->value == $this->hideCallback;
            }
            $this->withMeta(['shouldHide' => (bool)$shouldHide]);
        }
    }

    /**
     * Define the callback or value(s) that should be used to hide the field.
     *
     * @param callable|array|mixed $hideCallback
     *
     * @return $this
     */
    public function shouldHide($hideCallback)
    {
        $this->hideCallback = $hideCallback;

        return $this;
    }

    /**
     * Define that the field should be hidden if falsy (0, false, null, '')
     *
     * @return $this
     */
    public function shouldHideIfNo()
    {
        $this->hideCallback = function ($value) {
            return !$value;
        };

        return $this;
    }

    /**
     * The label to display when the value is not one of the defined statuses.
     *
     * @param string $label
     *
     * @return $this
     */
    public function unknown(string $label)
    {
        return $this->withMeta(['unknownLabel' => $label]);
    }

    /**
     * Display the raw value instead of a label.
     *
     * @return $this
     */
    public function useValues(callable $value = null)
    {
        $this->valuesCallback = $value;

        return $this->withMeta(['useValues' => true, 'withoutLabels' => false]);
    }

    /**
     * Display only color coded dots
     *
     * @return $this
     */
    public function withoutLabels()
    {
        return $this->withMeta(['withoutLabels' => true]);
    }
}
