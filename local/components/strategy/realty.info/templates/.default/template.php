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
?>

<div class="container-fluid realty-info">
    <div class="row mt-3">
        <div class="col-sm-12">
            <p class="h3">Информация</p>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-xl-3 col-lg-6 col-12 mb-5">
            <div class="card shadow border-0">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="media-body text-left">
                                <h3 class="text-success"><? echo $arResult['ACTIVE_REALTY_COUNT']?></h3>
                                <span><a href="/realty/list/">Активные недвижимости</a></span>
                            </div>
                            <div class="align-self-center">
                                <i class="glyphicon glyphicon-home text-success float-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-12 mb-5">
            <div class="card shadow border-0">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="media-body text-left">
                                <h3 class="text-warning"><? echo $arResult['ARCHIVE_REALTY_COUNT'];?></h3>
                                <span><a href="/realty/archive/">Архив недвижимости</a></span>
                            </div>
                            <div class="align-self-center">
                                <i class="glyphicon glyphicon-hdd text-warning float-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-12 mb-5">
            <div class="card shadow border-0">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="media-body text-left">
                                <h3 class="text-danger"><? echo $arResult['REMOVED_REALTY_COUNT'];?></h3>
                                <span><a href="/realty/removed/">Удаленная недвижимость</a></span>
                            </div>
                            <div class="align-self-center">
                                <i class="glyphicon glyphicon-trash text-danger float-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-12 mb-5">
            <div class="card shadow border-0">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="media-body text-left">
                                <h3 class="text-info"><?= $arResult['LOCKED_COUNT']['count']?></h3>
                                <span><a href="/realty/blocked/">Блокированная недвижимость</a></span>
                            </div>
                            <div class="align-self-center">
                                <i class="glyphicon glyphicon-lock text-info float-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-12 mb-5">
            <div class="card shadow border-0">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="media-body text-left">
                                <h3 class="text-primary"><? echo $arResult['REQUESTS_ACTIVE'];?></h3>
                                <span><a href="/realty/client-requests/">Активные запросы клиентов</a></span>
                            </div>
                            <div class="align-self-center">
                                <i class="glyphicon glyphicon-send text-primary float-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-12 mb-5">
            <div class="card shadow border-0">
                <div class="card-content">
                    <div class="card-body">
                        <div class="media d-flex">
                            <div class="media-body text-left">
                                <h3 class="text-danger"><? echo $arResult['REQUESTS_REMOVED'];?></h3>
                                <span><a href="/realty/client-requests/removed/">Удаленные запросы клиентов</a></span>
                            </div>
                            <div class="align-self-center">
                                <i class="glyphicon glyphicon-remove text-danger float-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
