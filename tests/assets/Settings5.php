<?php
/**
 * Copyright 2019 Wingify Software Pvt. Ltd.
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
Class Settings5{
 var $setting = [
  'sdkKey'=> 'loremipsum123456',
  'campaigns'=> [
    [
      'goals'=> [
        [
          'identifier'=> 'CUSTOM',
          'id'=> 217,
          'type'=> 'CUSTOM_GOAL'
        ]
],
'variations'=> [
        [
            'id'=> 1,
          'name'=> 'Control',
          'changes'=> [],
          'weight'=> 0
        ],
        [
            'id'=> 2,
          'name'=> 'Variation-1',
          'changes'=> [],
          'weight'=> 100
        ]
      ],
      'id'=> 234,
      'percentTraffic'=> 100,
      'key'=> 'DEV_TEST_5',
      'status'=> 'RUNNING',
      'type'=> 'VISUAL_AB'
    ]
  ],
  'accountId'=> 60781,
  'version'=> 1
];
}
