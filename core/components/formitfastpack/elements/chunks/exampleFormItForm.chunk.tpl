[[!FormIt? &hooks=`spam,fiProcessArrays,fiGenerateReport,email,redirect` &emailTpl=`exampleFormItEmailReport` &emailTo=`[[++emailsender]]` &emailSubject=`New message from [[++site_name]] [[*pagetitle]] page.` &redirectTo=`6` &submitVar=`submitForm` &figrTpl=`formReportRow`]]
<div>[[!+fi.validation_error_message]] </div>

<form id="formExample" action="[[~[[*id]]]]" method="post">

  <!-- By default, simple fields without options, chunks, or overrides are not cached, nor do they benefit from it. -->
  <!-- To enable caching anyways, specify &cache=`1`. -->
  [[!field? &type=`text` &req=`1` &name=`name`]]
  
  <!-- Here, a different style of form fields is used by switching out of the default chunks. -->
  <!-- Use property sets to easily maintain various form styles around the site. -->
  [[!field? &type=`text` &req=`1` &name=`email` &tpl=`aDifferentTemplate` &outer_tpl=`ADifferentOuterTpl`]]
  
  <!-- You can disable the outer template. -->
  [[!field? &type=`hidden` &outer_tpl=`` &name=`blank`]]
  
  <!-- Here, there were too many options to list, so a chunk name is specified instead that probably contains a snippet to generate the option HTML. -->
  <!-- By default, fields that have options use smart caching. To disable caching, specify &cache=`0`. -->
  <!-- Note that caching does not cache checked/ selected status or the following dynamically-generated placeholders: error, error_class, and current_value -->
  [[!field? &type=`select` &default=`1` &name=`country` &label=`Your Country:` &options_chunk=`optionsCountries`]]  
    
  <!-- &req=`1` is an example of a custom placeholder. In this case, it can be used to add an asterisk or something similar to the label using the notempty output filter -->
  [[!field? &type=`textarea` &class=`elastic` &req=`1` &name=`message` &label=`Comment`]]
  
  [[!field? &type=`radio` &req=`1` &name=`color` &label=`Your Favorite Color:` &default=`` &options=`Red==red||Blue==blue||Other==default`]]
  
  <!-- There is no need to specify a label if you have a naming convention for your form fields. -->
  <!-- For example, use [[+name:replace=`_== `:ucwords]] to generate a label in your outer template. -->
  [[!field? &type=`select` &name=`favorite_things` &multiple=`1` &array=`1`
  	&options=`MODx||Money||Power||Other`
  ]]
  
  <!-- a custom field with a custom type. -->
 <!--  If you use options with a custom type, you need to specify the type of the options fields with &option_type. -->
  [[!field? &type=`customtype` &name=`custom_field_type` &note=`Make sure you add this custom field to the &tpl chunk!` &custom_placeholder=`custom_value` &another_custom_placeholder=`And another custom value` &options=`One||Two||Three` &option_type=`radio`]]
  
  <!-- Even simple field types like the submit field are a breeze to add. -->
  [[!field? &type=`submit` &name=`submitForm`]]

</div>
</form>