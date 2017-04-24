<?php
 /*
  * DRUCKSTUDIO - LOG DATA LISTING
  *
  */

$logs = new DS_LOGS();
$logs->loadRequestLogs();
$logData = $logs->getLog('default');

echo '<div class="ds-logs">';
echo '  <ul>';
foreach($logData as $logEntry) {
    printf('    <li>%s</li>',$logEntry);
}
echo '  </ul>';
echo '</div>';
echo '<div class="ds-log-button"></div>';

$logs->clearLog();

?>