<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: ydb_operation_v1.proto

namespace GPBMetadata;

class YdbOperationV1
{
    public static $is_initialized = false;

    public static function initOnce() {
        $pool = \Google\Protobuf\Internal\DescriptorPool::getGeneratedPool();

        if (static::$is_initialized == true) {
          return;
        }
        \GPBMetadata\Protos\YdbOperation::initOnce();
        $pool->internalAddGeneratedFile(
            '
�
ydb_operation_v1.protoYdb.Operation.V12�
OperationServiceY
GetOperation#.Ydb.Operations.GetOperationRequest$.Ydb.Operations.GetOperationResponseb
CancelOperation&.Ydb.Operations.CancelOperationRequest\'.Ydb.Operations.CancelOperationResponseb
ForgetOperation&.Ydb.Operations.ForgetOperationRequest\'.Ydb.Operations.ForgetOperationResponse_
ListOperations%.Ydb.Operations.ListOperationsRequest&.Ydb.Operations.ListOperationsResponseBQ
tech.ydb.operation.v1Z8github.com/ydb-platform/ydb-go-genproto/Ydb_Operation_V1bproto3'
        , true);

        static::$is_initialized = true;
    }
}
