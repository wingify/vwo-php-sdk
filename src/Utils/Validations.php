<?php
/**
Copyright 2019 Wingify Software Pvt. Ltd.

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
**/
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