<?php
/**
 * Entry Export plugin for Craft CMS 3.x
 *
 * Export Entries to PDF, Excel or CSV
 *
 * @link      https://www.pulpmedia.at
 * @copyright Copyright (c) 2019 Alexandre Kilian
 */

namespace pulpmedia\entryexport\assetbundles\settingscpsection;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * SettingsCPSectionAsset AssetBundle
 *
 * AssetBundle represents a collection of asset files, such as CSS, JS, images.
 *
 * Each asset bundle has a unique name that globally identifies it among all asset bundles used in an application.
 * The name is the [fully qualified class name](http://php.net/manual/en/language.namespaces.rules.php)
 * of the class representing it.
 *
 * An asset bundle can depend on other asset bundles. When registering an asset bundle
 * with a view, all its dependent asset bundles will be automatically registered.
 *
 * http://www.yiiframework.com/doc-2.0/guide-structure-assets.html
 *
 * @author    Alexandre Kilian
 * @package   EntryExport
 * @since     1.0.0
 */
class SettingsCPSectionAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the bundle.
     */
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = "@pulpmedia/entryexport/assetbundles/settingscpsection/dist";

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'js/Settings.js',
        ];

        $this->css = [
            'css/Settings.css',
        ];

        parent::init();
    }
}
