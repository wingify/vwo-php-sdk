<?php

/**
 * Copyright 2019-2022 Wingify Software Pvt. Ltd.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace vwo;

use PHPUnit\Framework\TestCase;
use vwo\Utils\Common as CommonUtil;

class CommonTest extends TestCase
{
    public function setUp(): void
    {
    }

    public function testTypeCast()
    {
        $stringResult = CommonUtil::typeCast(12345, 'string');
        $floatResult = CommonUtil::typeCast(12345, 'double');
        $boolResult = CommonUtil::typeCast(12345, 'boolean');
        $intResult = CommonUtil::typeCast(123.45, 'integer');
        $failResult = CommonUtil::typeCast(123.45, 'lorem');

        $this->assertEquals('12345', $stringResult);
        $this->assertEquals(12345.00, $floatResult);
        $this->assertEquals(true, $boolResult);
        $this->assertEquals(123, $intResult);
        $this->assertEquals(null, $failResult);
    }
}
