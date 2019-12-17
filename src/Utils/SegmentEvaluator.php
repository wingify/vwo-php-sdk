<?php

namespace vwo\Utils;

use vwo\Utils\Common as Common;
use vwo\Utils\OperandEvaluator as OperandEvaluator;
use vwo\Constants\Segments as SegmentConstants;

/**
 * Class SegmentEvaluator
 * @package vwo\Utils
 */
class SegmentEvaluator
{
    /**
     * @param $dsl
     * @param $customVariables
     * @return bool
     */
    public function evaluate($dsl, $customVariables)
    {
        if (count($dsl) == 0) {
            return true;
        }
        list($operator, $subDsl) = Common::getKeyValue($dsl);
        if ($operator === SegmentConstants::OPERAND_NOT) {
            return !$this->evaluate($subDsl, $customVariables);
        } elseif ($operator === SegmentConstants::OPERAND_AND) {
            $list = true;
            for ($i = 0; $i < count($subDsl); $i++) {
                $list = $list && $this->evaluate($subDsl[$i], $customVariables);
            }
            return $list;
        } elseif ($operator === SegmentConstants::OPERAND_OR) {
            $list = false;
            for ($i = 0; $i < count($subDsl); $i++) {
                $list = $list || $this->evaluate($subDsl[$i], $customVariables);
            }
            return $list;
        } elseif ($operator === SegmentConstants::CUSTOM_VARIABLE) {
            return OperandEvaluator::evaluateCustomVariableOperand($subDsl, $customVariables);
        }
    }
}
