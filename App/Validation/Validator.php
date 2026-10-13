<?php

/**
 * Validation commune des données de TaskFlow.
 * Compatible avec PHP 5.5.12.
 */
class Validator
{
    public static function requis($valeur)
    {
        return isset($valeur) && trim((string) $valeur) !== '';
    }

    public static function entierPositif($valeur)
    {
        return filter_var(
            $valeur,
            FILTER_VALIDATE_INT,
            array('options' => array('min_range' => 1))
        ) !== false;
    }

    public static function longueurMaximale($valeur, $maximum)
    {
        return strlen((string) $valeur) <= $maximum;
    }

    public static function dateValide($date)
    {
        if (!self::requis($date)) {
            return false;
        }

        $format = 'Y-m-d';
        $objetDate = DateTime::createFromFormat($format, $date);

        return $objetDate !== false
            && $objetDate->format($format) === $date;
    }

    public static function valeurAutorisee($valeur, $valeursAutorisees)
    {
        return in_array($valeur, $valeursAutorisees, true);
    }
}