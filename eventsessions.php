<?php

require_once 'eventsessions.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function eventsessions_civicrm_config(&$config) {
  _eventsessions_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param array $files
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function eventsessions_civicrm_xmlMenu(&$files) {
  _eventsessions_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function eventsessions_civicrm_install() {
  _eventsessions_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function eventsessions_civicrm_uninstall() {
  _eventsessions_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function eventsessions_civicrm_enable() {
  _eventsessions_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function eventsessions_civicrm_disable() {
  _eventsessions_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function eventsessions_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _eventsessions_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function eventsessions_civicrm_managed(&$entities) {
  _eventsessions_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * @param array $caseTypes
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function eventsessions_civicrm_caseTypes(&$caseTypes) {
  _eventsessions_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function eventsessions_civicrm_angularModules(&$angularModules) {
_eventsessions_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function eventsessions_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _eventsessions_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function eventsessions_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function eventsessions_civicrm_navigationMenu(&$menu) {
  _eventsessions_civix_insert_navigation_menu($menu, NULL, array(
    'label' => ts('The Page', array('domain' => 'uk.co.vedaconsulting.eventsessions')),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _eventsessions_civix_navigationMenu($menu);
} // */

function eventsessions_civicrm_preProcess($formName, &$form) {
  if (in_array($formName, array('CRM_Event_Form_Registration_Register', 'CRM_Event_Form_Registration_AdditionalParticipant')) && !empty($form->_priceSetId)) {
    // Assumes templates are in a templates folder relative to this file
    $templatePath = realpath(dirname(__FILE__)."/templates");
    // dynamically insert a template block in the page
    CRM_Core_Region::instance('page-body')->add(array(
      'template' => "{$templatePath}/eventsessions.tpl"
    ));
    $query = "SELECT pfd.*
      FROM civicrm_price_field_dependency pfd
      WHERE pfd.price_set_id = %1";
    $dao   = CRM_Core_DAO::executeQuery($query, array(1 => array($form->_priceSetId, 'Positive')));
    while ($dao->fetch()) {
      $dependencyMapper["{$dao->depends_on_pfid}_{$dao->depends_on_fid}"][] = $dao->required_pfid;
      $dependentPfids[$dao->required_pfid] = 1;
      $triggeringPfids[$dao->depends_on_fid] = 1;
    }
    if (!empty($dependentPfids)) {
      $form->assign('dependentPfids', json_encode($dependentPfids));
      $form->assign('mapper', json_encode($dependencyMapper));

      $triggeringPfids = array_keys($triggeringPfids);
      $form->assign('triggeringPfids', json_encode($triggeringPfids));
    }
  }
}

function eventsessions_civicrm_pageRun(&$page) {
  $pageName = $page->getVar('_name');
  if ($pageName == 'CRM_Event_Page_EventInfo') {
    // #6749 - hide pricing for event sessions
    $eventId = $page->getVar('_id');
    $discountId = CRM_Core_BAO_Discount::findSet($eventId, 'civicrm_event');
    if ($discountId) {
      $priceSetId = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Discount', $discountId, 'price_set_id');
    }
    else {
      $priceSetId = CRM_Price_BAO_PriceSet::getFor('civicrm_event', $eventId);
    }
    if ($priceSetId) {
      $query = "SELECT pfd.id
        FROM civicrm_price_field_dependency pfd
        WHERE pfd.price_set_id = %1 LIMIT 1";
      $dao = CRM_Core_DAO::executeQuery($query, array(1 => array($priceSetId, 'Positive')));
      if ($dao->fetch()) {
        // set the stage/vars for calling buildAmountHook implementation explicitly
        //
        // we need to set the role in order for hook to work
        $session =& CRM_Core_Session::singleton( ); 
        $cid     = $session->get( 'userID' );
        $roleId  = CRM_Bgsutils_Utils::getRoleByMembershipAndProfession($cid);
        if ($roleId) {
          $page->set('participantrole', md5($roleId));
        } else {
          // if we don't set a default role, all price fields would appear
          $defaultParticipantRole = CRM_Core_DAO::getFieldValue('CRM_Event_DAO_Event', $eventId, 'default_role_id');
          $page->set('participantrole', md5($defaultParticipantRole));
        }
        // $priceSetFields returned by hook is what we 'll use to rebuild feeBlock
        $setDetails     = CRM_Price_BAO_PriceSet::getSetDetail($priceSetId, TRUE, TRUE);
        $priceSetFields = $setDetails[$priceSetId]['fields'];
        $page->set('priceSetId', $priceSetId);
        $page->_priceSet = $setDetails;
        $feeBlock = $page->getTemplate()->get_template_vars("feeBlock");
        rolebasedpricing_civicrm_buildAmount('event', $page, $priceSetFields);

        $values = array();

        // can't see a clean way to hijack and re-build feeblock for 'EventInfo'
        // use code block from EventInfo.php to rebuild the feeBlock.
        //
        // code block from event info - to re-scan $priceSetFeilds to re-build feeBlock
        if (is_array($priceSetFields)) {
          $fieldCnt = 1;
          $visibility = CRM_Core_PseudoConstant::visibility('name');

          // CRM-14492 Admin price fields should show up on event registration if user has 'administer CiviCRM' permissions
          $adminFieldVisible = FALSE;
          if (CRM_Core_Permission::check('administer CiviCRM')) {
            $adminFieldVisible = TRUE;
          }

          foreach ($priceSetFields as $fid => $fieldValues) {
            if (!is_array($fieldValues['options']) ||
              empty($fieldValues['options']) ||
              (CRM_Utils_Array::value('visibility_id', $fieldValues) != array_search('public', $visibility) && $adminFieldVisible == FALSE)
            ) {
              continue;
            }

            if (count($fieldValues['options']) > 1) {
              $values['feeBlock']['value'][$fieldCnt] = '';
              $values['feeBlock']['label'][$fieldCnt] = $fieldValues['label'];
              $values['feeBlock']['lClass'][$fieldCnt] = 'price_set_option_group-label';
              $values['feeBlock']['isDisplayAmount'][$fieldCnt] = CRM_Utils_Array::value('is_display_amounts', $fieldValues);
              $fieldCnt++;
              $labelClass = 'price_set_option-label';
            }
            else {
              $labelClass = 'price_set_field-label';
            }
            // show tax rate with amount
            $invoiceSettings = Civi::settings()->get('contribution_invoice_settings');
            $taxTerm = CRM_Utils_Array::value('tax_term', $invoiceSettings);
            $displayOpt = CRM_Utils_Array::value('tax_display_settings', $invoiceSettings);
            $invoicing = CRM_Utils_Array::value('invoicing', $invoiceSettings);
            foreach ($fieldValues['options'] as $optionId => $optionVal) {
              $values['feeBlock']['isDisplayAmount'][$fieldCnt] = CRM_Utils_Array::value('is_display_amounts', $fieldValues);
              if ($invoicing && isset($optionVal['tax_amount'])) {
                $values['feeBlock']['value'][$fieldCnt] = CRM_Price_BAO_PriceField::getTaxLabel($optionVal, 'amount', $displayOpt, $taxTerm);
                $values['feeBlock']['tax_amount'][$fieldCnt] = $optionVal['tax_amount'];
              }
              else {
                $values['feeBlock']['value'][$fieldCnt] = $optionVal['amount'];
              }
              // DS: extra bit - code to suppress amount if its a session
              $values['feeBlock']['value'][$fieldCnt] = ($optionVal['amount'] <= 0) ? '' : $values['feeBlock']['value'][$fieldCnt];
              $values['feeBlock']['label'][$fieldCnt] = $optionVal['label'];
              $values['feeBlock']['lClass'][$fieldCnt] = $labelClass;
              $fieldCnt++;
            }
          }
        }
        // code block from event info - ends
        $feeBlock = $values['feeBlock'];

        // set the new feeBlock
        $page->assign('feeBlock', $feeBlock);
      }
    }
  }
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_buildForm
 */
function eventsessions_civicrm_buildForm($formName, &$form) {
  if ($formName == "CRM_Price_Form_Field" && $form->getVar('_action') == CRM_Core_Action::UPDATE) {
    $mapperList = array();
    $psid = $form->getVar('_sid');
    $pfid = $form->getVar('_fid');
    if ($psid && $pfid) {
      $priceFieldBAO = new CRM_Price_BAO_PriceField();
      $priceFieldBAO->price_set_id = $psid;
      $priceFieldBAO->orderBy('weight', 'field_name');
      $priceFieldBAO->find();
      while ($priceFieldBAO->fetch()) {
        if ($priceFieldBAO->id == $pfid) {
          continue;
        }
        $priceOptions = civicrm_api3('PriceFieldValue', 'get', array(
          'price_field_id' => $priceFieldBAO->id,
          // Explicitly do not check permissions so we are not
          // restricted by financial type
          'check_permissions' => FALSE,
          'options' => array(
            'limit' => 0,
          ),
        ));
        foreach ($priceOptions['values'] as $key => $val) {
          $mapperList["{$psid}_{$pfid}_{$priceFieldBAO->id}_{$val['id']}"] = "{$priceFieldBAO->label}::{$val['label']}";
        }
      }
      $form->addCheckBox("depends_on_pfids", ts('Required but dependent on'), $mapperList, NULL, NULL, NULL, NULL, '<br />', TRUE);

      // set defaults
      $sql = "SELECT * FROM civicrm_price_field_dependency WHERE price_set_id = %1 AND required_pfid = %2";
      $dao = CRM_Core_DAO::executeQuery($sql,
        array(
          1 => array($psid, 'Positive'),
          2 => array($pfid, 'Positive'),
        )
      );
      $defaults = array();
      while ($dao->fetch()) {
        $defaults['depends_on_pfids']["{$dao->price_set_id}_{$dao->required_pfid}_{$dao->depends_on_pfid}_{$dao->depends_on_fid}"] = 1;
      }
      $form->setDefaults($defaults);

      $templatePath = realpath(dirname(__FILE__)."/templates");
      // dynamically insert a template block in the page
      // Note extra won't be called by civi in this case as its not the direct tpl
      CRM_Core_Region::instance('page-body')->add(array(
        'template' => "{$templatePath}/CRM/Price/Form/Field.extra.tpl"
      ));
    }
  }
}

/**
 * Implements hook_civicrm_validateForm().
 * Event form priceset validation
 *
 * @param string $formName
 * @param array $fields
 * @param array $files
 * @param CRM_Core_Form $form
 * @param array $errors
 */
function eventsessions_civicrm_validateForm($formName, &$fields, &$files, &$form, &$errors) {
  // sample implementation
  if (in_array($formName, array('CRM_Event_Form_Registration_Register', 'CRM_Event_Form_Registration_AdditionalParticipant'))) {
    if (!empty($fields['priceSetId'])) {
      foreach ($fields as $key => $val) {
        //FIXME: also try other options - select, checkbox etc, 
        //       and single value field (text/numeric)
        if (strpos($key, 'price_') !== false && !empty($val)) {
          $price = explode("_", $key);
          $pfid  = $price[1];
          $params = array(
            1 => array($pfid, 'Positive'),
            3 => array($fields['priceSetId'], 'Positive'),
          );
      
          // $val is array for checkboxes
          $fidFilter = NULL;
          if (is_array($val) && !CRM_Utils_Array::crmIsEmptyArray($val)) {
            CRM_Utils_Type::escapeAll($val, 'Positive');
            $pfids = implode(', ', array_keys($val));
            $fidFilter = " pfd.depends_on_fid IN ( {$pfids} ) ";
          }
          else {
            $fidFilter = " pfd.depends_on_fid = %2";
            $params[2]   = array($val, 'Positive');
          }

          $query = "SELECT pfd.required_pfid, pf.label 
            FROM civicrm_price_field_dependency pfd
            INNER JOIN civicrm_price_field pf ON pfd.required_pfid = pf.id
            WHERE pfd.depends_on_pfid = %1 AND {$fidFilter} AND pfd.price_set_id = %3";
          $dao = CRM_Core_DAO::executeQuery($query, $params);
          while ($dao->fetch()) {
            $reqPfid  = $dao->required_pfid;
            if ($reqPfid && empty($fields["price_{$reqPfid}"])) {
              $errors["price_{$reqPfid}"] = "{$dao->label} is a required but dependent field."; 
            }
          }
        }
      }
    }
  }
}

/**
 * Implements hook_civicrm_postProcess().
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postProcess
 */
function eventsessions_civicrm_postProcess($formName, &$form) {
  if ($formName == "CRM_Price_Form_Field") {
    $params = $form->controller->exportValues('Field');
    if ($params['sid'] && $params['fid']) {
      // delete records before adding new ones
      $sql = "DELETE FROM civicrm_price_field_dependency WHERE price_set_id = %2 AND required_pfid = %1";
      $dao = CRM_Core_DAO::executeQuery($sql,
        array(
          1 => array($params['fid'], 'Positive'),
          2 => array($params['sid'], 'Positive'),
        )
      );
    }
      // insert new ones
    if (!empty($params['depends_on_pfids'])) {
      foreach ($params['depends_on_pfids'] as $key => $val) {
        $dependencyIds = explode('_', $key);
        $sql = "INSERT INTO civicrm_price_field_dependency 
          (price_set_id, required_pfid, depends_on_pfid, depends_on_fid) 
          VALUES (%1, %2, %3, %4)";
        $sqlParams = array(
          1 => array($dependencyIds[0], 'Positive'),
          2 => array($dependencyIds[1], 'Positive'),
          3 => array($dependencyIds[2], 'Positive'),
          4 => array($dependencyIds[3], 'Positive'),
        );
        CRM_Core_DAO::executeQuery($sql, $sqlParams);
      }
    }
  }
}

/**
 * Implements hook_civicrm_copy(().
 *
 * Copy over dependency when priceset is copied.
 *
 */
function eventsessions_civicrm_copy($objectName, &$object) {
  $sid = CRM_Utils_Request::retrieve('sid', 'Positive', CRM_Core_DAO::$_nullObject, FALSE, NULL, 'GET');
  if ($objectName == 'Set' && $sid && $object->id) {
    $sql = "INSERT INTO civicrm_price_field_dependency (price_set_id, required_pfid, depends_on_pfid, depends_on_fid) 
      SELECT %1, d2_req_pfid.id, d2_dep_pfid.id, d2_dep_fid.id 
      FROM       civicrm_price_field_dependency d1 
      INNER JOIN civicrm_price_field d1_req_pfid ON d1.required_pfid = d1_req_pfid.id
      INNER JOIN civicrm_price_field d2_req_pfid ON d2_req_pfid.price_set_id = %1 AND d1_req_pfid.name = d2_req_pfid.name
      INNER JOIN civicrm_price_field d1_dep_pfid ON d1.depends_on_pfid = d1_dep_pfid.id
      INNER JOIN civicrm_price_field d2_dep_pfid ON d2_dep_pfid.price_set_id = %1 AND d1_dep_pfid.name = d2_dep_pfid.name
      INNER JOIN civicrm_price_field_value d1_dep_fid ON d1.depends_on_fid = d1_dep_fid.id
      INNER JOIN civicrm_price_field_value d2_dep_fid ON d2_dep_fid.price_field_id = d2_dep_pfid.id AND d1_dep_fid.name = d2_dep_fid.name
      WHERE d1.price_set_id = %2";
    $sqlParams = array(
      1 => array($object->id, 'Positive'),
      2 => array($sid, 'Positive'),
    );
    CRM_Core_DAO::executeQuery($sql, $sqlParams);
    CRM_Core_Error::debug_log_message("PriceSet dependency copied from PSID:{$sid} to PSID:{$object->id}");
  }
}
