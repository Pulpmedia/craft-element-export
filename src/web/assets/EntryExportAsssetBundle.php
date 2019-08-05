<?php
namespace pulpmedia\entryexport\web\assets;

use Craft;

use yii\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class EntryExportAssetBundle extends AssetBundle
{
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->sourcePath = "@pulpmedia/entryexport/web/assets/build";

        $this->depends = [];

        $this->js = [
            'js/ExportButton.js',
        ];

        $this->css = [
            'css/cp.css',
            'css/searchit.css',
        ];

        parent::init();
    }
}
