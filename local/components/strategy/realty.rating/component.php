<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
$this->setFrameMode(false);

function GegRatingByRaltyID($id){
	if(intval($id)>0){
		global $DB;
		$dbRes = $DB->query("SELECT sum(RATE) as TOTAL, count(ID) as COUNT FROM st_realty_rating WHERE REALTY_ID = '".$id."'");
		if( $row = $dbRes->fetch() ){
			return $row;
		}
	}
}


$arRating = GegRatingByRaltyID($arParams['REALTY_ID']);

function getRealtyRate($sumVotes, $totalVotes)
{
  $rating = $sumVotes/$totalVotes;
  return $rating;
}

$ratingSum = getRealtyRate($arRating['TOTAL'], $arRating['COUNT']);

$arResult = 0;

if($ratingSum>0)
$arResult = ceil($ratingSum);

$this->IncludeComponentTemplate();


