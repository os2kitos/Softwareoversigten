<?php
//$currentD1UserName_parts = explode("\\", $_SERVER['REMOTE_USER']);

$currentD1UserName = $_GET['user'];
if($currentD1UserName == '')
  $currentD1UserName = 'Anonymous';


//$currentD1UserName = $currentD1UserName_parts[1];

// Load array of Kitos administrators and set site admin d1 user
require('kitosAdministrators.php');
$siteAdministratorD1Name = "ital";


// Get current total amount of IT systems in use in Holstebro Kommune:
require_once 'dbConnect.php';
$mysqli_results_systems = DB::query("SELECT Name FROM `cached_data`");
$totalAmountOfSystems = sizeof($mysqli_results_systems);
if ($totalAmountOfSystems < 1) {
    exit;
}
?>
<!doctype html>
<html class="no-js" lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=10; IE=9; IE=8; IE=7; IE=EDGE" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="graphics/favicon.ico" />
        <title>Softwareoversigten</title>
        <link rel="stylesheet" href="css/foundation.css">
        <link rel="stylesheet" href="css/app.css">
        <!-- Foundation icons -->
        <link rel="stylesheet" href="css/foundation-icon-font-3/foundation-icons/foundation-icons.css">

        <!-- Custom fonts -->
        <link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet"> 

        <!-- Styling -->
        <link rel="stylesheet" href="css/styling.css">

        
    </head>
    <body>

        <div id="footerLoadingSpinner">Henter data <img src="graphics/hourglass.gif"></div>

        <div id="welcomeMsgBox">
            <div style="display: table-cell; height: 100%; width:100%; vertical-align: middle; text-align: center; color:white; font-size:1.6em; font-family: 'Oswald', sans-serif;">
                <img class="watermark" src="graphics/logo.png">
                <div style="font-size:4em; text-shadow:1px 1px gray;">Velkommen</div>
                I Holstebro Kommune har vi lige nu <span id="totalAmountOfSystemsInUse" style="font-size:1.5em;"><?php echo $totalAmountOfSystems;?></span> forskellige IT-systemer. På denne side kan du:<br>
                <div style="display: inline-block; text-align: left;">
                    <ul>
                        <li><span class="highlightedWelcomeMsgWord">Læse</span> om vores nuværende systemer.</li>
                        <li>Undersøge om vi har et system der kan det du leder efter, <span class="highlightedWelcomeMsgWord">før du køber et nyt</span>.</li>
                        <li>Finde informationer om <span class="highlightedWelcomeMsgWord">systemejer</span> og <span class="highlightedWelcomeMsgWord">kontaktperson</span> for et specifikt system.</li>
                    </ul>
                </div>
                <br><br>
                Indtast søgeord i feltet øverst for at starte søgning, eller tryk Esc for at vise alle systemer.<br>
                <span style="font-size:0.9em; vertical-align: middle;">Tryk på <img src="graphics/helpButton2.png" style="height:0.9em; vertical-align: middle;"> i øverste højre hjørne for at læse mere om hvordan du kan bruge denne side.</span>
            </div>
        </div>

        <div style="display: table; height:100%; width:100%;">
            
            <div style="display: table-row; height:8%;">
                <div style="display: table-cell; width:100%; vertical-align: middle;" class="header">
                    &nbsp;<img src="graphics/database.png"><span class="systemTitleInHeader2">Softwareoversigten</span> <span style="font-size:0.3em; text-shadow:none; font-weight:normal; color:orange;">
                        Version 1.2
                        <?php
                        if ($currentD1UserName == $siteAdministratorD1Name) { // If site admin then show usage stats: (U)nique users, (Q)uery count and (Sync) time for last data import
                            $mysqli_results_statsDistinctUsers = DB::query("SELECT DISTINCT(d1UserName) FROM usagelog");
                            $mysqli_results_statsQueryCount = DB::query("SELECT COUNT(searchString) as queryCount FROM usagelog WHERE d1UserName <> 'd1jakop'");
                            $mysqli_results_statsTimeOfLastImport = DB::query("SELECT TimeOfImport from cached_data ORDER BY TimeOfImport DESC LIMIT 1");
                            
                            $syncDate = explode(" ", $mysqli_results_statsTimeOfLastImport[0]['TimeOfImport'])[0];
                            $syncDateParts = explode("-", $syncDate);
                            $syncDateInDKFormat = $syncDateParts[2] . "-" . $syncDateParts[1] . "-" . $syncDateParts[0];
                            $usageStatsLabel = "&nbsp;<table id='usageStatsTable'><tr><th>U</th><td>" . sizeof($mysqli_results_statsDistinctUsers) . "</td><th>Q</th><td>"  . $mysqli_results_statsQueryCount[0]['queryCount'] . "</td><th>Sync</th><td>" . $syncDateInDKFormat . "</td></tr></table>";
                            echo $usageStatsLabel;
                        }
                        ?>
                    </span>
                    
                    <span style="position:absolute; right:6px; font-size:0.4em; top:6px;">
                        &copy; 2018 IT & Digitalisering, Holstebro Kommune
                        <span style="display: block; text-align:right;">
                            <?php
                            if (in_array($currentD1UserName, $kitosAdministrators)) { // A Kitos admin is viewing, so display a data analysis link
                                echo '<span id="dataAnalysisButton" title="Tryk her for at identificere manglende datakvalitet."><span class="fi-widget"></span> Analysér datakvalitet</span>';
                            }
                            ?>
                            <img id="helpButton" class="hoverGrow" src="graphics/helpButton2.png" style="margin:4px; padding:0px; height:2em; vertical-align: middle;" title="Tryk for for at læse om hvordan du kan bruge denne løsning.">
                        </span>
                    </span>
                </div>
            </div>
            
             <div style="display: table-row; height:10%;">
                <div style="display: table-cell; width:100%; vertical-align:middle; text-align: center;">
                    <center>
                        <input type="search" id="searchQuery" style="width:75%; height: 50%; margin:0px; font-size:1.5em;" placeholder="Indtast søgeord">
                    </center>
                </div>
            </div>
           
            <div style="display: table-row; height:82%;">
                
                <div id="resultsContainer" style="display: table-cell; width:100%; visibility: hidden;"> 
                    
                    <div id="twoColumnMainTable" style="display: table; height: 100%; width: 100%;">
                        <div style="display: table-row; height: 100%;">
                            <div id="leftColumn" style="display: table-cell; width: 60%;" class="paddedContent"> <!-- Search results section begin -->      
                                <div class="columnTitle"><span id="amountOfResultsFoundLabel"></span> Matchende system<span id="multipleResultsFoundLabelEnd">er</span><span id="sortedByDescendingLevelOfMissingInfoLabel"> (Sorteret efter manglende informationsniveau)</span></div>
                                
                                <div id="scrollableResultsDIV" style="width:59%; height:600px; overflow: auto; position:absolute;"> <!-- May overlap the details column in narrow-width conditions -->
                                    <table class="searchResultsTable" id="searchResultsTable" style="background:transparent !important;">
                                        <tbody style="background:transparent !important;">
                                            
                                        </tbody>
                                    </table>
                                </div>

                            </div> <!-- Search results section end -->

                            <div id="rightColumn" style="display: table-cell; width: 40%;" class="paddedContent"> <!-- Details section begin -->
                                <div class="columnTitle">Systemdetaljer</div>
                                
                                <div id="scrollableDetailsDIV" style="width:40%; height:600px; overflow: auto; position:absolute;">  <!-- Scrollable div begin -->
                                    <table class="systemDetailsTable">
                                        
                                        <tr>
                                            <th><span class="fi-info" title="Systemets officielle navn hos leverandøren."></span>Systemnavn</th>
                                            <td style='font-size:1.1em; background:#502e2e'><span id="systemDetails_name"></span></td>
                                        </tr>
                                        <tr>
                                            <th><span class="fi-bookmark" title="Systemets interne kaldenavn i Holstebro Kommune."></span>Lokalt navn</th>
                                            <td><span id="systemDetails_localName"></span></td>
                                        </tr>
                                        <tr>
                                            <th><span class="fi-home" title="Leverandøren af systemet."></span>Leverandør</th>
                                            <td><span id="systemDetails_supplierName"></span></td>
                                        </tr>
                                        <tr>
                                            <th><span class="fi-web" title="Link til yderligere information om systemet."></span>Link</th>
                                            <td><span id="systemDetails_url"></span></td>
                                        </tr>
                                        <tr>
                                            <th><span class="fi-list" title="Systemets funktion beskrevet via KL Emnesystematik."></span>KLE</th>
                                            <td><span id="systemDetails_kle"></span></td>
                                        </tr>
                                        <tr>
                                            <th><span class="fi-eye" title="Forretningstypen som systemet understøtter."></span>Forretningstype</th>
                                            <td><span id="systemDetails_businessType"></span></td>
                                        </tr>
                                        <tr>
                                            <th><span class="fi-torso-business" title="Systemejeren er den chef, der har ledelsesmæssige beføjelser over de forretningsprocesser, som systemet er med til at understøtte og har ansvaret for gevinstrealiseringen."></span>Systemejer</th>
                                            <td><span id="systemDetails_systemOwner"></span></td>
                                        </tr>
                                        <tr>
                                            <th><span class="fi-telephone" title="Systemets kontaktperson, som typisk er en superbruger med solidt kendskab til systemet og dets anvendelse."></span>Kontaktperson</th>
                                            <td><span id="systemDetails_contactPerson"></span></td>
                                        </tr>
                                        <tr class="highlightedSystemDetailsRow">
                                            <th><span class="fi-alert" title="Den ansvarlige for systemet - som kan bistå systemejeren med opgaver af forskellig karakter."></span>Systemansvarlig</th>
                                            <td><span id="systemDetails_operationalResponsible"></span></td>
                                        </tr>
                                        <tr>
                                            <th><span class="fi-torsos-all" title="Organisatorisk enhed i Holstebro Kommune med ansvar for systemet."></span>Ansvarlig org. enhed</th>
                                            <td><span id="systemDetails_responsibleOrgUnit"></span></td>
                                        </tr>
                                    </table>

                                    <div class="columnTitle_administrativeFunctionality dataAnalysisInformation"><span class="fi-widget" title="Baseret på en analyse af den samlede mængde af systemer i Holstebro Kommune."></span> Tilgængelighed af kritisk information iblandt alle systemer</div>
                                    <table class="dataAnalysisTable dataAnalysisInformation">
                                        <tr>
                                            <th><span class="fi-torso-business" title="Systemejeren er den chef, der har ledelsesmæssige beføjelser over de forretningsprocesser, som systemet er med til at understøtte og har ansvaret for gevinstrealiseringen."></span>Systemejer</th>
                                            <td>
                                                <div id="dataAnalysis_systemOwner" class="progress" role="progressbar" tabindex="0" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="margin-bottom:0px;">
                                                  <span class="progress-meter" style="width: 0%">
                                                    <p class="progress-meter-text">0%</p>
                                                  </span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><span class="fi-telephone" title="Systemets kontaktperson, som typisk er en superbruger med solidt kendskab til systemet og dets anvendelse."></span>Kontaktperson</th>
                                            <td>
                                                <div id="dataAnalysis_contactPerson" class="progress" role="progressbar" tabindex="0" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="margin-bottom:0px;">
                                                  <span class="progress-meter" style="width: 0%">
                                                    <p class="progress-meter-text">0%</p>
                                                  </span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><span class="fi-alert" title="Den ansvarlige for systemet - som kan bistå systemejeren med opgaver af forskellig karakter."></span>Systemansvarlig</th>
                                            <td>
                                                <div id="dataAnalysis_operationalResponsible" class="progress" role="progressbar" tabindex="0" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="margin-bottom:0px;">
                                                  <span class="progress-meter" style="width: 0%">
                                                    <p class="progress-meter-text">0%</p>
                                                  </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                </div> <!-- Scrollable div end -->

                            </div> <!-- Details section end -->

                        </div>
                    </div>

                </div> 
            </div>

        </div>




        <script src="js/jquery-3.2.1.min.js"></script>
        <script src="js/vendor/what-input.js"></script>
        <script src="js/vendor/foundation.min.js"></script>
        <script src="js/app.js"></script>
        <script src="js/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>

        <!-- Sweetalert functionality -->
        <script src="js/sweetalert2-master/dist/es6-promise.auto.min.js"></script> <!-- for IE support -->
        <script src="js/sweetalert2-master/dist/sweetalert2.min.js"></script>
        <link rel="stylesheet" href="js/sweetalert2-master/dist/sweetalert2.min.css">

        <!-- Animate css functionality -->
        <link rel="stylesheet" href="js/animate.css-master/animate.min.css">

        <script>

            var timeOutBeforeDoingSearch_global;

            // Set the fixed height of the results table (needs absolute height in order to be scrollable)
            var availableResultsTableHeight = $("#twoColumnMainTable").height() * 0.9;
            $("#scrollableResultsDIV").height(availableResultsTableHeight);
            $("#scrollableDetailsDIV").height(availableResultsTableHeight);


            //
            // Add an event handler to the search input field and clear it by default
            $(document.body).on('paste keyup', '#searchQuery', function(event, ui){ 
              if (event.originalEvent.code == "Escape" || event.originalEvent.key == "Esc") {
                $(this).val(''); // Clear the input field if the user clicks ESC
                $("#scrollableResultsDIV").scrollTop(0); // Scroll the results list to the top
              }
              if (event.keyCode == '38' || event.keyCode == '40') {
                return; // ignore arrow up/down keys
              }
              applyDelayBeforeDoingSearch();
            });
            $("#searchQuery").val('');


            //
            // Add an event handler to all systems in the results list in order for the user to click on them for the viewing of system details
            $(document.body).on('click', '.deSelectedSystemInSearchResults', function(event, ui) {
                var selectedId = $(this).attr('id');
                selectSpecificSystemInResultsList(selectedId);
            });


            <?php
            if (in_array($currentD1UserName, $kitosAdministrators)) { // A Kitos admin is viewing, so display a data analysis link
                echo "//\n";
                echo "// Add an event handler to the data analysis button\n";
                echo "$(document.body).on('click', '#dataAnalysisButton', function(event, ui) {\n";
                    echo "conductAnalysisOfDataQuality();\n";
                echo "})\n";
            }
            ?>
            

            function selectSpecificSystemInResultsList(id) {
                // Remove the markup of the currenlty selected system:
                $('#searchResultsTable > tbody > tr').removeClass('selectedSystemInSearchResults');
                $('#searchResultsTable > tbody > tr').addClass('deSelectedSystemInSearchResults');
                $('#searchResultsTable > tbody > tr').attr('title', 'Tryk for at vise detaljer for dette system.');
                // Add markup to the newly selected system:
                $('#searchResultsTable > tbody > tr[id=' + id + ']').removeClass('deSelectedSystemInSearchResults');
                $('#searchResultsTable > tbody > tr[id=' + id + ']').addClass('selectedSystemInSearchResults').foundation();
                $('#searchResultsTable > tbody > tr[id=' + id + ']').attr('title', '');
                // Load system details for the selected system:
                getSystemDetails(id);
            }


            //
            // Add an event handler to arrow-buttons being pressed, in which case the currently selected system in the results list is moved (if more than one result is shown)
            $(document.body).keydown(function(e) {
              
              // Get curent key pressed
              e = e || window.event;
              var keyCode = e.keyCode;

              // Get current list of results
              var resultList = $('#searchResultsTable > tbody > tr');
              var amountOfResultsShown = $(resultList).length;
              if (amountOfResultsShown < 2) {
                return; // Do not move the selected system marker in lists with less than 2 entires
              }

              if (keyCode == '38') { // Up arrow pressed
                // Traverse the list of results and make the previous system selected
                for (var i=0; i < amountOfResultsShown; i++) {
                    if ($(resultList)[i]['className'] == "selectedSystemInSearchResults" && i > 0) {
                        selectSpecificSystemInResultsList($(resultList)[i-1]['id']);

                        // Check if the newly selected system is still visible within the viewport of the scrollable div (https://coderwall.com/p/fnvjvg/jquery-test-if-element-is-in-viewport)
                        var viewport = {
                            top: $("#scrollableResultsDIV").offset().top,
                            left: $("#scrollableResultsDIV").offset().left
                        };
                        viewport.right = viewport.left + $("#scrollableResultsDIV").width();
                        viewport.bottom = viewport.top + $("#scrollableResultsDIV").height();

                        // When possible make sure that the system before the newly selected result is visible
                        if (i > 1) {
                            var stepCount = i - 2; // Make sure that the system _above_ the newly selected system (-1) is visible (--> -2) 
                        } else {
                            var stepCount = i - 1; // Make sure that the newly selected system (-1) is visible
                        }
                        
                        var bounds = $('#searchResultsTable > tbody > tr[id=' + $(resultList)[stepCount]['id'] + ']').offset(); 
                        bounds.right = bounds.left + $('#searchResultsTable > tbody > tr[id=' + $(resultList)[stepCount]['id'] + ']').outerWidth();
                        bounds.bottom = bounds.top + $('#searchResultsTable > tbody > tr[id=' + $(resultList)[stepCount]['id'] + ']').outerHeight();

                        var elementVisible = (!(viewport.bottom < bounds.top || viewport.top > bounds.bottom));
                        if (!elementVisible) { // The element is no longer visible - scroll to make it visible again:
                            var currentScrollPosition = $("#scrollableResultsDIV").scrollTop();
                            var newScrollPosition = currentScrollPosition - (2.5*$('#searchResultsTable > tbody > tr[id=' + $(resultList)[i-2]['id'] + ']').outerHeight());
                            $("#scrollableResultsDIV").scrollTop(newScrollPosition); // Set the amount of pixels that are hidden above the view
                        }

                        break; // Do not loop any further - the selected result was found and moved
                    } 
                }
                return false; // Do not move the cursor in the search input field
              }
              else if (keyCode == '40') { // Down arrow pressed
                
                // Traverse the list of results and make the next system selected
                for (var i=0; i < amountOfResultsShown; i++) {
                    if ($(resultList)[i]['className'] == "selectedSystemInSearchResults" && i < (amountOfResultsShown-1)) {
                        selectSpecificSystemInResultsList($(resultList)[i+1]['id']);
                        
                        // Check if the newly selected system is still visible within the viewport of the scrollable div (https://coderwall.com/p/fnvjvg/jquery-test-if-element-is-in-viewport)
                        var viewport = {
                            top: $("#scrollableResultsDIV").offset().top,
                            left: $("#scrollableResultsDIV").offset().left
                        };
                        viewport.right = viewport.left + $("#scrollableResultsDIV").width();
                        viewport.bottom = viewport.top + $("#scrollableResultsDIV").height();

                        // When possible make sure that the system after the newly selected result is visible
                        if (i < (amountOfResultsShown-2)) {
                            var stepCount = i + 2; // Make sure that the system _below_ the newly selected system (+1) is visible (--> +2)
                        } else {
                            var stepCount = i + 1; // Make sure that the newly selected system (+1) is visible
                        }
                        var bounds = $('#searchResultsTable > tbody > tr[id=' + $(resultList)[stepCount]['id'] + ']').offset();
                        bounds.right = bounds.left + $('#searchResultsTable > tbody > tr[id=' + $(resultList)[stepCount]['id'] + ']').outerWidth();
                        bounds.bottom = bounds.top + $('#searchResultsTable > tbody > tr[id=' + $(resultList)[stepCount]['id'] + ']').outerHeight();

                        var elementVisible = (!(viewport.bottom < bounds.top || viewport.top > bounds.bottom));
                        if (!elementVisible) { // The element is no longer visible - scroll to make it visible again:
                            var currentScrollPosition = $("#scrollableResultsDIV").scrollTop();
                            var newScrollPosition = currentScrollPosition + (2.5*$('#searchResultsTable > tbody > tr[id=' + $(resultList)[i+2]['id'] + ']').outerHeight());
                            $("#scrollableResultsDIV").scrollTop(newScrollPosition); // Set the amount of pixels that are hidden above the view
                        }

                        break; // Do not loop any further - the selected result was found and moved
                    }
                }
                return false; // Do not move the cursor in the search input field
              }

            });

    
            //
            // Bind action handler to the help button
            $(document.body).on('click', '#helpButton', function(event, ui) {
                var helpMsg = "<div style='text-align:left; font-size:0.8em;'>";
                helpMsg += "<br><h6>Med denne løsning kan du få et overblik over hvilke IT-løsninger vi pt. anvender i Holstebro Kommune, og kan samtidig finde oplysninger om kontaktpersoner m.m. for de enkelte produkter.</h6><br>";
                helpMsg += "<span style='background:lightblue; padding:8px;'>Fremsøg en specifik IT-løsning ved at indtaste søgeord i søgefeltet øverst på siden.</span><br><br><br>";
                helpMsg += "<img src='graphics/info2.png' style='vertical-align:middle; height:1em;'> Søgeresultater vises sorteret efter relevans, med mest relevante resultater øverst.<br><br>";
                helpMsg += "<img src='graphics/info2.png' style='vertical-align:middle; height:1em;'> Tilføj et '-' foran ord der <i>ikke</i> må indgå i søgeresultatet.<br><br>";
                helpMsg += "<img src='graphics/info2.png' style='vertical-align:middle; height:1em;'> Få vist detaljer for at fundet system ved at trykke på det med musen, eller flytte markøren hen på det med pil op/ned-knapperne.<br><br>";
                helpMsg += "<img src='graphics/info2.png' style='vertical-align:middle; height:1em;'> Du kan skrive en mail til en systemejer eller kontaktperson ved at trykke på dennes emailadresse under 'Systemdetaljer'.<br><br>";
				helpMsg += "<img src='graphics/info2.png' style='vertical-align:middle; height:1em;'> Data indlæses én gang dagligt fra KITOS.<br><br>";
                helpMsg += "<img src='graphics/edit.png' style='vertical-align:middle; height:1.5em;'> Hvis du har rettelser til de registrerede informationer, eller behov for at få oprettet et nyt system, så kontakt venligst din lokale KITOS administrator.";
                helpMsg += "</div>";
                showMessage('Softwareoversigten', helpMsg);
            });

            function showMessage(headline, content) {
              swal({
                title: headline,
                width: 600,
                text: content,
                html: content
              });
            }

            


            function applyDelayBeforeDoingSearch() {
                //
                // Function for making sure that the user is done typing a search query before the search is carried out
                //
                clearTimeout(timeOutBeforeDoingSearch_global); 
                timeOutBeforeDoingSearch_global = setTimeout(function() {
                  var searchQuery = $("#searchQuery").val();
                  doSearch(searchQuery);
                  return false;
                }, 250);
            }

            function doSearch(query) {   

                // Hide the welcome msg box (if still visible)
                $("#welcomeMsgBox").hide();

                // Show results container
                $("#resultsContainer").css('visibility', 'visible');

                // This is a normal listing, so do not tell the user that data is sorted by descending level of missing information
                $("#sortedByDescendingLevelOfMissingInfoLabel").hide();

                // Hide the data analysis information elements
                $( ".dataAnalysisInformation" ).hide();

                // Show loading spinnner
                showLoadingSpinner();

                $.ajax({
                    url: 'commandParser.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {command: 'getMatchingSystems', query: query},
                    success: function(responseData) {

                        // Traverse all projects one by one
                        var totalAmountOfSystems = responseData.length;
                        $("#amountOfResultsFoundLabel").html(totalAmountOfSystems);
                        if (totalAmountOfSystems == 1) {
                            $("#multipleResultsFoundLabelEnd").hide();
                        } else {
                            $("#multipleResultsFoundLabelEnd").show();
                        }
                        
                        // If no results are found then inform the user about this:
                        if (totalAmountOfSystems < 1) {
                          $('#searchResultsTable > tbody > tr').remove();
                          var htmlMsg = "<tr><td colspan='2' style='text-align:center; background:#65512d !important; color:white; font-size:1.5em; margin:10px; font-weight:bold;'>Ingen resultater fundet<br><span style='font-size:0.8em; font-weight:normal;'>Har du evt. stavet forkert?</span></td></tr>";
                          $('#searchResultsTable > tbody').append(htmlMsg).foundation();
                          clearSystemDetails();
                          hideLoadingSpinner();

                          return;
                        }
                        
                        // Remove current projects (if any) from the table
                        $('#searchResultsTable > tbody > tr').remove();

                        // Render the results one by one
                        var finalHTMLcontentForTable = "";
                        for (var systemCounter = 0; systemCounter < totalAmountOfSystems; systemCounter++) {
                          
                          if (systemCounter == 0 && totalAmountOfSystems > 0) { // Mark the first result as selected for detailed view
                            var tableRow = '<tr id="' + responseData[systemCounter]['id'] + '" class="selectedSystemInSearchResults">'; 
                          } else {
                            if (totalAmountOfSystems == 1) { // If only 1 result exists then the details of it are shown automatically, so no need for the deSelectedsystemInSearchResults class
                                var tableRow = '<tr id="' + responseData[systemCounter]['id'] + '">';
                            } else {
                                var tableRow = '<tr id="' + responseData[systemCounter]['id'] + '" class="deSelectedSystemInSearchResults" title="Tryk for at vise detaljer for dette system.">';
                            } 
                          }
                          
                          tableRow += '<td style="padding:4px;"><span style="position:absolute; right:4px; top:4px; font-size:0.7em;">' + (systemCounter+1) + ' / ' + totalAmountOfSystems + '</span>';
                          tableRow += '<div class="systemName"><img src="graphics/bullet_red.png">' + responseData[systemCounter]['Name'] + '</div>';
                          if (responseData[systemCounter]['Description'] !== null && responseData[systemCounter]['Description'] !== "") {
                            tableRow += '<div class="systemDescription">' + responseData[systemCounter]['Description'] + '</div>';
                            } else {
                                tableRow += '<div class="systemDescription_empty">Ingen beskrivelse tilgængelig</div>';
                            }
                          
                          tableRow += '</td>';
                          tableRow += '</tr>';
                          
                          finalHTMLcontentForTable += tableRow;
                        }

                        // Append aggregated projects to the table:
                        $('#searchResultsTable > tbody').append(finalHTMLcontentForTable).foundation(); // Avaid multiple DOM reflows to ensure acceptable performance..

                        hideLoadingSpinner();

                        // Show details of the first search result
                        getSystemDetails(responseData[0]['id']);


                    },
                    error: function (responseData, textStatus, errorThrown) {
                        console.warn(responseData, textStatus, errorThrown);
                        console.dir(responseData);
                        // Inform the user about the error
                        $('#searchResultsTable > tbody > tr').remove();
                        var htmlMsg = "<tr><td colspan='2' style='text-align:center; background:#65512d !important; color:white; font-size:1.5em; margin:10px; font-weight:bold;'>Fejl i søgestreng<br><span style='font-size:0.8em; font-weight:normal;'>Denne form for søgestreng kan ikke anvendes</span></td></tr>";
                        $('#searchResultsTable > tbody').append(htmlMsg).foundation();
                        hideLoadingSpinner();
                      return;
                    }
                });
            }

            function getSystemDetails(id) {

                var searchQuery = $("#searchQuery").val();
                $.ajax({
                    url: 'commandParser.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {command: 'getSystemDetails', id: id, searchQuery: searchQuery},
                    success: function(responseData) {

                        // SYSTEM NAME //
                        <?php
                        
                            if (in_array($currentD1UserName, $kitosAdministrators)) { // A Kitos admin is viewing, so also show a direct link to Kitos from the system name
                                $systemName = "\"<a class='kitosLink' href='https://kitos.dk/#/system/usage/\" + responseData['kitosID'] + \"/main' target='_blank' title='Tryk her for at gå direkte til dette system i Kitos.'>\" + responseData['Name'] + \"</a>\"";
                                echo "$('#systemDetails_name').html(" . $systemName . ");\n";
                            } else { // A non-Kitos admin is viewing, so simply show the system name
                                $systemName = "responseData['Name']";
                                echo "$('#systemDetails_name').html(" . $systemName . ");\n";
                            }
                            
                        ?>
                        
                        // LOCAL NAME //
                        if (responseData['LocalName'] !== null && responseData['LocalName'] !== "") {
                            $("#systemDetails_localName").html(responseData['LocalName']);
                        } else {
                            $("#systemDetails_localName").html('<span class="noInformationAvailableSpan">Ikke angivet</span>');
                        }

                        // SUPPLIER //
                        if (responseData['SupplierName'] !== null && responseData['SupplierName'] !== "") {
                            $("#systemDetails_supplierName").html(responseData['SupplierName']);
                        } else {
                            $("#systemDetails_supplierName").html('<span class="noInformationAvailableSpan">Ikke angivet</span>');
                        }

                        // URL //
                        if (responseData['Url'] !== null && responseData['Url'] !== "") {
                            if (responseData['Url'].length > 40) {
                                var linkContent = "<a class='dont-break-out' title='Tryk her for at åbne dette link' target='_blank' href='" + responseData['Url'] + "'>" + responseData['Url'].substring(0,40) + "...</a>";
                            } else {
                                var linkContent = "<a class='dont-break-out' title='Tryk her for at åbne dette link' target='_blank' href='" + responseData['Url'] + "'>" + responseData['Url'] + "</a>";
                            }
                            $("#systemDetails_url").html(linkContent);
                        } else {
                            $("#systemDetails_url").html('<span class="noInformationAvailableSpan">Ikke angivet</span>');
                        }
                        
                        // KLE //
                        var kleEntries_obj = jQuery.parseJSON(responseData['KleName']);
                        if (kleEntries_obj.length > 0) {
                            var kleEntries_html = "<ul style='margin-bottom:0px;'>";
                            $.each(kleEntries_obj, function(key, value) {
                                kleEntries_html += "<li>" + value.Description + " (" + value.TaskKey + ")</li>";
                            });
                            kleEntries_html += "</ul>";
                            $("#systemDetails_kle").html(kleEntries_html);
                        } else {
                            $("#systemDetails_kle").html('<span class="noInformationAvailableSpan">Ikke angivet</span>');
                        }

                        // BUSINESS TYPE //
                        if (responseData['BusinessType'] !== null && responseData['BusinessType'] !== "") {
                            $("#systemDetails_businessType").html(responseData['BusinessType']);
                        } else {
                            $("#systemDetails_businessType").html('<span class="noInformationAvailableSpan">Ikke angivet</span>');
                        }

                        // SYSTEM OWNER //
                        if (responseData['SystemOwner_name'] !== null && responseData['SystemOwner_name'] !== "") {
                            var systemOwner_html = responseData['SystemOwner_name'];
                            if (responseData['SystemOwner_email'] !== null && responseData['SystemOwner_email'] !== "") {
                                var systemName_rawText = responseData['Name'].replace(/<\/*span.*?>/ig, ""); // Remove potential highlighting of search words in the system name
                                var systemOwner_name_rawText = responseData['SystemOwner_name'].replace(/<\/*span.*?>/ig, ""); // Remove potential highlighting of search words in the system owner name
                                systemOwner_html += "<br><a title='Tryk her for at skrive en mail til " + systemOwner_name_rawText + "' href='mailto:" + responseData['SystemOwner_email'] + "?subject=Vedr. " + systemName_rawText + "'>" + responseData['SystemOwner_email'] + "</a>";
                            }
                        } else {
                            var systemOwner_html = '<span class="noInformationAvailableSpan">Ikke angivet</span>';
                        }
                        $("#systemDetails_systemOwner").html(systemOwner_html);

                        // CONTACT PERSON //
                        if (responseData['ContactPerson_name'] !== null && responseData['ContactPerson_name'] !== "") {
                            var contactPerson_html = responseData['ContactPerson_name'];
                            if (responseData['ContactPerson_email'] !== null && responseData['ContactPerson_email'] !== "") {
                                var systemName_rawText = responseData['Name'].replace(/<\/*span.*?>/ig, ""); // Remove potential highlighting of search words in the system name
                                var ContactPerson_name_rawText = responseData['ContactPerson_name'].replace(/<\/*span.*?>/ig, ""); // Remove potential highlighting of search words in the contact person name
                                contactPerson_html += " <br><a title='Tryk her for at skrive en mail til " + ContactPerson_name_rawText + "' href='mailto:" + responseData['ContactPerson_email'] + "?subject=Vedr. " + systemName_rawText + "'>" + responseData['ContactPerson_email'] + "</a>";
                            }
                        } else {
                            var contactPerson_html = '<span class="noInformationAvailableSpan">Ikke angivet</span>';
                        }
                        $("#systemDetails_contactPerson").html(contactPerson_html);

                        // OPERATIONAL RESPONSIBLE //
                        if (responseData['OperationalResponsible_name'] !== null && responseData['OperationalResponsible_name'] !== "") {
                            var operationalResponsible_html = responseData['OperationalResponsible_name'];
                            if (responseData['OperationalResponsible_email'] !== null && responseData['OperationalResponsible_email'] !== "") {
                                var systemName_rawText = responseData['Name'].replace(/<\/*span.*?>/ig, ""); // Remove potential highlighting of search words in the system name
                                var OperationalResponsible_name_rawText = responseData['OperationalResponsible_name'].replace(/<\/*span.*?>/ig, ""); // Remove potential highlighting of search words in the operational responsible person name
                                operationalResponsible_html += " <br><a title='Tryk her for at skrive en mail til " + OperationalResponsible_name_rawText + "' href='mailto:" + responseData['OperationalResponsible_email'] + "?subject=Vedr. " + systemName_rawText + "'>" + responseData['OperationalResponsible_email'] + "</a>";
                            }
                        } else {
                            var operationalResponsible_html = '<span class="noInformationAvailableSpan">Ikke angivet</span>';
                        }
                        $("#systemDetails_operationalResponsible").html(operationalResponsible_html);

                        // RESPONSIBLE ORGANIZATIONAL UNIT //
                        if (responseData['ResponsibleOrganizationalUnit'] !== null && responseData['ResponsibleOrganizationalUnit'] !== "") {
                            $("#systemDetails_responsibleOrgUnit").html(responseData['ResponsibleOrganizationalUnit']);
                        } else {
                            $("#systemDetails_responsibleOrgUnit").html('<span class="noInformationAvailableSpan">Ikke angivet</span>');
                        }

                        // Make sure to fit the content - especially on narrow-width devices where the right-side column may push and reduce the width of the left-side column.
                        $("#scrollableResultsDIV").width($("#leftColumn").width()); // Only allow the scrollable DIV to take up as much width as is left at the moment.
                        $("#scrollableDetailsDIV").width($("#rightColumn").width());

                        $( ".systemDetailsTable" ).show();
                        $("#searchQuery").focus();
                       
                    }
                });
            }


            function conductAnalysisOfDataQuality() {   

                // Hide the welcome msg box (if still visible)
                $("#welcomeMsgBox").hide();

                $("#searchQuery").val(''); // Clear the search input field - as this functionality deals with all available systems

                // Show results container
                $("#resultsContainer").css('visibility', 'visible');

                // Inform the user about data being sorted by descending level of missing information level
                $("#sortedByDescendingLevelOfMissingInfoLabel").show();

                // Show the data analysis information elements
                $( ".dataAnalysisInformation" ).show();

                // Show loading spinnner
                showLoadingSpinner();

                $.ajax({
                    url: 'commandParser.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {command: 'getAnalysisOfDataQuality'},
                    success: function(responseData) {

                        // Traverse all projects one by one
                        var totalAmountOfSystems = responseData.allSystemsSortedByAscendingInformationLevel.length;
                        $("#amountOfResultsFoundLabel").html(totalAmountOfSystems);
                        if (totalAmountOfSystems == 1) {
                            $("#multipleResultsFoundLabelEnd").hide();
                        } else {
                            $("#multipleResultsFoundLabelEnd").show();
                        }
                        
                        // If no results are found then inform the user about this:
                        if (totalAmountOfSystems < 1) {
                          $('#searchResultsTable > tbody > tr').remove();
                          var htmlMsg = "<tr><td colspan='2' style='text-align:center; background:#65512d !important; color:white; font-size:1.5em; margin:10px; font-weight:bold;'>Ingen resultater fundet<br><span style='font-size:0.8em; font-weight:normal;'>Har du evt. stavet forkert?</span></td></tr>";
                          $('#searchResultsTable > tbody').append(htmlMsg).foundation();
                          clearSystemDetails();
                          hideLoadingSpinner();

                          return;
                        }
                        
                        // Remove current projects (if any) from the table
                        $('#searchResultsTable > tbody > tr').remove();

                        // Render the results one by one
                        var finalHTMLcontentForTable = "";
                        for (var systemCounter = 0; systemCounter < totalAmountOfSystems; systemCounter++) {
                          
                          if (systemCounter == 0 && totalAmountOfSystems > 0) { // Mark the first result as selected for detailed view
                            var tableRow = '<tr id="' + responseData['allSystemsSortedByAscendingInformationLevel'][systemCounter]['id'] + '" class="selectedSystemInSearchResults">'; 
                          } else {
                            if (totalAmountOfSystems == 1) { // If only 1 result exists then the details of it are shown automatically, so no need for the deSelectedsystemInSearchResults class
                                var tableRow = '<tr id="' + responseData['allSystemsSortedByAscendingInformationLevel'][systemCounter]['id'] + '">';
                            } else {
                                var tableRow = '<tr id="' + responseData['allSystemsSortedByAscendingInformationLevel'][systemCounter]['id'] + '" class="deSelectedSystemInSearchResults" title="Tryk for at vise detaljer for dette system.">';
                            } 
                          }
                          
                          tableRow += '<td style="padding:4px;"><span style="position:absolute; right:4px; top:4px; font-size:0.7em;">' + (systemCounter+1) + ' / ' + totalAmountOfSystems + '</span>';
                          tableRow += '<div class="systemName"><img src="graphics/bullet_red.png">' + responseData['allSystemsSortedByAscendingInformationLevel'][systemCounter]['Name'] + '</div>';
                          if (responseData['allSystemsSortedByAscendingInformationLevel'][systemCounter]['Description'] !== null && responseData['allSystemsSortedByAscendingInformationLevel'][systemCounter]['Description'] !== "") {
                            tableRow += '<div class="systemDescription">' + responseData['allSystemsSortedByAscendingInformationLevel'][systemCounter]['Description'] + '</div>';
                            } else {
                                tableRow += '<div class="systemDescription_empty">Ingen beskrivelse tilgængelig</div>';
                            }
                          
                          tableRow += '</td>';
                          tableRow += '</tr>';
                          
                          finalHTMLcontentForTable += tableRow;
                        }

                        // Append aggregated projects to the table:
                        $('#searchResultsTable > tbody').append(finalHTMLcontentForTable).foundation(); // Avaid multiple DOM reflows to ensure acceptable performance..

                        hideLoadingSpinner();

                        // Show details of the first search result
                        getSystemDetails(responseData['allSystemsSortedByAscendingInformationLevel'][0]['id']);

                        //
                        // Update analytics (calculated for all systems):
                        $("#dataAnalysis_systemOwner").attr('aria-valuenow', responseData['dataCompletionPercentages']['systemOwner']);
                        $("#dataAnalysis_systemOwner").attr('title', responseData['dataCompletionPercentages']['systemOwner'] + '% af systemerne har en systemejer angivet.');
                        $("#dataAnalysis_systemOwner span.progress-meter").css('width', '0%');
                        $("#dataAnalysis_systemOwner span.progress-meter").animate({width:responseData['dataCompletionPercentages']['systemOwner'] + '%'});
                        $("#dataAnalysis_systemOwner span.progress-meter p.progress-meter-text").html(responseData['dataCompletionPercentages']['systemOwner'] + '%');
                        $("#dataAnalysis_systemOwner").removeClass("success warning alert"); // Remove all color formatting of the progress bar before adding the correct one - based on the value of the progress bar
                        if (responseData['dataCompletionPercentages']['systemOwner'] >= 0 && responseData['dataCompletionPercentages']['systemOwner'] < 33) {
                            $("#dataAnalysis_systemOwner").addClass("alert");
                        }
                        if (responseData['dataCompletionPercentages']['systemOwner'] >= 33 && responseData['dataCompletionPercentages']['systemOwner'] < 75) {
                            $("#dataAnalysis_systemOwner").addClass("warning");
                        }
                        if (responseData['dataCompletionPercentages']['systemOwner'] >= 75) {
                            $("#dataAnalysis_systemOwner").addClass("success");
                        }

                        $("#dataAnalysis_contactPerson").attr('aria-valuenow', responseData['dataCompletionPercentages']['contactPerson']);
                        $("#dataAnalysis_contactPerson").attr('title', responseData['dataCompletionPercentages']['contactPerson'] + '% af systemerne har en kontaktperson angivet.');
                        $("#dataAnalysis_contactPerson span.progress-meter").css('width', '0%');
                        $("#dataAnalysis_contactPerson span.progress-meter").animate({width:responseData['dataCompletionPercentages']['contactPerson'] + '%'});
                        $("#dataAnalysis_contactPerson span.progress-meter p.progress-meter-text").html(responseData['dataCompletionPercentages']['contactPerson'] + '%');
                        $("#dataAnalysis_contactPerson").removeClass("success warning alert"); // Remove all color formatting of the progress bar before adding the correct one - based on the value of the progress bar
                        if (responseData['dataCompletionPercentages']['contactPerson'] >= 0 && responseData['dataCompletionPercentages']['contactPerson'] < 33) {
                            $("#dataAnalysis_contactPerson").addClass("alert");
                        }
                        if (responseData['dataCompletionPercentages']['contactPerson'] >= 33 && responseData['dataCompletionPercentages']['contactPerson'] < 75) {
                            $("#dataAnalysis_contactPerson").addClass("warning");
                        }
                        if (responseData['dataCompletionPercentages']['contactPerson'] >= 75) {
                            $("#dataAnalysis_contactPerson").addClass("success");
                        }

                        $("#dataAnalysis_operationalResponsible").attr('aria-valuenow', responseData['dataCompletionPercentages']['operationalResponsible']);
                        $("#dataAnalysis_operationalResponsible").attr('title', responseData['dataCompletionPercentages']['operationalResponsible'] + '% af systemerne har en operationel systemansvarlig angivet.');
                        $("#dataAnalysis_operationalResponsible span.progress-meter").css('width', '0%');
                        $("#dataAnalysis_operationalResponsible span.progress-meter").animate({width:responseData['dataCompletionPercentages']['operationalResponsible'] + '%'});
                        $("#dataAnalysis_operationalResponsible span.progress-meter p.progress-meter-text").html(responseData['dataCompletionPercentages']['operationalResponsible'] + '%');
                        $("#dataAnalysis_operationalResponsible").removeClass("success warning alert"); // Remove all color formatting of the progress bar before adding the correct one - based on the value of the progress bar
                        if (responseData['dataCompletionPercentages']['operationalResponsible'] >= 0 && responseData['dataCompletionPercentages']['operationalResponsible'] < 33) {
                            $("#dataAnalysis_operationalResponsible").addClass("alert");
                        }
                        if (responseData['dataCompletionPercentages']['operationalResponsible'] >= 33 && responseData['dataCompletionPercentages']['operationalResponsible'] < 75) {
                            $("#dataAnalysis_operationalResponsible").addClass("warning");
                        }
                        if (responseData['dataCompletionPercentages']['operationalResponsible'] >= 75) {
                            $("#dataAnalysis_operationalResponsible").addClass("success");
                        }



                    },
                    error: function (responseData, textStatus, errorThrown) {
                        console.warn(responseData, textStatus, errorThrown);
                        console.dir(responseData);
                        // Inform the user about the error
                        $('#searchResultsTable > tbody > tr').remove();
                        var htmlMsg = "<tr><td colspan='2' style='text-align:center; background:#65512d !important; color:white; font-size:1.5em; margin:10px; font-weight:bold;'>Fejl i søgestreng<br><span style='font-size:0.8em; font-weight:normal;'>Denne form for søgestreng kan ikke anvendes</span></td></tr>";
                        $('#searchResultsTable > tbody').append(htmlMsg).foundation();
                        hideLoadingSpinner();
                      return;
                    }
                });
            }



            $(document).ready(function() { // Page loaded and ready
                // By default, give focus to the search input field
                $("#searchQuery").focus();
            });


            function clearSystemDetails() {
                var emptyDetailDescriptor = "";
                $("#systemDetails_name").html(emptyDetailDescriptor);
                $("#systemDetails_localName").html(emptyDetailDescriptor);
                $("#systemDetails_note").html(emptyDetailDescriptor);
                $("#systemDetails_supplierName").html(emptyDetailDescriptor);
                $("#systemDetails_url").html(emptyDetailDescriptor);
                $("#systemDetails_kle").html(emptyDetailDescriptor);
                $("#systemDetails_businessType").html(emptyDetailDescriptor);
                $("#systemDetails_systemOwner").html(emptyDetailDescriptor);
                $("#systemDetails_contactPerson").html(emptyDetailDescriptor);
                $("#systemDetails_operationalResponsible").html(emptyDetailDescriptor);
                $("#systemDetails_responsibleOrgUnit").html(emptyDetailDescriptor);
            }


            function showLoadingSpinner() {
                $("#footerLoadingSpinner").show();
            }
              
            
            function hideLoadingSpinner() {
                setTimeout(function() {
                    $("#footerLoadingSpinner").fadeOut('fast');
                }, 600);
                
            }

            $("#searchQuery").focus();

        </script>



    </body>
</html>
