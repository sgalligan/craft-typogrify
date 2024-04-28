<?php
/**
 * Typogrify plugin for Craft CMS 3.x
 *
 * Typogrify prettifies your web typography by preventing ugly quotes and
 * 'widows' and more
 *
 * @link      https://nystudio107.com/
 * @copyright Copyright (c) 2017 nystudio107
 */

namespace nystudio107\typogrify\services;

use craft\helpers\ArrayHelper;
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
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        // Merge in the passed config, so it our config can be overridden by Plugins::pluginConfigs['typogrify']
        // ref: https://github.com/craftcms/cms/issues/1989
        $config = ArrayHelper::merge([
            'components' => [
                'typogrify' => TypogrifyService::class,
            ],
        ], $config);

        parent::__construct($id, $parent, $config);
    }

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
