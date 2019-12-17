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

class SegmentEvaluatorJson
{
    var $setting = '{
	"And Operator": [{
		"dsl": {
			"and": [{
				"custom_variable": {
					"eq": "eq_value"
				}
			}]
		},
		"description": "single_and_operator_matching",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"custom_variable": {
					"eq": "eq_value"
				}
			}]
		},
		"description": "single_and_operator_case_mismatch",
		"tags": {
			"eq": "Eq_Value"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"and": [{
					"and": [{
						"and": [{
							"and": [{
								"custom_variable": {
									"eq": "eq_value"
								}
							}]
						}]
					}]
				}]
			}]
		},
		"description": "multiple_and_operator",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"eq": "eq_value"
					}
				}]
			}, {
				"or": [{
					"custom_variable": {
						"reg": "regex(myregex+)"
					}
				}]
			}]
		},
		"description": "multiple_and_operator_with_all_incorrect_correct_values",
		"tags": {
			"eq": "wrong",
			"reg": "wrong"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"eq": "eq_value"
					}
				}]
			}, {
				"or": [{
					"custom_variable": {
						"reg": "regex(myregex+)"
					}
				}]
			}]
		},
		"description": "multiple_and_operator_with_single_correct_value",
		"tags": {
			"eq": "wrong",
			"reg": "myregexxxxxx"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"eq": "eq_value"
					}
				}]
			}, {
				"or": [{
					"custom_variable": {
						"reg": "regex(myregex+)"
					}
				}]
			}]
		},
		"description": "multiple_and_operator_with_all_correct_values",
		"tags": {
			"eq": "eq_value",
			"reg": "myregexxxxxx"
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"custom_variable": {
					"eq": "eq_value"
				}
			}]
		},
		"description": "single_and_operator_mismatch",
		"tags": {
			"a": "n_eq_value"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"eq": "eq_value"
					}
				}]
			}, {
				"or": [{
					"custom_variable": {
						"reg": "regex(myregex+)"
					}
				}]
			}]
		},
		"description": "multiple_and_operator_with_single_correct_value",
		"tags": {
			"eq": "eq_value",
			"reg": "wrong"
		},
		"expectation": false
	}],
	"case_insensitive_equality_operand": [{
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(f25u!v@b#k$6%9^f&o*v(m)w_-=+s,./`(*&^%$#@!)"
				}
			}]
		},
		"description": "exact_match_with_special_characters",
		"tags": {
			"eq": "f25u!v@b#k$6%9^f&o*v(m)w_-=+s,./`(*&^%$#@!"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(123.456)"
				}
			}]
		},
		"description": "float_data_type_mismatch",
		"tags": {
			"eq": 123
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(123)"
				}
			}]
		},
		"description": "numeric_data_type_mismatch",
		"tags": {
			"eq": 12
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(123.456)"
				}
			}]
		},
		"description": "float_data_type_mismatch",
		"tags": {
			"eq": 123.4567
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(something)"
				}
			}]
		},
		"description": "incorrect_key",
		"tags": {
			"neq": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(something)"
				}
			}]
		},
		"description": "incorrect_key_case",
		"tags": {
			"EQ": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(zzsomethingzz)"
				}
			}]
		},
		"description": "single_char",
		"tags": {
			"eq": "i"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(e)"
				}
			}]
		},
		"description": "char_data_type_case_mismatch",
		"tags": {
			"eq": "E"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(something)"
				}
			}]
		},
		"description": "case_mismatch",
		"tags": {
			"eq": "Something"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(nice to see you. will    YOU be   my        Friend?)"
				}
			}]
		},
		"description": "exact_match_with_spaces",
		"tags": {
			"eq": "nice to see you. will    YOU be   my        Friend?"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(123.456)"
				}
			}]
		},
		"description": "stringified_float",
		"tags": {
			"eq": "123.456000000"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(E)"
				}
			}]
		},
		"description": "char_data_type_case_mismatch",
		"tags": {
			"eq": "e"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(E)"
				}
			}]
		},
		"description": "char_data_type",
		"tags": {
			"eq": "E"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(True)"
				}
			}]
		},
		"description": "boolean_data_type_mismatch",
		"tags": {
			"eq": false
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(True)"
				}
			}]
		},
		"description": "boolean_data_type",
		"tags": {
			"eq": true
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(123)"
				}
			}]
		},
		"description": "numeric_data_type_mismatch",
		"tags": {
			"eq": 123
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(something)"
				}
			}]
		},
		"description": "mismatch",
		"tags": {
			"eq": "notsomething"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(123)"
				}
			}]
		},
		"description": "numeric_data_type",
		"tags": {
			"eq": 123
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(something)"
				}
			}]
		},
		"description": "exact_match",
		"tags": {
			"eq": "something"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(zzsomethingzz)"
				}
			}]
		},
		"description": "part_of_text",
		"tags": {
			"eq": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(HgUvshFRjsbTnvsdiUFFTGHFHGvDRT.YGHGH)"
				}
			}]
		},
		"description": "exact_match_with_upper_case",
		"tags": {
			"eq": "HgUvshFRjsbTnvsdiUFFTGHFHGvDRT.YGHGH"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(something)"
				}
			}]
		},
		"description": "null_value_provided",
		"tags": {
			"eq": null
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(false)"
				}
			}]
		},
		"description": "boolean_data_type_mismatch",
		"tags": {
			"eq": true
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(something)"
				}
			}]
		},
		"description": "no_value_provided",
		"tags": {
			"eq": ""
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(something)"
				}
			}]
		},
		"description": "missingkey_value",
		"tags": {},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(123.4560000)"
				}
			}]
		},
		"description": "stringified_float",
		"tags": {
			"eq": 123.456
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(123.0)"
				}
			}]
		},
		"description": "stringified_float",
		"tags": {
			"eq": 123
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(something)"
				}
			}]
		},
		"description": "case_mismatch",
		"tags": {
			"eq": "SOMETHINg"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(123.456)"
				}
			}]
		},
		"description": "float_data_type_extra_decimal_zeros",
		"tags": {
			"eq": 123.456
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(True)"
				}
			}]
		},
		"description": "boolean_data_type",
		"tags": {
			"eq": true
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "lower(false)"
				}
			}]
		},
		"description": "boolean_data_type",
		"tags": {
			"eq": false
		},
		"expectation": true
	}],
	"complex_and_ors": [{
		"dsl": {
			"or": [{
				"or": [{
					"and": [{
						"or": [{
							"custom_variable": {
								"start_with": "wildcard(my_start_with_val*)"
							}
						}]
					}, {
						"not": {
							"or": [{
								"custom_variable": {
									"neq": "not_eq_value"
								}
							}]
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"contain": "wildcard(*my_contain_val*)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"custom_variable": {
							"eq": "eq_value"
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"reg": "regex(myregex+)"
						}
					}]
				}]
			}]
		},
		"description": "complex_dsl_1",
		"tags": {
			"reg": 1,
			"contain": 1,
			"eq": 1,
			"start_with": "my_start_with_valzzzzzzzzzzzzzzzz",
			"neq": 1
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"and": [{
						"or": [{
							"custom_variable": {
								"start_with": "wildcard(my_start_with_val*)"
							}
						}]
					}, {
						"not": {
							"or": [{
								"custom_variable": {
									"neq": "not_eq_value"
								}
							}]
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"contain": "wildcard(*my_contain_val*)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"custom_variable": {
							"eq": "eq_value"
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"reg": "regex(myregex+)"
						}
					}]
				}]
			}]
		},
		"description": "complex_dsl_2",
		"tags": {
			"reg": 1,
			"contain": 1,
			"eq": 1,
			"start_with": 1,
			"neq": "not_eq_value"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"and": [{
						"or": [{
							"custom_variable": {
								"start_with": "wildcard(my_start_with_val*)"
							}
						}]
					}, {
						"not": {
							"or": [{
								"custom_variable": {
									"neq": "not_eq_value"
								}
							}]
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"contain": "wildcard(*my_contain_val*)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"custom_variable": {
							"eq": "eq_value"
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"reg": "regex(myregex+)"
						}
					}]
				}]
			}]
		},
		"description": "complex_dsl_3",
		"tags": {
			"reg": 1,
			"contain": "zzzzzzmy_contain_valzzzzz",
			"eq": 1,
			"start_with": "m1y_1sta1rt_with_val",
			"neq": false
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"and": [{
						"or": [{
							"custom_variable": {
								"start_with": "wildcard(my_start_with_val*)"
							}
						}]
					}, {
						"not": {
							"or": [{
								"custom_variable": {
									"neq": "not_eq_value"
								}
							}]
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"contain": "wildcard(*my_contain_val*)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"custom_variable": {
							"eq": "eq_value"
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"reg": "regex(myregex+)"
						}
					}]
				}]
			}]
		},
		"description": "complex_dsl_4",
		"tags": {
			"reg": 1,
			"contain": "my_ contain _val",
			"eq": "eq_value",
			"start_with": "m1y_1sta1rt_with_val",
			"neq": null
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"and": [{
						"or": [{
							"custom_variable": {
								"start_with": "wildcard(my_start_with_val*)"
							}
						}]
					}, {
						"not": {
							"or": [{
								"custom_variable": {
									"neq": "not_eq_value"
								}
							}]
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"contain": "wildcard(*my_contain_val*)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"custom_variable": {
							"eq": "eq_value"
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"reg": "regex(myregex+)"
						}
					}]
				}]
			}]
		},
		"description": "complex_dsl_5",
		"tags": {
			"reg": "myregexxxxxx",
			"contain": "my_ contain _val",
			"eq": "eq__value",
			"start_with": "m1y_1sta1rt_with_val",
			"neq": 123
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"and": [{
						"or": [{
							"custom_variable": {
								"start_with": "wildcard(my_start_with_val*)"
							}
						}]
					}, {
						"not": {
							"or": [{
								"custom_variable": {
									"neq": "not_eq_value"
								}
							}]
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"contain": "wildcard(*my_contain_val*)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"custom_variable": {
							"eq": "eq_value"
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"reg": "regex(myregex+)"
						}
					}]
				}]
			}]
		},
		"description": "complex_dsl_6",
		"tags": {
			"reg": "myregexxxxxx",
			"contain": "my$contain$val",
			"eq": "eq_value",
			"start_with": "m1y_1sta1rt_with_val",
			"neq": "not_matching"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"and": [{
						"or": [{
							"custom_variable": {
								"start_with": "wildcard(my_start_with_val*)"
							}
						}]
					}, {
						"not": {
							"or": [{
								"custom_variable": {
									"neq": "not_eq_value"
								}
							}]
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"contain": "wildcard(*my_contain_val*)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"custom_variable": {
							"eq": "eq_value"
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"reg": "regex(myregex+)"
						}
					}]
				}]
			}]
		},
		"description": "matching_contains_with_value",
		"tags": {
			"reg": 1,
			"contain": "zzzzzzmy_contain_valzzzzz",
			"eq": 1,
			"start_with": "m1y_1sta1rt_with_val",
			"neq": false
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"and": [{
						"or": [{
							"custom_variable": {
								"start_with": "wildcard(my_start_with_val*)"
							}
						}]
					}, {
						"not": {
							"or": [{
								"custom_variable": {
									"neq": "not_eq_value"
								}
							}]
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"contain": "wildcard(*my_contain_val*)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"custom_variable": {
							"eq": "eq_value"
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"reg": "regex(myregex+)"
						}
					}]
				}]
			}]
		},
		"description": "matching_both_start_with_and_not_equal_to_value",
		"tags": {
			"reg": 1,
			"contain": 1,
			"eq": 1,
			"start_with": "my_start_with_valzzzzzzzzzzzzzzzz",
			"neq": "not_eq_value"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"and": [{
						"or": [{
							"custom_variable": {
								"start_with": "wildcard(my_start_with_val*)"
							}
						}]
					}, {
						"not": {
							"or": [{
								"custom_variable": {
									"neq": "not_eq_value"
								}
							}]
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"contain": "wildcard(*my_contain_val*)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"custom_variable": {
							"eq": "eq_value"
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"reg": "regex(myregex+)"
						}
					}]
				}]
			}]
		},
		"description": "matching_not_equal_to_value",
		"tags": {
			"reg": 1,
			"contain": 1,
			"eq": 1,
			"start_with": 1,
			"neq": "not_eq_value"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"and": [{
						"or": [{
							"custom_variable": {
								"start_with": "wildcard(my_start_with_val*)"
							}
						}]
					}, {
						"not": {
							"or": [{
								"custom_variable": {
									"neq": "not_eq_value"
								}
							}]
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"contain": "wildcard(*my_contain_val*)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"custom_variable": {
							"eq": "eq_value"
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"reg": "regex(myregex+)"
						}
					}]
				}]
			}]
		},
		"description": "matching_start_with_value",
		"tags": {
			"reg": 1,
			"contain": 1,
			"eq": 1,
			"start_with": "my_start_with_valzzzzzzzzzzzzzzzz",
			"neq": 1
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"and": [{
						"or": [{
							"custom_variable": {
								"start_with": "wildcard(my_start_with_val*)"
							}
						}]
					}, {
						"not": {
							"or": [{
								"custom_variable": {
									"neq": "not_eq_value"
								}
							}]
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"contain": "wildcard(*my_contain_val*)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"custom_variable": {
							"eq": "eq_value"
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"reg": "regex(myregex+)"
						}
					}]
				}]
			}]
		},
		"description": "matching_regex_value",
		"tags": {
			"reg": "myregexxxxxx",
			"contain": "my_ contain _val",
			"eq": "eq__value",
			"start_with": "m1y_1sta1rt_with_val",
			"neq": 123
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"and": [{
						"or": [{
							"custom_variable": {
								"start_with": "wildcard(my_start_with_val*)"
							}
						}]
					}, {
						"not": {
							"or": [{
								"custom_variable": {
									"neq": "not_eq_value"
								}
							}]
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"contain": "wildcard(*my_contain_val*)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"custom_variable": {
							"eq": "eq_value"
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"reg": "regex(myregex+)"
						}
					}]
				}]
			}]
		},
		"description": "matching_both_equal_to_and_regex_value",
		"tags": {
			"reg": "myregexxxxxx",
			"contain": "my$contain$val",
			"eq": "eq_value",
			"start_with": "m1y_1sta1rt_with_val",
			"neq": "not_matching"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"and": [{
						"or": [{
							"custom_variable": {
								"start_with": "wildcard(my_start_with_val*)"
							}
						}]
					}, {
						"not": {
							"or": [{
								"custom_variable": {
									"neq": "not_eq_value"
								}
							}]
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"contain": "wildcard(*my_contain_val*)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"custom_variable": {
							"eq": "eq_value"
						}
					}]
				}, {
					"or": [{
						"custom_variable": {
							"reg": "regex(myregex+)"
						}
					}]
				}]
			}]
		},
		"description": "matching_equal_to_value",
		"tags": {
			"reg": 1,
			"contain": "my_ contain _val",
			"eq": "eq_value",
			"start_with": "m1y_1sta1rt_with_val",
			"neq": null
		},
		"expectation": false
	}, {
		"dsl": {},
		"description": "empty_dsl",
		"tags": {
			"reg": 1,
			"contain": "my_ contain _val",
			"eq": "eq_value",
			"start_with": "m1y_1sta1rt_with_val",
			"neq": null
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"notvwo": "notvwo"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwovwovwo": "regex(vwovwovwo)"
							}
						}]
					}]
				}, {
					"or": [{
						"custom_variable": {
							"regex_vwo": "regex(this\\\\s+is\\\\s+vwo)"
						}
					}]
				}]
			}, {
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"vwo_not_equal_to": "owv"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwo_equal_to": "vwo"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"vwo_starts_with": "wildcard(owv vwo*)"
							}
						}]
					}, {
						"or": [{
							"custom_variable": {
								"vwo_contains": "wildcard(*vwo vwo vwo vwo vwo*)"
							}
						}]
					}]
				}]
			}]
		},
		"description": "false_1",
		"tags": {
			"vwo_starts_with": "v owv vwo",
			"vwo_not_equal_to": "vwo",
			"vwo_equal_to": "vwo",
			"notvwo": "vwo",
			"regex_vwo": "this   is vwo",
			"vwovwovwo": "vwovovwo",
			"vwo_contains": "vwo"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"notvwo": "notvwo"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwovwovwo": "regex(vwovwovwo)"
							}
						}]
					}]
				}, {
					"or": [{
						"custom_variable": {
							"regex_vwo": "regex(this\\\\s+is\\\\s+vwo)"
						}
					}]
				}]
			}, {
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"vwo_not_equal_to": "owv"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwo_equal_to": "vwo"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"vwo_starts_with": "wildcard(owv vwo*)"
							}
						}]
					}, {
						"or": [{
							"custom_variable": {
								"vwo_contains": "wildcard(*vwo vwo vwo vwo vwo*)"
							}
						}]
					}]
				}]
			}]
		},
		"description": "false_2",
		"tags": {
			"vwo_starts_with": "owv vwo",
			"vwo_not_equal_to": "vwo",
			"vwo_equal_to": "vwovwo",
			"notvwo": "vwo",
			"regex_vwo": "this   is vwo",
			"vwovwovwo": "vwovw",
			"vwo_contains": "vwo"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"notvwo": "notvwo"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwovwovwo": "regex(vwovwovwo)"
							}
						}]
					}]
				}, {
					"or": [{
						"custom_variable": {
							"regex_vwo": "regex(this\\\\s+is\\\\s+vwo)"
						}
					}]
				}]
			}, {
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"vwo_not_equal_to": "owv"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwo_equal_to": "vwo"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"vwo_starts_with": "wildcard(owv vwo*)"
							}
						}]
					}, {
						"or": [{
							"custom_variable": {
								"vwo_contains": "wildcard(*vwo vwo vwo vwo vwo*)"
							}
						}]
					}]
				}]
			}]
		},
		"description": "false_3",
		"tags": {
			"vwo_starts_with": "vwo owv vwo",
			"vwo_not_equal_to": "vwo",
			"vwo_equal_to": "vwo",
			"notvwo": "vwo",
			"regex_vwo": "this   isvwo",
			"vwovwovwo": "vwovwovw",
			"vwo_contains": "vwo"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"notvwo": "notvwo"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwovwovwo": "regex(vwovwovwo)"
							}
						}]
					}]
				}, {
					"or": [{
						"custom_variable": {
							"regex_vwo": "regex(this\\\\s+is\\\\s+vwo)"
						}
					}]
				}]
			}, {
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"vwo_not_equal_to": "owv"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwo_equal_to": "vwo"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"vwo_starts_with": "wildcard(owv vwo*)"
							}
						}]
					}, {
						"or": [{
							"custom_variable": {
								"vwo_contains": "wildcard(*vwo vwo vwo vwo vwo*)"
							}
						}]
					}]
				}]
			}]
		},
		"description": "false_4",
		"tags": {
			"vwo_starts_with": "vwo owv vwo",
			"vwo_not_equal_to": "vwo",
			"vwo_equal_to": "vwo",
			"notvwo": "vwo",
			"regex_vwo": "this   is vwo",
			"vwovwovwo": "vwo",
			"vwo_contains": "vwo"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"notvwo": "notvwo"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwovwovwo": "regex(vwovwovwo)"
							}
						}]
					}]
				}, {
					"or": [{
						"custom_variable": {
							"regex_vwo": "regex(this\\s+is\\s+vwo)"
						}
					}]
				}]
			}, {
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"vwo_not_equal_to": "owv"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwo_equal_to": "vwo"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"vwo_starts_with": "wildcard(owv vwo*)"
							}
						}]
					}, {
						"or": [{
							"custom_variable": {
								"vwo_contains": "wildcard(*vwo vwo vwo vwo vwo*)"
							}
						}]
					}]
				}]
			}]
		},
		"description": "true_1",
		"tags": {
			"vwo_starts_with": "vwo owv vwo",
			"vwo_not_equal_to": "vwo",
			"vwo_equal_to": "vwo",
			"notvwo": "vo",
			"regex_vwo": "this   is vwo",
			"vwovwovwo": "vwovwovwo",
			"vwo_contains": "vw"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"notvwo": "notvwo"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwovwovwo": "regex(vwovwovwo)"
							}
						}]
					}]
				}, {
					"or": [{
						"custom_variable": {
							"regex_vwo": "regex(this\\\\s+is\\\\s+vwo)"
						}
					}]
				}]
			}, {
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"vwo_not_equal_to": "owv"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwo_equal_to": "vwo"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"vwo_starts_with": "wildcard(owv vwo*)"
							}
						}]
					}, {
						"or": [{
							"custom_variable": {
								"vwo_contains": "wildcard(*vwo vwo vwo vwo vwo*)"
							}
						}]
					}]
				}]
			}]
		},
		"description": "true_2",
		"tags": {
			"vwo_starts_with": "owv vwo",
			"vwo_not_equal_to": "vwo",
			"vwo_equal_to": "vwo",
			"notvwo": "vwo",
			"regex_vwo": "this   is vwo",
			"vwovwovwo": "vwovwovwo",
			"vwo_contains": "vwo"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"notvwo": "notvwo"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwovwovwo": "regex(vwovwovwo)"
							}
						}]
					}]
				}, {
					"or": [{
						"custom_variable": {
							"regex_vwo": "regex(this\\\\s+is\\\\s+vwo)"
						}
					}]
				}]
			}, {
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"vwo_not_equal_to": "owv"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwo_equal_to": "vwo"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"vwo_starts_with": "wildcard(owv vwo*)"
							}
						}]
					}, {
						"or": [{
							"custom_variable": {
								"vwo_contains": "wildcard(*vwo vwo vwo vwo vwo*)"
							}
						}]
					}]
				}]
			}]
		},
		"description": "true_3",
		"tags": {
			"vwo_starts_with": "owv vwo",
			"vwo_not_equal_to": "vwo",
			"vwo_equal_to": "vwo",
			"notvwo": "vwovwo",
			"regex_vwo": "this   isvwo",
			"vwovwovwo": "vwovwovwo",
			"vwo_contains": "vwo"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"notvwo": "notvwo"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwovwovwo": "regex(vwovwovwo)"
							}
						}]
					}]
				}, {
					"or": [{
						"custom_variable": {
							"regex_vwo": "regex(this\\\\s+is\\\\s+vwo)"
						}
					}]
				}]
			}, {
				"and": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"vwo_not_equal_to": "owv"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"vwo_equal_to": "vwo"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"vwo_starts_with": "wildcard(owv vwo*)"
							}
						}]
					}, {
						"or": [{
							"custom_variable": {
								"vwo_contains": "wildcard(*vwo vwo vwo vwo vwo*)"
							}
						}]
					}]
				}]
			}]
		},
		"description": "true_4",
		"tags": {
			"vwo_starts_with": "owv vwo",
			"vwo_not_equal_to": "vwo",
			"vwo_equal_to": "vwo",
			"notvwo": "vwo",
			"regex_vwo": "this   is vwo",
			"vwovwovwo": "vwo",
			"vwo_contains": "vwo"
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"contains_vwo": "wildcard(*vwo*)"
					}
				}]
			}, {
				"and": [{
					"and": [{
						"or": [{
							"and": [{
								"or": [{
									"and": [{
										"or": [{
											"custom_variable": {
												"regex_for_all_letters": "regex(^[A-z]+$)"
											}
										}]
									}, {
										"or": [{
											"custom_variable": {
												"regex_for_capital_letters": "regex(^[A-Z]+$)"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_for_small_letters": "regex(^[a-z]+$)"
										}
									}]
								}]
							}, {
								"or": [{
									"custom_variable": {
										"regex_for_no_zeros": "regex(^[1-9]+$)"
									}
								}]
							}]
						}, {
							"or": [{
								"custom_variable": {
									"regex_for_zeros": "regex(^[0]+$)"
								}
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"regex_real_number": "regex(^\\\\d+(\\\\.\\\\d+)?)"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"this_is_regex": "regex(this\\\\s+is\\\\s+text)"
							}
						}]
					}, {
						"and": [{
							"and": [{
								"or": [{
									"custom_variable": {
										"starts_with": "wildcard(starts_with_variable*)"
									}
								}]
							}, {
								"or": [{
									"custom_variable": {
										"contains": "wildcard(*contains_variable*)"
									}
								}]
							}]
						}, {
							"or": [{
								"not": {
									"or": [{
										"custom_variable": {
											"is_not_equal_to": "is_not_equal_to_variable"
										}
									}]
								}
							}, {
								"or": [{
									"custom_variable": {
										"is_equal_to": "equal_to_variable"
									}
								}]
							}]
						}]
					}]
				}]
			}]
		},
		"description": "false_5",
		"tags": {
			"contains_vwo": "legends say that vwo is the best",
			"regex_for_no_zeros": 12231023,
			"regex_for_all_letters": "dsfASF6",
			"regex_for_small_letters": "sadfksjdf",
			"regex_real_number": 12321.2242,
			"regex_for_zeros": "0001000",
			"is_equal_to": "equal_to_variable",
			"contains": "contains_variable",
			"regex_for_capital_letters": "SADFLSDLF",
			"is_not_equal_to": "is_not_equal_to_variable",
			"this_is_regex": "this    is    regex",
			"starts_with": "starts_with_variable"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"contains_vwo": "wildcard(*vwo*)"
					}
				}]
			}, {
				"and": [{
					"and": [{
						"or": [{
							"and": [{
								"or": [{
									"and": [{
										"or": [{
											"custom_variable": {
												"regex_for_all_letters": "regex(^[A-z]+$)"
											}
										}]
									}, {
										"or": [{
											"custom_variable": {
												"regex_for_capital_letters": "regex(^[A-Z]+$)"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_for_small_letters": "regex(^[a-z]+$)"
										}
									}]
								}]
							}, {
								"or": [{
									"custom_variable": {
										"regex_for_no_zeros": "regex(^[1-9]+$)"
									}
								}]
							}]
						}, {
							"or": [{
								"custom_variable": {
									"regex_for_zeros": "regex(^[0]+$)"
								}
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"regex_real_number": "regex(^\\\\d+(\\\\.\\\\d+)?)"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"this_is_regex": "regex(this\\\\s+is\\\\s+text)"
							}
						}]
					}, {
						"and": [{
							"and": [{
								"or": [{
									"custom_variable": {
										"starts_with": "wildcard(starts_with_variable*)"
									}
								}]
							}, {
								"or": [{
									"custom_variable": {
										"contains": "wildcard(*contains_variable*)"
									}
								}]
							}]
						}, {
							"or": [{
								"not": {
									"or": [{
										"custom_variable": {
											"is_not_equal_to": "is_not_equal_to_variable"
										}
									}]
								}
							}, {
								"or": [{
									"custom_variable": {
										"is_equal_to": "equal_to_variable"
									}
								}]
							}]
						}]
					}]
				}]
			}]
		},
		"description": "false_6",
		"tags": {
			"contains_vwo": "legends say that vwo is the best",
			"regex_for_no_zeros": 1223123,
			"regex_for_all_letters": "dsfASF",
			"regex_for_small_letters": "sadfksjdf",
			"regex_real_number": 12321.2242,
			"regex_for_zeros": 0,
			"is_equal_to": "is_not_equal_to_variable",
			"contains": "contains_variable",
			"regex_for_capital_letters": "SADFLSDLF",
			"is_not_equal_to": "is_not_equal_to_variable",
			"this_is_regex": "this    is    regex",
			"starts_with": "starts_with_variable"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"contains_vwo": "wildcard(*vwo*)"
					}
				}]
			}, {
				"and": [{
					"and": [{
						"or": [{
							"and": [{
								"or": [{
									"and": [{
										"or": [{
											"custom_variable": {
												"regex_for_all_letters": "regex(^[A-z]+$)"
											}
										}]
									}, {
										"or": [{
											"custom_variable": {
												"regex_for_capital_letters": "regex(^[A-Z]+$)"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_for_small_letters": "regex(^[a-z]+$)"
										}
									}]
								}]
							}, {
								"or": [{
									"custom_variable": {
										"regex_for_no_zeros": "regex(^[1-9]+$)"
									}
								}]
							}]
						}, {
							"or": [{
								"custom_variable": {
									"regex_for_zeros": "regex(^[0]+$)"
								}
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"regex_real_number": "regex(^\\\\d+(\\\\.\\\\d+)?)"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"this_is_regex": "regex(this\\\\s+is\\\\s+text)"
							}
						}]
					}, {
						"and": [{
							"and": [{
								"or": [{
									"custom_variable": {
										"starts_with": "wildcard(starts_with_variable*)"
									}
								}]
							}, {
								"or": [{
									"custom_variable": {
										"contains": "wildcard(*contains_variable*)"
									}
								}]
							}]
						}, {
							"or": [{
								"not": {
									"or": [{
										"custom_variable": {
											"is_not_equal_to": "is_not_equal_to_variable"
										}
									}]
								}
							}, {
								"or": [{
									"custom_variable": {
										"is_equal_to": "equal_to_variable"
									}
								}]
							}]
						}]
					}]
				}]
			}]
		},
		"description": "false_7",
		"tags": {
			"contains_vwo": "legends say that vwo is the best",
			"regex_for_no_zeros": 12231023,
			"regex_for_all_letters": "dsfASF6",
			"regex_for_small_letters": "sadfksjdf",
			"regex_real_number": 12321.2242,
			"regex_for_zeros": 0,
			"is_equal_to": "equal_to_variable",
			"contains": "contains_variable",
			"regex_for_capital_letters": "SADFLSDLF",
			"is_not_equal_to": "is_not_equal_to_variable",
			"this_is_regex": "this    is    regex",
			"starts_with": "startss_with_variable"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"contains_vwo": "wildcard(*vwo*)"
					}
				}]
			}, {
				"and": [{
					"and": [{
						"or": [{
							"and": [{
								"or": [{
									"and": [{
										"or": [{
											"custom_variable": {
												"regex_for_all_letters": "regex(^[A-z]+$)"
											}
										}]
									}, {
										"or": [{
											"custom_variable": {
												"regex_for_capital_letters": "regex(^[A-Z]+$)"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_for_small_letters": "regex(^[a-z]+$)"
										}
									}]
								}]
							}, {
								"or": [{
									"custom_variable": {
										"regex_for_no_zeros": "regex(^[1-9]+$)"
									}
								}]
							}]
						}, {
							"or": [{
								"custom_variable": {
									"regex_for_zeros": "regex(^[0]+$)"
								}
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"regex_real_number": "regex(^\\\\d+(\\\\.\\\\d+)?)"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"this_is_regex": "regex(this\\\\s+is\\\\s+text)"
							}
						}]
					}, {
						"and": [{
							"and": [{
								"or": [{
									"custom_variable": {
										"starts_with": "wildcard(starts_with_variable*)"
									}
								}]
							}, {
								"or": [{
									"custom_variable": {
										"contains": "wildcard(*contains_variable*)"
									}
								}]
							}]
						}, {
							"or": [{
								"not": {
									"or": [{
										"custom_variable": {
											"is_not_equal_to": "is_not_equal_to_variable"
										}
									}]
								}
							}, {
								"or": [{
									"custom_variable": {
										"is_equal_to": "equal_to_variable"
									}
								}]
							}]
						}]
					}]
				}]
			}]
		},
		"description": "false_8",
		"tags": {
			"contains_vwo": "wingify",
			"regex_for_no_zeros": 1223123,
			"regex_for_all_letters": "dsfASF",
			"regex_for_small_letters": "sadfksjdf",
			"regex_real_number": 12321.2242,
			"regex_for_zeros": 0,
			"is_equal_to": "equal_to_variable",
			"contains": "contains_variable",
			"regex_for_capital_letters": "SADFLSDLF",
			"is_not_equal_to": "is_not_equal_to_variable",
			"this_is_regex": "this    is    regex",
			"starts_with": "starts_with_variable"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"contains_vwo": "wildcard(*vwo*)"
					}
				}]
			}, {
				"and": [{
					"and": [{
						"or": [{
							"and": [{
								"or": [{
									"and": [{
										"or": [{
											"custom_variable": {
												"regex_for_all_letters": "regex(^[A-z]+$)"
											}
										}]
									}, {
										"or": [{
											"custom_variable": {
												"regex_for_capital_letters": "regex(^[A-Z]+$)"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_for_small_letters": "regex(^[a-z]+$)"
										}
									}]
								}]
							}, {
								"or": [{
									"custom_variable": {
										"regex_for_no_zeros": "regex(^[1-9]+$)"
									}
								}]
							}]
						}, {
							"or": [{
								"custom_variable": {
									"regex_for_zeros": "regex(^[0]+$)"
								}
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"regex_real_number": "regex(^\\\\d+(\\\\.\\\\d+)?)"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"this_is_regex": "regex(this\\\\s+is\\\\s+text)"
							}
						}]
					}, {
						"and": [{
							"and": [{
								"or": [{
									"custom_variable": {
										"starts_with": "wildcard(starts_with_variable*)"
									}
								}]
							}, {
								"or": [{
									"custom_variable": {
										"contains": "wildcard(*contains_variable*)"
									}
								}]
							}]
						}, {
							"or": [{
								"not": {
									"or": [{
										"custom_variable": {
											"is_not_equal_to": "is_not_equal_to_variable"
										}
									}]
								}
							}, {
								"or": [{
									"custom_variable": {
										"is_equal_to": "equal_to_variable"
									}
								}]
							}]
						}]
					}]
				}]
			}]
		},
		"description": "false_9",
		"tags": {
			"contains_vwo": "legends say that vwo is the best",
			"regex_for_no_zeros": 1223123,
			"regex_for_all_letters": "dsfASF",
			"regex_for_small_letters": "sadfksjdf",
			"regex_real_number": "not a number",
			"regex_for_zeros": 0,
			"is_equal_to": "equal_to_variable",
			"contains": "contains_variable",
			"regex_for_capital_letters": "SADFLSDLF",
			"is_not_equal_to": "is_not_equal_to_variable",
			"this_is_regex": "this    is    regex",
			"starts_with": "starts_with_variable"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"contains_vwo": "wildcard(*vwo*)"
					}
				}]
			}, {
				"and": [{
					"and": [{
						"or": [{
							"and": [{
								"or": [{
									"and": [{
										"or": [{
											"custom_variable": {
												"regex_for_all_letters": "regex(^[A-z]+$)"
											}
										}]
									}, {
										"or": [{
											"custom_variable": {
												"regex_for_capital_letters": "regex(^[A-Z]+$)"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_for_small_letters": "regex(^[a-z]+$)"
										}
									}]
								}]
							}, {
								"or": [{
									"custom_variable": {
										"regex_for_no_zeros": "regex(^[1-9]+$)"
									}
								}]
							}]
						}, {
							"or": [{
								"custom_variable": {
									"regex_for_zeros": "regex(^[0]+$)"
								}
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"regex_real_number": "regex(^\\\\d+(\\\\.\\\\d+)?)"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"this_is_regex": "regex(this\\\\s+is\\\\s+text)"
							}
						}]
					}, {
						"and": [{
							"and": [{
								"or": [{
									"custom_variable": {
										"starts_with": "wildcard(starts_with_variable*)"
									}
								}]
							}, {
								"or": [{
									"custom_variable": {
										"contains": "wildcard(*contains_variable*)"
									}
								}]
							}]
						}, {
							"or": [{
								"not": {
									"or": [{
										"custom_variable": {
											"is_not_equal_to": "is_not_equal_to_variable"
										}
									}]
								}
							}, {
								"or": [{
									"custom_variable": {
										"is_equal_to": "equal_to_variable"
									}
								}]
							}]
						}]
					}]
				}]
			}]
		},
		"description": "false_10",
		"tags": {
			"contains_vwo": "legends say that vwo is the best",
			"regex_for_no_zeros": 1223123,
			"regex_for_all_letters": "dsfASF",
			"regex_for_small_letters": "sadfksjdf",
			"regex_real_number": 12321.2242,
			"regex_for_zeros": 0,
			"is_equal_to": "equal_to_variable",
			"contains": "contains_variable",
			"regex_for_capital_letters": "SADFLSDLF",
			"is_not_equal_to": "is_not_equal_to_variable",
			"this_is_regex": "thisis    regex",
			"starts_with": "_variable"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"contains_vwo": "wildcard(*vwo*)"
					}
				}]
			}, {
				"and": [{
					"and": [{
						"or": [{
							"and": [{
								"or": [{
									"and": [{
										"or": [{
											"custom_variable": {
												"regex_for_all_letters": "regex(^[A-z]+$)"
											}
										}]
									}, {
										"or": [{
											"custom_variable": {
												"regex_for_capital_letters": "regex(^[A-Z]+$)"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_for_small_letters": "regex(^[a-z]+$)"
										}
									}]
								}]
							}, {
								"or": [{
									"custom_variable": {
										"regex_for_no_zeros": "regex(^[1-9]+$)"
									}
								}]
							}]
						}, {
							"or": [{
								"custom_variable": {
									"regex_for_zeros": "regex(^[0]+$)"
								}
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"regex_real_number": "regex(^\\d+(\\.\\d+)?)"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"this_is_regex": "regex(this\\s+is\\s+text)"
							}
						}]
					}, {
						"and": [{
							"and": [{
								"or": [{
									"custom_variable": {
										"starts_with": "wildcard(starts_with_variable*)"
									}
								}]
							}, {
								"or": [{
									"custom_variable": {
										"contains": "wildcard(*contains_variable*)"
									}
								}]
							}]
						}, {
							"or": [{
								"not": {
									"or": [{
										"custom_variable": {
											"is_not_equal_to": "is_not_equal_to_variable"
										}
									}]
								}
							}, {
								"or": [{
									"custom_variable": {
										"is_equal_to": "equal_to_variable"
									}
								}]
							}]
						}]
					}]
				}]
			}]
		},
		"description": "true_5",
		"tags": {
			"contains_vwo": "legends say that vwo is the best",
			"regex_for_no_zeros": 1223123,
			"regex_for_all_letters": "dsfASF",
			"regex_for_small_letters": "sadfksjdf",
			"regex_real_number": 12321.2242,
			"regex_for_zeros": 0,
			"is_equal_to": "equal_to_variable",
			"contains": "contains_variable",
			"regex_for_capital_letters": "SADFLSDLF",
			"is_not_equal_to": "is_not_equal_to_variable",
			"this_is_regex": "this    is    regex",
			"starts_with": "starts_with_variable"
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"contains_vwo": "wildcard(*vwo*)"
					}
				}]
			}, {
				"and": [{
					"and": [{
						"or": [{
							"and": [{
								"or": [{
									"and": [{
										"or": [{
											"custom_variable": {
												"regex_for_all_letters": "regex(^[A-z]+$)"
											}
										}]
									}, {
										"or": [{
											"custom_variable": {
												"regex_for_capital_letters": "regex(^[A-Z]+$)"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_for_small_letters": "regex(^[a-z]+$)"
										}
									}]
								}]
							}, {
								"or": [{
									"custom_variable": {
										"regex_for_no_zeros": "regex(^[1-9]+$)"
									}
								}]
							}]
						}, {
							"or": [{
								"custom_variable": {
									"regex_for_zeros": "regex(^[0]+$)"
								}
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"regex_real_number": "regex(^\\d+(\\.\\d+)?)"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"this_is_regex": "regex(this\\s+is\\s+text)"
							}
						}]
					}, {
						"and": [{
							"and": [{
								"or": [{
									"custom_variable": {
										"starts_with": "wildcard(starts_with_variable*)"
									}
								}]
							}, {
								"or": [{
									"custom_variable": {
										"contains": "wildcard(*contains_variable*)"
									}
								}]
							}]
						}, {
							"or": [{
								"not": {
									"or": [{
										"custom_variable": {
											"is_not_equal_to": "is_not_equal_to_variable"
										}
									}]
								}
							}, {
								"or": [{
									"custom_variable": {
										"is_equal_to": "equal_to_variable"
									}
								}]
							}]
						}]
					}]
				}]
			}]
		},
		"description": "true_6",
		"tags": {
			"contains_vwo": "legends say that vwo is the best",
			"regex_for_no_zeros": 1223123,
			"regex_for_all_letters": "dsfASF",
			"regex_for_small_letters": "sadfksjdf",
			"regex_real_number": 1234,
			"regex_for_zeros": 0,
			"is_equal_to": "equal_to_variable",
			"contains": "contains_variable",
			"regex_for_capital_letters": "SADFLSDLF",
			"is_not_equal_to": "is_not_equal_to_variable",
			"this_is_regex": "this    is    regex",
			"starts_with": "starts_with_variable"
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"contains_vwo": "wildcard(*vwo*)"
					}
				}]
			}, {
				"and": [{
					"and": [{
						"or": [{
							"and": [{
								"or": [{
									"and": [{
										"or": [{
											"custom_variable": {
												"regex_for_all_letters": "regex(^[A-z]+$)"
											}
										}]
									}, {
										"or": [{
											"custom_variable": {
												"regex_for_capital_letters": "regex(^[A-Z]+$)"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_for_small_letters": "regex(^[a-z]+$)"
										}
									}]
								}]
							}, {
								"or": [{
									"custom_variable": {
										"regex_for_no_zeros": "regex(^[1-9]+$)"
									}
								}]
							}]
						}, {
							"or": [{
								"custom_variable": {
									"regex_for_zeros": "regex(^[0]+$)"
								}
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"regex_real_number": "regex(^\\d+(\\.\\d+)?)"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"this_is_regex": "regex(this\\s+is\\s+text)"
							}
						}]
					}, {
						"and": [{
							"and": [{
								"or": [{
									"custom_variable": {
										"starts_with": "wildcard(starts_with_variable*)"
									}
								}]
							}, {
								"or": [{
									"custom_variable": {
										"contains": "wildcard(*contains_variable*)"
									}
								}]
							}]
						}, {
							"or": [{
								"not": {
									"or": [{
										"custom_variable": {
											"is_not_equal_to": "is_not_equal_to_variable"
										}
									}]
								}
							}, {
								"or": [{
									"custom_variable": {
										"is_equal_to": "equal_to_variable"
									}
								}]
							}]
						}]
					}]
				}]
			}]
		},
		"description": "true_7",
		"tags": {
			"contains_vwo": "legends say that vwo is the best",
			"regex_for_no_zeros": 12231023,
			"regex_for_all_letters": "dsfASF6",
			"regex_for_small_letters": "sadfAksjdf",
			"regex_real_number": 12321.2242,
			"regex_for_zeros": 0,
			"is_equal_to": "equal_to_variable",
			"contains": "contains_variable",
			"regex_for_capital_letters": "SADFLSDLF",
			"is_not_equal_to": "is_not_equal_to_variable",
			"this_is_regex": "this    is    regex",
			"starts_with": "starts_with_variable"
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"contains_vwo": "wildcard(*vwo*)"
					}
				}]
			}, {
				"and": [{
					"and": [{
						"or": [{
							"and": [{
								"or": [{
									"and": [{
										"or": [{
											"custom_variable": {
												"regex_for_all_letters": "regex(^[A-z]+$)"
											}
										}]
									}, {
										"or": [{
											"custom_variable": {
												"regex_for_capital_letters": "regex(^[A-Z]+$)"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_for_small_letters": "regex(^[a-z]+$)"
										}
									}]
								}]
							}, {
								"or": [{
									"custom_variable": {
										"regex_for_no_zeros": "regex(^[1-9]+$)"
									}
								}]
							}]
						}, {
							"or": [{
								"custom_variable": {
									"regex_for_zeros": "regex(^[0]+$)"
								}
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"regex_real_number": "regex(^\\d+(\\.\\d+)?)"
							}
						}]
					}]
				}, {
					"or": [{
						"or": [{
							"custom_variable": {
								"this_is_regex": "regex(this\\s+is\\s+text)"
							}
						}]
					}, {
						"and": [{
							"and": [{
								"or": [{
									"custom_variable": {
										"starts_with": "wildcard(starts_with_variable*)"
									}
								}]
							}, {
								"or": [{
									"custom_variable": {
										"contains": "wildcard(*contains_variable*)"
									}
								}]
							}]
						}, {
							"or": [{
								"not": {
									"or": [{
										"custom_variable": {
											"is_not_equal_to": "is_not_equal_to_variable"
										}
									}]
								}
							}, {
								"or": [{
									"custom_variable": {
										"is_equal_to": "equal_to_variable"
									}
								}]
							}]
						}]
					}]
				}]
			}]
		},
		"description": "true_8",
		"tags": {
			"contains_vwo": "legends say that vwo is the best",
			"regex_for_no_zeros": 12231023,
			"regex_for_all_letters": "dsfASF6",
			"regex_for_small_letters": "sadfksjdf",
			"regex_real_number": 12321.2242,
			"regex_for_zeros": 0,
			"is_equal_to": "equal_to_variable",
			"contains": "contains_variable",
			"regex_for_capital_letters": "SADFLSDLF",
			"is_not_equal_to": "is_not_equal_to_variable",
			"this_is_regex": "this    is    regex",
			"starts_with": "starts_with_variable"
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"thanos": "snap"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"batman": "wildcard(*i am batman*)"
							}
						}]
					}]
				}, {
					"or": [{
						"custom_variable": {
							"joker": "regex((joker)+)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"or": [{
							"custom_variable": {
								"lol": "lolololololol"
							}
						}]
					}, {
						"or": [{
							"custom_variable": {
								"blablabla": "wildcard(*bla*)"
							}
						}]
					}]
				}, {
					"and": [{
						"and": [{
							"not": {
								"or": [{
									"custom_variable": {
										"notvwo": "notvwo"
									}
								}]
							}
						}, {
							"or": [{
								"and": [{
									"or": [{
										"custom_variable": {
											"vwovwovwo": "regex(vwovwovwo)"
										}
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_vwo": "regex(this\\\\s+is\\\\s+vwo)"
										}
									}]
								}]
							}, {
								"or": [{
									"and": [{
										"not": {
											"or": [{
												"custom_variable": {
													"vwo_not_equal_to": "owv"
												}
											}]
										}
									}, {
										"or": [{
											"custom_variable": {
												"vwo_equal_to": "vwo"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"vwo_starts_with": "wildcard(owv vwo*)"
										}
									}]
								}]
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"vwo_contains": "wildcard(*vwo vwo vwo vwo vwo*)"
							}
						}]
					}]
				}]
			}]
		},
		"description": "false_11",
		"tags": {
			"vwo_not_equal_to": "vwo",
			"vwo_equal_to": "vwo",
			"vwovwovwo": "vwovwovwo",
			"vwo_starts_with": "vwo",
			"regex_vwo": "this   is vwo",
			"thanos": "snap",
			"lol": "lollolololol",
			"notvwo": "vwo",
			"joker": "joker joker joker",
			"batman": "hello i am batman world",
			"blablabla": "lba",
			"vwo_contains": "vwo vwo vwo vwo vwo"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"thanos": "snap"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"batman": "wildcard(*i am batman*)"
							}
						}]
					}]
				}, {
					"or": [{
						"custom_variable": {
							"joker": "regex((joker)+)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"or": [{
							"custom_variable": {
								"lol": "lolololololol"
							}
						}]
					}, {
						"or": [{
							"custom_variable": {
								"blablabla": "wildcard(*bla*)"
							}
						}]
					}]
				}, {
					"and": [{
						"and": [{
							"not": {
								"or": [{
									"custom_variable": {
										"notvwo": "notvwo"
									}
								}]
							}
						}, {
							"or": [{
								"and": [{
									"or": [{
										"custom_variable": {
											"vwovwovwo": "regex(vwovwovwo)"
										}
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_vwo": "regex(this\\\\s+is\\\\s+vwo)"
										}
									}]
								}]
							}, {
								"or": [{
									"and": [{
										"not": {
											"or": [{
												"custom_variable": {
													"vwo_not_equal_to": "owv"
												}
											}]
										}
									}, {
										"or": [{
											"custom_variable": {
												"vwo_equal_to": "vwo"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"vwo_starts_with": "wildcard(owv vwo*)"
										}
									}]
								}]
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"vwo_contains": "wildcard(*vwo vwo vwo vwo vwo*)"
							}
						}]
					}]
				}]
			}]
		},
		"description": "false_12",
		"tags": {
			"vwo_not_equal_to": "vwo",
			"vwo_equal_to": "vwo",
			"vwovwovwo": "vwovwovwo",
			"vwo_starts_with": "owv vwo",
			"regex_vwo": "this   is vwo",
			"thanos": "half universe",
			"lol": "lolololololol",
			"notvwo": "vwo",
			"joker": "joker joker joker",
			"batman": "hello i am batman world",
			"blablabla": "bla bla bla",
			"vwo_contains": "vwo vwo"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"thanos": "snap"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"batman": "wildcard(*i am batman*)"
							}
						}]
					}]
				}, {
					"or": [{
						"custom_variable": {
							"joker": "regex((joker)+)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"or": [{
							"custom_variable": {
								"lol": "lolololololol"
							}
						}]
					}, {
						"or": [{
							"custom_variable": {
								"blablabla": "wildcard(*bla*)"
							}
						}]
					}]
				}, {
					"and": [{
						"and": [{
							"not": {
								"or": [{
									"custom_variable": {
										"notvwo": "notvwo"
									}
								}]
							}
						}, {
							"or": [{
								"and": [{
									"or": [{
										"custom_variable": {
											"vwovwovwo": "regex(vwovwovwo)"
										}
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_vwo": "regex(this\\\\s+is\\\\s+vwo)"
										}
									}]
								}]
							}, {
								"or": [{
									"and": [{
										"not": {
											"or": [{
												"custom_variable": {
													"vwo_not_equal_to": "owv"
												}
											}]
										}
									}, {
										"or": [{
											"custom_variable": {
												"vwo_equal_to": "vwo"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"vwo_starts_with": "wildcard(owv vwo*)"
										}
									}]
								}]
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"vwo_contains": "wildcard(*vwo vwo vwo vwo vwo*)"
							}
						}]
					}]
				}]
			}]
		},
		"description": "false_13",
		"tags": {
			"vwo_not_equal_to": "vwo",
			"vwo_equal_to": "vwo",
			"vwovwovwo": "vwovwovwo",
			"vwo_starts_with": "vwo",
			"regex_vwo": "this   is vwo",
			"thanos": "snap",
			"lol": "lollolololol",
			"notvwo": "vwo",
			"joker": "joker joker joker",
			"batman": "hello i am batman world",
			"blablabla": "bla bla bla",
			"vwo_contains": "vwo vwo vwo vwo"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"thanos": "snap"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"batman": "wildcard(*i am batman*)"
							}
						}]
					}]
				}, {
					"or": [{
						"custom_variable": {
							"joker": "regex((joker)+)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"or": [{
							"custom_variable": {
								"lol": "lolololololol"
							}
						}]
					}, {
						"or": [{
							"custom_variable": {
								"blablabla": "wildcard(*bla*)"
							}
						}]
					}]
				}, {
					"and": [{
						"and": [{
							"not": {
								"or": [{
									"custom_variable": {
										"notvwo": "notvwo"
									}
								}]
							}
						}, {
							"or": [{
								"and": [{
									"or": [{
										"custom_variable": {
											"vwovwovwo": "regex(vwovwovwo)"
										}
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_vwo": "regex(this\\\\s+is\\\\s+vwo)"
										}
									}]
								}]
							}, {
								"or": [{
									"and": [{
										"not": {
											"or": [{
												"custom_variable": {
													"vwo_not_equal_to": "owv"
												}
											}]
										}
									}, {
										"or": [{
											"custom_variable": {
												"vwo_equal_to": "vwo"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"vwo_starts_with": "wildcard(owv vwo*)"
										}
									}]
								}]
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"vwo_contains": "wildcard(*vwo vwo vwo vwo vwo*)"
							}
						}]
					}]
				}]
			}]
		},
		"description": "true_9",
		"tags": {
			"vwo_not_equal_to": "vwo",
			"vwo_equal_to": "vwo",
			"vwovwovwo": "vwovwovwo",
			"vwo_starts_with": "owv vwo",
			"regex_vwo": "this   is vwo",
			"thanos": "half universe",
			"lol": "lollolololol",
			"notvwo": "vwo",
			"joker": "joker joker joker",
			"batman": "hello i am batman world",
			"blablabla": "bla bla bla",
			"vwo_contains": "vwo vwo vwo vwo vwo"
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"thanos": "snap"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"batman": "wildcard(*i am batman*)"
							}
						}]
					}]
				}, {
					"or": [{
						"custom_variable": {
							"joker": "regex((joker)+)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"or": [{
							"custom_variable": {
								"lol": "lolololololol"
							}
						}]
					}, {
						"or": [{
							"custom_variable": {
								"blablabla": "wildcard(*bla*)"
							}
						}]
					}]
				}, {
					"and": [{
						"and": [{
							"not": {
								"or": [{
									"custom_variable": {
										"notvwo": "notvwo"
									}
								}]
							}
						}, {
							"or": [{
								"and": [{
									"or": [{
										"custom_variable": {
											"vwovwovwo": "regex(vwovwovwo)"
										}
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_vwo": "regex(this\\\\s+is\\\\s+vwo)"
										}
									}]
								}]
							}, {
								"or": [{
									"and": [{
										"not": {
											"or": [{
												"custom_variable": {
													"vwo_not_equal_to": "owv"
												}
											}]
										}
									}, {
										"or": [{
											"custom_variable": {
												"vwo_equal_to": "vwo"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"vwo_starts_with": "wildcard(owv vwo*)"
										}
									}]
								}]
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"vwo_contains": "wildcard(*vwo vwo vwo vwo vwo*)"
							}
						}]
					}]
				}]
			}]
		},
		"description": "true_10",
		"tags": {
			"vwo_not_equal_to": "vwo",
			"vwo_equal_to": "vwo",
			"vwovwovwo": "vwovwovwo",
			"vwo_starts_with": "owv vwo",
			"regex_vwo": "this   is vwo",
			"thanos": "half universe",
			"lol": "lolololololol",
			"notvwo": "vwo",
			"joker": "joker joker joker",
			"batman": "hello i am batman world",
			"blablabla": "bla bla bla",
			"vwo_contains": "vwo vwo vwo vwo vwo"
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"and": [{
						"not": {
							"or": [{
								"custom_variable": {
									"thanos": "snap"
								}
							}]
						}
					}, {
						"or": [{
							"custom_variable": {
								"batman": "wildcard(*i am batman*)"
							}
						}]
					}]
				}, {
					"or": [{
						"custom_variable": {
							"joker": "regex((joker)+)"
						}
					}]
				}]
			}, {
				"and": [{
					"or": [{
						"or": [{
							"custom_variable": {
								"lol": "lolololololol"
							}
						}]
					}, {
						"or": [{
							"custom_variable": {
								"blablabla": "wildcard(*bla*)"
							}
						}]
					}]
				}, {
					"and": [{
						"and": [{
							"not": {
								"or": [{
									"custom_variable": {
										"notvwo": "notvwo"
									}
								}]
							}
						}, {
							"or": [{
								"and": [{
									"or": [{
										"custom_variable": {
											"vwovwovwo": "regex(vwovwovwo)"
										}
									}]
								}, {
									"or": [{
										"custom_variable": {
											"regex_vwo": "regex(this\\\\s+is\\\\s+vwo)"
										}
									}]
								}]
							}, {
								"or": [{
									"and": [{
										"not": {
											"or": [{
												"custom_variable": {
													"vwo_not_equal_to": "owv"
												}
											}]
										}
									}, {
										"or": [{
											"custom_variable": {
												"vwo_equal_to": "vwo"
											}
										}]
									}]
								}, {
									"or": [{
										"custom_variable": {
											"vwo_starts_with": "wildcard(owv vwo*)"
										}
									}]
								}]
							}]
						}]
					}, {
						"or": [{
							"custom_variable": {
								"vwo_contains": "wildcard(*vwo vwo vwo vwo vwo*)"
							}
						}]
					}]
				}]
			}]
		},
		"description": "true_11",
		"tags": {
			"vwo_not_equal_to": "vwo",
			"vwo_equal_to": "vwo",
			"vwovwovwo": "vwovwovwo",
			"vwo_starts_with": "owv vwo",
			"regex_vwo": "this   is vwo",
			"thanos": "snap",
			"lol": "lolololololol",
			"notvwo": "vwo",
			"joker": "joker joker joker",
			"batman": "hello i am batman world",
			"blablabla": "bla bla bla",
			"vwo_contains": "vwo vwo vwo vwo vwo"
		},
		"expectation": true
	}],
	"contains_operand": [{
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something*)"
				}
			}]
		},
		"description": "incorrect_key",
		"tags": {
			"neq": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something*)"
				}
			}]
		},
		"description": "incorrect_key_case",
		"tags": {
			"EQ": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*zzsomethingzz*)"
				}
			}]
		},
		"description": "single_char",
		"tags": {
			"eq": "i"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something*)"
				}
			}]
		},
		"description": "case_mismatch",
		"tags": {
			"eq": "Something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*E*)"
				}
			}]
		},
		"description": "char_data_type",
		"tags": {
			"eq": "E"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(something*)"
				}
			}]
		},
		"description": "prefix_match",
		"tags": {
			"eq": "somethingdfgdwerewew"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*true*)"
				}
			}]
		},
		"description": "boolean_data_type",
		"tags": {
			"eq": true
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*zzsomethingzz*)"
				}
			}]
		},
		"description": "part_of_text",
		"tags": {
			"eq": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something*)"
				}
			}]
		},
		"description": "null_value_provided",
		"tags": {
			"eq": null
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*HgUvshFRjsbTnvsdiUFFTGHFHGvDRT.YGHGH*)"
				}
			}]
		},
		"description": "upper_case",
		"tags": {
			"eq": "A-N-Y-T-H-I-N-G---HgUvshFRjsbTnvsdiUFFTGHFHGvDRT.YGHGH----A-N-Y-T-H-I-N-G"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something*)"
				}
			}]
		},
		"description": "no_value_provided",
		"tags": {
			"eq": ""
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something*)"
				}
			}]
		},
		"description": "suffix_match",
		"tags": {
			"eq": "asdn3kn42knsdsomething"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*false*)"
				}
			}]
		},
		"description": "boolean_data_type",
		"tags": {
			"eq": false
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*123.456*)"
				}
			}]
		},
		"description": "float_data_type",
		"tags": {
			"eq": 765123.4567364
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*123*)"
				}
			}]
		},
		"description": "numeric_data_type_mismatch",
		"tags": {
			"eq": 12
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something*)"
				}
			}]
		},
		"description": "contains_match",
		"tags": {
			"eq": "asdn3kn42knsdsomethingjsbdj"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*e*)"
				}
			}]
		},
		"description": "char_data_type_case_mismatch",
		"tags": {
			"eq": "E"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*f25u!v@b#k$6%9^f&o*v(m)w_-=+s,./`(*&^%$#@!*)"
				}
			}]
		},
		"description": "special_characters",
		"tags": {
			"eq": "A-N-Y-T-H-I-N-G---f25u!v@b#k$6%9^f&o*v(m)w_-=+s,./`(*&^%$#@!----A-N-Y-T-H-I-N-G"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*123.456*)"
				}
			}]
		},
		"description": "stringified_float",
		"tags": {
			"eq": "87654123.4567902"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*E*)"
				}
			}]
		},
		"description": "char_data_type_case_mismatch",
		"tags": {
			"eq": "e"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*true*)"
				}
			}]
		},
		"description": "boolean_data_type_mismatch",
		"tags": {
			"eq": false
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*123*)"
				}
			}]
		},
		"description": "numeric_data_type_mismatch",
		"tags": {
			"eq": 765123.7364
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*nice to see you. will    you be   my        friend?*)"
				}
			}]
		},
		"description": "spaces",
		"tags": {
			"eq": "Hello there!! nice to see you. will    you be   my        friend? Yes, Great!!"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something*)"
				}
			}]
		},
		"description": "mismatch",
		"tags": {
			"eq": "qwertyu"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something*)"
				}
			}]
		},
		"description": "exact_match",
		"tags": {
			"eq": "something"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*123*)"
				}
			}]
		},
		"description": "numeric_data_type",
		"tags": {
			"eq": 365412363
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*false*)"
				}
			}]
		},
		"description": "boolean_data_type_mismatch",
		"tags": {
			"eq": true
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something*)"
				}
			}]
		},
		"description": "missingkey_value",
		"tags": {},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something*)"
				}
			}]
		},
		"description": "case_mismatch",
		"tags": {
			"eq": "SOMETHING"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"a": "wildcard(*some*thing*)"
				}
			}]
		},
		"description": "contains_operand_falsy_test_with_special_character",
		"tags": {
			"a": "hellosomethingworld"
		},
		"expectation": false
	}],
	"ends_with_operand_tests": [{
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*123)"
				}
			}]
		},
		"description": "numeric_data_type_mismatch",
		"tags": {
			"eq": 12
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*123.456)"
				}
			}]
		},
		"description": "float_data_type_mismatch",
		"tags": {
			"eq": 123
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*123.456)"
				}
			}]
		},
		"description": "float_data_type_mismatch",
		"tags": {
			"eq": 123.4567
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something)"
				}
			}]
		},
		"description": "incorrect_key",
		"tags": {
			"neq": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something)"
				}
			}]
		},
		"description": "contains_match",
		"tags": {
			"eq": "asdn3kn42knsdsomethingmm"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something)"
				}
			}]
		},
		"description": "incorrect_key_case",
		"tags": {
			"eq": "asdn3kn42knsdsomethingmm"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something)"
				}
			}]
		},
		"description": "incorrect_key_case",
		"tags": {
			"EQ": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*zzsomethingzz)"
				}
			}]
		},
		"description": "single_char",
		"tags": {
			"eq": "i"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*e)"
				}
			}]
		},
		"description": "char_data_type_case_mismatch",
		"tags": {
			"eq": "E"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something)"
				}
			}]
		},
		"description": "case_mismatch",
		"tags": {
			"eq": "Something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*f25u!v@b#k$6%9^f&o*v(m)w_-=+s,./`(*&^%$#@!)"
				}
			}]
		},
		"description": "special_characters",
		"tags": {
			"eq": "A-N-Y-T-H-I-N-G---f25u!v@b#k$6%9^f&o*v(m)w_-=+s,./`(*&^%$#@!"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*E)"
				}
			}]
		},
		"description": "char_data_type",
		"tags": {
			"eq": "E"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*123.456)"
				}
			}]
		},
		"description": "stringified_float",
		"tags": {
			"eq": "87654123.456000000"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*E)"
				}
			}]
		},
		"description": "char_data_type_case_mismatch",
		"tags": {
			"eq": "e"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something)"
				}
			}]
		},
		"description": "prefix_match",
		"tags": {
			"eq": "somethingdfgdwerewew"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*true)"
				}
			}]
		},
		"description": "boolean_data_type_mismatch",
		"tags": {
			"eq": false
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*true)"
				}
			}]
		},
		"description": "boolean_data_type",
		"tags": {
			"eq": true
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*123)"
				}
			}]
		},
		"description": "numeric_data_type_mismatch",
		"tags": {
			"eq": 765123
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*nice to see you. will    you be   my        friend?)"
				}
			}]
		},
		"description": "spaces",
		"tags": {
			"eq": "Hello there!! nice to see you. will    you be   my        friend?"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something)"
				}
			}]
		},
		"description": "mismatch",
		"tags": {
			"eq": "qwertyu"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*123)"
				}
			}]
		},
		"description": "numeric_data_type",
		"tags": {
			"eq": 3654123
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something)"
				}
			}]
		},
		"description": "exact_match",
		"tags": {
			"eq": "something"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*zzsomethingzz)"
				}
			}]
		},
		"description": "part_of_text",
		"tags": {
			"eq": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*false)"
				}
			}]
		},
		"description": "boolean_data_type_mismatch",
		"tags": {
			"eq": true
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something)"
				}
			}]
		},
		"description": "null_value_provided",
		"tags": {
			"eq": null
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*HgUvshFRjsbTnvsdiUFFTGHFHGvDRT.YGHGH)"
				}
			}]
		},
		"description": "upper_case",
		"tags": {
			"eq": "A-N-Y-T-H-I-N-G---HgUvshFRjsbTnvsdiUFFTGHFHGvDRT.YGHGH"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something)"
				}
			}]
		},
		"description": "no_value_provided",
		"tags": {
			"eq": ""
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something)"
				}
			}]
		},
		"description": "missingkey_value",
		"tags": {},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*123.4560000)"
				}
			}]
		},
		"description": "stringified_float",
		"tags": {
			"eq": 98765123.456
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*123.0)"
				}
			}]
		},
		"description": "stringified_float",
		"tags": {
			"eq": 7657123
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something)"
				}
			}]
		},
		"description": "case_mismatch",
		"tags": {
			"eq": "SOMETHING"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*123.456)"
				}
			}]
		},
		"description": "float_data_type_extra_decimal_zeros",
		"tags": {
			"eq": 765123.456
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*something)"
				}
			}]
		},
		"description": "suffix_match",
		"tags": {
			"eq": "asdn3kn42knsdsomething"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*false)"
				}
			}]
		},
		"description": "boolean_data_type",
		"tags": {
			"eq": false
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(*123.456)"
				}
			}]
		},
		"description": "float_data_type",
		"tags": {
			"eq": 765123.456
		},
		"expectation": true
	}],
	"equality_operand": [{
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "123.456"
				}
			}]
		},
		"description": "float_data_type_mismatch",
		"tags": {
			"eq": 123
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "123.456"
				}
			}]
		},
		"description": "float_data_type_mismatch",
		"tags": {
			"eq": 123.4567
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "something"
				}
			}]
		},
		"description": "incorrect_key",
		"tags": {
			"neq": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "something"
				}
			}]
		},
		"description": "incorrect_key_case",
		"tags": {
			"EQ": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "zzsomethingzz"
				}
			}]
		},
		"description": "single_char",
		"tags": {
			"eq": "i"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "something"
				}
			}]
		},
		"description": "case_mismatch",
		"tags": {
			"eq": "Something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "123"
				}
			}]
		},
		"description": "numeric_data_type_mismatch",
		"tags": {
			"eq": 12
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "E"
				}
			}]
		},
		"description": "char_data_type",
		"tags": {
			"eq": "E"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "true"
				}
			}]
		},
		"description": "boolean_data_type",
		"tags": {
			"eq": true
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "zzsomethingzz"
				}
			}]
		},
		"description": "part_of_text",
		"tags": {
			"eq": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "HgUvshFRjsbTnvsdiUFFTGHFHGvDRT.YGHGH"
				}
			}]
		},
		"description": "exact_match_with_upper_case",
		"tags": {
			"eq": "HgUvshFRjsbTnvsdiUFFTGHFHGvDRT.YGHGH"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "something"
				}
			}]
		},
		"description": "null_value_provided",
		"tags": {
			"eq": null
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "something"
				}
			}]
		},
		"description": "no_value_provided",
		"tags": {
			"eq": ""
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "false"
				}
			}]
		},
		"description": "boolean_data_type",
		"tags": {
			"eq": false
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "123.456"
				}
			}]
		},
		"description": "float_data_type",
		"tags": {
			"eq": 123.456
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "f25u!v@b#k$6%9^f&o*v(m)w_-=+s,./`(*&^%$#@!"
				}
			}]
		},
		"description": "exact_match_with_special_characters",
		"tags": {
			"eq": "f25u!v@b#k$6%9^f&o*v(m)w_-=+s,./`(*&^%$#@!"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "e"
				}
			}]
		},
		"description": "char_data_type_case_mismatch",
		"tags": {
			"eq": "E"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "nice to see you. will    you be   my        friend?"
				}
			}]
		},
		"description": "exact_match_with_spaces",
		"tags": {
			"eq": "nice to see you. will    you be   my        friend?"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "123.456"
				}
			}]
		},
		"description": "stringified_float",
		"tags": {
			"eq": "123.456000000"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "E"
				}
			}]
		},
		"description": "char_data_type_case_mismatch",
		"tags": {
			"eq": "e"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "true"
				}
			}]
		},
		"description": "boolean_data_type_mismatch",
		"tags": {
			"eq": false
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "123"
				}
			}]
		},
		"description": "numeric_data_type_mismatch",
		"tags": {
			"eq": 123
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "something"
				}
			}]
		},
		"description": "mismatch",
		"tags": {
			"eq": "notsomething"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "something"
				}
			}]
		},
		"description": "exact_match",
		"tags": {
			"eq": "something"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "123"
				}
			}]
		},
		"description": "numeric_data_type",
		"tags": {
			"eq": 123
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "false"
				}
			}]
		},
		"description": "boolean_data_type_mismatch",
		"tags": {
			"eq": true
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "something"
				}
			}]
		},
		"description": "missingkey_value",
		"tags": {},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "123.4560000"
				}
			}]
		},
		"description": "stringified_float",
		"tags": {
			"eq": 123.456
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "123.0"
				}
			}]
		},
		"description": "stringified_float",
		"tags": {
			"eq": 123
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "something"
				}
			}]
		},
		"description": "case_mismatch",
		"tags": {
			"eq": "SOMETHING"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "123.456"
				}
			}]
		},
		"description": "float_data_type_extra_decimal_zeros",
		"tags": {
			"eq": 123.456
		},
		"expectation": true
	}],
	"new_cases_for_decimal_mismatch": [{
		"dsl": {
			"or": [{
				"custom_variable": {
					"val": "wildcard(*123)"
				}
			}]
		},
		"description": "endswith_decimal",
		"tags": {
			"val": 765123
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"val": "wildcard(*123.0*)"
				}
			}]
		},
		"description": "contains_decimal_2",
		"tags": {
			"val": 876123
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"val": "wildcard(*123*)"
				}
			}]
		},
		"description": "contains_decimal_3",
		"tags": {
			"val": 654123.2323
		},
		"expectation": true
	}],
	"not_operator_tests": [{
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "f25u!v@b#k$6%9^f&o*v(m)w_-=+s,./`(*&^%$#@!"
					}
				}]
			}
		},
		"description": "exact_match_with_special_characters",
		"tags": {
			"eq": "f25u!v@b#k$6%9^f&o*v(m)w_-=+s,./`(*&^%$#@!"
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "123.456"
					}
				}]
			}
		},
		"description": "float_data_type_mismatch",
		"tags": {
			"eq": 123
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "123"
					}
				}]
			}
		},
		"description": "numeric_data_type_mismatch",
		"tags": {
			"eq": 12
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "123.456"
					}
				}]
			}
		},
		"description": "float_data_type_mismatch",
		"tags": {
			"eq": 123.4567
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "something"
					}
				}]
			}
		},
		"description": "incorrect_key",
		"tags": {
			"neq": "something"
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "something"
					}
				}]
			}
		},
		"description": "incorrect_key_case",
		"tags": {
			"EQ": "something"
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "zzsomethingzz"
					}
				}]
			}
		},
		"description": "single_char",
		"tags": {
			"eq": "i"
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "e"
					}
				}]
			}
		},
		"description": "char_data_type_case_mismatch",
		"tags": {
			"eq": "E"
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "something"
					}
				}]
			}
		},
		"description": "case_mismatch",
		"tags": {
			"eq": "Something"
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "nice to see you. will    you be   my        friend?"
					}
				}]
			}
		},
		"description": "exact_match_with_spaces",
		"tags": {
			"eq": "nice to see you. will    you be   my        friend?"
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"not": {
						"or": [{
							"not": {
								"or": [{
									"not": {
										"or": [{
											"custom_variable": {
												"neq": "not_eq_value"
											}
										}]
									}
								}]
							}
						}]
					}
				}]
			}
		},
		"description": "multiple_not_operator",
		"tags": {
			"neq": "eq_value"
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"not": {
						"or": [{
							"not": {
								"or": [{
									"not": {
										"or": [{
											"custom_variable": {
												"neq": "eq_value"
											}
										}]
									}
								}]
							}
						}]
					}
				}]
			}
		},
		"description": "multiple_not_operator",
		"tags": {
			"neq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "123.456"
					}
				}]
			}
		},
		"description": "stringified_float",
		"tags": {
			"eq": "123.456000000"
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"not": {
						"or": [{
							"not": {
								"or": [{
									"not": {
										"or": [{
											"not": {
												"or": [{
													"custom_variable": {
														"neq": "eq_value"
													}
												}]
											}
										}]
									}
								}]
							}
						}]
					}
				}]
			}
		},
		"description": "multiple_not_operator",
		"tags": {
			"neq": "eq_value"
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "false"
					}
				}]
			}
		},
		"description": "boolean_data_type_mismatch",
		"tags": {
			"eq": true
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"not": {
					"and": [{
						"not": {
							"and": [{
								"custom_variable": {
									"eq": "eq_value"
								}
							}]
						}
					}]
				}
			}]
		},
		"description": "multiple_not_operator",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"and": [{
					"not": {
						"and": [{
							"and": [{
								"custom_variable": {
									"eq": "eq_value"
								}
							}]
						}]
					}
				}]
			}]
		},
		"description": "multiple_not_operator",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "E"
					}
				}]
			}
		},
		"description": "char_data_type",
		"tags": {
			"eq": "E"
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"not": {
						"or": [{
							"not": {
								"or": [{
									"not": {
										"or": [{
											"not": {
												"or": [{
													"custom_variable": {
														"neq": "neq_value"
													}
												}]
											}
										}]
									}
								}]
							}
						}]
					}
				}]
			}
		},
		"description": "multiple_not_operator",
		"tags": {
			"neq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "true"
					}
				}]
			}
		},
		"description": "boolean_data_type_mismatch",
		"tags": {
			"eq": false
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "true"
					}
				}]
			}
		},
		"description": "boolean_data_type",
		"tags": {
			"eq": true
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "123"
					}
				}]
			}
		},
		"description": "numeric_data_type_mismatch",
		"tags": {
			"eq": 123
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "something"
					}
				}]
			}
		},
		"description": "mismatch",
		"tags": {
			"eq": "notsomething"
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "123"
					}
				}]
			}
		},
		"description": "numeric_data_type",
		"tags": {
			"eq": 123
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "something"
					}
				}]
			}
		},
		"description": "exact_match",
		"tags": {
			"eq": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "zzsomethingzz"
					}
				}]
			}
		},
		"description": "part_of_text",
		"tags": {
			"eq": "something"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"not": {
					"or": [{
						"not": {
							"or": [{
								"custom_variable": {
									"eq": "eq_value"
								}
							}]
						}
					}]
				}
			}]
		},
		"description": "multiple_not_operator",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "HgUvshFRjsbTnvsdiUFFTGHFHGvDRT.YGHGH"
					}
				}]
			}
		},
		"description": "exact_match_with_upper_case",
		"tags": {
			"eq": "HgUvshFRjsbTnvsdiUFFTGHFHGvDRT.YGHGH"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"not": {
						"or": [{
							"or": [{
								"custom_variable": {
									"eq": "eq_value"
								}
							}]
						}]
					}
				}]
			}]
		},
		"description": "nested_not_operator",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "something"
					}
				}]
			}
		},
		"description": "null_value_provided",
		"tags": {
			"eq": null
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "something"
					}
				}]
			}
		},
		"description": "no_value_provided",
		"tags": {
			"eq": ""
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "something"
					}
				}]
			}
		},
		"description": "missingkey_value",
		"tags": {},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "123.4560000"
					}
				}]
			}
		},
		"description": "stringified_float",
		"tags": {
			"eq": 123.456
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "123.0"
					}
				}]
			}
		},
		"description": "stringified_float",
		"tags": {
			"eq": 123
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "something"
					}
				}]
			}
		},
		"description": "case_mismatch",
		"tags": {
			"eq": "SOMETHING"
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "123.456"
					}
				}]
			}
		},
		"description": "float_data_type_extra_decimal_zeros",
		"tags": {
			"eq": 123.456
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "E"
					}
				}]
			}
		},
		"description": "char_data_type_case_mismatch",
		"tags": {
			"eq": "e"
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "false"
					}
				}]
			}
		},
		"description": "boolean_data_type",
		"tags": {
			"eq": false
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"eq": "123.456"
					}
				}]
			}
		},
		"description": "float_data_type",
		"tags": {
			"eq": 123.456
		},
		"expectation": false
	}],
	"or_operator": [{
		"dsl": {
			"or": [{
				"or": [{
					"custom_variable": {
						"eq": "eq_value"
					}
				}]
			}, {
				"or": [{
					"custom_variable": {
						"reg": "regex(myregex+)"
					}
				}]
			}]
		},
		"description": "multiple_or_operator_with_single_correct_value",
		"tags": {
			"eq": "eq_value",
			"reg": "wrong"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "eq_value"
				}
			}]
		},
		"description": "single_or_operator_mismatch",
		"tags": {
			"a": "n_eq_value"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"or": [{
						"or": [{
							"or": [{
								"custom_variable": {
									"eq": "eq_value"
								}
							}]
						}]
					}]
				}]
			}]
		},
		"description": "multiple_or_operator",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"custom_variable": {
						"eq": "eq_value"
					}
				}]
			}, {
				"or": [{
					"custom_variable": {
						"reg": "regex(myregex+)"
					}
				}]
			}]
		},
		"description": "multiple_or_operator_with_all_incorrect_correct_values",
		"tags": {
			"eq": "wrong",
			"reg": "wrong"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "eq_value"
				}
			}]
		},
		"description": "single_or_operator_case_mismatch",
		"tags": {
			"eq": "Eq_Value"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"custom_variable": {
						"eq": "eq_value"
					}
				}]
			}, {
				"or": [{
					"custom_variable": {
						"reg": "regex(myregex+)"
					}
				}]
			}]
		},
		"description": "multiple_or_operator_with_all_correct_values",
		"tags": {
			"eq": "eq_value",
			"reg": "myregeXxxxxx"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "eq_value"
				}
			}]
		},
		"description": "single_or_operator_matching",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"custom_variable": {
						"eq": "eq_value"
					}
				}]
			}, {
				"or": [{
					"custom_variable": {
						"reg": "regex(myregex+)"
					}
				}]
			}]
		},
		"description": "multiple_or_operator_with_single_correct_value",
		"tags": {
			"eq": "wrong",
			"reg": "myregexxxxxx"
		},
		"expectation": true
	}],
	"regex_tests": [{
		"dsl": {
			"or": [{
				"custom_variable": {
					"reg": "regex(<(W[^>]*)(.*?)>)"
				}
			}]
		},
		"description": "regex_operand_mismatch",
		"tags": {
			"reg": "<wingifySDK id=1></wingifySDK>"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"reg": "regex(<(W[^>]*)(.*?)>)"
				}
			}]
		},
		"description": "regex_operand",
		"tags": {
			"reg": "<WingifySDK id=1></WingifySDK>"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"reg": "regex(*)"
				}
			}]
		},
		"description": "invalid_reqex",
		"tags": {
			"reg": "*"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"reg": "regex(*)"
				}
			}]
		},
		"description": "invalid_reqex",
		"tags": {
			"reg": "asdf"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"reg": "regex(myregex+)"
				}
			}]
		},
		"description": "regex_operand_case_mismatch",
		"tags": {
			"reg": "myregeXxxxxx"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"reg": "regex(myregex+)"
				}
			}]
		},
		"description": "regex_operand",
		"tags": {
			"reg": "myregexxxxxx"
		},
		"expectation": true
	}],
	"simple_and_ors": [{
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"neq": "not_eq_value"
					}
				}]
			}
		},
		"description": "single_not_true",
		"tags": {
			"neq": "eq_valaue"
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"not": {
					"and": [{
						"not": {
							"and": [{
								"custom_variable": {
									"eq": "eq_value"
								}
							}]
						}
					}]
				}
			}]
		},
		"description": "chain_of_and_nullify_not_true",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"a": "lower(something)"
				}
			}]
		},
		"description": "dsl_lower_true",
		"tags": {
			"a": "SoMeThIng"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"reg": "regex(myregex+)"
				}
			}]
		},
		"description": "dsl_regex_true",
		"tags": {
			"reg": "myregexxxxxx"
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"not": {
						"or": [{
							"not": {
								"or": [{
									"not": {
										"or": [{
											"not": {
												"or": [{
													"custom_variable": {
														"neq": "eq_value"
													}
												}]
											}
										}]
									}
								}]
							}
						}]
					}
				}]
			}
		},
		"description": "chain_of_not_5_false",
		"tags": {
			"neq": "eq_value"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"a": "lower(something)"
				}
			}]
		},
		"description": "dsl_lower_false",
		"tags": {
			"a": "SoMeThIngS"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"reg": "regex(myregex+)"
				}
			}]
		},
		"description": "dsl_regex_false",
		"tags": {
			"reg": "myregeXxxxxx"
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"not": {
						"or": [{
							"not": {
								"or": [{
									"not": {
										"or": [{
											"custom_variable": {
												"neq": "eq_value"
											}
										}]
									}
								}]
							}
						}]
					}
				}]
			}
		},
		"description": "chain_of_not_4_true",
		"tags": {
			"neq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"a": "something"
					}
				}]
			}
		},
		"description": "dsl_eq_false",
		"tags": {
			"a": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"custom_variable": {
						"neq": "not_eq_value"
					}
				}]
			}
		},
		"description": "single_not_false",
		"tags": {
			"neq": "not_eq_value"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"not": {
					"or": [{
						"not": {
							"or": [{
								"custom_variable": {
									"eq": "eq_value"
								}
							}]
						}
					}]
				}
			}]
		},
		"description": "chain_of_or_nullify_not_true",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"a": "wildcard(*some*thing*)"
				}
			}]
		},
		"description": "dsl_wildcard_true_front_back_middle_star",
		"tags": {
			"a": "hellosome*thingworld"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"not": {
						"or": [{
							"or": [{
								"custom_variable": {
									"eq": "eq_value"
								}
							}]
						}]
					}
				}]
			}]
		},
		"description": "chain_of_or_middle_not_false",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": false
	}, {
		"dsl": {
			"not": {
				"or": [{
					"not": {
						"or": [{
							"not": {
								"or": [{
									"not": {
										"or": [{
											"custom_variable": {
												"neq": "not_eq_value"
											}
										}]
									}
								}]
							}
						}]
					}
				}]
			}
		},
		"description": "chain_of_not_4_false",
		"tags": {
			"neq": "eq_valaue"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"a": "wildcard(something*)"
				}
			}]
		},
		"description": "dsl_wildcard_true_back",
		"tags": {
			"a": "somethingworld"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "eq_value"
				}
			}]
		},
		"description": "single_or_true",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"custom_variable": {
						"eq": "eq_value"
					}
				}]
			}, {
				"or": [{
					"custom_variable": {
						"reg": "regex(myregex+)"
					}
				}]
			}]
		},
		"description": "multiple_or_true",
		"tags": {
			"eq": "eq_value",
			"reg": "myregeXxxxxx"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"a": "wildcard(*something)"
				}
			}]
		},
		"description": "dsl_wildcard_true_front",
		"tags": {
			"a": "hellosomething"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"a": "wildcard(*something)"
				}
			}]
		},
		"description": "dsl_wildcard_false",
		"tags": {
			"a": "somethin"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"and": [{
					"and": [{
						"and": [{
							"and": [{
								"custom_variable": {
									"eq": "eq_value"
								}
							}]
						}]
					}]
				}]
			}]
		},
		"description": "chain_of_and_true",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"and": [{
					"not": {
						"and": [{
							"and": [{
								"custom_variable": {
									"eq": "eq_value"
								}
							}]
						}]
					}
				}]
			}]
		},
		"description": "chain_of_and_middle_not_false",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "n_eq_value"
				}
			}]
		},
		"description": "single_or_false",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"a": "something"
				}
			}]
		},
		"description": "dsl_eq_true",
		"tags": {
			"a": "something"
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"eq": "eq_value"
					}
				}]
			}, {
				"or": [{
					"custom_variable": {
						"reg": "regex(myregex+)"
					}
				}]
			}]
		},
		"description": "multiple_and_true",
		"tags": {
			"eq": "eq_value",
			"reg": "myregexxxxxx"
		},
		"expectation": true
	}, {
		"dsl": {
			"not": {
				"or": [{
					"not": {
						"or": [{
							"not": {
								"or": [{
									"not": {
										"or": [{
											"not": {
												"or": [{
													"custom_variable": {
														"neq": "neq_value"
													}
												}]
											}
										}]
									}
								}]
							}
						}]
					}
				}]
			}
		},
		"description": "chain_of_not_5_true",
		"tags": {
			"neq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"custom_variable": {
						"eq": "eq_value"
					}
				}]
			}, {
				"or": [{
					"custom_variable": {
						"reg": "regex(myregex+)"
					}
				}]
			}]
		},
		"description": "multiple_or_false",
		"tags": {
			"eq": "eq_values",
			"reg": "myregeXxxxxx"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"custom_variable": {
					"eq": "eq_value"
				}
			}]
		},
		"description": "single_and_true",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"and": [{
				"or": [{
					"custom_variable": {
						"eq": "eq_value"
					}
				}]
			}, {
				"or": [{
					"custom_variable": {
						"reg": "regex(myregex+)"
					}
				}]
			}]
		},
		"description": "multiple_and_false",
		"tags": {
			"eq": "eq_value",
			"reg": "myregeXxxxxx"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"a": "wildcard(*something*)"
				}
			}]
		},
		"description": "dsl_wildcard_true_front_back",
		"tags": {
			"a": "hellosomethingworld"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"or": [{
					"or": [{
						"or": [{
							"or": [{
								"custom_variable": {
									"eq": "eq_value"
								}
							}]
						}]
					}]
				}]
			}]
		},
		"description": "chain_of_or_true",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"a": "wildcard(*some*thing*)"
				}
			}]
		},
		"description": "dsl_wildcard_false_front_back_middle_star",
		"tags": {
			"a": "hellosomethingworld"
		},
		"expectation": false
	}, {
		"dsl": {
			"and": [{
				"custom_variable": {
					"eq": "n_eq_value"
				}
			}]
		},
		"description": "single_and_false",
		"tags": {
			"eq": "eq_value"
		},
		"expectation": false
	}],
	"starts_with_operand_tests": [{
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(123.456*)"
				}
			}]
		},
		"description": "float_data_type_mismatch",
		"tags": {
			"eq": 123
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(something*)"
				}
			}]
		},
		"description": "incorrect_key",
		"tags": {
			"neq": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(something*)"
				}
			}]
		},
		"description": "incorrect_key_case",
		"tags": {
			"EQ": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(zzsomethingzz*)"
				}
			}]
		},
		"description": "single_char",
		"tags": {
			"eq": "i"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(something*)"
				}
			}]
		},
		"description": "case_mismatch",
		"tags": {
			"eq": "Something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(E*)"
				}
			}]
		},
		"description": "char_data_type",
		"tags": {
			"eq": "E"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(something*)"
				}
			}]
		},
		"description": "prefix_match",
		"tags": {
			"eq": "somethingdfgdwerewew"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(true*)"
				}
			}]
		},
		"description": "boolean_data_type",
		"tags": {
			"eq": true
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(zzsomethingzz*)"
				}
			}]
		},
		"description": "part_of_text",
		"tags": {
			"eq": "something"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(123.456*)"
				}
			}]
		},
		"description": "float_data_type",
		"tags": {
			"eq": 123.456789
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(something*)"
				}
			}]
		},
		"description": "null_value_provided",
		"tags": {
			"eq": null
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(HgUvshFRjsbTnvsdiUFFTGHFHGvDRT.YGHGH*)"
				}
			}]
		},
		"description": "upper_case",
		"tags": {
			"eq": "HgUvshFRjsbTnvsdiUFFTGHFHGvDRT.YGHGH---A-N-Y-T-H-I-N-G---"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(something*)"
				}
			}]
		},
		"description": "no_value_provided",
		"tags": {
			"eq": ""
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(something*)"
				}
			}]
		},
		"description": "suffix_match",
		"tags": {
			"eq": "asdsdsdsomething"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(false*)"
				}
			}]
		},
		"description": "boolean_data_type",
		"tags": {
			"eq": false
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(123*)"
				}
			}]
		},
		"description": "float_data_type",
		"tags": {
			"eq": 123.45
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(123*)"
				}
			}]
		},
		"description": "numeric_data_type_mismatch",
		"tags": {
			"eq": 12
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(something*)"
				}
			}]
		},
		"description": "contains_match",
		"tags": {
			"eq": "asdn3kn42knsdsomethingmm"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(e*)"
				}
			}]
		},
		"description": "char_data_type_case_mismatch",
		"tags": {
			"eq": "E"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(f25u!v@b#k$6%9^f&o*v(m)w_-=+s,./`(*&^%$#@!*)"
				}
			}]
		},
		"description": "special_characters",
		"tags": {
			"eq": "f25u!v@b#k$6%9^f&o*v(m)w_-=+s,./`(*&^%$#@!---A-N-Y-T-H-I-N-G---"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(123.456*)"
				}
			}]
		},
		"description": "stringified_float",
		"tags": {
			"eq": "123.456789"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(E*)"
				}
			}]
		},
		"description": "char_data_type_case_mismatch",
		"tags": {
			"eq": "e"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(true*)"
				}
			}]
		},
		"description": "boolean_data_type_mismatch",
		"tags": {
			"eq": false
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(123*)"
				}
			}]
		},
		"description": "numeric_data_type_mismatch",
		"tags": {
			"eq": 123
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(nice to see you. will    you be   my        friend?*)"
				}
			}]
		},
		"description": "spaces",
		"tags": {
			"eq": "nice to see you. will    you be   my        friend? Great!!"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(something*)"
				}
			}]
		},
		"description": "mismatch",
		"tags": {
			"eq": "qwertyu"
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(something*)"
				}
			}]
		},
		"description": "exact_match",
		"tags": {
			"eq": "something"
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(123*)"
				}
			}]
		},
		"description": "numeric_data_type",
		"tags": {
			"eq": 123456
		},
		"expectation": true
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(false*)"
				}
			}]
		},
		"description": "boolean_data_type_mismatch",
		"tags": {
			"eq": true
		},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(something*)"
				}
			}]
		},
		"description": "missingkey_value",
		"tags": {},
		"expectation": false
	}, {
		"dsl": {
			"or": [{
				"custom_variable": {
					"eq": "wildcard(something*)"
				}
			}]
		},
		"description": "case_mismatch",
		"tags": {
			"eq": "SOMETHING"
		},
		"expectation": false
	}]
}';
}
