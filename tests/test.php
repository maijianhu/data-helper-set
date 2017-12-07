<?php
/**
 * Created by PhpStorm.
 * User: maijianhu
 * Date: 2017/11/30
 * Time: 10:47
 */
use DataHelperSet\DataHelperSet;
use DataHelperSet\DataHelperSetValidator;
use DataHelperSet\DataHelperSetFormatter;
use DataHelperSet\DataHelperSetFilter;
use DataHelperSet\exception\DataHelperSetValidateException;
use DataHelperSet\exception\DataHelperSetFilterException;

require 'autoload.php';
$postData = [
    'name'     => 'mai',
    'app_id'   => '-20.1123',
    'quantity' => '20.32',
    'agree'  => 'yes',
];

$configs = [
    'name' => DataHelperSetFormatter::TYPE_STRING,
    'app_id' => DataHelperSetFormatter::TYPE_INT,
    'quantity' => DataHelperSetFormatter::TYPE_INT,
    'agree' => DataHelperSetFormatter::TYPE_BOOLEAN,
];


/*
 * 扩展格式化
DataHelperSet::extendFormat('object', function($data) {
    return 'object' . $data;
});

var_dump(DataHelperSet::format('m35', 'object'));
exit;
*/

//var_dump(DataHelperSet::formatBundle($postData, $configs));

/*
 * 扩展验证
 * DataHelperSet::extendValidate('false', '总是false', function() {
    return false;
});
try {
    $validator = DataHelperSet::getValidator();
    var_dump($validator->checkData('value', 'false'));
    exit;
    DataHelperSet::validate('name', ['false', '字段%field%返回false'], 'fake');
} catch (DataHelperSetValidateException $e) {
    var_dump($e->getMessage());
    exit;
}*/

$validator = DataHelperSet::getValidator();
$result = DataHelperSet::getValidator()->checkData(0, DataHelperSetValidator::REQUIRED);

try {
    DataHelperSet::validate('123', [$validator::REQUIRED], 'name');
    DataHelperSet::validate('maijianhu@360.cn', [[$validator::REQUIRED, '请填写电子邮件地址'], [$validator::EMAIL, '电子邮件地址不正确']], 'email');
    // var_dump('认证成功');
} catch (DataHelperSetValidateException $e) {
    var_dump($e->getMessage());
}


$filter = DataHelperSet::getFilter();
var_dump($filter->filterData('defaultIf', 'hello', 'default value'));
exit;
/*$filter = new DataHelperSetFilter();
echo DataHelperSet::filter([FILTER_SANITIZE_ENCODED, ['md5', true]], 'maijianhu@360.cn');
exit;*/

try {
    error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
    DataHelperSet::extendFilter('testFilter', function($data, $name, $pos) {
        throw new DataHelperSetFilterException('data filter exception');
//         return $data . '- this is a test filter from ' . $name . ' position :' . $pos;
    });
    $result = DataHelperSet::filter('testFilter', 'm35', 'mai');
    var_dump($result);
} catch (DataHelperSetFilterException $e) {
    var_dump($e->getMessage());
}


try {
    $pipeConfigs = [
        [
            'format'   => [
                'name'     => DataHelperSetFormatter::TYPE_STRING,
                'app_id'   => DataHelperSetFormatter::TYPE_INT,
                'quantity' => DataHelperSetFormatter::TYPE_INT,
                'agree'    => DataHelperSetFormatter::TYPE_BOOLEAN,
            ],
            'filter'   => [
                'name'   => FILTER_SANITIZE_STRING,
                'app_id' => ['abs'],
            ],
            'validate' => [
                'name' => [DataHelperSetValidator::REQUIRED, '请填写姓名'],
            ],
        ],
        [
            'filter' => [
                'name' => [function ($name) {
                    return $name;
                }],
            ],
        ],
    ];
    $postData = [
        'name'     => 'hello world!',
        'app_id'   => '-20.1123',
        'quantity' => '20.32',
        'agree'  => 'yes',
    ];

    $result = DataHelperSet::pipe($postData, $pipeConfigs);
    var_dump($result);
} catch (DataHelperSetValidateException $e) {
    echo $e->getMessage();
}