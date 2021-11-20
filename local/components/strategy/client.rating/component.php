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

function GegContactRating($id){

	if($id>0){
		$id  = intval($id);
		global $DB;
		$sql  = "SELECT sum(RATE) as TOTAL, count(ID) as COUNT FROM ak_rating_users WHERE CONTACT_ID = '".$id."'";
		$dbRes = $DB->query($sql);
	
		if( $row = $dbRes->fetch() ){
			return $row;
		}
	}
}

function Rate($totalVotes,$sumVotes)
{
    $rating = $sumVotes/$totalVotes;
    return $rating;
}


$arRating = GegContactRating($arParams['CONTACT_ID']);

//dump($arRating);

$ratingSum =  Rate($arRating['TOTAL'], $arRating['COUNT']);

$arResult = 0;

if($ratingSum>0)
    $arResult = ceil($ratingSum);

$this->IncludeComponentTemplate();


