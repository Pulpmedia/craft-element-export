<?php
/**
 * Entry Export plugin for Craft CMS 3.x
 *
 * Export Entries to PDF, Excel or CSV
 *
 * @link      https://www.pulpmedia.at
 * @copyright Copyright (c) 2019 Alexandre Kilian
 */

namespace pulpmedia\entryexport\controllers;


use Craft;
use craft\web\Controller;
use pulpmedia\entryexport\helpers\ElementHelper;
use pulpmedia\entryexport\records\ElementSettings;

use craft\web\Response;


/**
 * Export Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your plugin’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Alexandre Kilian
 * @package   EntryExport
 * @since     1.0.0
 */
class SettingsController extends Controller
{

    private $_source;

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something'];

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our plugin's index action URL,
     * e.g.: actions/entry-export/export
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->requireAdmin();
        return $this->renderTemplate('entry-export/settings', [
            // 'settings' => Searchit::$settings,
        ]);
    }
    public function actionSource($elementTypeHandle, $sourceHandle = null)
    {
        $elementType = ElementHelper::getElementTypeByHandle($elementTypeHandle);
        $this->requireAdmin();


        $config = ElementHelper::getConfigByTypeAndSource($elementTypeHandle, $sourceHandle);
        return $this->renderTemplate('entry-export/form', [
            'config' => $config,
            'elementType' => $elementTypeHandle,
            'sourceKey' => $sourceHandle
        ]);
    }
    public function actionSave()
    {
        $this->requirePostRequest();
        $request = Craft::$app->getRequest();
        $params = $request->getBodyParams();
        
        $type = $request->getRequiredBodyParam('type');
        $source = $request->getRequiredBodyParam('source');
        $settings = $params['settings'];
        $id = $params['id'];

        $record = new ElementSettings();
        if($id){
            $isNew = !$id;
        if (!$isNew)
        {
            $record = ElementSettings::findOne($id);
            if (!$record)
            {
                throw new Exception(Craft::t('searchit', 'No element filter exists with the ID “{id}”', ['id' => $model->id]));
            }
        }
        }
        
        $config = ['type' => $type, 'source' => $source, 'settings' => $settings];
        $record->type = $type;
        $record->source = $source;
        $record->settings = $settings;
        $record->save(false);

    }

}
