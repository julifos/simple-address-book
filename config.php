<?php
define("DB_SERVER", "YOUR_SERVER_HERE"); // MySQL server
define("DB_USER", "YOUR_USERNAME_HERE"); // DB user
define("DB_PASS", "YOUR_PASSWORD_HERE"); // DB password
define("DB_NAME", "YOUR_DATABASE_NAME_HERE"); // DB name
define("CONTACTS_PER_SHEET",144); // max contacts to show per page
define("LANGUAGE","YOUR_LANGUAGE_HERE"); // any of those available in the "lang" dir

// follows optional config stuff

// telephone matching regex patterns, includes Spain, US, UK, Brazil, Portugal, France, Germany, Japan, Italy, Netherlands, Russia, China, India
// You can add your own pattern, appending it to this array
const PHONE_REGEX_PATTERNS = array('/^(\+34|0034|34)?[\s|\-|\.]?[6|7|8|9][\s|\-|\.]?([0-9][\s|\-|\.]?){8}$/gi', // Spain+
											  '/(\d{9}|\d{4} \d{9})/g', // Spain
											  '/^[\\(]{0,1}([0-9]){3}[\\)]{0,1}[ ]?([^0-1]){1}([0-9]){2}[ ]?[-]?[ ]?([0-9]){4}[ ]*((x){0,1}([0-9]){1,5}){0,1}$/gi', // US
											  '/((\(\d{3}\)?)|(\d{3}))([\s-./]?)(\d{3})([\s-./]?)(\d{4})/gi', // US
											  '/(\s*\(?0\d{4}\)?\s*\d{6}\s*)|(\s*\(?0\d{3}\)?\s*\d{3}\s*\d{4}\s*)/gi', // UK
											  '/^((\(([1-9]{2})\))(\s)?(\.)?(\-)?([0-9]{0,1})?([0-9]{4})(\s)?(\.)?(\-)?([0-9]{4})|(([1-9]{2}))(\s)?(\.)?(\-)?([0-9]{0,1})?([0-9]{4})(\s)?(\.)?(\-)?([0-9]{4}))$/gi', // Brazil
											  '/^(\+351|00351|351)?[\s|\-|\.]?[2|9][\s|\-|\.]?([0-9][\s|\-|\.]?){8}$/gi', // Portugal
											  '/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/gi', // France
											  '/^(?:([+][0-9]{1,2})+[ .-]*)?([(]{1}[0-9]{1,6}[)])?([0-9 .-/]{3,20})((x|ext|extension)[ ]?[0-9]{1,4})?$/gi', // Germany
											  '/^\d{2}(?:-\d{4}-\d{4}|\d{8}|\d-\d{3,4}-\d{4})$/gi', // Japan
											  '/^(\((00|\+)39\)|(00|\+)39)?(38[890]|34[7-90]|36[680]|33[3-90]|32[89])\d{7}$/gi', // Italy
											  '/(^\+[0-9]{2}|^\+[0-9]{2}\(0\)|^\(\+[0-9]{2}\)\(0\)|^00[0-9]{2}|^0)([0-9]{9}$|[0-9\-\s]{10}$)/gi', // Netherlands
											  '/^(\+7|7|8)?[\s\-]?\(?[489][0-9]{2}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/gi', // Russia
											  '/^(13[0-9]|14[57]|15[012356789]|17[0678]|18[0-9])[0-9]{8}$/gi', // China
											  '/^(0|\+91)?[789]\d{9}$/gi' // India
);


/* * * * * * * * * * * * * * * * *
*                                *
* NO MORE EDITS BEYOND THIS LINE *
*                                *
* * * * * * * * * * * * * * * * **/

if(LANGUAGE!=("YOUR_LA"."NGUAGE_HERE")){
	$json = file_get_contents(__DIR__."/lang/locale_".LANGUAGE.".json", TRUE);
	$jsonIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator(json_decode($json)),RecursiveIteratorIterator::SELF_FIRST);

	foreach ($jsonIterator as $key => $val) define($key,$val);
}
?>
