<?php

namespace Geocuba\Utils;

/**
 * Class Functions
 * @package Geocuba\Utils
 */
abstract class Functions
{
    /*******************************************************************************************************************
     * Functions
     ******************************************************************************************************************/
    /**
     * @param array $definition
     * @param array $values
     * @return false|int|null|string
     */
    public static function getKeys($definition, $values)
    {
        $keys = null;

        foreach ($values as $value) {
            $key = array_search($value, $definition);
            $keys = is_null($keys) ? $key : ($keys | $key);
        }

        return $keys;
    }

    /**
     * @param array $definition
     * @param integer $keys
     * @param bool $from_keys
     * @return array
     */
    public static function getValues($definition, $keys, $from_keys = true)
    {
        $values = [];

        foreach (array_keys($definition) as $key) {
            if ($keys & $key) {
                $values[] = $from_keys ? $key : $definition[$key];
            }
        }

        return $values;
    }

    /**
     * @param array $values
     * @return int mixed
     */
    public static function getValue($values)
    {
        return array_reduce($values, function ($carry, $item) {
            return $carry | $item;
        });
    }

    /**
     * @param \DateTime $date_obj
     * @return mixed
     */
    public static function formatDate($date_obj)
    {
        return $date_obj ? $date_obj->format(Constants::DATE_FORMAT) : null;
    }

    /**
     * @param \DateTime $date_obj
     * @return mixed
     */
    public static function formatTime($date_obj)
    {
        return $date_obj ? $date_obj->format(Constants::TIME_FORMAT) : null;
    }

    /**
     * @param $date_str
     * @return \DateTime|false
     */
    public static function parseDate($date_str)
    {
        return $date_str ? date_create_from_format(Constants::DATE_FORMAT, $date_str) : null;
    }

    /**
     * @param \DateTime $datetime_obj
     * @return mixed
     */
    public static function formatDateTime($datetime_obj)
    {
        return $datetime_obj ? $datetime_obj->format(Constants::DATETIME_FORMAT) : null;
    }

    /**
     * @param $datetime_str
     * @return \DateTime|false
     */
    public static function parseDateTime($datetime_str)
    {
        return $datetime_str ? date_create_from_format(Constants::DATETIME_FORMAT, $datetime_str) : null;
    }

