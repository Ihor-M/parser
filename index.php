<?php

include_once 'curl_query.php';
include_once 'simple_html_dom.php';



$url = 'https://auto.ria.com/uk/newauto/';
$url_short = 'https://auto.ria.com';
$html = get_curl($url);
$dom = str_get_html($html);

$carBrand = $dom->find('.item-brands');

$cars = [];

foreach ($carBrand as $brand)
{
    $brandName = $brand->title;
    $brandHref = $brand->href;
    $brandHref = substr($brandHref, 9, strlen($brandHref));
    $brandHref = $url . $brandHref;

    $html = get_curl($brandHref);
    file_put_contents('model_html', $html);
    $oneBrand = str_get_html($html);
    $model = $oneBrand->find('.link');

    $str = strlen($brandName) + 1;

    foreach ($model as $mod)
    {
        $href = $mod->href;
        $href = $url_short . $href;
        $modelName = $mod->plaintext;
        $modelName = substr($modelName, $str, strlen($modelName));

        // get price
        $modelHtml = get_curl($href);
        $oneModel = str_get_html($modelHtml);

        $numb1 = strpos($oneModel, 'itemprop="lowPrice"');
        $str1 = substr($oneModel, $numb1);
        $numb2 = strpos($str1, '/>');
        $str2 = substr($str1, 0, $numb2);
        $price = preg_replace("/[^0-9]/","",$str2);

        $result = "Brand: " . $brandName . " Model: " . $modelName . " Price: " . $price;

        array_push($cars, $result);

    }
}

$file = fopen("cars.csv","w");
foreach ($cars as $car)
{
    fputcsv($file,explode(',',$car));
}
fclose($file);
echo "FINISH";