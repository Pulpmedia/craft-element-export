<?php
namespace pulpmedia\entryexport\records;

use craft\db\ActiveRecord;
use yii\db\ActiveQueryInterface;

class ElementSettings extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'source'], 'string'],
            [['type', 'source'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%entryexport_elementsettings}}';
    }

}
