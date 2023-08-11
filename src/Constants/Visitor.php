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

class Visitor
{
    const CUSTOM_HEADER_USER_AGENT = 'X-Device-User-Agent';
    const CUSTOM_HEADER_IP = 'VWO-X-Forwarded-For';
    const USER_AGENT = 'visitor_ua';
    const IP = 'visitor_ip';
}
