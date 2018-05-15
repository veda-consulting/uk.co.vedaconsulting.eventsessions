{* js to disable non relevant options - if dependency requirement is configured with the priceset *}
{if !empty($mapper)}
<script type="text/javascript">
  {literal}
  (function($) {
    // Show "Event Sessions" title assuming first is fee and rest are all sessions.
    $("div#priceset > div").first().after('<div class="crm-section event_fees-section"><div class="label"><label><fieldset><legend>Event Sessions</legend></fieldset></label></div><div class="clear"></div></div>');

    // disable all dependents first
    disableAllDependents();
    $("#priceset [price]").each(function () {
      // onload
      hideSessionPrices(this);
      if ($(this).prop('checked')) {
        enableDisableDependencyPriceFields(this, 'onload');
      }
      // click event
      $(this).click(function(){
        enableDisableDependencyPriceFields(this);
      });
    });

    function disableAllDependents(uncheck = false) {
      var dependentPfids = $.parseJSON('{/literal}{$dependentPfids}{literal}');
      $.each(dependentPfids, function(value, dnc) {
        if (uncheck) {
          $("input[type=checkbox][name^='price_" + value + "[']").prop('checked', false);
          $("input[type=radio][name='price_" + value + "']").prop('checked', false);
        }
        $("input[type=checkbox][name^='price_" + value + "[']").attr('disabled', true);
        $("input[type=radio][name='price_" + value + "']").attr('disabled', true);
      });
    }

    function hideSessionPrices(ele) {
      var fname = $("div#priceset").find("input:first:enabled").attr('name');
      var arr   = fname ? fname.split('[') : '-99';
      var firstPf = arr[0];
      if ($(ele).val() > 0 && $(ele).attr('name').indexOf(firstPf) == -1) {
        var lbl = $(ele).parent().find("label").children("span.crm-price-amount-label");
        $(ele).parent().find("label").html(lbl);
        $(ele).parent().find("label").children("span.crm-price-amount-label").children("span.crm-price-amount-label-separator").remove();// to remove hyphen for radio options
      }
    }

    function enableDisableDependents(pfid, enable) {
      if (!enable) { 
        // before disable, uncheck any selections
        $("input[type=checkbox][name^='price_" + pfid + "[']").prop('checked', enable);
        $("input[type=radio][name='price_" + pfid + "']").prop('checked', enable);
      }
      // enable disable related fields
      $("input[type=checkbox][name^='price_" + pfid + "[']").attr('disabled', !enable);
      $("input[type=radio][name='price_" + pfid + "']").attr('disabled', !enable);
    }
   
    function enableDisableDependencyPriceFields(priceElement, callType = 'click') {
      // mapper with non relevant fields / fields to disable
      var mapper = $.parseJSON('{/literal}{$mapper}{literal}');
      var triggeringPfids = $.parseJSON('{/literal}{$triggeringPfids}{literal}');
      //fixme: add support for select
      if ($(priceElement).attr('type') == 'radio') {
        var fid  = $(priceElement).val();
        var pfid = $(priceElement).data('priceFieldValues')[fid]['price_field_id'];
        if ($.inArray(parseInt(fid), triggeringPfids) !== -1) {
          // for radio it's like uncheck one option and select another
          // so we disable all first
          var uncheck = (callType == 'onload') ? false: true;
          disableAllDependents(uncheck);
          // then enable dependent
          $.each(mapper[pfid + '_' + fid], function(index, pfid) {
            enableDisableDependents(pfid, true);
          });
        }
      } else if ($(priceElement).attr('type') == 'checkbox') {
        var chkboxId  = $(priceElement).attr('id');
        var isChecked = $(priceElement).prop('checked');
        var arr  = chkboxId.split('_'); 
        var fid  = arr[2];
        var pfid = arr[1];
        if ($.inArray(parseInt(fid), triggeringPfids) !== -1) {
          // for checkbox it's a new/update selection, so we only enable / disable
          $.each(mapper[pfid + '_' + fid], function(index, pfid) {
            enableDisableDependents(pfid, isChecked);
          });
        }
      }
    }
  })(CRM.$ || cj);
  {/literal}
</script>
{/if}
