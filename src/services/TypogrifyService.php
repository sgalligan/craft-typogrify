<?php
/**
 * Typogrify plugin for Craft CMS
 *
 * Typogrify prettifies your web typography by preventing ugly quotes and
 * 'widows' and more
 *
 * @link      https://nystudio107.com/
 * @copyright Copyright (c) nystudio107
 */

namespace nystudio107\typogrify\services;

use Craft;
use craft\base\Component;
use DateInterval;
use DateTime;
use Michelf\SmartyPants;
use nystudio107\typogrify\Typogrify;
use PHP_Typography\PHP_Typography;
use PHP_Typography\Settings;
use Stringy\Stringy;
use yii\helpers\Inflector;

/**
 * @author    nystudio107
 * @package   Typogrify
 * @since     1.0.0
 */
class TypogrifyService extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * @var null|PHP_Typography
     */
    public ?PHP_Typography $phpTypography = null;

    /**
     * @var null|Settings
     */
    public ?Settings $phpTypographySettings = null;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        // Create a new phpTypographySettings instance
        $this->phpTypographySettings = new Settings();

        // Create a new PhpTypography instance
        $this->phpTypography = new PHP_Typography();

        // Apply our default settings
        $settings = Typogrify::$plugin->getSettings();
        if ($settings) {
            $settingsArray = $settings->toArray();
            foreach ($settingsArray as $key => $value) {
                if ($key !== 'default_escape') {
                    $this->phpTypographySettings->{$key}($value);
                }
            }
        }
    }

    /**
     * Typogrify applies a veritable kitchen sink of typographic treatments to
     * beautify your web typography
     *
     * @param string|int|float|null $text The text or HTML fragment to process
     * @param bool $isTitle Optional. If the HTML fragment is a title.
     *                        Default false
     *
     * @return string The processed HTML
     */
    public function typogrify(string|int|float|null $text, bool $isTitle = false): string
    {
        if (empty($text)) {
            return '';
        }

        return $this->phpTypography->process((string)$text, $this->phpTypographySettings, $isTitle);
    }

    /**
     * Typogrify applies a veritable kitchen sink of typographic treatments to
     * beautify your web typography but in a way that is appropriate for RSS
     * (or similar) feeds -- i.e. excluding processes that may cause issues in
     * contexts with limited character set intelligence.
     *
     * @param string|int|float|null $text The text or HTML fragment to process
     * @param bool $isTitle Optional. If the HTML fragment is a title.
     *                        Default false
     *
     * @return string The processed HTML
     */
    public function typogrifyFeed(string|int|float|null $text, bool $isTitle = false): string
    {
        if (empty($text)) {
            return '';
        }

        return $this->phpTypography->process_feed((string)$text, $this->phpTypographySettings, $isTitle);
    }

    /**
     * @param string|int|float|null $text
     *
     * @return string
     */
    public function smartypants(string|int|float|null $text): string
    {
        if (empty($text)) {
            return '';
        }

        return SmartyPants::defaultTransform((string)$text);
    }

    /**
     * Truncates the string to a given length. If $substring is provided, and
     * truncating occurs, the string is further truncated so that the substring
     * may be appended without exceeding the desired length.
     *
     * @param string|int|float|null $string The string to truncate
     * @param int $length Desired length of the truncated string
     * @param string $substring The substring to append if it can fit
     *
     * @return string with the resulting $str after truncating
     */
    public function truncate(string|int|float|null $string, int $length, string $substring = '…'): string
    {
        $result = (string)$string;

        if (!empty($string)) {
            $string = strip_tags($string);
            $result = (string)Stringy::create($string)->truncate($length, $substring);
        }

        return $result;
    }

    /**
     * Truncates the string to a given length, while ensuring that it does not
     * split words. If $substring is provided, and truncating occurs, the
     * string is further truncated so that the substring may be appended without
     * exceeding the desired length.
     *
     * @param string|int|float|null $string The string to truncate
     * @param int $length Desired length of the truncated string
     * @param string $substring The substring to append if it can fit
     *
     * @return string with the resulting $str after truncating
     */
    public function truncateOnWord(string|int|float|null $string, int $length, string $substring = '…'): string
    {
        $result = (string)$string;

        if (!empty($string)) {
            $string = strip_tags($string);
            $result = (string)Stringy::create($string)->safeTruncate($length, $substring);
        }

        return $result;
    }

    /**
     * Creates a Stringy object and assigns both string and encoding properties
     * the supplied values. $string is cast to a string prior to assignment,
     * and if
     * $encoding is not specified, it defaults to mb_internal_encoding(). It
     * then returns the initialized object. Throws an InvalidArgumentException
     * if the first argument is an array or object without a __toString method.
     *
     * @param string|int|float|null $string The string initialize the Stringy object with
     * @param null|string $encoding The character encoding
     *
     * @return Stringy
     */
    public function stringy(string|int|float|null $string = '', ?string $encoding = null): Stringy
    {
        return Stringy::create($string, $encoding);
    }

    /**
     * Formats the value in bytes as a size in human readable form for example
     * `12 KB`.
     *
     * This is the short form of [[asSize]].
     *
     * If [[sizeFormatBase]] is 1024, [binary
     * prefixes](http://en.wikipedia.org/wiki/Binary_prefix)
     * (e.g. kibibyte/KiB, mebibyte/MiB, ...) are used in the formatting
     * result.
     *
     * @param string|int|float $bytes value in bytes to be formatted.
     * @param int $decimals the number of digits after the decimal
     *                                   point.
     *
     * @return string the formatted result.
     */
    public function humanFileSize(string|int|float $bytes, int $decimals = 1): string
    {
        $oldSize = Craft::$app->formatter->sizeFormatBase;
        Craft::$app->formatter->sizeFormatBase = 1000;
        $result = Craft::$app->formatter->asShortSize($bytes, $decimals);
        Craft::$app->formatter->sizeFormatBase = $oldSize;

        return $result;
    }

    /**
     * Represents the value as duration in human readable format.
     *
     * @param DateInterval|string|int $value the value to be formatted.
     *                                        Acceptable formats:
     *                                        - [DateInterval
     *                                        object](http://php.net/manual/ru/class.dateinterval.php)
     *                                        - integer - number of seconds.
     *                                        For example: value `131`
     *                                        represents `2 minutes, 11
     *                                        seconds`
     *                                        - ISO8601 duration format. For
     *                                        example, all of these values
     *                                        represent `1 day, 2 hours, 30
     *                                        minutes` duration:
     *                                        `2015-01-01T13:00:00Z/2015-01-02T13:30:00Z`
     *                                        - between two datetime values
     *                                        `2015-01-01T13:00:00Z/P1D2H30M` -
     *                                        time interval after datetime
     *                                        value
     *                                        `P1D2H30M/2015-01-02T13:30:00Z` -
     *                                        time interval before datetime
     *                                        value
     *                                        `P1D2H30M` - simply a date
     *                                        interval
     *                                        `P-1D2H30M` - a negative date
     *                                        interval (`-1 day, 2 hours, 30
     *                                        minutes`)
     *
     * @return string the formatted duration.
     */
    public function humanDuration(DateInterval|string|int $value): string
    {
        return Craft::$app->formatter->asDuration($value);
    }

    /**
     * Formats the value as the time interval between a date and now in human
     * readable form.
     *
     * This method can be used in three different ways:
     *
     * 1. Using a timestamp that is relative to `now`.
     * 2. Using a timestamp that is relative to the `$referenceTime`.
     * 3. Using a `DateInterval` object.
     *
     * @param int|string|DateTime|DateInterval $value the value to be
     *                                                          formatted. The
     *                                                          following types
     *                                                          of value are
     *                                                          supported:
     *
     * - an integer representing a UNIX timestamp
     * - a string that can be [parsed to create a DateTime
     * object](http://php.net/manual/en/datetime.formats.php). The timestamp is
     * assumed to be in [[defaultTimeZone]] unless a time zone is explicitly
     * given.
     * - a PHP [DateTime](http://php.net/manual/en/class.datetime.php) object
     * - a PHP DateInterval object (a positive time interval will refer to the
     * past, a negative one to the future)
     *
     * @param null|int|string|DateTime $referenceTime if specified
     *                                                          the value is
     *                                                          used as a
     *                                                          reference time
     *                                                          instead of
     *                                                          `now` when
     *                                                          `$value` is not
     *                                                          a
     *                                                          `DateInterval`
     *                                                          object.
     *
     * @return string the formatted result.
     */
    public function humanRelativeTime(int|string|DateTime|DateInterval $value, null|int|string|DateTime $referenceTime = null): string
    {
        return Craft::$app->formatter->asRelativeTime($value, $referenceTime);
    }

    /**
     * Converts number to its ordinal English form
     * For example, converts 13 to 13th, 2 to 2nd
     *
     * @param int $number
     *
     * @return string
     */
    public function ordinalize(int $number): string
    {
        return Inflector::ordinalize($number);
    }

    /**
     * Converts a word to its plural form
     * For example, 'apple' will become 'apples', and 'child' will become
     * 'children'
     *
     * @param string $word
     * @param int $number
     *
     * @return string
     */
    public function pluralize(string $word, int $number = 2): string
    {
        return abs($number) === 1 ? $word : Inflector::pluralize($word);
    }

    /**
     * Converts a word to its singular form
     * For example, 'apples' will become 'apple', and 'children' will become
     * 'child'
     *
     * @param string $word
     * @param int $number
     *
     * @return string
     */
    public function singularize(string $word, int $number = 1): string
    {
        return abs($number) === 1 ? Inflector::singularize($word) : $word;
    }

    /**
     * Returns transliterated version of a string
     * For example, 获取到 どちら Українська: ґ,є, Српска: ђ, њ, џ! ¿Español?
     * will be transliterated to huo qu dao dochira Ukrainsʹka: g,e, Srpska: d,
     * n, d! ¿Espanol?
     *
     * @param string $string
     * @param null $transliterator
     *
     * @return string
     */
    public function transliterate(string $string, $transliterator = null): string
    {
        return Inflector::transliterate($string, $transliterator);
    }

    /**
     * Limits a string by word count. If $substring is provided, and truncating
     * occurs, the string is further truncated so that the substring may be
     * appended without exceeding the desired length.
     *
     * @param string $string
     * @param int $length
     * @param string $substring
     *
     * @return string
     */
    public function wordLimit(string $string, int $length, string $substring = '…'): string
    {
        $words = preg_split("/[\s]+/u", strip_tags($string));
        $result = implode(' ', array_slice($words, 0, $length));

        return count($words) > $length ? $result . $substring : $result;
    }
}
