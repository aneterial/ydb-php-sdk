<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: protos/ydb_table.proto

namespace Ydb\Table;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>Ydb.Table.RenameIndexItem</code>
 */
class RenameIndexItem extends \Google\Protobuf\Internal\Message
{
    /**
     * Index name to rename
     *
     * Generated from protobuf field <code>string source_name = 1;</code>
     */
    protected $source_name = '';
    /**
     * Target index name
     *
     * Generated from protobuf field <code>string destination_name = 2;</code>
     */
    protected $destination_name = '';
    /**
     * Move options
     *
     * Generated from protobuf field <code>bool replace_destination = 3;</code>
     */
    protected $replace_destination = false;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $source_name
     *           Index name to rename
     *     @type string $destination_name
     *           Target index name
     *     @type bool $replace_destination
     *           Move options
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Protos\YdbTable::initOnce();
        parent::__construct($data);
    }

    /**
     * Index name to rename
     *
     * Generated from protobuf field <code>string source_name = 1;</code>
     * @return string
     */
    public function getSourceName()
    {
        return $this->source_name;
    }

    /**
     * Index name to rename
     *
     * Generated from protobuf field <code>string source_name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setSourceName($var)
    {
        GPBUtil::checkString($var, True);
        $this->source_name = $var;

        return $this;
    }

    /**
     * Target index name
     *
     * Generated from protobuf field <code>string destination_name = 2;</code>
     * @return string
     */
    public function getDestinationName()
    {
        return $this->destination_name;
    }

    /**
     * Target index name
     *
     * Generated from protobuf field <code>string destination_name = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setDestinationName($var)
    {
        GPBUtil::checkString($var, True);
        $this->destination_name = $var;

        return $this;
    }

    /**
     * Move options
     *
     * Generated from protobuf field <code>bool replace_destination = 3;</code>
     * @return bool
     */
    public function getReplaceDestination()
    {
        return $this->replace_destination;
    }

    /**
     * Move options
     *
     * Generated from protobuf field <code>bool replace_destination = 3;</code>
     * @param bool $var
     * @return $this
     */
    public function setReplaceDestination($var)
    {
        GPBUtil::checkBool($var);
        $this->replace_destination = $var;

        return $this;
    }

}
