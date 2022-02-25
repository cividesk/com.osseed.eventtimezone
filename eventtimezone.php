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
    $submit = $form->getVar('_submitValues');
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

function get_timezone_for_events($eventID) {
  $result = civicrm_api3('Event', 'get', array(
    'sequential' => 1,
    'return' => array('timezone'),
    'id' => $eventID,
  ));

  if (isset($result['values'][0])){
    return $result['values'][0]['timezone'];
  }
  return;
}

/**
 * Implementation of hook_civicrm_buildForm
 */
function eventtimezone_civicrm_buildForm($formName, &$form) {
  if ($formName == 'CRM_Event_Form_ManageEvent_EventInfo') {
    $timezones = tz_lookup();
    $form->addSelect('timezone', ['placeholder' => ts('- Select time zone -'), 'options' => $timezones, 'class' => 'huge']);
    $eventID = $form->getVar('_id');
    if ($eventID) {
      $event_tz = get_timezone_for_events($eventID);
      $form->setDefaults(array('timezone' => $event_tz));
    }

    // Add our template to the page. Note our template uses jQuery to reorder/change the form
    CRM_Core_Region::instance('page-body')->add(array('template' => 'timezone.tpl'));
  }
}

/**
 * Implementation of hook_civicrm_alterMailParams
 */
function eventtimezone_civicrm_alterMailParams(&$params, $context = NULL) {
  if ($params['valueName'] == 'event_online_receipt') {
    if ($eventID = $params['tplParams']['event']['id']) {
      $event_tz = get_timezone_for_events($eventID);
      $params['tplParams']['event']['timezone'] = get_timezone_abbr($event_tz);
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

function tz_lookup() {
  $tz = [
// 'GMT' => 'Greenwich Mean Time (GMT)',
// 'GMT+01:00' => 'Central European Time (CET)',
// 'GMT+02:00' => 'Eastern European Time (EET)',
// 'GMT+03:00' => 'Moscow Time (MSK)',
// 'GMT+03:30' => (GMT+03:30) Asia, Tehran
// 'GMT+04:00' => 'Armenia Time (AMT)',
// 'GMT+04:30' => (GMT+04:30) Asia, Kabul
// 'GMT+05:00' => 'Pakistan Standard Time (PKT)',
// 'GMT+05:30' => (GMT+05:30) Asia, Kolkata
// 'GMT+05:45' => (GMT+05:45) Asia, Kathmandu
// 'GMT+06:00' => 'Omsk Time (OMSK)',
// 'GMT+06:30' => 'Kranoyask Time (KRAT)',
// 'GMT+07:00' => 'Kranoyask Time (KRAT)',
// 'GMT+08:00' => 'China Standard Time (CST)',
// 'GMT+08:45' => (GMT+08:45) Australia, Eucla
// 'GMT+09:00' => 'Japan Standard Time (JST)',
// 'GMT+09:30' => (GMT+09:30) Australia, Darwin
// 'GMT+10:00' => 'Eastern Australia Standard Time (AEST)',
// 'GMT+10:30' => (GMT+10:30) Australia, Broken Hill
// 'GMT+11:00' => 'Sakhalin Time (SAKT)',
// 'GMT+12:00' => 'New Zealand Standard Time (NZST)',
// 'GMT+13:00' => (GMT+13:00) Pacific, Tongatapu
// 'GMT+13:45' => (GMT+13:45) Pacific, Chatham
// 'GMT+14:00' => (GMT+14:00) Pacific, Kiritimati
// 'GMT-01:00' => 'West Africa Time (WAT)',
// 'GMT-02:00' => 'Azores Time (AT)',
// 'GMT-03:00' => 'Argentina Time (ART)',
// 'GMT-03:30' => (GMT-03:30) America, St. Johns
// 'GMT-04:00' => 'Atlantic Standard Time (AST)',
//osticket 10143 : remove standard from EST
// so people don't worry when it's daylight savings time.
   'GMT-05:00' => 'Eastern Time (ET)',
   'GMT-06:00' => 'Central Time (CT)',
   'GMT-07:00' => 'Mountain Time (MT)',
   'GMT-08:00' => 'Pacific Time (PT)',
// 'GMT-09:00' => 'Alaska Standard Time (AKST)',
// 'GMT-09:30' => (GMT-09:30) Pacific, Marquesas
// 'GMT-10:00' => 'Hawaii Standard Time (HST)',
// 'GMT-11:00' => 'Nome Time (NT)',
  ];

  return $tz;
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

/*
 * get abbreviation for the given timezone
 *
 */
function get_timezone_abbr($timezone) {
  if (!$timezone) {
    return ;
  }
  $all_tz = tz_lookup();
  $tz = $all_tz[$timezone];
  if (preg_match('!\(([^\)]+)\)!', $tz, $match)) {
    $abbr = $match[1];
  }
  return $abbr;
}

/**
 * Implements hook_civicrm_alterContent().
 */
function eventtimezone_civicrm_alterContent( &$content, $context, $tplName, &$object ) {
  $eventInfoPageContext = ($context == 'page' && $tplName == 'CRM/Event/Page/EventInfo.tpl');
  $eventConfirmFormContext = ($context == 'form' && $tplName == 'CRM/Event/Form/Registration/Confirm.tpl');
  $eventConfirmPageContext = ($context == 'form' && $tplName == 'CRM/Event/Form/Registration/ThankYou.tpl');

  if ($eventInfoPageContext) {
    $event_tz = get_timezone_for_events($object->_id);
    $timezone = get_timezone_abbr($event_tz);
    if($eventInfoPageContext && $timezone != '_none' && !empty($timezone)) {
      // Add timezone besides the date data
      $content = str_replace("</abbr>", " " . $timezone . " </abbr>", $content);
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
    $event_tz = get_timezone_for_events($object->_eventId);
    $all_tz = tz_lookup();
    $timezone = $all_tz[$event_tz];
    $match = [];
    if (preg_match('!\(([^\)]+)\)!', $all_tz[$event_tz], $match)) {
      $timezone = $match[1];
    }

    //$timezone = get_timezone_abbr($result['values'][0]['timezone']);
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
