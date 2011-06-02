
<!-- text -->
  <input type="[[+type]]" name="[[+name]]" id="[[+name]]" value="[[+current_value]]" class="[[+class]][[+error_class]]" size="40">
<!-- text -->

<!-- textarea -->
  <textarea id="[[+name]]" class="[[+type]] [[+class]][[+error_class]]" name="[[+name]]">[[+current_value]]</textarea>
<!-- textarea -->

<!-- checkbox -->
<span class="boolWrap">
<input name="[[+name]][[+array:notempty=`[]`]]" type="hidden" value="" />
[[+options_html]]
</span>
<!-- checkbox -->

<!-- radio -->
<span class="boolWrap">
<input type="hidden" name="[[+name]]" value="" />
[[+options_html]]
</span>
<!-- radio -->

<!-- select -->
<select name="[[+name]][[+array:notempty=`[]`]]" id="[[+name]]" class="[[+class]]"[[+multiple:notempty=` multiple="multiple"`]][[+title:notempty=` title="[[+title]]"`]]>
  [[+header:notempty=`<option value="[[+default]]">[[+header]]</option>`]]
  [[+options_html]]
</select>
<!-- select -->

<!-- static -->
<span class="static_field">[[!+[[+name]]]]</span>
<!-- static -->

<!-- submit -->
<input id="[[+name]]" class="button" name="[[+name]]" type="[[+type]]" value="[[+message:default=`Submit`]]" />
<input id="[[+name]]-clear" class="button" type="reset" value="[[+clear_message:default=`Clear Form`]]" />
<!-- submit -->

<!-- option --><option value="[[+value]]">[[+label]]</option><!-- option -->

<!-- bool -->
<span class="boolDiv [[+class]]">
<input type="[[+type]]" class="[[+type]]" value="[[+value]]" name="[[+name]][[+array:notempty=`[]`]]" id="[[+key]]"  /> 
<label for="[[+key]]" class="[[+type]]" id="label[[+key]]">[[+label]]</label></span><!-- bool -->
