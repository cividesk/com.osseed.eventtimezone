<?php

require_once 'eventtimezone.civix.php';
use CRM_Eventtimezone_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function eventtimezone_civicrm_config(&$config) {
  _eventtimezone_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function eventtimezone_civicrm_xmlMenu(&$files) {
  _eventtimezone_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function eventtimezone_civicrm_install() {
  _eventtimezone_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function eventtimezone_civicrm_postInstall() {
  _eventtimezone_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function eventtimezone_civicrm_uninstall() {
  _eventtimezone_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function eventtimezone_civicrm_enable() {
  _eventtimezone_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function eventtimezone_civicrm_disable() {
  _eventtimezone_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function eventtimezone_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _eventtimezone_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function eventtimezone_civicrm_managed(&$entities) {
  _eventtimezone_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function eventtimezone_civicrm_caseTypes(&$caseTypes) {
  _eventtimezone_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function eventtimezone_civicrm_angularModules(&$angularModules) {
  _eventtimezone_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function eventtimezone_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _eventtimezone_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_postProcess().
 */
function eventtimezone_civicrm_postProcess($formName, &$form) {
  if ($formName == 'CRM_Event_Form_ManageEvent_EventInfo') {
    $submit =  $form->getVar('_submitValues');
    $timezone = $submit['timezone'];
    if (empty($form->_id) && !empty($submit['timezone'])) {
      $result = civicrm_api3('Event', 'get', array(
        'sequential' => 1,
        'return' => array("id"),
        'title' => $submit['title'],
        'event_type_id' => $submit['event_type_id'],
        'default_role_id' => $submit['default_role_id'],
      ));
      if ($result['count'] == 1) {
        $event_id = $result['values'][0]['id'];
        $query = "
        UPDATE civicrm_event
        SET timezone = '$timezone'
        WHERE id = $event_id
        ";
        CRM_Core_DAO::executeQuery($query);
      }
    }
    else {
      $event_id = $form->_id;
      $query = "
      UPDATE civicrm_event
      SET timezone = '$timezone'
      WHERE id = $event_id
      ";
      CRM_Core_DAO::executeQuery($query);
    }
  }
}

/**
 * Implements hook_civicrm_apiWrappers().
 */
function eventtimezone_civicrm_entityTypes(&$entityTypes) {
  $entityTypes['CRM_Event_DAO_Event']['fields_callback'][]
    = function ($class, &$fields) {
      $fields['timezone'] = array(
         'name' => 'timezone',
         'type' => CRM_Utils_Type::T_INT,
         'title' => ts('Timezone') ,
         'description' => 'Event Timezone',
         'table_name' => 'civicrm_event',
         'entity' => 'Event',
         'bao' => 'CRM_Event_BAO_Event',
         'localizable' => 0,
       );
    };
}

function get_timezone_for_event($eventID) {
  list($zones, $zoneabbr) = get_timezones();
  $result = civicrm_api3('Event', 'get', array(
    'sequential' => 1,
    'return' => array('timezone'),
    'id' => $eventID,
  ));
  if (isset($result['values'][0])){
    $timezone = $zoneabbr[$result['values'][0]['timezone']];
  }
  return $timezone;
}


function eventtimezone_civicrm_alterMailParams(&$params, $context = NULL) {
  if ($params['valueName'] == 'event_online_receipt') {
    if ($eventID = $params['tplParams']['event']['id']) {
      $params['tplParams']['event']['timezone'] = get_timezone_for_event($eventID);
    }
  }
}

function timezone_list() {
  static $timezones = null;
  if ($timezones === null) {
    $timezones = $offsets = [];
    $now = new DateTime('now', new DateTimeZone('UTC'));

    foreach (DateTimeZone::listIdentifiers() as $timezone) {
      $now->setTimezone(new DateTimeZone($timezone));
      $offsets[] = $offset = $now->getOffset();
      $timezones[$timezone] = '(' . format_GMT_offset($offset) . ') ' . format_timezone_name($timezone);
    }
     array_multisort($offsets, $timezones);
  }
  return $timezones;
}

function format_GMT_offset($offset) {
  $hours = intval($offset / 3600);
  $minutes = abs(intval($offset % 3600 / 60));
  return 'GMT' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '');
}

function format_timezone_name($name) {
  $name = str_replace('/', ', ', $name);
  $name = str_replace('_', ' ', $name);
  $name = str_replace('St ', 'St. ', $name);
  return $name;
}

function get_timezones() {
  $zones = timezone_list();
  $zoneabbr = [];
  foreach ($zones as $t => $x) {
    $date = new DateTime(null, new DateTimeZone($t));
    $zoneabbr[$t] = $date->format('T');
  }
  return [$zones, $zoneabbr];
}

/**
 * Implements hook_civicrm_alterContent().
 */
function eventtimezone_civicrm_alterContent( &$content, $context, $tplName, &$object ) {
  list($zones, $zoneabbr) = get_timezones();
  $eventInfoFormContext = ($context == 'form' && $tplName == 'CRM/Event/Form/ManageEvent/EventInfo.tpl');
  $eventInfoPageContext = ($context == 'page' && $tplName == 'CRM/Event/Page/EventInfo.tpl');
  $eventConfirmFormContext = ($context == 'form' && $tplName == 'CRM/Event/Form/Registration/Confirm.tpl');
  $eventConfirmPageContext = ($context == 'form' && $tplName == 'CRM/Event/Form/Registration/ThankYou.tpl');

  if ($eventInfoFormContext || $eventInfoPageContext) {
    $result = civicrm_api3('Event', 'get', array(
      'sequential' => 1,
      'return' => array("timezone"),
      'id' => $object->_id,
    ));
    if (isset($result['values'][0])){
      $timezone = $zoneabbr[$result['values'][0]['timezone']];
    }

    if($eventInfoPageContext && $timezone != '_none' && !empty($timezone)) {
      // Add timezone besides the date data
      $content = str_replace("</abbr>", " " . $timezone . " </abbr>", $content);
    } elseif ($eventInfoFormContext) {
      $timezone_identifiers = DateTimeZone::listIdentifiers();
      $timezone_field = '<tr class="crm-event-manage-eventinfo-form-block-timezone">
      <td class="label"><label for="timezone">Timezone</label></td>
      <td>
      <select name="timezone" id="timezone" class="crm-form-select">';
      if ($timezone) {
       $timezone_field .= '<option value="'.$result['values'][0]['timezone'].'" selected="">'.$zones[$result['values'][0]['timezone']].'</option>';
      }
      $timezone_field .= '<option value="_none">Select Timezone</option>';
      foreach ($zones as $name => $value) {
        $timezone_field .= '<option value="' . $name . '">' . $value . '</option>';
      }
      $timezone_field .= '</select>
      </td>
      </tr>
      <tr class="crm-event-manage-eventinfo-form-block-start_date">';
      $content = str_replace('<tr class="crm-event-manage-eventinfo-form-block-start_date">', $timezone_field, $content);
    }
  }
  elseif ($eventConfirmFormContext || $eventConfirmPageContext) {
    $result = $result = civicrm_api3('Event', 'get', array(
      'sequential' => 1,
      'return' => array("start_date","end_date", "timezone"),
      'id' => $object->_eventId,
    ));
    $event_start_date = $result['values'][0]['event_start_date'];
    $event_end_date = $result['values'][0]['event_end_date'];

    $timezone = $zoneabbr[$result['values'][0]['timezone']];
    $start_date_con = new DateTime($event_start_date);
    $start_date_st = date_format($start_date_con, 'F jS, Y g:iA');
    $start_date = date_format($start_date_con, 'F jS');

    $end_date_con = new DateTime($event_end_date);
    $end_date_st = date_format($end_date_con, 'F jS, Y g:iA');
    $end_date = date_format($end_date_con, 'F jS');

    $end_date_time = new DateTime($event_end_date);
    $end_time = date_format($end_date_time, 'g:iA');

    if($timezone != '_none' && !empty($timezone && !empty($event_end_date))) {
      // Add timezone besides the date data
      if ($start_date == $end_date) {
        $replacement = "<td width='90%'>" . $start_date_st . " " .  $timezone . " through " . $end_time . " " . $timezone . "</td>";
        $content = preg_replace('#(<td width="90%">)(.*?)(</td>)#si', $replacement, $content);
      }
      else {
        $replacement = "<td width='90%'>" . $start_date_st . " " .  $timezone . " through " . $end_date_st . " " . $timezone . "</td>";
        $content = preg_replace('#(<td width="90%">)(.*?)(</td>)#si', $replacement, $content);
      }
    }
    elseif (empty($event_end_date)) {
      $replacement = "<td width='90%'>" . $start_date_st . " " .  $timezone . "</td>";
      $content = preg_replace('#(<td width="90%">)(.*?)(</td>)#si', $replacement, $content);
    }
  }
}
