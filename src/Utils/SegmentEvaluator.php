<?php

/**
 * Copyright 2019-2020 Wingify Software Pvt. Ltd.
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

namespace vwo\Utils;

use vwo\Utils\Common as Common;
use vwo\Utils\OperandEvaluator as OperandEvaluator;
use vwo\Constants\Segments as SegmentConstants;

/**
 * Class SegmentEvaluator
 *
 * @package vwo\Utils
 */
class SegmentEvaluator
{
    /**
     * @param  $dsl
     * @param  $customVariables
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
        } elseif ($operator === SegmentConstants::USER) {
            return OperandEvaluator::evaluateUserOperand($subDsl, $customVariables);
        }
    }
}
