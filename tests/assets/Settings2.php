<?php
namespace vwo;
Class Settings2{

    var $setting=[
  'sdkKey'=> 'loremipsum123456',
  'campaigns'=> [
    [
      'goals'=> [
        [
          'identifier'=> 'abcd',
          'id'=> 1,
          'type'=> 'REVENUE_TRACKING'
        ],
[
    'identifier'=> 'CUSTOM',
          'id'=> 214,
          'type'=> 'CUSTOM_GOAL'
        ]
],
'variations'=> [
        [
            'id'=> 1,
          'name'=> 'Control',
          'changes'=> [],
          'weight'=> 50
        ],
        [
            'id'=> 2,
          'name'=> 'Variation-1',
          'changes'=> [],
          'weight'=> 50
        ]
      ],
      'id'=> 231,
      'percentTraffic'=> 100,
      'key'=> 'DEV_TEST_2',
      'status'=> 'RUNNING',
      'type'=> 'VISUAL_AB'
    ]
  ],
  'accountId'=> 60781,
  'version'=> 1
];
}