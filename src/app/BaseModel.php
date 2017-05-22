<?php

namespace Sjdaws\LoyaltyCorpTest;

use Illuminate\Database\Eloquent\Model;

/**
 * Provide shared functionality between Models
 */
class BaseModel extends Model
{
    /**
     * Keys which indicate an array allowing us to convert stats_time_opened to an array stats['time_opened']
     *
     * @var array
     */
    protected $arrayKeys = [];

    /**
     * Keys which shouldn't be expanded, but rather serialised
     *
     * @var array
     */
    protected $serialise = [];

    /**
     * Listen for save event
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // When model is saving replace empty strings with nullables
        static::saving(function($model)
        {
            self::setNullables($model);
        });
    }

    /**
     * Convert some mailchimp dates from 0000-00-00T00:00:00+00:00 to 0000-00-00 00:00:00
     *
     * @param string $value The value to test/fix
     *
     * @return string The original value or fixed value
     */
    private function fixDates(string $value) : string
    {
        if (preg_match('/[0-9]{4}\-[0-9]{2}\-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}\+[0-9]{2}:[0-9]{2}/', $value)) {
            $value = date('Y-m-d H:i:s', strtotime($value));
        }

        return $value;
    }

    /**
     * Convert the model to mailchimp format
     *
     * @return array
     */
    public function modelToMailchimp() : array
    {
        // Sort data to mailchimp format
        $data = [];
        foreach ($this->getAttributes() as $key => $value) {
            // Do we have an array?
            foreach ($this->arrayKeys as $arrayKey) {
                // If key doesn't match move on
                if (strpos($key, $arrayKey) !== 0) {
                    continue;
                }

                // We have an array, ensure we have a holder
                if (!isset($data[$arrayKey]) || !is_array($data[$arrayKey])) {
                    $data[$arrayKey] = [];
                }

                // If value is serialised, decode
                if (in_array($arrayKey, $this->serialise)) {
                    $value = unserialize($value);
                }

                // Add value to array
                $newKey = substr($key, mb_strlen($arrayKey) + 1);
                $data[$arrayKey][$newKey] = $value;

                // Skip to next list field
                continue 2;
            }

            // If value is serialised, decode
            if (in_array($key, $this->serialise)) {
                $value = unserialize($value);
            }

            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Convert a mailchimp response to model format
     *
     * @param array $list The receieved list
     *
     * @return array
     */
    public function mailchimpToModel(array $list = []) : array
    {
        // Convert list to the right format
        $data = [];
        foreach ($list as $key => $value) {
            // Recurse one level deep
            if (is_array($value) && !in_array($key, $this->serialise)) {
                // Ignore links
                if ($key == '_links') {
                    continue;
                }

                foreach ($value as $subKey => $subValue) {
                    $data[$key . '_' . $subKey] = $this->fixDates($subValue);
                }

                continue;
            }

            // Remap id to mailchimp id for our models
            if ($key == 'id') {
                $key = 'mailchimp_id';
            }

            // If value is an array, serialise it
            if (is_array($value)) {
                $value = serialize($value);
            }

            $data[$key] = $this->fixDates($value);
        }

        return $data;
    }

    /**
     * Set empty nullable fields to null
     *
     * @param Model $model The model to check attributes of
     *
     * @return void
     */
    protected static function setNullables(Model $model)
    {
        // Loop through model attributes to look for empty values
        foreach ($model->getAttributes() as $field => $value) {
            if (!is_array($value) && trim($value) === '') {
                $model->setAttribute($field, null);
            }
        }
    }
}
