<?php
/**
 * Typogrify plugin for Craft CMS
 *
 * Typogrify prettifies your web typography by preventing ugly quotes and 'widows' and more
 *
 * @link      https://nystudio107.com/
 * @copyright Copyright (c) nystudio107
 */

namespace nystudio107\typogrify;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\web\twig\variables\CraftVariable;
use nystudio107\typogrify\models\Settings;
use nystudio107\typogrify\services\ServicesTrait;
use nystudio107\typogrify\twigextensions\TypogrifyTwigExtension;
use nystudio107\typogrify\variables\TypogrifyVariable;
use yii\base\Event;

/**
 * Class Typogrify
 *
 * @author    nystudio107
 * @package   Typogrify
 * @since     1.0.0
 *
 * @property  Settings $settings
 */
class Typogrify extends Plugin
{
    // Traits
    // =========================================================================

    use ServicesTrait;

    // Static Properties
    // =========================================================================

    /**
     * @var ?Typogrify
     */
    public static ?Typogrify $plugin = null;

    /**
     * @var ?TypogrifyVariable
     */
    public static ?TypogrifyVariable $variable = null;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    /**
     * @var bool
     */
    public bool $hasCpSettings = false;
    /**
     * @var bool
     */
    public bool $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;
        self::$variable = new TypogrifyVariable();

        Craft::$app->view->registerTwigExtension(new TypogrifyTwigExtension());
        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            static function(Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('typogrify', self::$variable);
            }
        );

        Craft::info(
            Craft::t(
                'typogrify',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }
}
