<div class="crm-block crm-form-block crm-event-manage-eventinfo-form-block">
<table class="form-layout-compressed">
 <tr class="crm-event-manage-eventinfo-form-block-event_tz">
    <td class="label">{$form.event_tz.label}</td>
    <td>{$form.event_tz.html}</td>
 </tr>
</table>
</div>
{literal}
<script type="text/javascript">
  // Move timezone block before event start date
  //alert(cj('.crm-event-manage-eventinfo-form-block-event_tz').length);
  cj('.crm-event-manage-eventinfo-form-block-event_tz').insertAfter('.crm-event-manage-eventinfo-form-block-description');
  cj('.crm-event-manage-eventinfo-form-block-event_tz:eq(1)').hide();
</script>
{/literal}
