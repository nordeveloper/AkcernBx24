<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(false);

use \Bitrix\Main\Page\Asset;

Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/css/Chart.min.css');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/Chart.bundle.min.js');

Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/css/uikit.css');
Asset::getInstance()->addCss(SITE_TEMPLATE_PATH.'/js/chosen/chosen.css');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH.'/js/chosen/chosen.jquery.min.js');

$form = new Forms();
$RealtyAvgByMonth = '';
$RequestAvgByMonth = '';

if( !empty($arResult['REALTY_AVG']['AVG']) ){
    $RealtyAvgByMonth =  implode(',', $arResult['REALTY_AVG']['AVG']);
}

if( !empty($arResult['REQUETS_AVG']['AVG']) ){
    $RequestAvgByMonth =  implode(',', $arResult['REQUETS_AVG']['AVG']);
}

$rooms = array(
    ['ID'=>1, 'VALUE'=>1],['ID'=>2, 'VALUE'=>2], ['ID'=>3, 'VALUE'=>3],
    ['ID'=>4, 'VALUE'=>4], ['ID'=>5, 'VALUE'=>5], ['ID'=>6, 'VALUE'=>6],
    ['ID'=>7, 'VALUE'=>7], ['ID'=>8, 'VALUE'=>8], ['ID'=>9, 'VALUE'=>9],
    ['ID'=>10, 'VALUE'=>10]
);
?>

<div class="container-fluid">

    <div class="col-sm-12 text-center">
        <p class="h3">Аналитика рынка недвижимости</p>
    </div>
    <hr>

    <!-- Filter form -->
    <div class="col-sm-12">
        <form class="row" action="" method="get"  style="border: 1px solid #f7f7f7;  border-radius: 5px; padding:10px 0">

            <div class="col-md-9">
                <div class="row">
                    <? foreach ($arResult["PROPERTY_LIST"] as $arProp):?>

                        <?
                          $value = '';
                         if(!empty($_GET[$arProp['CODE']])) {$value = $_GET[$arProp['CODE']];} 
                         ?>

                        <div class="col-lg-2 col-md-3 form-group c-form-group">

                            <? if($arProp['PROPERTY_TYPE']=='N' and empty($arProp['USER_TYPE'])):?>
                                <?
                                if($arProp['CODE']=='ROOMS'):  $arProp['ENUM'] = $rooms; $arProp['MULTIPLE']='Y'; 
                                $arProp['IS_REQUIRED']='';
                                ?>
                                <? $form->Select($arProp, $value, false, true);?>
                                <? else: ?>
                                <? $form->input($arProp, false, false, true);?>
                                <? endif ?>
                            <? endif ?>

                            <? if($arProp['PROPERTY_TYPE']=='L'): $arProp['IS_REQUIRED']='';?>
                                <? $form->select($arProp, $value, false, true) ?>
                            <? endif ?>

                            <? if (!empty($arProp['USER_TYPE'])): ?><?//dump($value)?>
                                <? $form->showUserType($arProp, $value, false, true); ?>
                            <? endif ?>

                        </div>
                    <? endforeach;?>
                </div>
            </div>

            <div class="col-md-3" style="border-left: 1px solid #e0e0e0">
                <div class="row">

                    <div class="col-sm-12 form-group c-form-group">
                        <label class="c-label-text">Год</label>
                        <select name="YEAR" class="form-control c-form-text">
                            <option value=""></option>
                            <? for ($i=2012; $i<=date('Y');$i++):?>
                                <option <? if($_GET['YEAR']==$i):?>selected<?endif?> value="<?=$i?>"><?=$i?></option>
                            <? endfor?>
                        </select>
                    </div>

                    <div class="col-sm-12 form-group c-form-group">
                        <label class="c-label-text">Ключевые индикаторы</label>
                        <select name="FilterType" class="form-control c-form-text">
                            <option <? if($_GET['FilterType']=='avg'):?>selected<?endif?> value="avg">Cтоимость</option>
                            <option <? if($_GET['FilterType']=='count'):?>selected<?endif?> value="count">Количество</option>
                        </select>
                    </div>
                </div>

                <div class="col-sm-12 form-group">
                    <button type="submit" class="btn btn-success" value="Y"><i class="glyphicon glyphicon-filter"></i> Показать</button>
                    <a href="/realty/analitics/" class="btn btn-info">Очистить</a>
                </div>
            </div>

            </form>
        <div class="col-sm-12"><p>Если ничего не выбрано показываются данные на текуший год</p></div>
    </div>


    <!-- Main chart -->
