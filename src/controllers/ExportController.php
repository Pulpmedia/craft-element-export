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

use pulpmedia\entryexport\EntryExport;

use Craft;
use craft\web\Controller;
use pulpmedia\entryexport\helpers\ElementHelper;

use craft\web\Response;

use craft\elements\User;
use craft\elements\Entry;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf;

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
class ExportController extends Controller
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
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $params = $request->getBodyParams();
        $type = ElementHelper::getElementHandleByType($params['elementType']);
        $source = str_replace(':', '-', $params['sourceKey']);
        $config = ElementHelper::getConfigByTypeAndSource($type, $source);
        $elements = Craft::$app->getElements();

        $settings = ['id' => 'ID', 'title' => 'Title'];
        if($config){
            $settingRows = explode("\n", $config['settings']);
            foreach($settingRows as $row){
                $line = explode(':', $row);
                $settings[trim($line[1])] = $line[0];
            }
        }

        $query = $this->_elementQuery();
        $elements = $query->all();
        $response = $this->export($elements, $settings, 'pdf');
        return $response;
    }

    // protected function exportCsv($elements, $fields = ['id' => 'ID', 'title' => 'Title']) {
    //     $f = fopen('php://output', 'w'); 

    //     fputcsv($f,$fields,";");

    //     foreach($elements as $element) {
    //         $line = [];
    //         foreach($fields as $key => $label){
    //             $line[] = $this->getFieldValue($element, $key);
    //         }
    //         fputcsv($f,$line,";");
    //         $elements[] = [$element->title, $element->id];
    //     }

    //     $response = new Response();
    //     $response->stream = $f;
    //     $response->setDownloadHeaders('file.csv', 'application/csv');

    //     return $response;
    // }
    protected function export($elements, $fields = ['id' => 'ID', 'title' => 'Title'], $type = 'xlsx') {
        $alphabet = array('a', 'b', 'c', 'd', 'e',
        'f', 'g', 'h', 'i', 'j',
        'k', 'l', 'm', 'n', 'o',
        'p', 'q', 'r', 's', 't',
        'u', 'v', 'w', 'x', 'y',
        'z'
        );

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $i = 0;
        foreach($fields as $field){
            $sheet->setCellValue($alphabet[$i]. 1, $field);
            $i++;
        }
        $i = 1;
        foreach($elements as $element) {
            $j = 0;
            $i++;
            foreach($fields as $key => $label){
                $sheet->setCellValue($alphabet[$j] . $i, $this->getFieldValue($element, $key));
                $j++;
            }

        }
        $filename = 'exports/export-'.sha1(md5(uniqid())).'-'.date('d-m-y-H-i').'.';
        $applicatiom = 'application/vnd.ms-excel';
        switch($type){
            case 'xlsx': {
                $writer = new Xlsx($spreadsheet);
                $applicatiom = 'application/vnd.ms-excel';
                $filename.='xlsx';
                break;
            }
            case 'pdf': {
                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
                $filename.='pdf';
                $applicatiom = 'application/pdf';
                break;
            }
        }
        
        // $writer->save($filename);
        $writer->save($filename);
        // $f = fopen($filename, 'w'); 
        $response = new Response();
        // $response->stream = $f;
        // $response->setDownloadHeaders($filename, $applicatiom);

        return $this->redirect('/'.$filename);
    }

    private function getFieldValue($element, $key){
        if(strpos($key, '.') > -1){
            $children = explode('.', $key);
            $childElement = $element->getFieldValue($children[0])->first();
            unset($children[0]);
            $remainingKey = implode('.',$children);
            return $this->getFieldValue($childElement, $remainingKey);
        } else {

        switch($key) {
            case 'id':
            return $element->id;
            case 'title':
            return $element->title;
            case 'firstname':
            return $element->firstName;
            case 'lastname':
            return $element->lastName;
            case 'email':
            return $element->email;
            default:
            $value =  $element->getFieldValue($key);
            break;
        }
        return $value;
    }
    }

    /**
     * Returns the posted element type class.
     *
     * @return string
     * @throws BadRequestHttpException if the requested element type is invalid
     */
    protected function elementType(): string
    {
        $class = Craft::$app->getRequest()->getRequiredParam('elementType');

        // // TODO: should probably move the code inside try{} to a helper method
        // try {
        //     if (!is_subclass_of($class, ElementInterface::class)) {
        //         throw new InvalidTypeException($class, ElementInterface::class);
        //     }
        // } catch (InvalidTypeException $e) {
        //     throw new BadRequestHttpException($e->getMessage());
        // }

        return $class;
    }

    private function _elementQuery()
    {
        /** @var string|ElementInterface $elementType */
        $elementType = $this->elementType();
        $query = $elementType::find();

        $request = Craft::$app->getRequest();

        // Does the source specify any criteria attributes?
        if (isset($this->_source['criteria'])) {
            Craft::configure($query, $this->_source['criteria']);
        }

        // Override with the request's params
        if ($criteria = $request->getBodyParam('criteria')) {
            if (isset($criteria['trashed'])) {
                $criteria['trashed'] = (bool)$criteria['trashed'];
            }
            Craft::configure($query, $criteria);
        }

        // Exclude descendants of the collapsed element IDs
        $collapsedElementIds = $request->getParam('collapsedElementIds');

        if ($collapsedElementIds) {
            $descendantQuery = (clone $query)
                ->offset(null)
                ->limit(null)
                ->orderBy(null)
                ->positionedAfter(null)
                ->positionedBefore(null)
                ->anyStatus();

            // Get the actual elements
            /** @var Element[] $collapsedElements */
            $collapsedElements = (clone $descendantQuery)
                ->id($collapsedElementIds)
                ->orderBy(['lft' => SORT_ASC])
                ->all();

            if (!empty($collapsedElements)) {
                $descendantIds = [];

                foreach ($collapsedElements as $element) {
                    // Make sure we haven't already excluded this one, because its ancestor is collapsed as well
                    if (in_array($element->id, $descendantIds, false)) {
                        continue;
                    }

                    $elementDescendantIds = (clone $descendantQuery)
                        ->descendantOf($element)
                        ->ids();

                    $descendantIds = array_merge($descendantIds, $elementDescendantIds);
                }

                if (!empty($descendantIds)) {
                    $query->andWhere(['not', ['elements.id' => $descendantIds]]);
                }
            }
        }

        return $query;
    }

    // /**
    //  * Handle a request going to our plugin's actionDoSomething URL,
    //  * e.g.: actions/entry-export/export/do-something
    //  *
    //  * @return mixed
    //  */
    // public function actionDoSomething()
    // {
    //     $result = 'Welcome to the ExportController actionDoSomething() method';

    //     return $result;
    // }
}
