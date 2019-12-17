<?php

namespace vwo\Utils;

use vwo\Utils\Common as Common;
use vwo\Constants\Segments as SegmentConstants;

/**
 * Class SegmentEvaluator
 * @package vwo\Utils
 */
class OperandEvaluator
{

    /**
     * @param $operand
     * @param $customVariables
     * @return bool
     */
    public static function evaluateCustomVariableOperand($operand, $customVariables)
    {
        list($operandKey, $operand) = Common::getKeyValue($operand);
        if (array_key_exists($operandKey, $customVariables)) {
            $customVariable = $customVariables[$operandKey];
        } else {
            return false;
        }
        if (preg_match(SegmentConstants::REGEX_LOWER, $operand)) {
            $operandType = SegmentConstants::OPERAND_LOWER;
            preg_match(SegmentConstants::REGEX_LOWER, $operand, $matches);
            $operandValue = isset($matches[1]) ? $matches[1] : '';
        } elseif (preg_match(SegmentConstants::REGEX_WILDCARD, $operand)) {
            preg_match(SegmentConstants::REGEX_WILDCARD, $operand, $matches);
            $operandValue = isset($matches[1]) ? $matches[1] : '';
            $starting_star = preg_match(SegmentConstants::REGEX_START_WITH_STAR, $operandValue);
            $ending_star = preg_match(SegmentConstants::REGEX_END_WITH_STAR, $operandValue);

            if ($starting_star && $ending_star) {
                $operandType = SegmentConstants::OPERAND_CONTAINS;
            } elseif ($starting_star) {
                $operandType = SegmentConstants::OPERAND_STARTS_WITH;
            } elseif ($ending_star) {
                $operandType = SegmentConstants::OPERAND_ENDS_WITH;
            } else {
                $operandType = SegmentConstants::OPERAND_EQUALS;
            }
            $operandValue = preg_replace(SegmentConstants::REGEX_START_WITH_STAR, '', $operandValue);
            $operandValue = preg_replace(SegmentConstants::REGEX_END_WITH_STAR, '', $operandValue);
        } elseif (preg_match(SegmentConstants::REGEX_ALL_SEARCH, $operand)) {
            $operandType = SegmentConstants::OPERAND_ALL_SEARCH;
            preg_match(SegmentConstants::REGEX_ALL_SEARCH, $operand, $matches);
            $operandValue = isset($matches[1]) ? $matches[1] : '';
        } else {
            $operandType = SegmentConstants::OPERAND_EQUALS;
            $operandValue = $operand;
        }
        // Process both operand and tag values
        if (!is_bool($customVariable)) {
            list($operandValue, $customVariable) = self::processValues($operandValue, $customVariable);
        } else {
            $operandValue = strtolower($operandValue);
            if ($customVariable == true) {
                $customVariable = "true";
            } else {
                $customVariable = "false";
            }
        }
        if ($operandType == SegmentConstants::OPERAND_LOWER) {
            $result = strtolower($operandValue) === strtolower($customVariable);
        } elseif ($operandType == SegmentConstants::OPERAND_CONTAINS) {
            $pos = strpos($customVariable, $operandValue);
            $result = false;
            if ($pos !== false) {
                $result = true;
            }
        } elseif ($operandType == SegmentConstants::OPERAND_STARTS_WITH) {
            $pos = strrpos($customVariable, $operandValue, 0);
            $result = false;
            if ($pos !== false) {
                $result = $pos + strlen($operandValue) == strlen($customVariable);
            }
        } elseif ($operandType == SegmentConstants::OPERAND_ENDS_WITH) {
            $pos = strpos($customVariable, $operandValue);
            $result = false;
            if ($pos !== false && $pos == 0) {
                $result = true;
            }
        } elseif ($operandType == SegmentConstants::OPERAND_ALL_SEARCH) {
            try {
                $result = @preg_match('/' . $operandValue . '/', $customVariable);
            } catch (Exception $err) {
                $result = false;
            }
        } else {
            $result = $customVariable === $operandValue;
        }
        return $result;
    }

    /**
     * @param $op_value
     * @param $customVariable
     * @return array
     */
    public static function processValues($operandValue, $customVariable)
    {
        // this is atomic, either both will be processed or none
        $processedOperandValue = (float)($operandValue);
        $processedTagValue = (float)($customVariable);

        if (!($operandValue == (string)$processedOperandValue && $customVariable == (string)$processedTagValue)) {
            return [$operandValue, $customVariable];
        }
        // now we have surity that both are numbers
        // now we can convert them independently to int type if they
        // are int rather than floats
        if ($processedOperandValue === floor($processedOperandValue)) {
            $processedOperandValue = (int)$processedOperandValue;
        }
        if ($processedTagValue === floor($processedTagValue)) {
            $processedTagValue = (int)$processedTagValue;
        }
        // convert it back to string and return
        return [(string)$processedOperandValue, (string)$processedTagValue];
    }
}