<div class="row">
    <div class="col-md-9">
        <canvas class="my-4" id="analytic" width="900" height="250"></canvas>
    </div>

    <!-- Small charts -->
    <div class="small-charts mt-10 col-md-3">
        <div class="row pt-3">
            <div class="col-md-12 text-center">
                <h5>Недвижимость</h5>
                <p>Арендованные и продаанные - количество</p>
                <canvas id="chart1" width="400" height="200"></canvas>
            </div>
            <div class="col-md-12 text-center">
                <h5>Запросы клиентов</h5>
                <p>Аренда и продажа - количество</p>
                <canvas id="chart2" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

</div>

</div>


<script>

    $(document).ready(function() {
        $(".chosen-select").chosen();
    });

    var ctx = document.getElementById("analytic");
    var mainChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["Январь", "Февраль", "Март", "Арпрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
            datasets: [{
                data: [<?=$RealtyAvgByMonth?>],
                //lineTension: 0,
                backgroundColor: 'transparent',
                borderColor: '#007bff',
                borderWidth: 4,
                pointBackgroundColor: '#007bff'
            },{
                //lineTension: 0,
                label: 'Запросы клиентов',
                fill: false,
                backgroundColor: '#2cbf1f',
                borderColor: '#2cbf1f',
                data: [<?=$RequestAvgByMonth?>],
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: false
                    }
                }]
            },
            legend: {
                display: false,
            },
            tooltips: {
                mode: 'label',
                label: 'mylabel',
                callbacks: {
                    label: function(tooltipItem, data) {
                        return tooltipItem.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ")<? if($_GET['FilterType']=='avg'):?>+' $'<?endif?>;
                    },
                },
            },
        }
    });


    // Chart 1 Realty count
    var data = {
        labels: [
            "Rented - Арендованные",
            "Sold - Проданные"
        ],
        datasets: [{
            data: [<?=$arResult['REALTY_RENTED']['COUNT']?>, <?=$arResult['REALTY_SOLD']['COUNT']?>],
            backgroundColor: [
                "#2cbf1f",
                "#36A2EB"
            ],
            hoverBackgroundColor: [
                "#2cbf1f",
                "#36A2EB",
            ]
        }]
    };
    var ctx = $("#chart1");
    var chart1 = new Chart(ctx, {
        type: 'pie',
        data: data
    });

    // Chart 2 Realty New
    var data = {
        labels: [
            "Rented - Арнеда",
            "Sold - Продажа"
        ],
        datasets: [{
            data: [0, 0],
            backgroundColor: [
                "#FF6384",
                "#864ed9",
            ],
            hoverBackgroundColor: [
                "#FF6384",
                "#864ed9",
            ]
        }]
    };


    // Chart 2 Requests
    var data = {
        labels: [
            "Rent - Аренда",
            "Sale - Продажа"
        ],
        datasets: [{
            data: [<?=$arResult['REQUEST_RENTS']['COUNT']?>, <?=$arResult['REQUEST_SALE']['COUNT']?>],
            backgroundColor: [
                "#FF6384",
                "#1a41b8"
            ],
            hoverBackgroundColor: [
                "#FF6384",
                "#1a41b8"
            ]
        }]
    };
    var ctx = $("#chart2");
    var chart3 = new Chart(ctx, {
        type: 'pie',
        data: data
    });

    var data = {
        labels: [
            "Rent - Аренда",
            "Sale - Продажа"
        ],
        datasets: [{
            data: [0, 0],
            backgroundColor: [
                "#2cbf1f",
                "#FFCE56"
            ],
            hoverBackgroundColor: [
                "#2cbf1f",
                "#FFCE56"
            ]
        }]
    };

</script>