    /**
     * Días Festivos y de Conmemoración Nacional
     *
     * @param \DateTime $date
     * @return bool
     */
    public static function isFreeDate($date)
    {
        try {
            $year = intval($date->format('Y'));
            $day = intval($date->format('j'));
            $month = intval($date->format('n'));

            $free_days = [1 => [1, 2], 5 => [1], 7 => [25, 26, 27], 10 => [10], 12 => [25, 31]];
            $holy_friday_date = new \DateTime();
            $holy_friday_date->setTimestamp(easter_date($year)); // sunday
            $holy_friday_date->sub(new \DateInterval('P2D')); // friday
            $holy_friday_month = intval($holy_friday_date->format('n'));
            $holy_friday_day = intval($holy_friday_date->format('j'));

            if (array_key_exists($holy_friday_month, $free_days) && !array_key_exists($holy_friday_day, $free_days[$holy_friday_month])) {
                $_days = $free_days[$holy_friday_month];
                $free_days[$holy_friday_month] = array_push($_days, $holy_friday_day);
            }
            $free_days[$holy_friday_month] = [$holy_friday_day];

            return array_key_exists($month, $free_days) && in_array($day, $free_days[$month]);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * This method is part of the Nette Framework (https://nette.org)
     * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
     *
     * Converts to ASCII.
     * @param  string $value UTF-8 encoding
     * @return string  ASCII
     */
    public static function toASCII($value)
    {
        static $transliterator = NULL;
        if ($transliterator === NULL && class_exists('Transliterator', FALSE)) {
            $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');
        }
        $value = preg_replace('#[^\x09\x0A\x0D\x20-\x7E\xA0-\x{2FF}\x{370}-\x{10FFFF}]#u', '', $value);
        $value = strtr($value, '`\'"^~?', "\x01\x02\x03\x04\x05\x06");
        $value = str_replace(
            ["\xE2\x80\x9E", "\xE2\x80\x9C", "\xE2\x80\x9D", "\xE2\x80\x9A", "\xE2\x80\x98", "\xE2\x80\x99", "\xC2\xB0"],
            ["\x03", "\x03", "\x03", "\x02", "\x02", "\x02", "\x04"], $value
        );
        if ($transliterator !== NULL) {
            $value = $transliterator->transliterate($value);
        }
        if (ICONV_IMPL === 'glibc') {
            $value = str_replace(
                ["\xC2\xBB", "\xC2\xAB", "\xE2\x80\xA6", "\xE2\x84\xA2", "\xC2\xA9", "\xC2\xAE"],
                ['>>', '<<', '...', 'TM', '(c)', '(R)'], $value
            );
            $value = iconv('UTF-8', 'WINDOWS-1250//TRANSLIT//IGNORE', $value);
            $value = strtr($value, "\xa5\xa3\xbc\x8c\xa7\x8a\xaa\x8d\x8f\x8e\xaf\xb9\xb3\xbe\x9c\x9a\xba\x9d\x9f\x9e"
                . "\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3"
                . "\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8"
                . "\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf8\xf9\xfa\xfb\xfc\xfd\xfe"
                . "\x96\xa0\x8b\x97\x9b\xa6\xad\xb7",
                'ALLSSSSTZZZallssstzzzRAAAALCCCEEEEIIDDNNOOOOxRUUUUYTsraaaalccceeeeiiddnnooooruuuuyt- <->|-.');
            $value = preg_replace('#[^\x00-\x7F]++#', '', $value);
        } else {
            $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        }
        $value = str_replace(['`', "'", '"', '^', '~', '?'], '', $value);
        return strtr($value, "\x01\x02\x03\x04\x05\x06", '`\'"^~?');
    }

    /**
     * TODO: define a param to set the string function used to compare: ?(bytes) or mb_strlen(characters).
     *
     * @param string $text
     * @param integer $limit
     * @param bool $bytes
     * @return array
     */
    public static function hyphenateText($text, $limit, $bytes = false)
    {
        $syllable = new \Syllable('es', new \Syllable_Hyphen_Dash());

        $lines = [''];
        $current = 0;

        foreach (explode('-', $syllable->hyphenateText($text)) as $hyphenated_text) {
            $hyphenated_text = utf8_decode($hyphenated_text);

            $temp = $lines[$current] . $hyphenated_text;
            if ($bytes ? strlen($temp) : mb_strlen($temp) < $limit) {
                $lines[$current] = $temp;
            } else {
                $words = explode(' ', $hyphenated_text);
                foreach ($words as $index => $word) {
                    $temp2 = $lines[$current] . ($index > 0 ? ' ' : '') . $word;

                    if ($bytes ? strlen($temp2) : mb_strlen($temp2) < $limit) {
                        $lines[$current] = $temp2 . (($index + 1) === count($words) ? '' : ' ');
                    } else {
                        $lines[++$current] = $word . (($index + 1) === count($words) ? '' : ' ');
                    }
                }
            }
        }

        return $lines;
    }

    /**
     * @param \FPDF $pdf_obj
     * @param $text_width_limit
     * @param $chr_width_max
     * @param $chr_width_min
     * @param $text
     * @return float|int
     */
    public static function calculateTextWidth($pdf_obj, $text_width_limit, $chr_width_max, $chr_width_min, $text)
    {
        $text_width = $pdf_obj->GetStringWidth($text);
        $text_width = $text_width > $text_width_limit ? $text_width_limit : $text_width;
        $is_valid = false;

        while (!$is_valid) {
            $tmp_max_width = 0;

            foreach (self::hyphenateText($text, $text_width) as $line) {
                $_text_width = $pdf_obj->GetStringWidth($line);
                $tmp_max_width = $tmp_max_width < $_text_width ? $_text_width : $tmp_max_width;
            }

            if ($tmp_max_width <= $text_width_limit) {
                break;
            }

            $text_width = $text_width - (($tmp_max_width / $text_width_limit) > 2 ? $chr_width_max : $chr_width_min);
        }

        return $text_width;
    }

    /**
     * @param $filepath
     * @param $name
     * @param $content
     * @return bool
     */
    public static function createZipFile($filepath, $name, $content)
    {
        $zipfile = new \ZipArchive();
        if ($zipfile->open($filepath, \ZipArchive::CREATE) !== true) {
            return false;
        }
        $zipfile->addFromString($name, $content);
        $zipfile->close();

        return true;
    }

    /**
     * @param $filepath
     * @param int $decimals
     * @return string
     */
    public static function getFileSize($filepath, $decimals = 2)
    {
        return self::getSize(filesize($filepath), $decimals);
    }


    /**
     * @param $bytes
     * @param int $decimals
     * @return string
     */
    public static function getSize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = intval(floor((strlen($bytes) - 1) / 3));

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}