<?

// SERVICE
define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"] . "/logans/" . date("Y_m_d") . ".log");

$arSite = getSiteInfo();
define("SITE_LID", $arSite['LID']);
define("SITE_NAME", $arSite['SITE_NAME']);
define("SITE_URL", $arSite['SERVER_NAME']);

define("UPLOAD_MAX_FILE_SIZE", (5 * 1024 * 1024));
define("NO_IMAGE", '/upload/_base/noimage.jpg');
define('GOOGLE_API_KEY', 'AIzaSyALIXMyl5TSrTYwkXIk3_94Xof3g3dMznM');

// IBLOCKS
define('SOCNETS_IB', 5);
define('GALLERIES_IB', 32);

define('PROMO_SLIDER_IB', 6);
define('ACTIONS_IB', 7);
define('GOODSOFTHEWEEK_IB', 8);
define('BUBBLES_IB', 12);
define('NEWS_IB', 13);
define('SHOPS_IB', 14);
define('SALES_IB', 18);
define('ACHIEVEMENTS_IB', 19);
define('TENANTS_IB', 20);
define('VACANCIES_IB', 21);
define('BONUSES_IB', 22);

//CATALOGS
define('FOOD_IB', 16);
define('BAKERY_IB', 17);
define('OURBRAND_IB', 24);

// FOS
define('FOS_FEEDBACK_IB', 23);
define('FOS_FEEDBACK_EVENT', 'FOS_FEEDBACK_EVENT');
define('FOS_QUESTION_IB', 25);
define('FOS_QUESTION_EVENT', 'FOS_QUESTION_EVENT');
define('FOS_PROMO_IB', 26);
define('FOS_PROMO_EVENT', 'FOS_PROMO_EVENT');
define('FOS_REVIEW_IB', 27);
define('FOS_REVIEW_EVENT', 'FOS_REVIEW_EVENT');
define('FOS_ACTION_MEMBER_IB', 28);
define('FOS_ACTION_MEMBER_EVENT', 'FOS_ACTION_MEMBER_EVENT');
define('FOS_RESUME_IB', 29);
define('FOS_RESUME_EVENT', 'FOS_RESUME_EVENT');
define('FOS_TENANTS_IB', 30);
define('FOS_TENANTS_EVENT', 'FOS_TENANTS_EVENT');
define('FOS_LESSORS_IB', 31);
define('FOS_LESSORS_EVENT', 'FOS_LESSORS_EVENT');

// API
define('API_INFOSCREEN_IB', 9);
define('API_BONUS_IB', 10);
define('API_SALE_IB', 11);
define('API_STAR_FOS_IB', 15);

// PUBLIC
define('CITIES_LIST', getCitiesList());
define('SOCNETS_LIST', SocnetsModel::select(['NAME', 'PROPS'])->filter(['ACTIVE' => 'Y'])->cache(30)->sort('sort')->getList()->toArray());
define('AOS_OFF_PAGES', ['achievements', 'shops']);