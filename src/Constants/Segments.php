<?php

/**
 * Copyright 2019-2022 Wingify Software Pvt. Ltd.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace vwo\Constants;

/***
 * Class Constants
 * All the constants related to Segmentation
 *
 * @package vwo\Constants
 */
class Segments
{
    const OPERAND_NOT = 'not';
    const OPERAND_AND = 'and';
    const OPERAND_OR = 'or';
    const CUSTOM_VARIABLE = 'custom_variable';
    const USER = 'user';

    /**
     * REGEX CONSTANTS
     */
    const REGEX_LOWER = '/^lower\((.*)\)/';
    const REGEX_WILDCARD = '/^wildcard\((.*)\)/';
    const REGEX_START_WITH_STAR = '/^\*/';
    const REGEX_END_WITH_STAR = '/\*$/';
    const REGEX_ALL_SEARCH = '/^regex\((.*)\)/';
    const REGEX_GREATER_THAN = '/^gt\(((\d+\.?\d*)|(\.\d+))\)/';
    const REGEX_LESS_THAN = '/^lt\(((\d+\.?\d*)|(\.\d+))\)/';
    const REGEX_GREATER_THAN_EQUAL_TO = '/^gte\(((\d+\.?\d*)|(\.\d+))\)/';
    const REGEX_LESS_THAN_EQUAL_TO = '/^lte\(((\d+\.?\d*)|(\.\d+))\)/';
    /**
     * OPERAND CONSTANTS
     */
    const OPERAND_LOWER = 1;
    const OPERAND_CONTAINS = 2;
    const OPERAND_STARTS_WITH = 3;
    const OPERAND_ENDS_WITH = 4;
    const OPERAND_ALL_SEARCH = 5;
    const OPERAND_EQUALS = 6;
    const OPERAND_GREATER_THAN = 7;
    const OPERAND_LESS_THAN = 8;
    const OPERAND_GREATER_THAN_EQUAL_TO = 9;
    const OPERAND_LESS_THAN_EQUAL_TO = 10;
}
