
<!-- text -->
  <input type="[[+type]]" name="[[+name]]" id="[[+name]]" value="[[+current_value]]" class="[[+class]][[+error_class]]" size="[[+size:default=`40`]]" />
<!-- text -->

<!-- file -->
  <input type="[[+type]]" name="[[+name]][[+array:notempty=`[]`]]" id="[[+name]]" value="[[+current_value]]" class="[[+class]][[+error_class]]" />
<!-- file -->

<!-- hidden -->
  <input type="[[+hidden]]" name="[[+name]]" value="[[+current_value]]" />
<!-- hidden -->

<!-- textarea -->
  <textarea id="[[+name]]" class="[[+type]] [[+class]][[+error_class]]" name="[[+name]]">[[+current_value]]</textarea>
<!-- textarea -->

<!-- checkbox -->
<span class="boolWrap[[+error_class]]">
<input name="[[+name]][[+array:notempty=`[]`]]" type="hidden" value="" />
[[+options_html]]
</span>
<!-- checkbox -->

<!-- radio -->
<span class="boolWrap[[+error_class]]">
<input type="hidden" name="[[+name]]" value="" />
[[+options_html]]
</span>
<!-- radio -->

<!-- select -->
<span class="[[+class]][[+error_class]]">
<input type="hidden" name="[[+name]][[+array:notempty=`[]`]]" value="" />
<select name="[[+name]][[+array:notempty=`[]`]]" id="[[+name]]" class="[[+class]]"[[+multiple:notempty=` multiple="multiple"`]][[+title:notempty=` title="[[+title]]"`]]>
  [[+header:notempty=`<option value="[[+default]]">[[+header]]</option>`]]
  [[+options_html]]
</select>
</span>
<!-- select -->

<!-- static -->
<span class="static_field[[+error_class]]">[[!+[[+name]]]]</span>
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
