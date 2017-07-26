<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
if (version_compare ( phpversion (), '5.2.0', '<' ) === true) {
	echo '<div style="font:12px/1.35em arial, helvetica, sans-serif;">
<div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
<h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">
Whoops, it looks like you have an invalid PHP version.</h3></div><p>Magento supports PHP 5.2.0 or newer.
<a href="http://www.magentocommerce.com/install" target="">Find out</a> how to install</a>
 Magento using PHP-CGI as a work-around.</p></div>';
	exit ();
}
/**
 * Error reporting
 */
error_reporting ( E_ALL | E_STRICT );

/**
 * Compilation includes configuration file
 */
define ( 'MAGENTO_ROOT', getcwd () );
$lang_cookie_duration = time () + (365 * 24 * 60 * 60);

$compilerConfig = MAGENTO_ROOT . '/includes/config.php';
if (file_exists ( $compilerConfig )) {
	include $compilerConfig;
}

$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
$maintenanceFile = 'maintenance.flag';

if (! file_exists ( $mageFilename )) {
	if (is_dir ( 'downloader' )) {
		header ( "Location: downloader" );
	} else {
		echo $mageFilename . " was not found";
	}
	exit ();
}

if (file_exists ( $maintenanceFile )) {
	include_once dirname ( __FILE__ ) . '/errors/503.php';
	exit ();
}

require_once $mageFilename;

#Varien_Profiler::enable();


if (isset ( $_SERVER ['MAGE_IS_DEVELOPER_MODE'] )) {
	Mage::setIsDeveloperMode ( true );
}

#ini_set('display_errors', 1);


umask ( 0 );

//simulate on USA IP
//$_SERVER['IS_USA_IP']=1;

/*
if(stripos($_SERVER["HTTP_REFERER"], "sinalitedashboard")!==false){
	header ( 'Location: https://sinalite.com/', true, 301 );
	die ();
}
*/

//if users visits the server IP directly, then show nothing
if($_SERVER["SERVER_NAME"]=="127.0.0.1" || stripos($_SERVER["SERVER_NAME"], "192.168.0.")!==false || $_SERVER["SERVER_NAME"]=="192.168.0.244" || $_SERVER["SERVER_NAME"]=="216.138.238.153"){
	//if eprint customer tries to visit by the IP address, they will see sinalite SSL info, it's better redirect the visitor to google
	header ( 'Location: http://google.ca/' );
	die ();
}

//redirection for old subdomains
if (isset($_SERVER ['HTTP_HOST']) && stripos ( $_SERVER ['HTTP_HOST'], "fr.sinalite" ) !== false) {
	header ( 'Location: https://' . str_ireplace ( "fr.sinalite", "sinalite", $_SERVER ['HTTP_HOST'] ) . '/fr_ca/' );
	die ();
} else if (isset($_SERVER ['HTTP_HOST']) && stripos ( $_SERVER ['HTTP_HOST'], "usa.sinalite" ) !== false) {
	header ( 'Location: https://' . str_ireplace ( "usa.sinalite", "sinalite", $_SERVER ['HTTP_HOST'] ) . '/en_us/' );
	die ();
}

$mageRunCode = "";

