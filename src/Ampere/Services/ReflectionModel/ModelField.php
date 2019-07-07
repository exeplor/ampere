<?php

namespace Ampere\Services\ReflectionModel;

/**
 * Class ModelField
 * @package Ampere\Services\ReflectionModel
 */
class ModelField
{
    const TYPE_STRING = 1;
    const TYPE_NUMERIC = 2;
    const TYPE_DATE = 3;
    const TYPE_BOOLEAN = 4;
    const TYPE_JSON = 5;

    /**
     * @var array
     */
    private $meta = [];

    /**
     * @var string
     */
    private $relationName;

    /**
     * @var ReflectionModel
     */
    private $relationReflectionModel;

    /**
     * @var int
     */
    private $fieldType;

    /**
     * @var string
     */
    private $fieldTypeString;

    /**
     * @var int|null
     */
    private $fieldSize = null;

    /**
     * @var int|null
     */
    private $fieldSizeSecond = null;

    /**
     * ModelField constructor.
     * @param array $meta
     */
    public function __construct(array $meta)
    {
        $this->meta = $meta;
        $this->prepare($meta);
    }

    /**
     * Prepare current field
     */
    private function prepare()
    {
        $this->parseFieldType();
    }


    /**
     * @return bool
     */
    public function isPrimary(): bool
    {
        return $this->meta['Key'] === 'PRI';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->meta['Field'];
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->fieldSize;
    }

    /**
     * @return string
     */
    public function getStringType(): string
    {
       return $this->fieldTypeString;
    }

    /**
     * @return bool
     */
    public function isString(): bool
    {
        return $this->fieldType === self::TYPE_STRING;
    }

    /**
     * @return bool
     */
    public function isSmallString(): bool
    {
        return in_array($this->fieldTypeString, ['char', 'varchar']);
    }

    /**
     * @return bool
     */
    public function isBigString(): bool
    {
        return !$this->isSmallString();
    }

    /**
     * @return bool
     */
    public function isNumeric(): bool
    {
        return $this->fieldType === self::TYPE_NUMERIC;
    }

    /**
     * @return bool
     */
    public function isInteger(): bool
    {
        return in_array($this->fieldTypeString, ['tinyint', 'smallint', 'mediumint', 'int', 'bigint']);
    }

    /**
     * @return bool
     */
    public function isFloat(): bool
    {
        return in_array($this->fieldTypeString, ['decimal', 'float', 'double']);
    }

    /**
     * @return bool
     */
    public function isDate(): bool
    {
        return $this->fieldType === self::TYPE_DATE;
    }

    /**
     * @return bool
     */
    public function isBoolean(): bool
    {
        return $this->fieldType === self::TYPE_BOOLEAN;
    }

    /**
     * @return bool
     */
    public function isJson(): bool
    {
        return $this->fieldType === self::TYPE_JSON;
    }

    /**
     * @return bool
     */
    public function isBit(): bool
    {
        return $this->isBoolean() || ($this->isInteger() && (int)$this->fieldSize === 1);
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->meta['Null'] === 'NO';
    }

    /**
     * @return bool
     */
    public function hasRelation(): bool
    {
        return !!$this->getRelationName();
    }

    /**
     * @return null|string
     */
    public function getRelationName(): ?string
    {
        return $this->relationName;
    }

    /**
     * @return null|string
     */
    public function getRelationField(): ?string
    {
        if ($this->relationReflectionModel) {
            return $this->relationReflectionModel->getPrimaryField()->getName();
        }

        return null;
    }

    /**
     * @return ReflectionModel
     */
    public function getRelationReflectionModel(): ?ReflectionModel
    {
        return $this->relationReflectionModel ?? null;
    }

    /**
     * @param string $relationName
     * @param ReflectionModel $reflectionModel
     */
    public function setRelation(string $relationName, ReflectionModel $reflectionModel)
    {
        $this->relationName = $relationName;
        $this->relationReflectionModel = $reflectionModel;
    }

    /**
     * Parse field
     */
    private function parseFieldType()
    {
        $fieldTypes = [

            self::TYPE_STRING => [
                'char', 'varchar', 'binary', 'varbinary', 'tinyblob',
                'blog', 'mediumblob', 'longblob', 'tinytext', 'text',
                'mediumtext', 'longtext', 'enum', 'set'
            ],

            self::TYPE_NUMERIC => [
                'tinyint', 'smallint', 'mediumint', 'int', 'bigint',
                'decimal', 'float', 'double', 'bit'
            ],

            self::TYPE_DATE => [
                'date', 'datetime', 'time', 'timestamp', 'year'
            ],

            self::TYPE_BOOLEAN => [
                'boolean'
            ],

            self::TYPE_JSON => [
                'json'
            ]
        ];

        $typePattern = '(?<type>[a-z]+)' . '(\((?<size>[0-9,]+)\))?' . '(\s+(?<unsigned>unsigned))?';
        if (!preg_match('/^' . $typePattern . '$/', $this->meta['Type'], $match)) {
            throw new \Exception('Unknown field type ' . $this->meta['Type']);
        }

        $this->fieldTypeString = $match['type'];

        if (isset($match['size'])) {
            $size = explode(',', $match['size']);
            $this->fieldSize = $size[0];

            if (count($size) > 1) {
                $this->fieldSizeSecond = $size[1];
            }
        }

        foreach($fieldTypes as $type => $list) {
            if (in_array($this->fieldTypeString, $list)) {
                $this->fieldType = $type;
            }
        }
    }
}