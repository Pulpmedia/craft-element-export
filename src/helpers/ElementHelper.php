<?php
namespace pulpmedia\entryexport\helpers;

use pulpmedia\entryexport\EntryExport;

use Craft;
use craft\elements\Entry;
use craft\elements\Category;
use craft\elements\Asset;
use craft\elements\User;

use craft\helpers\StringHelper;
use craft\db\Query;

class ElementHelper
{

    public static function getConfigByTypeAndSource($type, $source) {
        $config = ElementHelper::_createElementFilterQuery()
                        ->where([
                            'type' => $type,
                            'source' => $source,
                        ])
                        ->one();
        return $config;
    }
    // Public Methods
    // =========================================================================

    public static function getElementTypeByHandle(string $handle)
    {
        switch ($handle)
        {
            case 'entry':
            case 'entries':
                return Entry::class;
                break;

            case 'category':
            case 'categories':
                return Category::class;
                break;

            case 'asset':
            case 'assets':
                return Asset::class;
                break;

            case 'user':
            case 'users':
                return User::class;
                break;

            default:
                return false;
                break;
        }
    }
    public static function getElementHandleByType(string $type)
    {
        switch ($type)
        {
            case 'craft\elements\Entry':
                return 'entries';
                break;
            case 'craft\elements\Category':
                return 'categories';
                break;
            case 'craft\elements\User':
                return 'users';
                break;
            default:
                return false;
                break;
        }
    }

    private static function _createElementFilterQuery(): Query
    {
        return (new Query())
            ->select([
                'id',
                'type',
                'source',
                'settings',
            ])
            ->from(['{{%entryexport_elementsettings}}'])
            ;
    }

    // public static function sourceKeyAsHandle(string $key)
    // {
    //     if ($key == Searchit::$plugin->getElementFilters()::GLOBAL_SOURCE_KEY)
    //     {
    //         return Searchit::$plugin->getElementFilters()::GLOBAL_SOURCE_HANDLE;
    //     }

    //     return StringHelper::replace($key, ':', '-');
    // }


}
