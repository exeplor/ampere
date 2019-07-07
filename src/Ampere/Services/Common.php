<?php

namespace Ampere\Services;

/**
 * Class Common
 * @package Ampere\Services
 */
class Common
{
    /**
     * @param string $field
     * @return string
     */
    public static function convertModelFieldToTitle(string $field): string
    {
        $titles = [
            'created_at' => 'Created',
            'updated_at' => 'Updated'
        ];

        if (isset($titles[$field])) {
            $title = $titles[$field];

        } else {
            $title = preg_replace('/_|\./', ' ', $field);
            $title = str_replace('id', 'ID', $title);
            $title = ucwords($title);
        }

        return $title;
    }

    /**
     * @param string $field
     * @return string
     */
    public static function convertModelFieldToTitleExtended(string $field): string
    {
        $title = self::convertModelFieldToTitle($field);

        $title = preg_replace('/\s*id$/i', '', $title);

        return $title;
    }
}