if (isset($_SERVER ['HTTP_HOST']) && stripos ( $_SERVER ['HTTP_HOST'], "sinalite" ) !== false) {
	
	//if somebody visits these keywords, it will be redirected to sinalite U.S. index page
	if($_SERVER ['REQUEST_URI'] == "/p" || $_SERVER ['REQUEST_URI'] == "/ag" || $_SERVER ['REQUEST_URI'] == "/mp" || $_SERVER ['REQUEST_URI'] == "/pn" 
			|| $_SERVER ['REQUEST_URI'] == "/s" || $_SERVER ['REQUEST_URI'] == "/sn" 
			|| $_SERVER ['REQUEST_URI'] == "/bc" || $_SERVER ['REQUEST_URI'] == "/sb" || $_SERVER ['REQUEST_URI'] == "/20" || $_SERVER ['REQUEST_URI'] == "/30"){
		//header ( 'Location: https://sinalite.com/' );
		echo "
<!-- Google Analytics -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-23284600-1','auto');
        ga('send', 'pageview', '');
		
       	setTimeout(function(){
       		window.location='https://sinalite.com/en_us';
    	}, 2000);
    </script>
";
		die ();
	}
	
	//for "/fs", if it's from US IP, then go to US; else, go to CA
	if($_SERVER ['REQUEST_URI'] == "/fs"){
		if((isset($_SERVER['IS_USA_IP']) && $_SERVER['IS_USA_IP'] == 1) || isset($_COOKIE['is_usa'])){
			echo "
<!-- Google Analytics -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
					
        ga('create', 'UA-23284600-1','auto');
        ga('send', 'pageview', '');
					
       	setTimeout(function(){
       		window.location='https://sinalite.com/en_us';
    	}, 2000);
    </script>
";
			die ();
		}else{
			echo "
<!-- Google Analytics -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
					
        ga('create', 'UA-23284600-1','auto');
        ga('send', 'pageview', '');
					
       	setTimeout(function(){
       		window.location='https://sinalite.com/en_ca';
    	}, 2000);
    </script>
";
			die ();
		}
	}
	
	
	if($_SERVER ['REQUEST_URI'] == "/pc" ){
		echo "
<!-- Google Analytics -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
        ga('create', 'UA-23284600-1','auto');
        ga('send', 'pageview', '');
	
       	setTimeout(function(){
       		window.location='http://lp.sinalite.com/march-2017-mailer-promotion-us';
    	}, 2000);
    </script>
";
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/ps" || $_SERVER ['REQUEST_URI'] == "/gs"){
		//header ( 'Location: https://sinalite.com/' );
		echo "
<!-- Google Analytics -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
        ga('create', 'UA-23284600-1','auto');
        ga('send', 'pageview', '');
	
       	setTimeout(function(){
       		window.location='http://lp.sinalite.com/free-delivery';
    	}, 2000);
    </script>
";
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/free"){
		//header ( 'Location: https://sinalite.com/' );
		echo "
<!-- Google Analytics -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
        ga('create', 'UA-23284600-1','auto');
        ga('send', 'pageview', '');
	
       	setTimeout(function(){
       		window.location='http://lp.sinalite.com/free-delivery-magazine';
    	}, 2000);
    </script>
";
		die ();
	}
	//if somebody visits these keywords, it will be redirected to sinalite CA index page
	if($_SERVER ['REQUEST_URI'] == "/sm" || $_SERVER ['REQUEST_URI'] == "/ga" || $_SERVER ['REQUEST_URI'] == "/pa" || $_SERVER ['REQUEST_URI'] == "/al" || $_SERVER ['REQUEST_URI'] == "/pt" 
	|| $_SERVER ['REQUEST_URI'] == "/tph" || $_SERVER ['REQUEST_URI'] == "/kkp" || $_SERVER ['REQUEST_URI'] == "/sp" || $_SERVER ['REQUEST_URI'] == "/pnt" || $_SERVER ['REQUEST_URI'] == "/si" 
	|| $_SERVER ['REQUEST_URI'] == "/fa" || $_SERVER ['REQUEST_URI'] == "/in" || $_SERVER ['REQUEST_URI'] == "/mm" || $_SERVER ['REQUEST_URI'] == "/gc"){
		echo "
<!-- Google Analytics -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
        ga('create', 'UA-23284600-1','auto');
        ga('send', 'pageview', '');
	
       	setTimeout(function(){
       		window.location='https://sinalite.com/en_ca';
    	}, 2000);
    </script>
";
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/careers" ){
		header ( 'Location: https://sinalite.com/careers.html', true, 301);
		die ();
	}
	
	//if the current date & time is less than the specified date & time $eventDateBegin, then show old content; else show new content.
	$currentDate = new DateTime();
	$eventDateBegin = new DateTime('2017-02-01');
	
	if($_SERVER ['REQUEST_URI'] == "/en_ca/monthlypromo" ){
		if ($currentDate->getTimestamp() < $eventDateBegin->getTimestamp()){
			//not yet, show old content
			header ( 'Location: http://lp.sinalite.com/january-2017-monthly-special-ca', true, 301);
		}else{
			//already passed, show new content
			header ( 'Location: http://lp.sinalite.com/february-2017-monthly-special-ca-us', true, 301);
		}
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/en_us/monthlypromo" ){
		if ($currentDate->getTimestamp() < $eventDateBegin->getTimestamp()){
			//not yet, show old content
			header ( 'Location: http://lp.sinalite.com/january-2017-monthly-regular-us', true, 301);
		}else{
			//already passed, show new content
			header ( 'Location: http://lp.sinalite.com/february-2017-monthly-special-ca-us', true, 301);
		}
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/en_us/monthlypromo-special" ){
		if ($currentDate->getTimestamp() < $eventDateBegin->getTimestamp()){
			//not yet, show old content
			header ( 'Location: http://lp.sinalite.com/january-2017-monthly-special-us-promo', true, 301);
		}else{
			//already passed, show new content
			header ( 'Location: http://lp.sinalite.com/february-2017-monthly-special-usa-spl', true, 301);
		}
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/en_us/sitewide" ){
		header ( 'Location: http://lp.sinalite.com/january-2017-US-sitewide', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/en_us/marketing-assets" ){
		header ( 'Location: https://sinalite.com/en_us/marketing-assets-images', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/en_ca/december-promotion" ){
		header ( 'Location: http://lp.sinalite.com/december-2016-monthly-special', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/en_us/december-promotion" ){
		header ( 'Location: http://lp.sinalite.com/december-2016-monthly-special-us', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/en_us/december-promotion-special" ){
		header ( 'Location: http://lp.sinalite.com/december-2016-monthly-special-us-promo', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/fr_ca/december-promotion" ){
		header ( 'Location: http://lp.sinalite.com/december-2016-monthly-special-fr', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/pa18" || $_SERVER ['REQUEST_URI'] == "/ga18"){
		header ( 'Location: http://lp.sinalite.com/request-your-free-18pt-business-cards-samples', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/ny" ){
		echo "
<!-- Google Analytics -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
        ga('create', 'UA-23284600-1','auto');
        ga('send', 'pageview', '');
		
       	setTimeout(function(){
       		window.location='https://sinalite.com/en_us/top-sellers/business-cards/16pt-uv.html';
    	}, 2000);
    </script>
";
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/bg" ){
		header ( 'Location: http://lp.sinalite.com/sign-media-ad', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/pcu" ){
		header ( 'Location: http://lp.sinalite.com/nov-sale-pc', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/fl" ){
		header ( 'Location: http://lp.sinalite.com/nov-sale', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/holiday" ){
		header ( 'Location: http://lp.sinalite.com/print-action-ad', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/season" ){
		header ( 'Location: http://lp.sinalite.com/graphics-arts-ad', true, 301);
		die ();
	}
		
	if($_SERVER ['REQUEST_URI'] == "/ncr" ){
		header ( 'Location: http://lp.sinalite.com/free-ncr-form-samples', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/ncrfr" ){
		header ( 'Location: http://lp.sinalite.com/free-ncr-form-samples-fr', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/pi" ){
		echo "
<!-- Google Analytics -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
        ga('create', 'UA-23284600-1','auto');
        ga('send', 'pageview', '');
		
       	setTimeout(function(){
       		window.location='https://sinalite.com/en_us';
    	}, 2000);
    </script>
";
		die ();
	}
	
	//Sinalite websites
	if (stripos ( $_SERVER ['REQUEST_URI'], "/en_ca" ) !== false) {
		$mageRunCode = "en_ca";

		setcookie ( 'preferred_language', 'en', $lang_cookie_duration, '/' );
	} else if (stripos ( $_SERVER ['REQUEST_URI'], "/fr_ca" ) !== false) {
		$mageRunCode = "fr_ca";

		setcookie ( 'preferred_language', 'fr', $lang_cookie_duration, '/' );
	} else if (stripos ( $_SERVER ['REQUEST_URI'], "/en_us" ) !== false) {
		$mageRunCode = "en_us";

		setcookie ( 'preferred_language', 'en', $lang_cookie_duration, '/' );
	} else {
		
		//be careful: some visitor might visit https://sinalite.com/us_en/, "us_en" is eprint's store code, it must not show eprint website!
		if ((isset ( $_SERVER ['IS_USA_IP'] ) && $_SERVER ['IS_USA_IP'] == 1) || stripos ( $_SERVER ['REQUEST_URI'], "/us_en" ) !== false) {
			if(isset($_COOKIE['is_usa']) && $_COOKIE['is_usa'] == "1"){
				//$mageRunCode = "en_us";
				header ( 'Location: https://' . $_SERVER ['HTTP_HOST'].'/en_us', true, 301);
				die ();
			}else{
				//$mageRunCode = "en_ca";
				header ( 'Location: https://' . $_SERVER ['HTTP_HOST'].'/en_ca', true, 301);
				die ();
			}
			/*
			if ($_SERVER ['REQUEST_URI'] == "/") {
				//if user just visits sinalite.com root
				$mageRunCode = "en_us";
			} else {
				header ( 'Location: https://' . $_SERVER ['HTTP_HOST'].'/en_us'.$_SERVER ['REQUEST_URI'], true, 301);
				die ();
			}
			*/
		} else {
			//don't show sinalite index page if somebody visits https://sinalite.com/tickets
			if ($_SERVER ['REQUEST_URI'] == "/tickets") {
				header ( 'Location: https://google.ca/' );
				die ();
			}

			$allowed_langs = array ('en','fr' );
			$lang = 'en';
			// figure out if we have a preference in cookie
			if (isset ( $_COOKIE ['preferred_language'] )) {
				// get lang preference
				//var_dump($_COOKIE['preferred_language']); exit;
				$lang = $_COOKIE ['preferred_language'];
			} else { // no cookie value set
				if (! isset ( $_SERVER ['HTTP_ACCEPT_LANGUAGE'] ) || strlen ( $_SERVER ['HTTP_ACCEPT_LANGUAGE'] ) < 2) {
					$lang = 'en';
				} else {
					// until we have more languages than en / fr this should be good enough
					$lang = substr ( $_SERVER ['HTTP_ACCEPT_LANGUAGE'], 0, 2 ); // e.g. Accept-Language = en-US,fr;q=0.7,en;q=0.3
				}

				if (! in_array ( $lang, $allowed_langs )) {
					$lang = 'en';
				}

				// set in cookie the preference (en, fr)
				setcookie ( 'preferred_language', $lang, $lang_cookie_duration, '/' );
			}

			// validate the language
			if (! $lang || ! in_array ( $lang, $allowed_langs )) {
				$lang = 'en';
			}
			
			if (strtolower ( $lang ) == 'fr') {
				// go to fr_ca
				if ($_SERVER ['REQUEST_URI'] == "/") {
					//if user just visits sinalite.com root
					//$mageRunCode = "fr_ca";
					header ( 'Location: https://' . $_SERVER ['HTTP_HOST'].'/fr_ca', true, 301);
					die ();
				} else {
					header ( 'Location: https://' . $_SERVER ['HTTP_HOST'].'/fr_ca'.$_SERVER ['REQUEST_URI'], true, 301);
					die ();
				}
			} else {
				// go to en_ca
				if ($_SERVER ['REQUEST_URI'] == "/") {
					//if user just visits sinalite.com root
					//$mageRunCode = "en_ca";
					header ( 'Location: https://' . $_SERVER ['HTTP_HOST'].'/en_ca', true, 301);
					die ();
				} else {
					header ( 'Location: https://' . $_SERVER ['HTTP_HOST'].'/en_ca'.$_SERVER ['REQUEST_URI'], true, 301);
					die ();
				}
			}
		}
	}
} else if (isset($_SERVER ['HTTP_HOST']) && stripos ( $_SERVER ['HTTP_HOST'], "eprint" ) !== false) {
	//EPrint websites
	
	if($_SERVER ['REQUEST_URI'] == "/ebook" ){
		header ( 'Location: http://lpage.eprintfast.com/e-book', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/december-promotion" ){
		header ( 'Location: http://lpage.eprintfast.com/dec-promo-landing-page', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/black-friday" ){
		header ( 'Location: http://lpage.eprintfast.com/nov-flash-sale', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/bc" ){
		header ( 'Location: http://lpage.eprintfast.com/nov-flash-sale-bc', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/pcu" ){
		header ( 'Location: http://lpage.eprintfast.com/nov-flash-sale-pc', true, 301);
		die ();
	}
	
	//if the current date & time is less than the specified date & time $eventDateBegin, then show old content; else show new content.
	$currentDate = new DateTime();
	$eventDateBegin = new DateTime('2017-02-01');
	
	if(strtoupper($_SERVER ['REQUEST_URI']) == "/REM" ){
		if ($currentDate->getTimestamp() < $eventDateBegin->getTimestamp()){
			//not yet, show old content
			header ( 'Location: http://lpage.eprintfast.com/jan-rem-promo-landing-page-0', true, 301);
		}else{
			//already passed, show new content
			header ( 'Location: http://lpage.eprintfast.com/eprintfast-agent-march-sale-rem', true, 301);
		}
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/monthlypromo" ){
		if ($currentDate->getTimestamp() < $eventDateBegin->getTimestamp()){
			//not yet, show old content
			header ( 'Location: http://lpage.eprintfast.com/jan-landing-page-0', true, 301);
		}else{
			//already passed, show new content
			header ( 'Location: http://lpage.eprintfast.com/eprintfast-february-sale', true, 301);
		}
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/agentmonthlypromo" ){
		if ($currentDate->getTimestamp() < $eventDateBegin->getTimestamp()){
			//not yet, show old content
			header ( 'Location: http://lpage.eprintfast.com/eprintfast-agent-jan-promo-landing-page-0', true, 301);
		}else{
			//already passed, show new content
			header ( 'Location: http://lpage.eprintfast.com/eprintfast-february-sale-agent', true, 301);
		}
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/resellersitewide" ){
		header ( 'Location: http://lpage.eprintfast.com/eprintfast-february-sitewide-sale-resellers', true, 301);
		die ();
	}
	
	if($_SERVER ['REQUEST_URI'] == "/sitewide" ){
		header ( 'Location: http://lpage.eprintfast.com/eprintfast-february-sitewide-sale', true, 301);
		die ();
	}
	
	//if user just visits sinalite.com
	if (stripos ( $_SERVER ['REQUEST_URI'], "/ca_en" ) !== false) {
		$mageRunCode = "ca_en";
	} else if (stripos ( $_SERVER ['REQUEST_URI'], "/us_en" ) !== false) {
		$mageRunCode = "us_en";
	} else {
		//be careful: some visitor might visit https://eprintfast.com/en_us/, "en_us" is sinalite's store code, it must not show sinalite website!
		if ((isset ( $_SERVER ['IS_USA_IP'] ) && $_SERVER ['IS_USA_IP'] == 1) || stripos ( $_SERVER ['REQUEST_URI'], "/en_us" ) !== false) {
			
			if(isset($_COOKIE['is_usa']) && $_COOKIE['is_usa'] == "1"){
				//$mageRunCode = "us_en";
				header ( 'Location: https://' . $_SERVER ['HTTP_HOST'].'/us_en', true, 301);
				die ();
			}else{
				//$mageRunCode = "ca_en";
				header ( 'Location: https://' . $_SERVER ['HTTP_HOST'].'/ca_en', true, 301);
				die ();
			}
			
			/*
			if ($_SERVER ['REQUEST_URI'] == "/") {
				//if user just visits eprintfast.com root
				$mageRunCode = "us_en";
			} else {
				header ( 'Location: https://' . $_SERVER ['HTTP_HOST'].'/us_en'.$_SERVER ['REQUEST_URI'], true, 301);
				die ();
			}
			*/
		} else {
			if ($_SERVER ['REQUEST_URI'] == "/") {
				//if user just visits eprintfast.com root
				//$mageRunCode = "ca_en";
				header ( 'Location: https://' . $_SERVER ['HTTP_HOST'].'/ca_en', true, 301);
				die ();
			} else {
				header ( 'Location: https://' . $_SERVER ['HTTP_HOST'].'/ca_en'.$_SERVER ['REQUEST_URI'], true, 301);
				die ();
			}
		}
	}
} else {
	//Zuluprint backend
}

$_SERVER ['MAGE_RUN_CODE'] = $mageRunCode;
$_SERVER ['MAGE_RUN_TYPE'] = "store";

/* Store or website code */
$mageRunCode = isset ( $_SERVER ['MAGE_RUN_CODE'] ) ? $_SERVER ['MAGE_RUN_CODE'] : '';

/* Run store or run website */
$mageRunType = isset ( $_SERVER ['MAGE_RUN_TYPE'] ) ? $_SERVER ['MAGE_RUN_TYPE'] : 'store';

Mage::run ( $mageRunCode, $mageRunType );
