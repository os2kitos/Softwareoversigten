<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$debug = 0;


// Hook up to the db 
require_once 'dbConnect.php';


//
// Execute the desired AJAX command 
//
$command = $_POST['command'];
switch (trim($command)) {
    

    case 'getMatchingSystems':
        //
        // In this case, either a natural language search (in boolean mode, to enable substring matching) is conducted (if the search string is long enough) - othwerise an ordinary search is carried out:
        //

        $searchQuery = $_POST['query'];


	$currentD1UserName = "Anonymous";
        // Log the query
	/*if($_SERVER['REMOTE_USER'] != '')
	{
	        $currentD1UserName_parts = explode("\\", $_SERVER['REMOTE_USER']);
        	$currentD1UserName = $currentD1UserName_parts[1];
	}
*/
        // Insert idea into DB:
        DB::insert('usagelog',
            array('d1UserName' => $currentD1UserName,
             'searchString' => strip_tags($searchQuery)
             )
        );

        
        if (strlen($searchQuery) >= 3) { // Do natural language search in the MySQL DB:
            // Natural language search requires the MySQL db to maintain fulltext indexes on text-columns.
            // Natural language search also requires a minimum of 3 characters as search string - if this not fulfilled then this case simply reverts to an ordinary search approach.
            
            // Modifieres '+' and '-' MUST be succedded by one or more characters! Make sure that they are removed otherwise:
            $illegalSearchQueries_regEx = '/(\+|\-)+(?:\s|$)/'; // All '+' or '-' (in any amount) succeded by whitespace or end of string
            $searchQuery = preg_replace($illegalSearchQueries_regEx, "", $searchQuery);

            // Wrap the search query in wildcars ('*') in order to allow substring matching (photo --> photoshop).
            // However, only do this, if the first character is not a '+' or '-' 
            if ($searchQuery[0] == '+' || $searchQuery[0] == '-') {
                $mySqlSearchQuery = "'" . $searchQuery . "*'"; // Add wildcards on the end of the search query to allow substring matching (photo --> photoshop)
            } else {
                $mySqlSearchQuery = "'*" . $searchQuery . "*'"; // Add wildcards on both sides of the search query to allow substring matching (photo --> photoshop)
            }
            
           
           // Set a threshold for similarity when searching via natural language comparison, and define the MySQL query
            $minimumSimilarityScore = 0.3; 
            $res_minimumScores = array();
            $res = DB::query("SELECT Name, Description, UUID, id, MATCH (Name,LocalName,SupplierName,Description,KleName,BusinessType,SystemOwner_name,SystemOwner_email,ContactPerson_name,ContactPerson_email,ResponsibleOrganizationalUnit) AGAINST (" . $mySqlSearchQuery . " IN BOOLEAN MODE) AS score FROM cached_data ORDER BY score desc");
            
            // Filter out weak similarities
            for ($i=0; $i<sizeof($res); $i++) {
                if ($res[$i]['score'] > $minimumSimilarityScore) {
                    array_push($res_minimumScores, $res[$i]);
                }
            }

            // Convert textual links to active clickable ones in the description fields
            foreach ($res_minimumScores as &$systemArray_item) {
                $systemArray_item["Description"] = convertTextualLinkToClickableLink($systemArray_item["Description"]);
            }

            // Mark the search query in the results:
            if ($searchQuery !== "") {
                $searchWords = explode(" ", $searchQuery);
                foreach ($searchWords as $searchWord) {
                    if ($searchWord !== "" && strlen($searchWord) > 2) { // Only mark words longer than 2 characters:
                        foreach ($res_minimumScores as &$systemArray_item) {
                           $systemArray_item["Name"] = markSearchWordInText($searchWord, $systemArray_item["Name"]);
                           $systemArray_item["Description"] = markSearchWordInText($searchWord, $systemArray_item["Description"]);
                        }
                    }
                }
            }

            echo json_encode($res_minimumScores);

        } else { // Too small search input string.. => Do ordinary search instead:

            $mysqli_results_systems = DB::query("SELECT Name, Description, UUID, id
                        FROM `cached_data` 
                                WHERE (Name LIKE '%$searchQuery%' 
                                    OR LocalName LIKE '%$searchQuery%' 
                                    OR Description LIKE '%$searchQuery%' 
                                    OR KleName LIKE '%$searchQuery%' 
                                    OR Note LIKE '%$searchQuery%' 
                                    OR BusinessType LIKE '%$searchQuery%' 
                                    OR SystemOwner_name LIKE '%$searchQuery%' 
                                    OR ContactPerson_name LIKE '%$searchQuery%'
                                    OR ResponsibleOrganizationalUnit LIKE '%$searchQuery%'
                                ) ORDER BY Name
            ");


            // Convert textual links to active clickable ones in the description fields
            foreach ($mysqli_results_systems as &$systemArray_item) {
                $systemArray_item["Description"] = convertTextualLinkToClickableLink($systemArray_item["Description"]);
            }


            // Mark the search query in the results
            if ($searchQuery !== "" && strlen($searchQuery) > 2) { // Only mark words longer than 2 characters:
                foreach ($mysqli_results_systems as &$systemArray_item) {
                    $systemArray_item["Name"] = markSearchWordInText($searchWord, $systemArray_item["Name"]);
                    $systemArray_item["Description"] = markSearchWordInText($searchWord, $systemArray_item["Description"]);
                }
            }
            
            echo json_encode($mysqli_results_systems);
        }
        break;


    case 'getSystemDetails':
        $id = $_POST['id'];
        $searchQuery = $_POST['searchQuery'];
        $mysqli_results_systemDetails = DB::query("SELECT id,kitosID,UUID,SupplierName,Name,LocalName,Description,Url,KleName,BusinessType,SystemOwner_name,SystemOwner_email,OperationalResponsible_name,OperationalResponsible_email,ContactPerson_name,ContactPerson_email,ResponsibleOrganizationalUnit,TimeOfImport FROM `cached_data` WHERE id=%s", $id);
        
        // Mark the search query in the results
        if ($searchQuery !== "") {
                $searchWords = explode(" ", $searchQuery);
                foreach ($searchWords as $searchWord) {
                    if ($searchWord !== "" && strlen($searchWord) > 2) { // Only mark search strings longer than 2 characters:
                        $searchPattern = '/' . "(" . $searchWord . ")" . '/i';
                        $systemDetailsFieldsToMarkSearchWordsIn = array("kitosID", "Name", "SupplierName", "KleName", "BusinessType", "SystemOwner_name", "ContactPerson_name", "ResponsibleOrganizationalUnit", "OperationalResponsible_name");
                        foreach ($systemDetailsFieldsToMarkSearchWordsIn as $systemDetailField) {
                            $mysqli_results_systemDetails[0][$systemDetailField] = preg_replace($searchPattern, '<span class=\'searchQueryInResult\'>${1}</span>', $mysqli_results_systemDetails[0][$systemDetailField]);
                        }
                    }
                }
        }
        echo json_encode($mysqli_results_systemDetails[0]);
        break;

    case 'getUsageStats':
        $mysqli_results_statsDistinctUsers = DB::query("SELECT DISTINCT(d1UserName) FROM usagelog");
        $mysqli_results_statsQueryCount = DB::query("SELECT COUNT(searchString) as queryCount FROM usagelog WHERE searchString <> ''");
        $mysqli_results_statsTimeOfLastImport = DB::query("SELECT TimeOfImport from cached_data ORDER BY TimeOfImport DESC LIMIT 1;");
        $usageStats = array('amountOfDistinctUsers' => sizeof($mysqli_results_statsDistinctUsers), 'queryCount' => $mysqli_results_statsQueryCount[0]['queryCount'], 'timeOfLastDataImport' => $mysqli_results_statsTimeOfLastImport[0]['TimeOfImport']);
        echo json_encode($usageStats);
        break;

    case 'getAnalysisOfDataQuality':
        
        // Get all data and sort by ascending level of information (descending level of empty fields)
        $mysqli_results = DB::query("SELECT * FROM cached_data");
        $mysqli_results_sorted = sortMysqliResultsByDescendingAmountOfEmptyFields($mysqli_results);

        // Get the percentage of systems with important fields filled out
        $totalAmountOfSystems = sizeof($mysqli_results);
        $mysqli_results_missingSystemOwners = DB::query("SELECT count(*) as count FROM `cached_data` WHERE (SystemOwner_name = '' OR SystemOwner_name = null)");
        $mysqli_results_missingContactPersons = DB::query("SELECT count(*) as count FROM `cached_data` WHERE (ContactPerson_name = '' OR ContactPerson_name = null)");
        $mysqli_results_missingOperationalResponsibles = DB::query("SELECT count(*) as count FROM `cached_data` WHERE (OperationalResponsible_name = '' OR OperationalResponsible_name = null)");
        $dataCompletion_systemOwner = round((($totalAmountOfSystems - $mysqli_results_missingSystemOwners[0]['count']) / $totalAmountOfSystems) * 100, 1);
        $dataCompletion_contactPerson = round((($totalAmountOfSystems - $mysqli_results_missingContactPersons[0]['count']) / $totalAmountOfSystems) * 100, 1);
        $dataCompletion_operationalResponsible = round((($totalAmountOfSystems - $mysqli_results_missingOperationalResponsibles[0]['count']) / $totalAmountOfSystems) * 100, 1);
        $dataCompletionPercentages = array('systemOwner' => $dataCompletion_systemOwner, 'contactPerson' => $dataCompletion_contactPerson, 'operationalResponsible' => $dataCompletion_operationalResponsible);

        echo json_encode(array('allSystemsSortedByAscendingInformationLevel' => $mysqli_results_sorted, 'dataCompletionPercentages' => $dataCompletionPercentages));
        break;


}

function convertTextualLinkToClickableLink($input) {

    $searchPattern = '/((?:https*|ftps*:\/\/|www).*?)(?:\.*)(?:\s|$)/i';
    $output = preg_replace($searchPattern, '<span class=\'hyperlinkInDescription\' style=\'display:block;\'><a href=\'${1}\' target=\'_blank\' title=\'Tryk her for at åbne dette link i et nyt vindue.\'>${1}</a></span>', $input);

    return $output;
}

function markSearchWordInText($searchWord, $text) {
    $searchWord = str_replace("+", "\+", $searchWord); // Handle uses of '+' in the search query - which needs to be escaped in the search pattern for preg_replace
    $searchPattern = '/' . "(" . $searchWord . ")" . '/im';
    $text = preg_replace($searchPattern, '<span class="searchQueryInResult">${1}</span>', $text);

    // Make sure to remove search word marking inside active hyperlinks, as these mess up the final urls:
    $searchPatternMarkedSearchWordInActiveLink = '/<span class=\'hyperlinkInDescription\' .*?>.*<a href=\'.*?(<\/*span.*?>).*(<\/*span.*?>).*?\'.*<\/span>/im';
    $text = preg_replace_callback($searchPatternMarkedSearchWordInActiveLink, 
    function($matches) { 
        $completeHref = $matches[0];
        $completeHref = str_replace($matches[1], "", $completeHref);
        $completeHref = str_replace($matches[2], "", $completeHref);
        return $completeHref;
    }, $text);

    return $text;
}



function convertDateFromDKToUSFormat($input) {
	// Function for converting dates in DK format to US format, which is used by MySQL in datetime fields (used when transforming the form input into MySQL content)
	$dateParts = explode("-", $input);
	$output = $dateParts[2] . "-" . $dateParts[1] . "-" . $dateParts[0];
	return $output;
}

function convertDateFromUSToDKFormat($input) {
    // Function for converting dates in US format to DK format, which is used in the frontend
	$dateParts = explode(" ", $input);
    $dateParts2 = explode("-", $dateParts);
	$output = $dateParts[2] . "-" . $dateParts[1] . "-" . $dateParts[0];
	return $output;
}


function sortMysqliResultsByDescendingAmountOfEmptyFields($resultsArray) {
    // Count, and add, the amount of empty fields for each system:
    foreach ($resultsArray as &$itSystem) {
        $amountOfEmptyFields = 0;
        $fieldsToCheckForEmptyness = array("SupplierName", "Description", "Url", "KleName", "BusinessType", "SystemOwner_name", "OperationalResponsible_name", "ContactPerson_name", "ResponsibleOrganizationalUnit");
        foreach ($fieldsToCheckForEmptyness as $fieldName) {
            if ($itSystem[$fieldName] == null || $itSystem[$fieldName] == "") {
                $amountOfEmptyFields++;
            } 
        }
        $itSystem['amountOfEmptyFields'] = $amountOfEmptyFields;
    }

    // Sort by descending amount of empty columns
    function sortByDescendingAmountOfEmptyFields($a, $b) {
        $output = $b['amountOfEmptyFields'] - $a['amountOfEmptyFields']; // First sort by amount of empty fields
        $output .= $a['Name'] > $b['Name']; // Then sort by name
        return $output;
    }
    usort($resultsArray, 'sortByDescendingAmountOfEmptyFields');
    return $resultsArray;
}



?>
