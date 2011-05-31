
<!-- text -->
  <input type="[[+type]]" name="[[+name]]" id="[[+name]]" value="[[+current_value]]" class="[[+class]][[+error_class]]" size="40">
<!-- text -->

<!-- textarea -->
  <textarea id="[[+name]]" class="[[+type]] [[+class]][[+error_class]]" name="[[+name]]">[[+current_value]]</textarea>
<!-- textarea -->

<!-- checkbox -->
<input name="[[+name]]" type="hidden" value="[[+default]]" />
[[+options_html]]
<!-- checkbox -->

<!-- radio -->
<span class="boolWrap">
<input type="hidden" name="[[+name]]" value="[[+default]]" />
[[+options_html]]
</span>
<!-- radio -->

<!-- submit -->
<input id="[[+name]]" class="button" name="[[+name]]" type="[[+type]]" value="[[+message:default=`Submit`]]" />
<a href="[[~[[*id]]]]" class="button">Cancel</a>
<!-- submit -->

<!-- select -->
<select name="[[+name]]" id="[[+name]]" class="[[+class]]"[[+multiple:notempty=` multiple="multiple"`]]>
  [[+header:notempty=`<option name="[[+name]]" value="[[+default]]">[[+header]]</option>`]]
  [[+options_html]]
</select>
<!-- select -->

<!-- static -->
<span class="static_field">[[!+[[+name]]]]</span>
<!-- static -->

<!-- option --><option value="[[+value]]" [[!+[[+prefix]][[+name]]:FormItIsSelected=`[[+value]]`]]>[[+label]]</option><!-- option -->

<!-- bool --><span class="boolDiv [[+class]]">
  <input type="[[+subtype]]" class="[[+subtype]]" value="[[+label]]" name="[[+name]][[+array:notempty=`[]`]]" id="[[+name]][[+label:strip:stripString=` `:stripString=`/`]]" [[!+[[+prefix]][[+name]]:FormitIsChecked=`[[+label]]`]] /> 
<label for="[[+name]][[+label:strip:stripString=` `:stripString=`/`]]" class="[[+subtype]]" id="label[[+name]][[+idx]]">[[+message:default=`[[+label]]`]]</label></span><!-- bool -->
