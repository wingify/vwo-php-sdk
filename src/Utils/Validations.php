<?php
namespace vwo\Utils;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator as Validator;
use JsonSchema\Constraints\Factory;
use JsonSchema\Constraints\Constraint;

/***
 * Class Validations
 * All the validations will be done using Class Validations
 * @package vwo\Utils
 */
class Validations {

    /**
     *
     * schema for settings array
     *
     * @var array
     */
    static  $jsonSchemaObject = [
                                "type"=> "array",
                                "properties"    => [
                                        "sdkKey"    => ["type"=> "string"],
                                        "version"   => ["type"=> "number"],
                                        "accountId" => ["type"=> "number"],
                                        "campaigns" =>[
                                            'type'      =>'array',
                                            "goals"     => [ "type"=> "array",
                                                "identifier"=> ["type"=> "string"],
                                                "type"      => ["type"=> "string"],
                                                "id"        => ["type"=> "number"],
                                            ],
                                            "variations"=> [
                                                "type"=> "array",
                                                "name"=> ["type"=> "string"],
                                                "weight"=> ["type"=> "number"],
                                                "id"=> ["type"=> "number"],],
                                            "percentTraffic"=> ["type"=> "number"],
                                            "key"   => ["type"=> "string"],
                                            "status"=> ["type"=> "string"],
                                        ],
                                ],



                            ];

    /**
     *
     * function to validation request setting schema provided by client
     *
     * @param $request
     * @return bool
     */
    public static function checkSettingSchema($request){
        $schemaStorage = new SchemaStorage();
        $schemaStorage->addSchema('file://mySchema', self::$jsonSchemaObject);
        $jsonValidator = new Validator( new Factory($schemaStorage));
        $jsonValidator->validate($request, self::$jsonSchemaObject,Constraint::CHECK_MODE_VALIDATE_SCHEMA);
        if ($jsonValidator->isValid()) {
            return true;
        }
        return false;
    }

}