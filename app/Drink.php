<?php

namespace App;

enum Drink: string
{
    case Champagne = 'champagne';
    case RedWine = 'red_wine';
    case WhiteWine = 'white_wine';
    case Vodka = 'vodka';
    case Cognac = 'cognac';
    case Whisky = 'whisky';
    case NonAlcoholic = 'non_alcoholic';

    /**
     * The name of the drink as guests see it.
     */
    public function label(): string
    {
        return match ($this) {
            self::Champagne => 'Шампанское',
            self::RedWine => 'Вино красное',
            self::WhiteWine => 'Вино белое',
            self::Vodka => 'Водка',
            self::Cognac => 'Коньяк',
            self::Whisky => 'Виски',
            self::NonAlcoholic => 'Безалкогольное',
        };
    }
}
