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

use yii\base\InvalidConfigException;

/**
 * @author    nystudio107
 * @package   Typogrify
 * @since     1.0.0
 *
 * @property  TypogrifyService $typogrify
 */
trait ServicesTrait
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function config(): array
    {
        return [
            'components' => [
                'typogrify' => TypogrifyService::class,
            ],
        ];
    }

    // Public Methods
    // =========================================================================

    /**
     * Returns the typogrify service
     *
     * @return TypogrifyService The typogrify service
     * @throws InvalidConfigException
     */
    public function getTypogrify(): TypogrifyService
    {
        return $this->get('typogrify');
    }
}
